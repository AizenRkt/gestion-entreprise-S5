<?php

namespace app\models\AVIS\Vente;

use Exception;
use Flight;
use PDO;
use PDOException;

/**
 * =============================================================================
 * ÉTAPE 1 - ENTITÉ LIVRAISON CLIENT
 * =============================================================================
 * 
 * RÔLE FONCTIONNEL:
 * -----------------
 * La table livraison_client enregistre les sorties physiques de marchandises.
 * Elle fait le lien entre la commande validée et la sortie effective du stock.
 * Peut être partielle (plusieurs livraisons pour une commande).
 * 
 * FLUX LOGIQUE:
 * -------------
 * DEVIS -> COMMANDE -> [LIVRAISON] -> FACTURE -> ENCAISSEMENT
 * 
 * RELATIONS:
 * ----------
 * - livraison_client -> commande_client (obligatoire, commande VALIDEE)
 * - livraison_client -> depot (stock de sortie)
 * - livraison_client -> facture_client (une livraison = une facture)
 * 
 * CHAMPS DE TRAÇABILITÉ:
 * ----------------------
 * - cree_par: utilisateur créateur
 * - valide_par: utilisateur validateur
 * - date_validation: date de validation
 * - statut: BROUILLON, LIVREE
 * 
 * =============================================================================
 * ÉTAPE 2 - WORKFLOW D'ÉTATS
 * =============================================================================
 * 
 * ÉTATS AUTORISÉS:
 * ----------------
 * - BROUILLON: Livraison en préparation
 * - LIVREE: Livraison effectuée, stock décrémenté
 * 
 * TRANSITIONS POSSIBLES:
 * ----------------------
 * BROUILLON -> LIVREE (validation avec décrémentation stock)
 * 
 * ACTIONS INTERDITES:
 * -------------------
 * - Créer livraison sur commande non VALIDEE
 * - Modifier une livraison LIVREE
 * - Livrer plus que le stock disponible
 */
class LivraisonClientModel
{
    const STATUT_BROUILLON = 'BROUILLON';
    const STATUT_LIVREE = 'LIVREE';

    /**
     * Liste toutes les livraisons
     */
    public function listLivraisons(): array
    {
        try {
            $db = Flight::db();
            $sql = "SELECT lc.*, 
                           cc.commande_numero,
                           c.nom AS client_nom,
                           d.nom AS depot_nom
                    FROM livraison_client lc
                    LEFT JOIN commande_client cc ON lc.id_commande_client = cc.id_commande_client
                    LEFT JOIN client c ON cc.id_client = c.id_client
                    LEFT JOIN depot d ON lc.id_depot = d.id_depot
                    ORDER BY lc.created_at DESC";
            $stmt = $db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            throw new Exception('Erreur lors de la récupération des livraisons: ' . $e->getMessage());
        }
    }

    /**
     * Récupère une livraison par son ID
     */
    public function findById(int $livraisonId): ?array
    {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("SELECT lc.*, 
                                         cc.commande_numero, cc.id_client,
                                         c.nom AS client_nom,
                                         d.nom AS depot_nom
                                  FROM livraison_client lc
                                  LEFT JOIN commande_client cc ON lc.id_commande_client = cc.id_commande_client
                                  LEFT JOIN client c ON cc.id_client = c.id_client
                                  LEFT JOIN depot d ON lc.id_depot = d.id_depot
                                  WHERE lc.id_livraison_client = :id");
            $stmt->execute(['id' => $livraisonId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;
        } catch (PDOException $e) {
            throw new Exception('Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Génère un numéro de livraison unique
     */
    public function generateNumero(): string
    {
        $prefix = 'BL-' . date('Ymd') . '-';
        $db = Flight::db();
        $stmt = $db->query("SELECT MAX(CAST(SUBSTRING(livraison_numero, LENGTH('$prefix') + 1) AS UNSIGNED)) AS max_num 
                            FROM livraison_client 
                            WHERE livraison_numero LIKE '$prefix%'");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $nextNum = ($result['max_num'] ?? 0) + 1;
        return $prefix . str_pad($nextNum, 4, '0', STR_PAD_LEFT);
    }

    /**
     * =============================================================================
     * ÉTAPE 4 - BLOCAGE: Création de livraison
     * =============================================================================
     * 
     * BLOCAGE 1: Livraison sans commande validée
     * CONDITION: La commande référencée doit avoir statut = 'VALIDE'
     * MESSAGE: "Impossible de créer une livraison: la commande n'est pas validée"
     */
    public function create(array $data): int
    {
        $commandeId = (int)($data['id_commande_client'] ?? 0);
        $depotId = (int)($data['id_depot'] ?? 0);
        $creePar = (int)($data['cree_par'] ?? 0);
        $date = $data['livraison_date'] ?? date('Y-m-d H:i:s');

        if ($commandeId === 0) {
            throw new Exception('La commande est obligatoire.');
        }
        if ($creePar === 0) {
            throw new Exception('L\'utilisateur créateur est obligatoire.');
        }

        // =============================================================
        // BLOCAGE: Vérifier que la commande est VALIDEE
        // =============================================================
        $commandeModel = new CommandeClientModel();
        $commande = $commandeModel->findById($commandeId);
        
        if (!$commande) {
            throw new Exception('Commande introuvable.');
        }
        
        if ($commande['statut'] !== CommandeClientModel::STATUT_VALIDE) {
            throw new Exception(
                "BLOCAGE: Impossible de créer une livraison. " .
                "La commande {$commande['commande_numero']} n'est pas validée (statut: {$commande['statut']})."
            );
        }

        // Utiliser le dépôt de la commande si non fourni
        if ($depotId === 0) {
            $depotId = (int)$commande['id_depot'];
        }

        $numero = $this->generateNumero();
        $parsedDate = strtotime($date);
        if ($parsedDate === false) {
            throw new Exception('Date de livraison invalide.');
        }

        try {
            $db = Flight::db();
            $stmt = $db->prepare("INSERT INTO livraison_client 
                                  (livraison_numero, livraison_date, id_commande_client, 
                                   id_depot, statut, cree_par) 
                                  VALUES 
                                  (:numero, :date, :commande, :depot, :statut, :cree_par)");
            $stmt->execute([
                'numero' => $numero,
                'date' => date('Y-m-d H:i:s', $parsedDate),
                'commande' => $commandeId,
                'depot' => $depotId,
                'statut' => self::STATUT_BROUILLON,
                'cree_par' => $creePar
            ]);
            return (int)$db->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception('Erreur lors de la création de la livraison: ' . $e->getMessage());
        }
    }

    /**
     * =============================================================================
     * ÉTAPE 4 - BLOCAGE: Validation de livraison
     * =============================================================================
     * 
     * BLOCAGE 2: Livraison sans stock disponible
     * CONDITION: stock_courant.quantite >= quantite_a_livrer pour chaque article
     * MESSAGE: "Stock insuffisant pour l'article X"
     * 
     * IMPACT STOCK:
     * - Création mouvement_stock de type 'sortie_vente'
     * - Décrémentation stock_courant
     * - Mise à jour lot (FIFO/LIFO selon méthode)
     */
    public function valider(int $livraisonId, int $validePar): bool
    {
        $db = Flight::db();
        
        try {
            $db->beginTransaction();

            $livraison = $this->findById($livraisonId);
            if (!$livraison) {
                throw new Exception('Livraison introuvable.');
            }

            if ($livraison['statut'] !== self::STATUT_BROUILLON) {
                throw new Exception('Seule une livraison en BROUILLON peut être validée.');
            }

            // Récupérer les lignes de livraison
            $lignesModel = new LigneLivraisonClientModel();
            $lignes = $lignesModel->getByLivraisonId($livraisonId);

            if (empty($lignes)) {
                throw new Exception('La livraison doit contenir au moins un article.');
            }

            // =============================================================
            // BLOCAGE: Vérifier le stock disponible
            // =============================================================
            $stockModel = new StockModel();
            foreach ($lignes as $ligne) {
                $stockDispo = $stockModel->getStockDisponible($ligne['id_article'], $livraison['id_depot']);
                if ($stockDispo < $ligne['quantite_livree']) {
                    throw new Exception(
                        "BLOCAGE: Stock insuffisant pour l'article {$ligne['designation']}. " .
                        "Disponible: {$stockDispo}, Demandé: {$ligne['quantite_livree']}"
                    );
                }
            }

            // Effectuer les sorties de stock
            foreach ($lignes as $ligne) {
                $stockModel->sortieVente(
                    $ligne['id_article'],
                    $livraison['id_depot'],
                    $ligne['quantite_livree'],
                    $livraisonId,
                    $validePar
                );
            }

            // Mettre à jour le statut
            $stmt = $db->prepare("UPDATE livraison_client 
                                  SET statut = :statut, 
                                      valide_par = :valide_par, 
                                      date_validation = NOW() 
                                  WHERE id_livraison_client = :id");
            $stmt->execute([
                'statut' => self::STATUT_LIVREE,
                'valide_par' => $validePar,
                'id' => $livraisonId
            ]);

            // Vérifier si la commande doit être clôturée
            $this->verifierCloturCommande($livraison['id_commande_client']);

            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /**
     * Vérifie si toutes les lignes de commande sont livrées et clôture si oui
     */
    private function verifierCloturCommande(int $commandeId): void
    {
        $db = Flight::db();
        
        // Vérifier les quantités restantes
        $stmt = $db->prepare("SELECT lcc.id_ligne_commande_client,
                                     lcc.quantite AS qte_commandee,
                                     COALESCE(SUM(llc.quantite_livree), 0) AS qte_livree
                              FROM ligne_commande_client lcc
                              LEFT JOIN ligne_livraison_client llc ON lcc.id_ligne_commande_client = llc.id_ligne_commande_client
                              LEFT JOIN livraison_client lc ON llc.id_livraison_client = lc.id_livraison_client AND lc.statut = 'LIVREE'
                              WHERE lcc.id_commande_client = :id
                              GROUP BY lcc.id_ligne_commande_client");
        $stmt->execute(['id' => $commandeId]);
        $lignes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $toutLivre = true;
        foreach ($lignes as $ligne) {
            if ((float)$ligne['qte_livree'] < (float)$ligne['qte_commandee']) {
                $toutLivre = false;
                break;
            }
        }

        if ($toutLivre) {
            $commandeModel = new CommandeClientModel();
            $commandeModel->cloturer($commandeId);
        }
    }

    /**
     * Récupère les livraisons validées sans facture
     */
    public function getLivraisonsSansFacture(): array
    {
        try {
            $db = Flight::db();
            $sql = "SELECT lc.*, 
                           cc.commande_numero,
                           c.nom AS client_nom,
                           cc.id_client
                    FROM livraison_client lc
                    LEFT JOIN commande_client cc ON lc.id_commande_client = cc.id_commande_client
                    LEFT JOIN client c ON cc.id_client = c.id_client
                    LEFT JOIN facture_client fc ON fc.livraison_client_id = lc.id_livraison_client
                    WHERE lc.statut = :statut AND fc.id_facture_client IS NULL
                    ORDER BY lc.livraison_date ASC";
            $stmt = $db->prepare($sql);
            $stmt->execute(['statut' => self::STATUT_LIVREE]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            throw new Exception('Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Alias pour getLivraisonsSansFacture (pour la vue factures)
     */
    public function getLivraisonsEffectuees(): array
    {
        return $this->getLivraisonsSansFacture();
    }
}
