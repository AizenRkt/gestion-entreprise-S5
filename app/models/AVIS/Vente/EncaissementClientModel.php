<?php

namespace app\models\AVIS\Vente;

use Exception;
use Flight;
use PDO;
use PDOException;

/**
 * =============================================================================
 * ÉTAPE 1 - ENTITÉ ENCAISSEMENT CLIENT
 * =============================================================================
 * 
 * RÔLE FONCTIONNEL:
 * -----------------
 * La table encaissement_client enregistre les paiements reçus des clients.
 * C'est l'étape finale du flux de vente.
 * Permet les paiements partiels et multi-modes.
 * 
 * FLUX LOGIQUE:
 * -------------
 * DEVIS -> COMMANDE -> LIVRAISON -> FACTURE -> [ENCAISSEMENT]
 * 
 * RELATIONS:
 * ----------
 * - encaissement_client -> facture_client (obligatoire, facture VALIDEE)
 * - encaissement_client -> mode_paiement (espèces, chèque, virement, CB)
 * 
 * CHAMPS DE TRAÇABILITÉ:
 * ----------------------
 * - cree_par: utilisateur qui a enregistré l'encaissement
 * - created_at: date d'enregistrement
 * - reference_paiement: numéro de chèque, référence virement, etc.
 * 
 * =============================================================================
 * ÉTAPE 2 - WORKFLOW
 * =============================================================================
 * 
 * L'encaissement n'a pas de workflow complexe, il est créé directement.
 * L'impact sur la facture est automatique:
 * - Si total encaissements >= montant_ttc → facture passe à PAYE
 */
class EncaissementClientModel
{
    /**
     * Liste tous les encaissements
     */
    public function listEncaissements(): array
    {
        try {
            $db = Flight::db();
            $sql = "SELECT ec.*, 
                           fc.numero_facture,
                           c.nom AS client_nom,
                           mp.libelle AS mode_paiement_libelle
                    FROM encaissement_client ec
                    LEFT JOIN facture_client fc ON ec.facture_client_id = fc.id_facture_client
                    LEFT JOIN client c ON fc.client_id = c.id_client
                    LEFT JOIN mode_paiement mp ON ec.id_mode_paiement = mp.id_mode_paiement
                    ORDER BY ec.created_at DESC";
            $stmt = $db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            throw new Exception('Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Récupère un encaissement par son ID
     */
    public function findById(int $encaissementId): ?array
    {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("SELECT ec.*, 
                                         fc.numero_facture, fc.montant_ttc,
                                         c.nom AS client_nom,
                                         mp.libelle AS mode_paiement_libelle
                                  FROM encaissement_client ec
                                  LEFT JOIN facture_client fc ON ec.facture_client_id = fc.id_facture_client
                                  LEFT JOIN client c ON fc.client_id = c.id_client
                                  LEFT JOIN mode_paiement mp ON ec.id_mode_paiement = mp.id_mode_paiement
                                  WHERE ec.id_encaissement_client = :id");
            $stmt->execute(['id' => $encaissementId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;
        } catch (PDOException $e) {
            throw new Exception('Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Génère un numéro d'encaissement unique
     */
    public function generateNumero(): string
    {
        $prefix = 'ENC-' . date('Ymd') . '-';
        $db = Flight::db();
        $stmt = $db->query("SELECT MAX(CAST(SUBSTRING(numero_encaissement, LENGTH('$prefix') + 1) AS UNSIGNED)) AS max_num 
                            FROM encaissement_client 
                            WHERE numero_encaissement LIKE '$prefix%'");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $nextNum = ($result['max_num'] ?? 0) + 1;
        return $prefix . str_pad($nextNum, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Crée un encaissement
     * 
     * RÈGLES:
     * - La facture doit être VALIDEE
     * - Le montant ne peut pas dépasser le reste à payer
     * - Met à jour automatiquement le statut de la facture si soldée
     */
    public function create(array $data): int
    {
        $factureId = (int)($data['facture_client_id'] ?? 0);
        $montant = (float)($data['montant'] ?? 0);
        $modePaiementId = (int)($data['id_mode_paiement'] ?? 0);
        $referencePaiement = trim($data['reference_paiement'] ?? '');
        $creePar = (int)($data['cree_par'] ?? 0);
        $date = $data['date_encaissement'] ?? date('Y-m-d H:i:s');

        if ($factureId === 0) {
            throw new Exception('La facture est obligatoire.');
        }
        if ($montant <= 0) {
            throw new Exception('Le montant doit être supérieur à 0.');
        }
        if ($modePaiementId === 0) {
            throw new Exception('Le mode de paiement est obligatoire.');
        }
        if ($creePar === 0) {
            throw new Exception('L\'utilisateur créateur est obligatoire.');
        }

        // Vérifier que la facture est VALIDEE
        $factureModel = new FactureClientModel();
        $facture = $factureModel->findById($factureId);
        
        if (!$facture) {
            throw new Exception('Facture introuvable.');
        }
        
        if ($facture['statut'] === FactureClientModel::STATUT_BROUILLON) {
            throw new Exception("Impossible d'encaisser: la facture {$facture['numero_facture']} n'est pas validée.");
        }
        
        if ($facture['statut'] === FactureClientModel::STATUT_PAYE) {
            throw new Exception("La facture {$facture['numero_facture']} est déjà totalement payée.");
        }

        // Vérifier le montant restant
        $resteAPayer = $factureModel->getMontantRestant($factureId);
        if ($montant > $resteAPayer) {
            throw new Exception("Le montant ({$montant}) dépasse le reste à payer ({$resteAPayer}).");
        }

        $db = Flight::db();
        
        try {
            $db->beginTransaction();

            $numero = $this->generateNumero();
            $parsedDate = strtotime($date);
            if ($parsedDate === false) {
                throw new Exception('Date d\'encaissement invalide.');
            }

            $stmt = $db->prepare("INSERT INTO encaissement_client 
                                  (numero_encaissement, date_encaissement, facture_client_id, 
                                   montant, id_mode_paiement, reference_paiement, cree_par) 
                                  VALUES 
                                  (:numero, :date, :facture, :montant, :mode, :ref, :cree_par)");
            $stmt->execute([
                'numero' => $numero,
                'date' => date('Y-m-d H:i:s', $parsedDate),
                'facture' => $factureId,
                'montant' => $montant,
                'mode' => $modePaiementId,
                'ref' => $referencePaiement,
                'cree_par' => $creePar
            ]);

            $encaissementId = (int)$db->lastInsertId();

            // Vérifier si la facture est soldée
            $nouveauReste = $factureModel->getMontantRestant($factureId);
            if ($nouveauReste <= 0) {
                $factureModel->marquerPayee($factureId);
            }

            $db->commit();
            return $encaissementId;
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /**
     * Récupère les encaissements d'une facture
     */
    public function getByFactureId(int $factureId): array
    {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("SELECT ec.*, mp.libelle AS mode_paiement_libelle
                                  FROM encaissement_client ec
                                  LEFT JOIN mode_paiement mp ON ec.id_mode_paiement = mp.id_mode_paiement
                                  WHERE ec.facture_client_id = :id
                                  ORDER BY ec.date_encaissement ASC");
            $stmt->execute(['id' => $factureId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            throw new Exception('Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Récupère tous les modes de paiement
     */
    public function getModesPaiement(): array
    {
        try {
            $db = Flight::db();
            $sql = "SELECT * FROM mode_paiement ORDER BY libelle ASC";
            $stmt = $db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            throw new Exception('Erreur: ' . $e->getMessage());
        }
    }
}
