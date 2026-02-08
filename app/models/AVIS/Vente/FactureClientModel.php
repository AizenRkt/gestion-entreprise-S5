<?php

namespace app\models\AVIS\Vente;

use Exception;
use Flight;
use PDO;
use PDOException;

/**
 * =============================================================================
 * ÉTAPE 1 - ENTITÉ FACTURE CLIENT
 * =============================================================================
 * 
 * RÔLE FONCTIONNEL:
 * -----------------
 * La table facture_client matérialise la créance envers le client.
 * Elle est générée à partir d'une livraison validée.
 * Document comptable et légal obligatoire.
 * 
 * FLUX LOGIQUE:
 * -------------
 * DEVIS -> COMMANDE -> LIVRAISON -> [FACTURE] -> ENCAISSEMENT
 * 
 * RELATIONS:
 * ----------
 * - facture_client -> livraison_client (obligatoire, livraison LIVREE)
 * - facture_client -> client
 * - facture_client -> encaissement_client (peut avoir plusieurs encaissements)
 * 
 * CHAMPS DE TRAÇABILITÉ:
 * ----------------------
 * - cree_par: utilisateur créateur
 * - valide_par: utilisateur validateur
 * - date_validation: date de validation
 * - statut: BROUILLON, VALIDE, PAYE
 * 
 * =============================================================================
 * ÉTAPE 2 - WORKFLOW D'ÉTATS
 * =============================================================================
 * 
 * ÉTATS AUTORISÉS:
 * ----------------
 * - BROUILLON: Facture en cours de création
 * - VALIDE: Facture émise, en attente de paiement
 * - PAYE: Facture totalement réglée
 * 
 * TRANSITIONS POSSIBLES:
 * ----------------------
 * BROUILLON -> VALIDE (validation comptable)
 * VALIDE -> PAYE (automatique quand encaissements = montant TTC)
 * 
 * ACTIONS INTERDITES:
 * -------------------
 * - Créer facture sur livraison non LIVREE
 * - Modifier une facture VALIDE ou PAYE
 * - Supprimer une facture avec encaissements
 */
class FactureClientModel
{
    const STATUT_BROUILLON = 'BROUILLON';
    const STATUT_VALIDE = 'VALIDE';
    const STATUT_PAYE = 'PAYE';

    /**
     * Liste toutes les factures
     */
    public function listFactures(): array
    {
        try {
            $db = Flight::db();
            $sql = "SELECT fc.*, 
                           c.nom AS client_nom,
                           lc.livraison_numero,
                           (SELECT COALESCE(SUM(montant), 0) FROM encaissement_client ec WHERE ec.facture_client_id = fc.id_facture_client) AS montant_encaisse
                    FROM facture_client fc
                    LEFT JOIN client c ON fc.client_id = c.id_client
                    LEFT JOIN livraison_client lc ON fc.livraison_client_id = lc.id_livraison_client
                    ORDER BY fc.created_at DESC";
            $stmt = $db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            throw new Exception('Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Récupère une facture par son ID
     */
    public function findById(int $factureId): ?array
    {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("SELECT fc.*, 
                                         c.nom AS client_nom, c.adresse AS client_adresse,
                                         c.telephone AS client_telephone, c.email AS client_email,
                                         lc.livraison_numero,
                                         cc.commande_numero
                                  FROM facture_client fc
                                  LEFT JOIN client c ON fc.client_id = c.id_client
                                  LEFT JOIN livraison_client lc ON fc.livraison_client_id = lc.id_livraison_client
                                  LEFT JOIN commande_client cc ON lc.id_commande_client = cc.id_commande_client
                                  WHERE fc.id_facture_client = :id");
            $stmt->execute(['id' => $factureId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;
        } catch (PDOException $e) {
            throw new Exception('Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Génère un numéro de facture unique
     */
    public function generateNumero(): string
    {
        $prefix = 'FAC-' . date('Ymd') . '-';
        $db = Flight::db();
        $stmt = $db->query("SELECT MAX(CAST(SUBSTRING(numero_facture, LENGTH('$prefix') + 1) AS UNSIGNED)) AS max_num 
                            FROM facture_client 
                            WHERE numero_facture LIKE '$prefix%'");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $nextNum = ($result['max_num'] ?? 0) + 1;
        return $prefix . str_pad($nextNum, 4, '0', STR_PAD_LEFT);
    }

    /**
     * =============================================================================
     * ÉTAPE 4 - BLOCAGE: Création de facture
     * =============================================================================
     * 
     * BLOCAGE 3: Facture sans livraison validée
     * CONDITION: La livraison référencée doit avoir statut = 'LIVREE'
     * MESSAGE: "Impossible de créer une facture: la livraison n'est pas validée"
     */
    public function create(array $data): int
    {
        $livraisonId = (int)($data['livraison_client_id'] ?? 0);
        $creePar = (int)($data['cree_par'] ?? 0);
        $date = $data['date_facture'] ?? date('Y-m-d H:i:s');

        if ($livraisonId === 0) {
            throw new Exception('La livraison est obligatoire.');
        }
        if ($creePar === 0) {
            throw new Exception('L\'utilisateur créateur est obligatoire.');
        }

        // =============================================================
        // BLOCAGE: Vérifier que la livraison est LIVREE
        // =============================================================
        $livraisonModel = new LivraisonClientModel();
        $livraison = $livraisonModel->findById($livraisonId);
        
        if (!$livraison) {
            throw new Exception('Livraison introuvable.');
        }
        
        if ($livraison['statut'] !== LivraisonClientModel::STATUT_LIVREE) {
            throw new Exception(
                "BLOCAGE: Impossible de créer une facture. " .
                "La livraison {$livraison['livraison_numero']} n'est pas validée (statut: {$livraison['statut']})."
            );
        }

        // Vérifier qu'il n'y a pas déjà une facture pour cette livraison
        $db = Flight::db();
        $stmt = $db->prepare("SELECT 1 FROM facture_client WHERE livraison_client_id = :id LIMIT 1");
        $stmt->execute(['id' => $livraisonId]);
        if ($stmt->fetchColumn()) {
            throw new Exception("Une facture existe déjà pour cette livraison.");
        }

        // Calculer les montants à partir des lignes de livraison
        $montants = $this->calculerMontantsDepuisLivraison($livraisonId);

        $numero = $this->generateNumero();
        $parsedDate = strtotime($date);
        if ($parsedDate === false) {
            throw new Exception('Date de facture invalide.');
        }

        try {
            $stmt = $db->prepare("INSERT INTO facture_client 
                                  (numero_facture, date_facture, client_id, livraison_client_id, 
                                   montant_ht, montant_tva, montant_ttc, statut, cree_par) 
                                  VALUES 
                                  (:numero, :date, :client, :livraison, :ht, :tva, :ttc, :statut, :cree_par)");
            $stmt->execute([
                'numero' => $numero,
                'date' => date('Y-m-d H:i:s', $parsedDate),
                'client' => $livraison['id_client'],
                'livraison' => $livraisonId,
                'ht' => $montants['ht'],
                'tva' => $montants['tva'],
                'ttc' => $montants['ttc'],
                'statut' => self::STATUT_BROUILLON,
                'cree_par' => $creePar
            ]);
            return (int)$db->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception('Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Calcule les montants à partir des lignes de livraison
     */
    private function calculerMontantsDepuisLivraison(int $livraisonId): array
    {
        $db = Flight::db();
        $stmt = $db->prepare("SELECT 
                                SUM(llc.quantite_livree * lcc.prix_unitaire * (1 - lcc.remise_pourcent/100)) AS ht,
                                SUM(llc.quantite_livree * lcc.prix_unitaire * (1 - lcc.remise_pourcent/100) * lcc.taux_tva/100) AS tva
                              FROM ligne_livraison_client llc
                              JOIN ligne_commande_client lcc ON llc.id_ligne_commande_client = lcc.id_ligne_commande_client
                              WHERE llc.id_livraison_client = :id");
        $stmt->execute(['id' => $livraisonId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $ht = (float)($result['ht'] ?? 0);
        $tva = (float)($result['tva'] ?? 0);
        
        return [
            'ht' => $ht,
            'tva' => $tva,
            'ttc' => $ht + $tva
        ];
    }

    /**
     * Valide une facture (BROUILLON -> VALIDE)
     */
    public function valider(int $factureId, int $validePar): bool
    {
        $db = Flight::db();
        
        $facture = $this->findById($factureId);
        if (!$facture) {
            throw new Exception('Facture introuvable.');
        }

        if ($facture['statut'] !== self::STATUT_BROUILLON) {
            throw new Exception('Seule une facture en BROUILLON peut être validée.');
        }

        $stmt = $db->prepare("UPDATE facture_client 
                              SET statut = :statut, 
                                  valide_par = :valide_par, 
                                  date_validation = NOW() 
                              WHERE id_facture_client = :id");
        return $stmt->execute([
            'statut' => self::STATUT_VALIDE,
            'valide_par' => $validePar,
            'id' => $factureId
        ]);
    }

    /**
     * Marque une facture comme payée (automatique via encaissements)
     */
    public function marquerPayee(int $factureId): bool
    {
        $db = Flight::db();
        $stmt = $db->prepare("UPDATE facture_client SET statut = :statut WHERE id_facture_client = :id");
        return $stmt->execute([
            'statut' => self::STATUT_PAYE,
            'id' => $factureId
        ]);
    }

    /**
     * Récupère le montant restant à payer
     */
    public function getMontantRestant(int $factureId): float
    {
        $db = Flight::db();
        $stmt = $db->prepare("SELECT 
                                fc.montant_ttc,
                                COALESCE(SUM(ec.montant), 0) AS montant_encaisse
                              FROM facture_client fc
                              LEFT JOIN encaissement_client ec ON ec.facture_client_id = fc.id_facture_client
                              WHERE fc.id_facture_client = :id
                              GROUP BY fc.id_facture_client");
        $stmt->execute(['id' => $factureId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$result) {
            return 0;
        }
        
        return max(0, (float)$result['montant_ttc'] - (float)$result['montant_encaisse']);
    }

    /**
     * Récupère les factures validées non totalement payées
     */
    public function getFacturesImpayees(): array
    {
        try {
            $db = Flight::db();
            $sql = "SELECT fc.*, 
                           c.nom AS client_nom,
                           fc.montant_ttc - COALESCE((SELECT SUM(montant) FROM encaissement_client ec WHERE ec.facture_client_id = fc.id_facture_client), 0) AS reste_a_payer
                    FROM facture_client fc
                    LEFT JOIN client c ON fc.client_id = c.id_client
                    WHERE fc.statut = :statut
                    HAVING reste_a_payer > 0
                    ORDER BY fc.date_facture ASC";
            $stmt = $db->prepare($sql);
            $stmt->execute(['statut' => self::STATUT_VALIDE]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            throw new Exception('Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Alias pour getFacturesImpayees (pour la vue encaissements)
     */
    public function getFacturesAEncaisser(): array
    {
        return $this->getFacturesImpayees();
    }
}
