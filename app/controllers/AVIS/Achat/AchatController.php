<?php

namespace app\controllers\AVIS\Achat;

use app\models\AVIS\Achat\ArticleModel;
use app\models\AVIS\Achat\DepotModel;
use app\models\AVIS\Achat\InvoiceModel;
use app\models\AVIS\Achat\PaymentModel;
use app\models\AVIS\Achat\PaymentModeModel;
use app\models\AVIS\Achat\PurchaseOrderModel;
use app\models\AVIS\Achat\PurchaseRequestModel;
use app\models\AVIS\Achat\ReceptionModel;
use app\models\AVIS\Achat\SupplierModel;
use Exception;
use Flight;

class AchatController
{
    private SupplierModel $supplierModel;
    private PurchaseRequestModel $purchaseRequestModel;
    private ArticleModel $articleModel;
    private PurchaseOrderModel $purchaseOrderModel;
    private DepotModel $depotModel;
    private ReceptionModel $receptionModel;
    private InvoiceModel $invoiceModel;
    private PaymentModel $paymentModel;
    private PaymentModeModel $paymentModeModel;

    public function __construct()
    {
        $this->supplierModel = new SupplierModel();
        $this->purchaseRequestModel = new PurchaseRequestModel();
        $this->articleModel = new ArticleModel();
        $this->purchaseOrderModel = new PurchaseOrderModel();
        $this->depotModel = new DepotModel();
        $this->receptionModel = new ReceptionModel();
        $this->invoiceModel = new InvoiceModel();
        $this->paymentModel = new PaymentModel();
        $this->paymentModeModel = new PaymentModeModel();
    }

    public function dashboard(): void
    {
        try {
            $suppliers = $this->supplierModel->getAll();
            $orders = $this->purchaseOrderModel->listOrders();
            $receptions = $this->receptionModel->listReceptions();
            $invoices = $this->invoiceModel->listInvoices();
            $payments = $this->paymentModel->listPayments();

            Flight::render('AVIS/Achat/dashboard', [
                'suppliers' => $suppliers,
                'orders' => $orders,
                'receptions' => $receptions,
                'invoices' => $invoices,
                'payments' => $payments,
            ]);
        } catch (Exception $e) {
            Flight::halt(500, 'Erreur lors du chargement du tableau de bord: ' . $e->getMessage());
        }
    }

    public function showCreateForm(): void
    {
        try {
            $suppliers = $this->supplierModel->getAll();
            $articles = $this->articleModel->listWithStock();

            Flight::render('AVIS/Achat/create', [
                'suppliers' => $suppliers,
                'articles' => $articles,
            ]);
        } catch (Exception $e) {
            Flight::halt(500, 'Erreur lors du chargement de la saisie: ' . $e->getMessage());
        }
    }

    public function storeRequest(): void
    {
        if (Flight::request()->method !== 'POST') {
            Flight::halt(405, 'Méthode non autorisée');
            return;
        }

        if (!isset($_SESSION['user']['id_user'])) {
            Flight::halt(401, 'Utilisateur non connecté');
            return;
        }

        try {
            $raw = Flight::request()->data->getData();
            $lines = $this->normalizeLines($raw['lines'] ?? []);

            $requestId = $this->purchaseRequestModel->create([
                'date' => $raw['request_date'] ?? '',
                'supplier_id' => $raw['supplier_id'] ?? 0,
                'remark' => $raw['remark'] ?? '',
                'created_by' => $_SESSION['user']['id_user'],
                'lines' => $lines,
            ]);

            Flight::redirect('/avis/achat/demandes/' . $requestId);
        } catch (Exception $e) {
            Flight::halt(400, 'Impossible d\'enregistrer la demande: ' . $e->getMessage());
        }
    }

    public function listRequests(): void
    {
        try {
            $requests = $this->purchaseRequestModel->listRequests();
            $count = $this->purchaseRequestModel->countRequests();

            Flight::render('AVIS/Achat/list', [
                'requests' => $requests,
                'count' => $count,
            ]);
        } catch (Exception $e) {
            Flight::halt(500, 'Erreur lors du chargement de la liste: ' . $e->getMessage());
        }
    }

    public function listOrders(): void
    {
        try {
            $orders = $this->purchaseOrderModel->listOrders();
            Flight::render('AVIS/Achat/orders_list', [
                'orders' => $orders,
            ]);
        } catch (Exception $e) {
            Flight::halt(500, 'Erreur lors du chargement des bons de commande: ' . $e->getMessage());
        }
    }

    public function showOrderForm(): void
    {
        try {
            $suppliers = $this->supplierModel->getAll();
            $depots = $this->depotModel->getAll();
            $validatedRequests = $this->purchaseRequestModel->listValidatedRequests();
            Flight::render('AVIS/Achat/orders_create', [
                'suppliers' => $suppliers,
                'depots' => $depots,
                'requests' => $validatedRequests,
            ]);
        } catch (Exception $e) {
            Flight::halt(500, 'Erreur lors du chargement du formulaire BC: ' . $e->getMessage());
        }
    }

    public function storeOrder(): void
    {
        if (Flight::request()->method !== 'POST') {
            Flight::halt(405, 'Méthode non autorisée');
            return;
        }

        if (!isset($_SESSION['user']['id_user'])) {
            Flight::halt(401, 'Utilisateur non connecté');
            return;
        }

        try {
            $raw = Flight::request()->data->getData();

            $supplierId = (int) ($raw['supplier_id'] ?? 0);
            $depotId = (int) ($raw['depot_id'] ?? 0);
            $requestId = (int) ($raw['request_id'] ?? 0);

            if (!$this->supplierModel->exists($supplierId)) {
                throw new Exception('Fournisseur introuvable.');
            }

            if (!$this->depotModel->exists($depotId)) {
                throw new Exception('Dépôt introuvable.');
            }

            $request = $this->purchaseRequestModel->findById($requestId);
            if (!$request) {
                throw new Exception('Demande d\'achat introuvable.');
            }
            if (($request['statut'] ?? '') !== 'VISEE') {
                throw new Exception('La demande d\'achat doit être validée avant création du BC.');
            }

            $ht = (float) ($raw['montant_ht'] ?? 0);
            $tvaRate = (float) ($raw['tva_rate'] ?? 0);
            if ($tvaRate < 0) {
                throw new Exception('Le taux de TVA doit être positif.');
            }
            $tvaAmount = round($ht * $tvaRate / 100, 2);
            $ttc = round($ht + $tvaAmount, 2);

            $orderId = $this->purchaseOrderModel->create([
                'bc_numero' => trim($raw['bc_numero'] ?? ''),
                'bc_date' => $raw['bc_date'] ?? '',
                'id_fournisseur' => $supplierId,
                'id_depot' => $depotId,
                'id_demande_achat' => $requestId,
                'montant_ht' => $ht,
                'montant_tva' => $tvaAmount,
                'montant_ttc' => $ttc,
                'created_by' => $_SESSION['user']['id_user'],
            ]);

            Flight::redirect('/avis/achat/bc');
        } catch (Exception $e) {
            Flight::halt(400, 'Impossible de créer le BC: ' . $e->getMessage());
        }
    }

    public function listReceptions(): void
    {
        try {
            $receptions = $this->receptionModel->listReceptions();
            Flight::render('AVIS/Achat/receptions_list', [
                'receptions' => $receptions,
            ]);
        } catch (Exception $e) {
            Flight::halt(500, 'Erreur lors du chargement des réceptions: ' . $e->getMessage());
        }
    }

    public function showReceptionForm(): void
    {
        try {
            $suppliers = $this->supplierModel->getAll();
            $orders = $this->purchaseOrderModel->listOrders();
            $depots = $this->depotModel->getAll();

            Flight::render('AVIS/Achat/receptions_create', [
                'suppliers' => $suppliers,
                'orders' => $orders,
                'depots' => $depots,
            ]);
        } catch (Exception $e) {
            Flight::halt(500, 'Erreur lors du chargement du formulaire de réception: ' . $e->getMessage());
        }
    }

    public function storeReception(): void
    {
        if (Flight::request()->method !== 'POST') {
            Flight::halt(405, 'Méthode non autorisée');
            return;
        }

        if (!isset($_SESSION['user']['id_user'])) {
            Flight::halt(401, 'Utilisateur non connecté');
            return;
        }

        try {
            $raw = Flight::request()->data->getData();

            $supplierId = (int) ($raw['supplier_id'] ?? 0);
            $orderId = (int) ($raw['order_id'] ?? 0);
            $depotId = (int) ($raw['depot_id'] ?? 0);

            if (!$this->supplierModel->exists($supplierId)) {
                throw new Exception('Fournisseur introuvable.');
            }

            $order = $this->purchaseOrderModel->findById($orderId);
            if (!$order) {
                throw new Exception('Bon de commande introuvable.');
            }

            if (!$this->depotModel->exists($depotId)) {
                throw new Exception('Dépôt introuvable.');
            }

            $this->receptionModel->create([
                'reception_numero' => trim($raw['reception_numero'] ?? ''),
                'reception_date' => $raw['reception_date'] ?? '',
                'id_fournisseur' => $supplierId,
                'id_bon_commande_fournisseur' => $orderId,
                'id_depot' => $depotId,
                'created_by' => $_SESSION['user']['id_user'],
            ]);

            Flight::redirect('/avis/achat/receptions');
        } catch (Exception $e) {
            Flight::halt(400, 'Impossible de créer la réception: ' . $e->getMessage());
        }
    }

    public function listInvoices(): void
    {
        try {
            $invoices = $this->invoiceModel->listInvoices();
            Flight::render('AVIS/Achat/invoices_list', [
                'invoices' => $invoices,
            ]);
        } catch (Exception $e) {
            Flight::halt(500, 'Erreur lors du chargement des factures: ' . $e->getMessage());
        }
    }

    public function showInvoiceForm(): void
    {
        try {
            $suppliers = $this->supplierModel->getAll();
            $receptions = $this->receptionModel->listReceptions();

            Flight::render('AVIS/Achat/invoices_create', [
                'suppliers' => $suppliers,
                'receptions' => $receptions,
            ]);
        } catch (Exception $e) {
            Flight::halt(500, 'Erreur lors du chargement du formulaire facture: ' . $e->getMessage());
        }
    }

    public function storeInvoice(): void
    {
        if (Flight::request()->method !== 'POST') {
            Flight::halt(405, 'Méthode non autorisée');
            return;
        }

        if (!isset($_SESSION['user']['id_user'])) {
            Flight::halt(401, 'Utilisateur non connecté');
            return;
        }

        try {
            $raw = Flight::request()->data->getData();

            $supplierId = (int) ($raw['supplier_id'] ?? 0);
            $receptionId = (int) ($raw['reception_id'] ?? 0);
            $ht = (float) ($raw['montant_ht'] ?? 0);
            $tvaRate = (float) ($raw['tva_rate'] ?? 0);

            if (!$this->supplierModel->exists($supplierId)) {
                throw new Exception('Fournisseur introuvable.');
            }

            $reception = $this->receptionModel->findById($receptionId);
            if (!$reception) {
                throw new Exception('Réception introuvable.');
            }

            if ($tvaRate < 0) {
                throw new Exception('Le taux de TVA doit être positif.');
            }
            $tvaAmount = round($ht * $tvaRate / 100, 2);
            $ttc = round($ht + $tvaAmount, 2);

            $this->invoiceModel->create([
                'facture_numero' => trim($raw['facture_numero'] ?? ''),
                'facture_date' => $raw['facture_date'] ?? '',
                'id_fournisseur' => $supplierId,
                'id_reception_fournisseur' => $receptionId,
                'montant_ht' => $ht,
                'montant_tva' => $tvaAmount,
                'montant_ttc' => $ttc,
                'created_by' => $_SESSION['user']['id_user'],
            ]);

            Flight::redirect('/avis/achat/factures');
        } catch (Exception $e) {
            Flight::halt(400, 'Impossible de créer la facture: ' . $e->getMessage());
        }
    }

    public function listPayments(): void
    {
        try {
            $payments = $this->paymentModel->listPayments();
            Flight::render('AVIS/Achat/payments_list', [
                'payments' => $payments,
            ]);
        } catch (Exception $e) {
            Flight::halt(500, 'Erreur lors du chargement des paiements: ' . $e->getMessage());
        }
    }

    public function showPaymentForm(): void
    {
        try {
            $invoices = $this->invoiceModel->listInvoices();
            $modes = $this->paymentModeModel->getAll();
            Flight::render('AVIS/Achat/payments_create', [
                'invoices' => $invoices,
                'modes' => $modes,
            ]);
        } catch (Exception $e) {
            Flight::halt(500, 'Erreur lors du chargement du formulaire paiement: ' . $e->getMessage());
        }
    }

    public function storePayment(): void
    {
        if (Flight::request()->method !== 'POST') {
            Flight::halt(405, 'Méthode non autorisée');
            return;
        }

        if (!isset($_SESSION['user']['id_user'])) {
            Flight::halt(401, 'Utilisateur non connecté');
            return;
        }

        try {
            $raw = Flight::request()->data->getData();

            $invoiceId = (int) ($raw['invoice_id'] ?? 0);
            $modeId = (int) ($raw['mode_id'] ?? 0);
            $amount = (float) ($raw['amount'] ?? 0);

            $invoice = $this->invoiceModel->findById($invoiceId);
            if (!$invoice) {
                throw new Exception('Facture introuvable.');
            }

            if (!$this->paymentModeModel->exists($modeId)) {
                throw new Exception('Mode de paiement introuvable.');
            }

            $this->paymentModel->create([
                'paiement_numero' => trim($raw['paiement_numero'] ?? ''),
                'paiement_date' => $raw['paiement_date'] ?? '',
                'id_facture_fournisseur' => $invoiceId,
                'montant_total' => $amount,
                'reference_paiement' => trim($raw['reference'] ?? ''),
                'cree_par' => $_SESSION['user']['id_user'],
                'details' => [
                    ['id_mode_paiement' => $modeId, 'montant' => $amount],
                ],
            ]);

            Flight::redirect('/avis/achat/paiements');
        } catch (Exception $e) {
            Flight::halt(400, 'Impossible d\'enregistrer le paiement: ' . $e->getMessage());
        }
    }

    public function showRequest(int $id): void
    {
        try {
            $request = $this->purchaseRequestModel->findWithLines($id);
            if (!$request) {
                Flight::halt(404, 'Demande introuvable');
                return;
            }

            Flight::render('AVIS/Achat/detail', [
                'request' => $request,
            ]);
        } catch (Exception $e) {
            Flight::halt(500, 'Erreur lors du chargement de la demande: ' . $e->getMessage());
        }
    }

    public function validateRequest(int $id): void
    {
        if (Flight::request()->method !== 'POST') {
            Flight::halt(405, 'Méthode non autorisée');
            return;
        }

        if (!isset($_SESSION['user']['id_user'])) {
            Flight::halt(401, 'Utilisateur non connecté');
            return;
        }

        try {
            $this->purchaseRequestModel->updateStatus($id, 'VISEE', (int) $_SESSION['user']['id_user'], null);
            Flight::redirect('/avis/achat/demandes/' . $id);
        } catch (Exception $e) {
            Flight::halt(400, 'Impossible de valider la demande: ' . $e->getMessage());
        }
    }

    private function normalizeLines(array $rawLines): array
    {
        $normalized = [];

        foreach ($rawLines as $line) {
            if ($line instanceof \stdClass) {
                $line = (array) $line;
            }

            $designation = trim($line['designation'] ?? '');
            $quantity = (float) ($line['quantity'] ?? 0);
            $unitPrice = (float) ($line['unit_price'] ?? 0);

            if ($designation === '' && $quantity <= 0) {
                continue;
            }

            $normalized[] = [
                'article_id' => isset($line['article_id']) && $line['article_id'] !== '' ? (int) $line['article_id'] : null,
                'product_code' => trim($line['product_code'] ?? ''),
                'designation' => $designation !== '' ? $designation : trim($line['product_code'] ?? ''),
                'quantity' => $quantity,
                'unit_price' => $unitPrice >= 0 ? $unitPrice : 0,
                'vat' => (float) ($line['vat'] ?? 0),
                'stock_quantity' => (float) ($line['stock_quantity'] ?? 0),
            ];
        }

        if (empty($normalized)) {
            throw new Exception('Veuillez saisir au moins une ligne valide.');
        }

        return $normalized;
    }
}
