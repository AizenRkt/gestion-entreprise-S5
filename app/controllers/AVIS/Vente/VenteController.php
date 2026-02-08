<?php

namespace app\controllers\AVIS\Vente;

use app\models\AVIS\Vente\ClientModel;
use app\models\AVIS\Vente\CommandeClientModel;
use app\models\AVIS\Vente\LigneCommandeClientModel;
use app\models\AVIS\Vente\LivraisonClientModel;
use app\models\AVIS\Vente\LigneLivraisonClientModel;
use app\models\AVIS\Vente\FactureClientModel;
use app\models\AVIS\Vente\EncaissementClientModel;
use app\models\AVIS\Vente\StockModel;
use app\models\AVIS\Vente\VenteKPIModel;
use app\models\AVIS\Achat\DepotModel;
use Exception;
use Flight;

/**
 * =============================================================================
 * CONTRÔLEUR PRINCIPAL DU MODULE VENTES
 * =============================================================================
 * 
 * Ce contrôleur gère l'ensemble du flux de vente:
 * COMMANDE -> LIVRAISON -> FACTURE -> ENCAISSEMENT
 * 
 * Il implémente les règles métier et les blocages techniques définis
 * dans les étapes 3 et 4 du cahier des charges.
 */
class VenteController
{
    private ClientModel $clientModel;
    private CommandeClientModel $commandeModel;
    private LigneCommandeClientModel $ligneCommandeModel;
    private LivraisonClientModel $livraisonModel;
    private LigneLivraisonClientModel $ligneLivraisonModel;
    private FactureClientModel $factureModel;
    private EncaissementClientModel $encaissementModel;
    private StockModel $stockModel;
    private VenteKPIModel $kpiModel;
    private DepotModel $depotModel;

    public function __construct()
    {
        $this->clientModel = new ClientModel();
        $this->commandeModel = new CommandeClientModel();
        $this->ligneCommandeModel = new LigneCommandeClientModel();
        $this->livraisonModel = new LivraisonClientModel();
        $this->ligneLivraisonModel = new LigneLivraisonClientModel();
        $this->factureModel = new FactureClientModel();
        $this->encaissementModel = new EncaissementClientModel();
        $this->stockModel = new StockModel();
        $this->kpiModel = new VenteKPIModel();
        $this->depotModel = new DepotModel();
    }

    // =========================================================================
    // DASHBOARD & VUES PRINCIPALES
    // =========================================================================

    /**
     * Affiche le tableau de bord des ventes avec KPI
     */
    public function dashboard(): void
    {
        try {
            $clients = $this->clientModel->getAll();
            $commandes = $this->commandeModel->listCommandes();
            $livraisons = $this->livraisonModel->listLivraisons();
            $factures = $this->factureModel->listFactures();
            $encaissements = $this->encaissementModel->listEncaissements();
            $depots = $this->depotModel->getAll();
            $modesPaiement = $this->encaissementModel->getModesPaiement();
            $kpi = $this->kpiModel->getDashboardData();

            Flight::render('AVIS/Vente/dashboard', [
                'clients' => $clients,
                'commandes' => $commandes,
                'livraisons' => $livraisons,
                'factures' => $factures,
                'encaissements' => $encaissements,
                'depots' => $depots,
                'modesPaiement' => $modesPaiement,
                'kpi' => $kpi
            ]);
        } catch (Exception $e) {
            Flight::halt(500, 'Erreur lors du chargement: ' . $e->getMessage());
        }
    }

    /**
     * Page Clients séparée
     */
    public function clientsPage(): void
    {
        try {
            $clients = $this->clientModel->getAll();
            $types = $this->clientModel->getTypes();
            
            Flight::render('AVIS/Vente/clients', [
                'clients' => $clients,
                'types' => $types
            ]);
        } catch (Exception $e) {
            Flight::halt(500, 'Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Page Commandes séparée avec articles disponibles
     */
    public function commandesPage(): void
    {
        try {
            $commandes = $this->commandeModel->listCommandes();
            $clients = $this->clientModel->getAll();
            $depots = $this->depotModel->getAll();
            $articles = $this->getArticlesDisponibles();
            
            Flight::render('AVIS/Vente/commandes', [
                'commandes' => $commandes,
                'clients' => $clients,
                'depots' => $depots,
                'articles' => $articles
            ]);
        } catch (Exception $e) {
            Flight::halt(500, 'Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Page Livraisons séparée
     */
    public function livraisonsPage(): void
    {
        try {
            $livraisons = $this->livraisonModel->listLivraisons();
            $commandesValidees = $this->commandeModel->getByStatut('VALIDE');
            
            Flight::render('AVIS/Vente/livraisons', [
                'livraisons' => $livraisons,
                'commandesValidees' => $commandesValidees
            ]);
        } catch (Exception $e) {
            Flight::halt(500, 'Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Page Factures séparée
     */
    public function facturesPage(): void
    {
        try {
            $factures = $this->factureModel->listFactures();
            $livraisonsEffectuees = $this->livraisonModel->getLivraisonsEffectuees();
            
            Flight::render('AVIS/Vente/factures', [
                'factures' => $factures,
                'livraisonsEffectuees' => $livraisonsEffectuees
            ]);
        } catch (Exception $e) {
            Flight::halt(500, 'Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Page Encaissements séparée
     */
    public function encaissementsPage(): void
    {
        try {
            $encaissements = $this->encaissementModel->listEncaissements();
            $facturesAEncaisser = $this->factureModel->getFacturesAEncaisser();
            $modesPaiement = $this->encaissementModel->getModesPaiement();
            
            Flight::render('AVIS/Vente/encaissements', [
                'encaissements' => $encaissements,
                'facturesAEncaisser' => $facturesAEncaisser,
                'modesPaiement' => $modesPaiement
            ]);
        } catch (Exception $e) {
            Flight::halt(500, 'Erreur: ' . $e->getMessage());
        }
    }

    /**
     * Récupère la liste des articles avec stock disponible
     */
    private function getArticlesDisponibles(): array
    {
        $db = Flight::db();
        $sql = "SELECT 
                    a.id_article,
                    a.code,
                    a.designation,
                    a.unite,
                    a.prix_vente,
                    COALESCE(sc.quantite, 0) as stock_disponible
                FROM article a
                LEFT JOIN stock_courant sc ON a.id_article = sc.id_article
                WHERE a.actif = 1
                ORDER BY a.designation";
        $stmt = $db->query($sql);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * API: Liste des articles disponibles (JSON)
     */
    public function getArticles(): void
    {
        try {
            $articles = $this->getArticlesDisponibles();
            Flight::json($articles);
        } catch (Exception $e) {
            Flight::json(['error' => $e->getMessage()], 500);
        }
    }

    // =========================================================================
    // GESTION DES CLIENTS
    // =========================================================================

    public function createClient(): void
    {
        if (Flight::request()->method !== 'POST') {
            Flight::json(['error' => 'Méthode non autorisée'], 405);
            return;
        }

        try {
            $data = Flight::request()->data->getData();
            $id = $this->clientModel->create($data);
            Flight::json(['success' => true, 'id' => $id, 'message' => 'Client créé avec succès']);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function getClient(int $id): void
    {
        try {
            $client = $this->clientModel->findById($id);
            if (!$client) {
                Flight::json(['error' => 'Client introuvable'], 404);
                return;
            }
            Flight::json($client);
        } catch (Exception $e) {
            Flight::json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Met à jour un client
     */
    public function updateClient(int $id): void
    {
        try {
            $data = Flight::request()->data->getData();
            $this->clientModel->update($id, $data);
            Flight::json(['success' => true, 'message' => 'Client mis à jour']);
        } catch (Exception $e) {
            Flight::json(['error' => $e->getMessage()], 400);
        }
    }

    /**
     * Supprime un client
     */
    public function deleteClient(int $id): void
    {
        try {
            $this->clientModel->delete($id);
            Flight::json(['success' => true, 'message' => 'Client supprimé']);
        } catch (Exception $e) {
            Flight::json(['error' => $e->getMessage()], 400);
        }
    }

    // =========================================================================
    // GESTION DES COMMANDES
    // =========================================================================

    /**
     * Crée une nouvelle commande client (en BROUILLON)
     */
    public function createCommande(): void
    {
        if (Flight::request()->method !== 'POST') {
            Flight::json(['error' => 'Méthode non autorisée'], 405);
            return;
        }

        try {
            $data = Flight::request()->data->getData();
            $data['created_by'] = $_SESSION['user']['id_user'] ?? 1;
            $id = $this->commandeModel->create($data);
            Flight::json(['success' => true, 'id' => $id, 'message' => 'Commande créée avec succès']);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * Récupère les détails d'une commande
     */
    public function getCommande(int $id): void
    {
        try {
            $commande = $this->commandeModel->findById($id);
            if (!$commande) {
                Flight::json(['error' => 'Commande introuvable'], 404);
                return;
            }
            $commande['lignes'] = $this->ligneCommandeModel->getByCommandeId($id);
            Flight::json($commande);
        } catch (Exception $e) {
            Flight::json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Ajoute une ligne à une commande
     * Implémente les règles de remise (ÉTAPE 3 & 4)
     */
    public function addLigneCommande(): void
    {
        if (Flight::request()->method !== 'POST') {
            Flight::json(['error' => 'Méthode non autorisée'], 405);
            return;
        }

        try {
            $data = Flight::request()->data->getData();
            $id = $this->ligneCommandeModel->create($data);
            Flight::json(['success' => true, 'id' => $id, 'message' => 'Ligne ajoutée']);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * Valide une commande (BROUILLON -> VALIDE)
     * Implémente les blocages techniques (ÉTAPE 4):
     * - Vérifie le stock disponible
     * - Réserve le stock
     * - Vérifie les remises
     */
    public function validerCommande(int $id): void
    {
        if (Flight::request()->method !== 'POST') {
            Flight::json(['error' => 'Méthode non autorisée'], 405);
            return;
        }

        try {
            $userId = $_SESSION['user']['id_user'] ?? 1;
            $this->commandeModel->valider($id, $userId);
            Flight::json(['success' => true, 'message' => 'Commande validée avec succès']);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * Annule la validation d'une commande (VALIDE -> BROUILLON)
     */
    public function annulerCommande(int $id): void
    {
        if (Flight::request()->method !== 'POST') {
            Flight::json(['error' => 'Méthode non autorisée'], 405);
            return;
        }

        try {
            $userId = $_SESSION['user']['id_user'] ?? 1;
            $this->commandeModel->annulerValidation($id, $userId);
            Flight::json(['success' => true, 'message' => 'Validation annulée']);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * Récupère les commandes validées (pour création de livraison)
     */
    public function getCommandesValidees(): void
    {
        try {
            $commandes = $this->commandeModel->getCommandesValidees();
            Flight::json($commandes);
        } catch (Exception $e) {
            Flight::json(['error' => $e->getMessage()], 500);
        }
    }

    // =========================================================================
    // GESTION DES LIVRAISONS
    // =========================================================================

    /**
     * Crée une nouvelle livraison
     * BLOCAGE: Nécessite une commande VALIDEE (ÉTAPE 4)
     */
    public function createLivraison(): void
    {
        if (Flight::request()->method !== 'POST') {
            Flight::json(['error' => 'Méthode non autorisée'], 405);
            return;
        }

        try {
            $data = Flight::request()->data->getData();
            $data['cree_par'] = $_SESSION['user']['id_user'] ?? 1;
            $id = $this->livraisonModel->create($data);
            Flight::json(['success' => true, 'id' => $id, 'message' => 'Livraison créée']);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * Récupère les détails d'une livraison
     */
    public function getLivraison(int $id): void
    {
        try {
            $livraison = $this->livraisonModel->findById($id);
            if (!$livraison) {
                Flight::json(['error' => 'Livraison introuvable'], 404);
                return;
            }
            $livraison['lignes'] = $this->ligneLivraisonModel->getByLivraisonId($id);
            Flight::json($livraison);
        } catch (Exception $e) {
            Flight::json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Ajoute une ligne à une livraison
     */
    public function addLigneLivraison(): void
    {
        if (Flight::request()->method !== 'POST') {
            Flight::json(['error' => 'Méthode non autorisée'], 405);
            return;
        }

        try {
            $data = Flight::request()->data->getData();
            $id = $this->ligneLivraisonModel->create($data);
            Flight::json(['success' => true, 'id' => $id, 'message' => 'Ligne ajoutée']);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * Valide une livraison (BROUILLON -> LIVREE)
     * BLOCAGE: Vérifie le stock disponible (ÉTAPE 4)
     * IMPACT: Sortie de stock effective
     */
    public function validerLivraison(int $id): void
    {
        if (Flight::request()->method !== 'POST') {
            Flight::json(['error' => 'Méthode non autorisée'], 405);
            return;
        }

        try {
            $userId = $_SESSION['user']['id_user'] ?? 1;
            $this->livraisonModel->valider($id, $userId);
            Flight::json(['success' => true, 'message' => 'Livraison validée']);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * Récupère les livraisons validées sans facture
     */
    public function getLivraisonsSansFacture(): void
    {
        try {
            $livraisons = $this->livraisonModel->getLivraisonsSansFacture();
            Flight::json($livraisons);
        } catch (Exception $e) {
            Flight::json(['error' => $e->getMessage()], 500);
        }
    }

    // =========================================================================
    // GESTION DES FACTURES
    // =========================================================================

    /**
     * Crée une facture à partir d'une livraison
     * BLOCAGE: Nécessite une livraison LIVREE (ÉTAPE 4)
     */
    public function createFacture(): void
    {
        if (Flight::request()->method !== 'POST') {
            Flight::json(['error' => 'Méthode non autorisée'], 405);
            return;
        }

        try {
            $data = Flight::request()->data->getData();
            $data['cree_par'] = $_SESSION['user']['id_user'] ?? 1;
            $id = $this->factureModel->create($data);
            Flight::json(['success' => true, 'id' => $id, 'message' => 'Facture créée']);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * Récupère les détails d'une facture
     */
    public function getFacture(int $id): void
    {
        try {
            $facture = $this->factureModel->findById($id);
            if (!$facture) {
                Flight::json(['error' => 'Facture introuvable'], 404);
                return;
            }
            $facture['encaissements'] = $this->encaissementModel->getByFactureId($id);
            $facture['reste_a_payer'] = $this->factureModel->getMontantRestant($id);
            Flight::json($facture);
        } catch (Exception $e) {
            Flight::json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Valide une facture (BROUILLON -> VALIDE)
     */
    public function validerFacture(int $id): void
    {
        if (Flight::request()->method !== 'POST') {
            Flight::json(['error' => 'Méthode non autorisée'], 405);
            return;
        }

        try {
            $userId = $_SESSION['user']['id_user'] ?? 1;
            $this->factureModel->valider($id, $userId);
            Flight::json(['success' => true, 'message' => 'Facture validée']);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * Récupère les factures impayées (pour encaissement)
     */
    public function getFacturesImpayees(): void
    {
        try {
            $factures = $this->factureModel->getFacturesImpayees();
            Flight::json($factures);
        } catch (Exception $e) {
            Flight::json(['error' => $e->getMessage()], 500);
        }
    }

    // =========================================================================
    // GESTION DES ENCAISSEMENTS
    // =========================================================================

    /**
     * Crée un encaissement
     * Règles: 
     * - Facture doit être VALIDEE
     * - Montant <= reste à payer
     * - Met à jour automatiquement le statut de la facture si soldée
     */
    public function createEncaissement(): void
    {
        if (Flight::request()->method !== 'POST') {
            Flight::json(['error' => 'Méthode non autorisée'], 405);
            return;
        }

        try {
            $data = Flight::request()->data->getData();
            $data['cree_par'] = $_SESSION['user']['id_user'] ?? 1;
            $id = $this->encaissementModel->create($data);
            Flight::json(['success' => true, 'id' => $id, 'message' => 'Encaissement enregistré']);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * Récupère un encaissement
     */
    public function getEncaissement(int $id): void
    {
        try {
            $encaissement = $this->encaissementModel->findById($id);
            if (!$encaissement) {
                Flight::json(['error' => 'Encaissement introuvable'], 404);
                return;
            }
            Flight::json($encaissement);
        } catch (Exception $e) {
            Flight::json(['error' => $e->getMessage()], 500);
        }
    }

    // =========================================================================
    // STOCK
    // =========================================================================

    /**
     * Récupère les articles disponibles avec stock pour un dépôt spécifique
     */
    public function getArticlesByDepot(int $depotId): void
    {
        try {
            $articles = $this->stockModel->getArticlesDisponibles($depotId);
            Flight::json($articles);
        } catch (Exception $e) {
            Flight::json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Vérifie le stock disponible pour un article
     */
    public function checkStock(int $articleId, int $depotId): void
    {
        try {
            $stock = $this->stockModel->getStockDisponible($articleId, $depotId);
            Flight::json(['stock_disponible' => $stock]);
        } catch (Exception $e) {
            Flight::json(['error' => $e->getMessage()], 500);
        }
    }

    // =========================================================================
    // KPI
    // =========================================================================

    /**
     * Récupère tous les KPI pour le dashboard
     */
    public function getKPI(): void
    {
        try {
            $kpi = $this->kpiModel->getDashboardData();
            Flight::json($kpi);
        } catch (Exception $e) {
            Flight::json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Récupère le CA par période
     */
    public function getCA(): void
    {
        try {
            $debut = Flight::request()->query['debut'] ?? date('Y-m-01');
            $fin = Flight::request()->query['fin'] ?? date('Y-m-t');
            $ca = $this->kpiModel->getChiffreAffaires($debut, $fin);
            Flight::json($ca);
        } catch (Exception $e) {
            Flight::json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Récupère le backlog des commandes
     */
    public function getBacklog(): void
    {
        try {
            $backlog = $this->kpiModel->getBacklogCommandes();
            Flight::json($backlog);
        } catch (Exception $e) {
            Flight::json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Récupère les commandes en retard
     */
    public function getCommandesRetard(): void
    {
        try {
            $delai = (int)(Flight::request()->query['delai'] ?? 7);
            $commandes = $this->kpiModel->getCommandesEnRetard($delai);
            Flight::json($commandes);
        } catch (Exception $e) {
            Flight::json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Récupère les créances clients
     */
    public function getCreances(): void
    {
        try {
            $creances = $this->kpiModel->getFacturesImpayees();
            $parAnciennete = $this->kpiModel->getCreancesParAnciennete();
            Flight::json([
                'details' => $creances,
                'par_anciennete' => $parAnciennete
            ]);
        } catch (Exception $e) {
            Flight::json(['error' => $e->getMessage()], 500);
        }
    }
}
