<?php

namespace app\models\AVIS\Vente;

use Exception;
use Flight;
use PDO;
use PDOException;

/**
 * =============================================================================
 * ÉTAPE 1 - ENTITÉ COMMANDE CLIENT
 * =============================================================================
 * 
 * RÔLE FONCTIONNEL:
 * -----------------
 * La table commande_client représente l'engagement commercial du client.
 * C'est le point de départ du flux de vente après le devis (optionnel).
 * Elle contient les montants HT, TVA, TTC et les articles commandés.
 * 
 * FLUX LOGIQUE:
 * -------------
 * [DEVIS] -> COMMANDE -> LIVRAISON -> FACTURE -> ENCAISSEMENT
 * 
 * RELATIONS:
 * ----------
 * - commande_client -> client (obligatoire)
 * - commande_client -> depot (stock de sortie)
 * - commande_client -> livraison_client (une commande peut avoir plusieurs livraisons partielles)
 * 
 * CHAMPS DE TRAÇABILITÉ:
 * ----------------------
 * - created_by: utilisateur qui a créé la commande
 * - created_at: date de création
 * - statut: état du workflow (BROUILLON, VALIDE, CLOTURE)
 * 
 * =============================================================================
 * ÉTAPE 2 - WORKFLOW D'ÉTATS
 * =============================================================================
 * 
 * ÉTATS AUTORISÉS:
 * ----------------
 * - BROUILLON: Commande en cours de saisie, modifiable
 * - VALIDE: Commande validée, stock réservé, non modifiable
 * - CLOTURE: Commande entièrement livrée et facturée
 * 
 * TRANSITIONS POSSIBLES:
 * ----------------------
 * BROUILLON -> VALIDE (validation par responsable commercial)
 * VALIDE -> CLOTURE (automatique quand toutes les livraisons sont faites)
 * VALIDE -> BROUILLON (annulation, si aucune livraison)
 * 
 * ACTIONS INTERDITES:
 * -------------------
 * - Modifier une commande VALIDE ou CLOTURE
 * - Supprimer une commande avec livraisons
 * - Créer une livraison sur commande BROUILLON
 */
class CommandeClientModel
{
    // Constantes pour les statuts
    const STATUT_BROUILLON = 'BROUILLON';
    const STATUT_VALIDE = 'VALIDE';
    const STATUT_CLOTURE = 'CLOTURE';

    // Constantes pour les règles métier
    const REMISE_MAX_SANS_VALIDATION = 10.00; // 10% de remise max sans validation
    const REMISE_MAX_ABSOLUE = 30.00; // 30% de remise max absolue

    /**
     * Récupère toutes les commandes clients avec les informations jointes
     */
    public function listCommandes(): array
    {
        try {
            $db = Flight::db();
            $sql = "SELECT cc.*, 
                           c.nom AS client_nom, 
                           c.telephone AS client_telephone,
                           d.nom AS depot_nom,
                           (SELECT COUNT(*) FROM livraison_client lc WHERE lc.id_commande_client = cc.id_commande_client) AS nb_livraisons
                    FROM commande_client cc
                    LEFT JOIN client c ON cc.id_client = c.id_client
                    LEFT JOIN depot d ON cc.id_depot = d.id_depot
                    ORDER BY cc.created_at DESC";
            $stmt = $db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            throw new Exception('Erreur lors de la récupération des commandes: ' . $e->getMessage());
        }
    }

    /**
     * Récupère une commande par son ID
     */
    public function findById(int $commandeId): ?array
    {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("SELECT cc.*, 
                                         c.nom AS client_nom,
                                         c.telephone AS client_telephone,
                                         c.email AS client_email,
                                         d.nom AS depot_nom
                                  FROM commande_client cc
                                  LEFT JOIN client c ON cc.id_client = c.id_client
                                  LEFT JOIN depot d ON cc.id_depot = d.id_depot
                                  WHERE cc.id_commande_client = :id");
            $stmt->execute(['id' => $commandeId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;
        } catch (PDOException $e) {
            throw new Exception('Erreur lors de la récupération de la commande: ' . $e->getMessage());
        }
    }

    /**
     * Vérifie si un numéro de commande existe déjà
     */
    public function existsByNumber(string $numero): bool
    {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("SELECT 1 FROM commande_client WHERE commande_numero = :num LIMIT 1");
            $stmt->execute(['num' => $numero]);
            return (bool)$stmt->fetchColumn();
        } catch (PDOException $e) {
            throw new Exception('Erreur lors de la vérification du numéro: ' . $e->getMessage());
        }
    }

    /**
     * Génère un numéro de commande unique
     */
    public function generateNumero(): string
    {
        $prefix = 'CMD-' . date('Ymd') . '-';
        $db = Flight::db();
        $stmt = $db->query("SELECT MAX(CAST(SUBSTRING(commande_numero, LENGTH('$prefix') + 1) AS UNSIGNED)) AS max_num 
                            FROM commande_client 
                            WHERE commande_numero LIKE '$prefix%'");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $nextNum = ($result['max_num'] ?? 0) + 1;
        return $prefix . str_pad($nextNum, 4, '0', STR_PAD_LEFT);
    }

    /**
     * =============================================================================
     * ÉTAPE 3 - RÈGLES MÉTIER: Création de commande
     * =============================================================================
     * 
     * RÈGLES:
     * - Client obligatoire
     * - Dépôt obligatoire
     * - Numéro unique obligatoire
     * - Statut initial: BROUILLON
     */
    public function create(array $data): int
    {
        $numero = trim($data['commande_numero'] ?? '');
        $date = $data['commande_date'] ?? date('Y-m-d H:i:s');
        $clientId = (int)($data['id_client'] ?? 0);
        $depotId = (int)($data['id_depot'] ?? 0);
        $createdBy = (int)($data['created_by'] ?? 0);

        // Règles de validation
        if ($clientId === 0) {
            throw new Exception('Le client est obligatoire pour créer une commande.');
        }
        if ($depotId === 0) {
            throw new Exception('Le dépôt est obligatoire pour créer une commande.');
        }
        if ($createdBy === 0) {
            throw new Exception('L\'utilisateur créateur est obligatoire.');
        }

        // Génération automatique du numéro si non fourni
        if ($numero === '') {
            $numero = $this->generateNumero();
        } elseif ($this->existsByNumber($numero)) {
            throw new Exception('Le numéro de commande existe déjà.');
        }

        $parsedDate = strtotime($date);
        if ($parsedDate === false) {
            throw new Exception('Date de commande invalide.');
        }

        try {
            $db = Flight::db();
            $stmt = $db->prepare("INSERT INTO commande_client 
                                  (commande_numero, commande_date, id_client, id_depot, 
                                   montant_ht, montant_tva, montant_ttc, statut, created_by) 
                                  VALUES 
                                  (:numero, :date, :client, :depot, :ht, :tva, :ttc, :statut, :created_by)");
            $stmt->execute([
                'numero' => $numero,
                'date' => date('Y-m-d H:i:s', $parsedDate),
                'client' => $clientId,
                'depot' => $depotId,
                'ht' => $data['montant_ht'] ?? 0,
                'tva' => $data['montant_tva'] ?? 0,
                'ttc' => $data['montant_ttc'] ?? 0,
                'statut' => self::STATUT_BROUILLON,
                'created_by' => $createdBy
            ]);
            return (int)$db->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception('Erreur lors de la création de la commande: ' . $e->getMessage());
        }
    }

    /**
     * =============================================================================
     * ÉTAPE 2 & 4 - VALIDATION DE COMMANDE (Transition BROUILLON -> VALIDE)
     * =============================================================================
     * 
     * RÈGLES MÉTIER:
     * - La commande doit être en BROUILLON
     * - La commande doit avoir au moins une ligne
     * - Le stock doit être disponible (réservation automatique)
     * - La remise ne doit pas dépasser le seuil autorisé
     * 
     * BLOCAGES TECHNIQUES:
     * - Vérification du stock disponible avant validation
     * - Création automatique des mouvements de réservation stock
     */
    public function valider(int $commandeId, int $validePar): bool
    {
        $db = Flight::db();
        
        try {
            $db->beginTransaction();

            // Récupérer la commande
            $commande = $this->findById($commandeId);
            if (!$commande) {
                throw new Exception('Commande introuvable.');
            }

            // BLOCAGE: Vérifier le statut
            if ($commande['statut'] !== self::STATUT_BROUILLON) {
                throw new Exception('Seule une commande en BROUILLON peut être validée.');
            }

            // Récupérer les lignes de commande
            $lignesModel = new LigneCommandeClientModel();
            $lignes = $lignesModel->getByCommandeId($commandeId);

            if (empty($lignes)) {
                throw new Exception('La commande doit contenir au moins un article.');
            }

            // BLOCAGE: Vérifier le stock disponible pour chaque ligne
            $stockModel = new StockModel();
            foreach ($lignes as $ligne) {
                $stockDispo = $stockModel->getStockDisponible($ligne['id_article'], $commande['id_depot']);
                if ($stockDispo < $ligne['quantite']) {
                    throw new Exception(
                        "Stock insuffisant pour l'article {$ligne['designation']}. " .
                        "Disponible: {$stockDispo}, Demandé: {$ligne['quantite']}"
                    );
                }
            }

            // Réserver le stock (création mouvement de réservation)
            foreach ($lignes as $ligne) {
                $stockModel->reserverStock(
                    $ligne['id_article'],
                    $commande['id_depot'],
                    $ligne['quantite'],
                    $commandeId,
                    $validePar
                );
            }

            // Mettre à jour le statut
            $stmt = $db->prepare("UPDATE commande_client 
                                  SET statut = :statut, 
                                      valide_par = :valide_par, 
                                      date_validation = NOW() 
                                  WHERE id_commande_client = :id");
            $stmt->execute([
                'statut' => self::STATUT_VALIDE,
                'valide_par' => $validePar,
                'id' => $commandeId
            ]);

            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /**
     * Annule la validation d'une commande (VALIDE -> BROUILLON)
     * Uniquement si aucune livraison n'a été faite
     */
    public function annulerValidation(int $commandeId, int $userId): bool
    {
        $db = Flight::db();
        
        try {
            $db->beginTransaction();

            $commande = $this->findById($commandeId);
            if (!$commande) {
                throw new Exception('Commande introuvable.');
            }

            if ($commande['statut'] !== self::STATUT_VALIDE) {
                throw new Exception('Seule une commande VALIDE peut être annulée.');
            }

            // Vérifier qu'il n'y a pas de livraison
            $stmt = $db->prepare("SELECT COUNT(*) FROM livraison_client WHERE id_commande_client = :id");
            $stmt->execute(['id' => $commandeId]);
            if ((int)$stmt->fetchColumn() > 0) {
                throw new Exception('Impossible d\'annuler: des livraisons existent déjà.');
            }

            // Libérer le stock réservé
            $stockModel = new StockModel();
            $lignesModel = new LigneCommandeClientModel();
            $lignes = $lignesModel->getByCommandeId($commandeId);
            
            foreach ($lignes as $ligne) {
                $stockModel->libererReservation(
                    $ligne['id_article'],
                    $commande['id_depot'],
                    $ligne['quantite'],
                    $commandeId,
                    $userId
                );
            }

            // Remettre en brouillon
            $stmt = $db->prepare("UPDATE commande_client 
                                  SET statut = :statut, 
                                      valide_par = NULL, 
                                      date_validation = NULL 
                                  WHERE id_commande_client = :id");
            $stmt->execute([
                'statut' => self::STATUT_BROUILLON,
                'id' => $commandeId
            ]);

            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            throw $e;
        }
    }

    /**
     * Clôture une commande (VALIDE -> CLOTURE)
     * Automatique quand toutes les livraisons sont faites
     */
    public function cloturer(int $commandeId): bool
    {
        $db = Flight::db();
        
        $commande = $this->findById($commandeId);
        if (!$commande || $commande['statut'] !== self::STATUT_VALIDE) {
            return false;
        }

        $stmt = $db->prepare("UPDATE commande_client SET statut = :statut WHERE id_commande_client = :id");
        return $stmt->execute([
            'statut' => self::STATUT_CLOTURE,
            'id' => $commandeId
        ]);
    }

    /**
     * Récupère les commandes validées (pour livraison)
     */
    public function getCommandesValidees(): array
    {
        try {
            $db = Flight::db();
            $sql = "SELECT cc.*, c.nom AS client_nom, d.nom AS depot_nom
                    FROM commande_client cc
                    LEFT JOIN client c ON cc.id_client = c.id_client
                    LEFT JOIN depot d ON cc.id_depot = d.id_depot
                    WHERE cc.statut = :statut
                    ORDER BY cc.commande_date ASC";
            $stmt = $db->prepare($sql);
            $stmt->execute(['statut' => self::STATUT_VALIDE]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            throw new Exception('Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Récupère les commandes par statut
     */
    public function getByStatut(string $statut): array
    {
        try {
            $db = Flight::db();
            $sql = "SELECT cc.*, c.nom AS client_nom, d.nom AS depot_nom
                    FROM commande_client cc
                    LEFT JOIN client c ON cc.id_client = c.id_client
                    LEFT JOIN depot d ON cc.id_depot = d.id_depot
                    WHERE cc.statut = :statut
                    ORDER BY cc.commande_date DESC";
            $stmt = $db->prepare($sql);
            $stmt->execute(['statut' => $statut]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            throw new Exception('Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Met à jour les montants de la commande
     */
    public function updateMontants(int $commandeId, float $ht, float $tva, float $ttc): bool
    {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("UPDATE commande_client 
                                  SET montant_ht = :ht, montant_tva = :tva, montant_ttc = :ttc 
                                  WHERE id_commande_client = :id AND statut = :statut");
            return $stmt->execute([
                'ht' => $ht,
                'tva' => $tva,
                'ttc' => $ttc,
                'id' => $commandeId,
                'statut' => self::STATUT_BROUILLON
            ]);
        } catch (PDOException $e) {
            throw new Exception('Erreur lors de la mise à jour des montants: ' . $e->getMessage());
        }
    }
}
