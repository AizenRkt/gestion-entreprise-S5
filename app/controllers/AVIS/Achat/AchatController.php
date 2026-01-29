<?php

namespace app\controllers\AVIS\Achat;

use app\models\AVIS\Achat\SupplierModel;
use app\models\AVIS\Achat\PurchaseOrderModel;
use app\models\AVIS\Achat\ReceptionModel;
use app\models\AVIS\Achat\InvoiceModel;
use app\models\AVIS\Achat\PaymentModel;
use app\models\AVIS\Achat\DepotModel;
use app\models\AVIS\Achat\PaymentModeModel;
use Exception;
use Flight;

class AchatController
{
    private SupplierModel $supplierModel;
    private PurchaseOrderModel $purchaseOrderModel;
    private ReceptionModel $receptionModel;
    private InvoiceModel $invoiceModel;
    private PaymentModel $paymentModel;
    private DepotModel $depotModel;
    private PaymentModeModel $paymentModeModel;

    public function __construct()
    {
        $this->supplierModel = new SupplierModel();
        $this->purchaseOrderModel = new PurchaseOrderModel();
        $this->receptionModel = new ReceptionModel();
        $this->invoiceModel = new InvoiceModel();
        $this->paymentModel = new PaymentModel();
        $this->depotModel = new DepotModel();
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
            $depots = $this->depotModel->getAll();
            $paymentModes = $this->paymentModeModel->getAll();

            Flight::render('AVIS/Achat/dashboard', [
                'suppliers' => $suppliers,
                'orders' => $orders,
                'receptions' => $receptions,
                'invoices' => $invoices,
                'payments' => $payments,
                'depots' => $depots,
                'paymentModes' => $paymentModes,
            ]);
        } catch (Exception $e) {
            Flight::halt(500, 'Erreur lors du chargement du tableau de bord achats: ' . $e->getMessage());
        }
    }

    public function createSupplier(): void
    {
        if (Flight::request()->method !== 'POST') {
            Flight::json(['error' => 'Méthode non autorisée'], 405);
            return;
        }

        try {
            $data = Flight::request()->data->getData();
            $id = $this->supplierModel->create($data);
            Flight::json(['success' => true, 'id' => $id]);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function createOrder(): void
    {
        if (Flight::request()->method !== 'POST') {
            Flight::json(['error' => 'Méthode non autorisée'], 405);
            return;
        }

        try {
            $data = Flight::request()->data->getData();
            $data['created_by'] = $_SESSION['user']['id_user'] ?? 0;
            $id = $this->purchaseOrderModel->create($data);
            Flight::json(['success' => true, 'id' => $id]);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function createReception(): void
    {
        if (Flight::request()->method !== 'POST') {
            Flight::json(['error' => 'Méthode non autorisée'], 405);
            return;
        }

        try {
            $data = Flight::request()->data->getData();
            $data['created_by'] = $_SESSION['user']['id_user'] ?? 0;
            $id = $this->receptionModel->create($data);
            Flight::json(['success' => true, 'id' => $id]);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function createInvoice(): void
    {
        if (Flight::request()->method !== 'POST') {
            Flight::json(['error' => 'Méthode non autorisée'], 405);
            return;
        }

        try {
            $data = Flight::request()->data->getData();
            $data['created_by'] = $_SESSION['user']['id_user'] ?? 0;
            $id = $this->invoiceModel->create($data);
            Flight::json(['success' => true, 'id' => $id]);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function createPayment(): void
    {
        if (Flight::request()->method !== 'POST') {
            Flight::json(['error' => 'Méthode non autorisée'], 405);
            return;
        }

        try {
            $data = Flight::request()->data->getData();
            $data['cree_par'] = $_SESSION['user']['id_user'] ?? 0;
            $id = $this->paymentModel->create($data);
            Flight::json(['success' => true, 'id' => $id]);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }
}
