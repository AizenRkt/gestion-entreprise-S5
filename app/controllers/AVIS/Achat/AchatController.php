<?php

namespace app\controllers\AVIS\Achat;

use app\models\AVIS\Achat\ArticleModel;
use app\models\AVIS\Achat\PurchaseRequestModel;
use app\models\AVIS\Achat\SupplierModel;
use Exception;
use Flight;

class AchatController
{
    private SupplierModel $supplierModel;
    private PurchaseRequestModel $purchaseRequestModel;
    private ArticleModel $articleModel;

    public function __construct()
    {
        $this->supplierModel = new SupplierModel();
        $this->purchaseRequestModel = new PurchaseRequestModel();
        $this->articleModel = new ArticleModel();
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

            Flight::redirect(Flight::base() . '/avis/achat/demandes/' . $requestId);
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
            Flight::redirect(Flight::base() . '/avis/achat/demandes/' . $id);
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
