<?php

namespace app\models\AVIS\Vente;

use Exception;
use Flight;
use PDO;
use PDOException;

/**
 * =============================================================================
 * ÉTAPE 3 & 4 - GESTION DU STOCK POUR LES VENTES
 * =============================================================================
 * 
 * IMPACT SUR LE STOCK:
 * --------------------
 * 1. Validation commande → Réservation stock (blocage virtuel)
 * 2. Validation livraison → Sortie effective du stock
 * 3. Annulation commande → Libération de la réservation
 * 
 * TABLES IMPACTÉES:
 * -----------------
 * - stock_courant: quantité et valeur actuelles par article/dépôt
 * - mouvement_stock: historique des mouvements
 * - lot: gestion des lots (FIFO/LIFO/CUMP)
 */
class StockModel
{
    // Types de mouvements stock
    const TYPE_RESERVATION_VENTE = 'RESERVATION_VENTE';
    const TYPE_LIBERATION_RESERVATION = 'LIBERATION_RESERVATION';
    const TYPE_SORTIE_VENTE = 'SORTIE_VENTE';

    /**
     * Récupère le stock disponible pour un article dans un dépôt
     * Stock disponible = Stock courant - Réservations en cours
     */
    public function getStockDisponible(int $articleId, int $depotId): float
    {
        try {
            $db = Flight::db();
            
            // Stock courant
            $stmt = $db->prepare("SELECT COALESCE(quantite, 0) AS quantite 
                                  FROM stock_courant 
                                  WHERE id_article = :article AND id_depot = :depot");
            $stmt->execute(['article' => $articleId, 'depot' => $depotId]);
            $stockCourant = (float)($stmt->fetchColumn() ?: 0);

            // Réservations en cours (commandes validées non livrées)
            $stmt = $db->prepare("SELECT COALESCE(SUM(lcc.quantite - COALESCE(
                                    (SELECT SUM(llc.quantite_livree) 
                                     FROM ligne_livraison_client llc 
                                     JOIN livraison_client lc ON llc.id_livraison_client = lc.id_livraison_client
                                     WHERE llc.id_ligne_commande_client = lcc.id_ligne_commande_client
                                     AND lc.statut = 'LIVREE'), 0)
                                  ), 0) AS reserve
                                  FROM ligne_commande_client lcc
                                  JOIN commande_client cc ON lcc.id_commande_client = cc.id_commande_client
                                  WHERE lcc.id_article = :article 
                                  AND cc.id_depot = :depot
                                  AND cc.statut = 'VALIDE'");
            $stmt->execute(['article' => $articleId, 'depot' => $depotId]);
            $reserve = (float)($stmt->fetchColumn() ?: 0);

            return max(0, $stockCourant - $reserve);
        } catch (PDOException $e) {
            throw new Exception('Erreur lors de la vérification du stock: ' . $e->getMessage());
        }
    }

    /**
     * Récupère le stock courant brut (sans tenir compte des réservations)
     */
    public function getStockCourant(int $articleId, int $depotId): array
    {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("SELECT * FROM stock_courant 
                                  WHERE id_article = :article AND id_depot = :depot");
            $stmt->execute(['article' => $articleId, 'depot' => $depotId]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result ?: [
                'id_article' => $articleId,
                'id_depot' => $depotId,
                'quantite' => 0,
                'valeur_stock' => 0,
                'cout_moyen' => 0
            ];
        } catch (PDOException $e) {
            throw new Exception('Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Réserve du stock lors de la validation d'une commande
     * (enregistre un mouvement de réservation)
     */
    public function reserverStock(int $articleId, int $depotId, float $quantite, int $commandeId, int $userId): bool
    {
        try {
            $db = Flight::db();
            
            // Récupérer le type de mouvement pour réservation
            $typeId = $this->getOrCreateTypeMouvement(self::TYPE_RESERVATION_VENTE, 'out', 'Réservation pour vente', false);

            $stmt = $db->prepare("INSERT INTO mouvement_stock 
                                  (id_article, id_depot, id_type_mouvement_stock, sens, quantite, 
                                   motif, date_mouvement, created_by) 
                                  VALUES 
                                  (:article, :depot, :type, -1, :qte, :motif, NOW(), :user)");
            return $stmt->execute([
                'article' => $articleId,
                'depot' => $depotId,
                'type' => $typeId,
                'qte' => $quantite,
                'motif' => "Réservation pour commande #$commandeId",
                'user' => $userId
            ]);
        } catch (PDOException $e) {
            throw new Exception('Erreur lors de la réservation: ' . $e->getMessage());
        }
    }

    /**
     * Libère une réservation de stock (annulation de commande)
     */
    public function libererReservation(int $articleId, int $depotId, float $quantite, int $commandeId, int $userId): bool
    {
        try {
            $db = Flight::db();
            
            $typeId = $this->getOrCreateTypeMouvement(self::TYPE_LIBERATION_RESERVATION, 'in', 'Libération de réservation', false);

            $stmt = $db->prepare("INSERT INTO mouvement_stock 
                                  (id_article, id_depot, id_type_mouvement_stock, sens, quantite, 
                                   motif, date_mouvement, created_by) 
                                  VALUES 
                                  (:article, :depot, :type, 1, :qte, :motif, NOW(), :user)");
            return $stmt->execute([
                'article' => $articleId,
                'depot' => $depotId,
                'type' => $typeId,
                'qte' => $quantite,
                'motif' => "Annulation réservation commande #$commandeId",
                'user' => $userId
            ]);
        } catch (PDOException $e) {
            throw new Exception('Erreur lors de la libération: ' . $e->getMessage());
        }
    }

    /**
     * =============================================================================
     * SORTIE DE STOCK EFFECTIVE (Livraison validée)
     * =============================================================================
     * 
     * IMPACT:
     * - Décrémente stock_courant.quantite
     * - Met à jour stock_courant.valeur_stock
     * - Crée mouvement_stock de type 'sortie_vente'
     * - Gère les lots selon la méthode de valorisation (FIFO/LIFO/CUMP)
     */
    public function sortieVente(int $articleId, int $depotId, float $quantite, int $livraisonId, int $userId): bool
    {
        $db = Flight::db();
        
        try {
            // Récupérer le stock courant
            $stock = $this->getStockCourant($articleId, $depotId);
            if ((float)$stock['quantite'] < $quantite) {
                throw new Exception("Stock insuffisant. Disponible: {$stock['quantite']}, Demandé: $quantite");
            }

            // Récupérer la méthode de valorisation de l'article
            $stmt = $db->prepare("SELECT mv.code 
                                  FROM article a
                                  JOIN methode_valorisation mv ON a.id_methode_valorisation = mv.id_methode_valorisation
                                  WHERE a.id_article = :id");
            $stmt->execute(['id' => $articleId]);
            $methode = $stmt->fetchColumn() ?: 'CUMP';

            // Calculer le coût de sortie selon la méthode
            $coutSortie = $this->calculerCoutSortie($articleId, $depotId, $quantite, $methode);

            // Type de mouvement
            $typeId = $this->getOrCreateTypeMouvement(self::TYPE_SORTIE_VENTE, 'out', 'Sortie pour vente', true);

            // Créer le mouvement de stock
            $stmt = $db->prepare("INSERT INTO mouvement_stock 
                                  (id_article, id_depot, id_type_mouvement_stock, sens, quantite, 
                                   cout_unitaire, motif, date_mouvement, date_validation, created_by) 
                                  VALUES 
                                  (:article, :depot, :type, -1, :qte, :cout, :motif, NOW(), NOW(), :user)");
            $stmt->execute([
                'article' => $articleId,
                'depot' => $depotId,
                'type' => $typeId,
                'qte' => $quantite,
                'cout' => $coutSortie,
                'motif' => "Sortie vente - Livraison #$livraisonId",
                'user' => $userId
            ]);

            // Mettre à jour stock_courant
            $nouvelleQte = (float)$stock['quantite'] - $quantite;
            $nouvelleValeur = max(0, (float)$stock['valeur_stock'] - ($quantite * $coutSortie));
            $nouveauCoutMoyen = $nouvelleQte > 0 ? $nouvelleValeur / $nouvelleQte : 0;

            $stmt = $db->prepare("INSERT INTO stock_courant (id_article, id_depot, quantite, valeur_stock, cout_moyen)
                                  VALUES (:article, :depot, :qte, :valeur, :cout)
                                  ON DUPLICATE KEY UPDATE 
                                  quantite = :qte, valeur_stock = :valeur, cout_moyen = :cout, updated_at = NOW()");
            $stmt->execute([
                'article' => $articleId,
                'depot' => $depotId,
                'qte' => $nouvelleQte,
                'valeur' => $nouvelleValeur,
                'cout' => $nouveauCoutMoyen
            ]);

            return true;
        } catch (PDOException $e) {
            throw new Exception('Erreur lors de la sortie de stock: ' . $e->getMessage());
        }
    }

    /**
     * Calcule le coût de sortie selon la méthode de valorisation
     */
    private function calculerCoutSortie(int $articleId, int $depotId, float $quantite, string $methode): float
    {
        $db = Flight::db();

        switch ($methode) {
            case 'FIFO':
                // Premier Entré, Premier Sorti
                $stmt = $db->prepare("SELECT cout_unitaire FROM lot 
                                      WHERE id_article = :article AND id_depot = :depot 
                                      AND quantite_initiale > 0
                                      ORDER BY date_entree ASC LIMIT 1");
                $stmt->execute(['article' => $articleId, 'depot' => $depotId]);
                $cout = $stmt->fetchColumn();
                return $cout ?: $this->getCoutMoyen($articleId, $depotId);

            case 'LIFO':
                // Dernier Entré, Premier Sorti
                $stmt = $db->prepare("SELECT cout_unitaire FROM lot 
                                      WHERE id_article = :article AND id_depot = :depot 
                                      AND quantite_initiale > 0
                                      ORDER BY date_entree DESC LIMIT 1");
                $stmt->execute(['article' => $articleId, 'depot' => $depotId]);
                $cout = $stmt->fetchColumn();
                return $cout ?: $this->getCoutMoyen($articleId, $depotId);

            case 'CUMP':
            default:
                // Coût Unitaire Moyen Pondéré
                return $this->getCoutMoyen($articleId, $depotId);
        }
    }

    /**
     * Récupère le coût moyen d'un article
     */
    private function getCoutMoyen(int $articleId, int $depotId): float
    {
        $db = Flight::db();
        $stmt = $db->prepare("SELECT COALESCE(cout_moyen, 0) FROM stock_courant 
                              WHERE id_article = :article AND id_depot = :depot");
        $stmt->execute(['article' => $articleId, 'depot' => $depotId]);
        return (float)($stmt->fetchColumn() ?: 0);
    }

    /**
     * Récupère ou crée un type de mouvement stock
     */
    private function getOrCreateTypeMouvement(string $code, string $categorieCode, string $libelle, bool $impactValo): int
    {
        $db = Flight::db();
        
        // Vérifier si le type existe
        $stmt = $db->prepare("SELECT id_type_mouvement_stock FROM mouvement_stock_type WHERE code = :code");
        $stmt->execute(['code' => $code]);
        $id = $stmt->fetchColumn();
        
        if ($id) {
            return (int)$id;
        }

        // Récupérer la catégorie
        $stmt = $db->prepare("SELECT id_categorie_mouvement_stock FROM mouvement_stock_categorie WHERE code = :code");
        $stmt->execute(['code' => $categorieCode]);
        $categorieId = $stmt->fetchColumn();
        
        if (!$categorieId) {
            throw new Exception("Catégorie de mouvement '$categorieCode' introuvable.");
        }

        // Créer le type
        $stmt = $db->prepare("INSERT INTO mouvement_stock_type 
                              (id_categorie_mouvement_stock, code, libelle, impact_valorisation) 
                              VALUES (:cat, :code, :libelle, :impact)");
        $stmt->execute([
            'cat' => $categorieId,
            'code' => $code,
            'libelle' => $libelle,
            'impact' => $impactValo ? 1 : 0
        ]);
        
        return (int)$db->lastInsertId();
    }

    /**
     * Récupère les articles disponibles avec leur stock
     */
    public function getArticlesDisponibles(int $depotId): array
    {
        try {
            $db = Flight::db();
            $stmt = $db->prepare("SELECT a.*, 
                                         COALESCE(sc.quantite, 0) AS stock_courant,
                                         af.nom AS famille_nom
                                  FROM article a
                                  LEFT JOIN stock_courant sc ON a.id_article = sc.id_article AND sc.id_depot = :depot
                                  LEFT JOIN article_famille af ON a.id_famille_article_famille = af.id_article_famille
                                  WHERE a.actif = 1
                                  ORDER BY a.designation ASC");
            $stmt->execute(['depot' => $depotId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
        } catch (PDOException $e) {
            throw new Exception('Erreur: ' . $e->getMessage());
        }
    }
}
