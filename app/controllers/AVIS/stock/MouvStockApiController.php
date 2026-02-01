<?php

namespace app\controllers\AVIS\stock;

use app\models\AVIS\stock\MouvStockModel;
use app\models\AVIS\stock\StockCourantModel;
use app\models\AVIS\stock\LotModel;
use app\models\AVIS\stock\MouvStockLotDetailModel;
use app\models\AVIS\stock\StockClosureModel;
use app\models\AVIS\stock\StockReservationModel;
use Exception;
use Flight;

class MouvStockApiController
{
	public static function listMovements() {
		try {
			$data = MouvStockModel::getAll();
			Flight::json(['success' => true, 'data' => $data]);
		} catch (Exception $e) {
			Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
		}
	}

	public static function listReservations() {
		try {
			$db = \Flight::db();
			$q = \Flight::request()->query;
			$params = [];
			$where = [];
			$where[] = '(r.date_expiration IS NULL OR r.date_expiration >= NOW() OR r.quantite < 0)';
			if (!empty($q->article)) { $where[] = 'r.id_article = :a'; $params[':a'] = (int)$q->article; }
			if (!empty($q->depot)) { $where[] = 'r.id_depot = :d'; $params[':d'] = (int)$q->depot; }
			if (!empty($q->reference)) { $where[] = 'r.reference LIKE :r'; $params[':r'] = '%' . $q->reference . '%'; }
			if (!empty($q->client)) { $where[] = 'r.id_client = :c'; $params[':c'] = (int)$q->client; }
			$sql = "SELECT r.id_article,
				a.designation AS article_designation,
				r.id_depot,
				d.nom AS depot_nom,
				r.id_client,
				c.nom AS client_nom,
				r.reference,
				COALESCE(SUM(r.quantite),0) AS reserved,
				MIN(r.created_at) AS first_date,
				MAX(r.created_at) AS last_date,
				MIN(CASE WHEN r.quantite > 0 THEN r.date_expiration END) AS expiration\nFROM stock_reservation r\nLEFT JOIN article a ON a.id_article = r.id_article\nLEFT JOIN depot d ON d.id_depot = r.id_depot\nLEFT JOIN client c ON c.id_client = r.id_client";
			if (!empty($where)) { $sql .= "\nWHERE " . implode(' AND ', $where); }
			$sql .= "\nGROUP BY r.id_article, r.id_depot, r.reference, r.id_client, c.nom\nHAVING COALESCE(SUM(r.quantite),0) <> 0\nORDER BY last_date DESC";
			$st = $db->prepare($sql);
			$st->execute($params);
			$rows = $st->fetchAll(\PDO::FETCH_ASSOC);
			\Flight::json(['success' => true, 'data' => $rows]);
		} catch (\Exception $e) {
			\Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
		}
	}

	public static function getMovement($id) {
		try {
			$mv = \app\models\AVIS\stock\MouvStockModel::getById((int)$id);
			if (!$mv) { Flight::json(['success' => false, 'message' => 'Introuvable'], 404); return; }
			Flight::json(['success' => true, 'data' => $mv]);
		} catch (Exception $e) {
			Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
		}
	}

	public static function getMovementLotDetails($id) {
		try {
			$details = \app\models\AVIS\stock\MouvStockLotDetailModel::getByMovement((int)$id);
			Flight::json(['success' => true, 'data' => $details]);
		} catch (Exception $e) {
			Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
		}
	}

	public static function exportMovementLotDetailsCsv($id) {
		try {
			$mv = \app\models\AVIS\stock\MouvStockModel::getById((int)$id);
			if (!$mv) { \Flight::halt(404, 'Introuvable'); return; }
			$details = \app\models\AVIS\stock\MouvStockLotDetailModel::getByMovement((int)$id);
			$filename = 'mouvement_' . $id . '_details.csv';
			header('Content-Type: text/csv; charset=utf-8');
			header('Content-Disposition: attachment; filename=' . $filename);
			$out = fopen('php://output', 'w');
			// meta
			fputcsv($out, ['Mouvement', $id]);
			fputcsv($out, ['Article', $mv['article_designation'] ?? $mv['id_article']]);
			fputcsv($out, ['Depot', $mv['depot_nom'] ?? $mv['id_depot']]);
			fputcsv($out, ['Type', $mv['type_libelle'] ?? $mv['id_type_mouvement_stock']]);
			fputcsv($out, ['Sens', $mv['sens'] == 1 ? 'Entree' : 'Sortie']);
			fputcsv($out, ['Quantite', $mv['quantite']]);
			fputcsv($out, ['Date', $mv['date_mouvement']]);
			fputcsv($out, []);
			// header
			fputcsv($out, ['Lot', 'Date entree', 'Quantite', 'Cout unitaire', 'Valeur']);
			foreach ($details as $d) {
				fputcsv($out, [
					$d['lot_numero'] ?? $d['id_lot'],
					$d['date_entree'] ?? '',
					$d['quantite'],
					$d['cout_unitaire'],
					$d['valeur']
				]);
			}
			fclose($out);
			// Flight response already sent by headers and output
		} catch (\Exception $e) {
			\Flight::halt(500, $e->getMessage());
		}
	}

	public static function exportMovementLotDetailsPdf($id) {
		try {
			$root = dirname(__DIR__, 4);
			$fpdfPath = $root . '/vendor/fpdf186/fpdf.php';
			if (!file_exists($fpdfPath)) { \Flight::halt(500, 'Bibliothèque FPDF manquante'); return; }
			require_once $fpdfPath;

			$mv = \app\models\AVIS\stock\MouvStockModel::getById((int)$id);
			if (!$mv) { \Flight::halt(404, 'Introuvable'); return; }
			$details = \app\models\AVIS\stock\MouvStockLotDetailModel::getByMovement((int)$id);

			$pdf = new \FPDF();
			$pdf->AddPage();
			$pdf->SetFont('Arial','B',16);
			$pdf->Cell(0,10, utf8_decode('Détails de consommation par lot'), 0, 1, 'C');

			$pdf->SetFont('Arial','',12);
			$pdf->Cell(0,8, utf8_decode('Mouvement #: ') . $id, 0, 1);
			$pdf->Cell(0,8, utf8_decode('Article: ') . utf8_decode($mv['article_designation'] ?? (string)$mv['id_article']), 0, 1);
			$pdf->Cell(0,8, utf8_decode('Dépôt: ') . utf8_decode($mv['depot_nom'] ?? (string)$mv['id_depot']), 0, 1);
			$pdf->Cell(0,8, utf8_decode('Type: ') . utf8_decode($mv['type_libelle'] ?? (string)$mv['id_type_mouvement_stock']), 0, 1);
			$pdf->Cell(0,8, utf8_decode('Sens: ') . ($mv['sens'] == 1 ? 'Entrée' : 'Sortie'), 0, 1);
			$pdf->Cell(0,8, utf8_decode('Quantité: ') . (string)$mv['quantite'], 0, 1);
			$pdf->Cell(0,8, utf8_decode('Date: ') . (string)$mv['date_mouvement'], 0, 1);

			$pdf->Ln(4);
			// Table header
			$pdf->SetFont('Arial','B',11);
			$pdf->Cell(50,8, utf8_decode('Lot'), 1);
			$pdf->Cell(40,8, utf8_decode('Date entrée'), 1);
			$pdf->Cell(30,8, utf8_decode('Quantité'), 1, 0, 'R');
			$pdf->Cell(35,8, utf8_decode('Coût unitaire'), 1, 0, 'R');
			$pdf->Cell(35,8, utf8_decode('Valeur'), 1, 1, 'R');

			$pdf->SetFont('Arial','',11);
			if (empty($details)) {
				$pdf->Cell(190,8, utf8_decode('Aucun détail disponible'), 1, 1, 'C');
			} else {
				foreach ($details as $d) {
					$lotNum = $d['lot_numero'] ?? (string)$d['id_lot'];
					$dateEntree = isset($d['date_entree']) ? (string)$d['date_entree'] : '';
					$pdf->Cell(50,8, utf8_decode($lotNum), 1);
					$pdf->Cell(40,8, utf8_decode($dateEntree), 1);
					$pdf->Cell(30,8, (string)$d['quantite'], 1, 0, 'R');
					$pdf->Cell(35,8, (string)$d['cout_unitaire'], 1, 0, 'R');
					$pdf->Cell(35,8, (string)$d['valeur'], 1, 1, 'R');
				}

			}

			$filename = 'mouvement_' . $id . '_details.pdf';
			// Force download
			$pdf->Output('D', $filename);
		} catch (\Exception $e) {
			\Flight::halt(500, $e->getMessage());
		}
	}

	public static function createMovement() {
		try {
			$rawBody = Flight::request()->getBody();
			$req = json_decode($rawBody, true);
			if (!is_array($req)) {
				$formData = Flight::request()->data->getData();
				$req = is_array($formData) && !empty($formData) ? $formData : [];
			}

			// Debug: log the received request
			error_log("DEBUG: Received request data: " . json_encode($req));
			error_log("DEBUG: cout_unitaire value: " . var_export($req['cout_unitaire'] ?? 'NOT SET', true));
			$reservationExpirationInput = $req['reservation_expiration'] ?? null;
			$reservationClientIdInput = $req['id_client'] ?? null;

			$payload = [
				'id_article' => (int)($req['id_article'] ?? 0),
				'id_depot' => (int)($req['id_depot'] ?? 0),
				'id_lot' => $req['id_lot'] ?? null,
				'id_type_mouvement_stock' => (int)($req['id_type_mouvement_stock'] ?? 0),
				'id_reference' => (int)($req['id_reference'] ?? 0),
				'table_reference' => $req['table_reference'] ?? null,
				'sens' => (int)($req['sens'] ?? 0),
				'quantite' => (float)($req['quantite'] ?? 0),
				'cout_unitaire' => isset($req['cout_unitaire']) && $req['cout_unitaire'] !== null && $req['cout_unitaire'] !== '' ? (float)$req['cout_unitaire'] : null,
				'motif' => $req['motif'] ?? null,
				'date_mouvement' => $req['date_mouvement'] ?? date('Y-m-d H:i:s'),
				'created_by' => (int)($_SESSION['user']['id_user'] ?? 0)
			];

			// Validate DLC and DLUO
			$db = Flight::db();
			$lotQuery = $db->prepare("SELECT date_limite_consommation, date_limite_utilisation_optimale FROM lot WHERE id_lot = :id_lot LIMIT 1");
			$lotQuery->execute([':id_lot' => $payload['id_lot']]);
			$lot = $lotQuery->fetch(\PDO::FETCH_ASSOC);

			if ($lot) {
				$dateMouvement = new \DateTime($payload['date_mouvement']);

				if ($lot['date_limite_consommation'] && $dateMouvement > new \DateTime($lot['date_limite_consommation'])) {
					Flight::halt(400, 'Le mouvement ne peut pas être validé car la DLC est dépassée.');
				}

				if ($lot['date_limite_utilisation_optimale'] && $dateMouvement > new \DateTime($lot['date_limite_utilisation_optimale'])) {
					// Allow only if the movement type permits it (e.g., discounted sales)
					$allowedTypes = [/* Add allowed movement type IDs here */];
					if (!in_array($payload['id_type_mouvement_stock'], $allowedTypes)) {
						Flight::halt(400, 'Le mouvement ne peut pas être validé car la DLUO est dépassée.');
					}
				}
			}

			error_log("DEBUG: Payload cout_unitaire: " . var_export($payload['cout_unitaire'], true));

			$model = new MouvStockModel();
			$id = $model->insert($payload);
			// Immediate processing for non-validating movement types (e.g., reservation)
			$db = Flight::db();
			$st = $db->prepare("SELECT code, impact_valorisation, necessite_validation FROM mouvement_stock_type WHERE id_type_mouvement_stock = :id LIMIT 1");
			$st->execute([':id' => (int)$payload['id_type_mouvement_stock']]);
			$type = $st->fetch(\PDO::FETCH_ASSOC);
			$userId = (int)($_SESSION['user']['id_user'] ?? 0);
			if ($type && (int)$type['necessite_validation'] === 0) {
				$mv = MouvStockModel::getById((int)$id);
				$ref = ($mv['table_reference'] && $mv['id_reference']) ? ($mv['table_reference'] . '#' . $mv['id_reference']) : null;
				if (strtoupper((string)$type['code']) === 'RESERVATION') {
					$reservationExpiration = null;
					if (is_string($reservationExpirationInput) && trim($reservationExpirationInput) !== '') {
						try {
							$expiresAt = new \DateTime($reservationExpirationInput);
							if ($expiresAt <= new \DateTime()) {
								Flight::halt(400, "La date d'expiration doit être ultérieure à maintenant.");
							}
							$reservationExpiration = $expiresAt->format('Y-m-d H:i:s');
						} catch (\Exception $e) {
							Flight::halt(400, "Date d'expiration invalide");
						}
					}
					$reservationClientId = null;
					if ($reservationClientIdInput !== null && $reservationClientIdInput !== '') {
						$reservationClientId = (int)$reservationClientIdInput;
						if ($reservationClientId <= 0) { $reservationClientId = null; }
					}
					StockReservationModel::reserve((int)$mv['id_article'], (int)$mv['id_depot'], (float)$mv['quantite'], $ref, $userId, $reservationExpiration, $reservationClientId);
				} elseif (strtoupper((string)$type['code']) === 'ANNULATION_RESERVATION') {
					$reservationClientId = null;
					if ($reservationClientIdInput !== null && $reservationClientIdInput !== '') {
						$reservationClientId = (int)$reservationClientIdInput;
						if ($reservationClientId <= 0) { $reservationClientId = null; }
					}
					StockReservationModel::cancel((int)$mv['id_article'], (int)$mv['id_depot'], (float)$mv['quantite'], $ref, $userId, $reservationClientId);
				}
				// mark validated
				MouvStockModel::updateFields((int)$id, ['date_validation' => date('Y-m-d H:i:s')]);
				$st2 = $db->prepare("INSERT INTO mouvement_stock_status (id_mouvement_stock, libelle, created_by) VALUES (:id, 'validé', :user)");
				$st2->execute([':id' => (int)$id, ':user' => $userId]);
			}
			Flight::json(['success' => true, 'message' => 'Mouvement créé', 'id' => $id]);
		} catch (Exception $e) {
			Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
		}
	}

	public static function validateMovement($id) {
		try {
			$userId = (int)($_SESSION['user']['id_user'] ?? 0);
			$id = (int)$id;
			$req = Flight::request();
			$valuation = strtolower($req->query->valuation ?? 'auto'); // 'cump'|'fifo'|'lifo'|'auto'
			$allocation = strtolower($req->query->allocation ?? 'auto'); // 'fifo'|'fefo'|'lifo'|'auto'

			$mv = MouvStockModel::getById((int)$id);
			if (!$mv) { Flight::json(['success' => false, 'message' => 'Mouvement introuvable'], 404); return; }
			if (!empty($mv['date_validation'])) { Flight::json(['success' => false, 'message' => 'Déjà validé'], 400); return; }

			// Guardrail: block validation in or before closed periods
			if (!StockClosureModel::isDateAllowed($mv['date_mouvement'])) {
				Flight::json(['success' => false, 'message' => 'La période du mouvement est clôturée ou antérieure à une clôture. Validation interdite.'], 400);
				return;
			}

			$article = (int)$mv['id_article'];
			$depot = (int)$mv['id_depot'];
			$quantite = (float)$mv['quantite'];
			$sens = (int)$mv['sens'];
			// DB handle
			$db = Flight::db();
			// Fetch type code for special flows
			$typeCode = null;
			try {
				$tc = $db->prepare("SELECT code FROM mouvement_stock_type WHERE id_type_mouvement_stock = :id");
				$tc->execute([':id' => (int)$mv['id_type_mouvement_stock']]);
				$typeCode = strtoupper((string)($tc->fetch(\PDO::FETCH_ASSOC)['code'] ?? ''));
			} catch (\Exception $ignore) {}

			$articleMethodCode = '';
			// Enforce per-article defaults for valuation/allocation when 'auto'
			try {
				$st = $db->prepare("SELECT a.id_methode_valorisation, mv.code AS methode_code, a.allocation_defaut FROM article a LEFT JOIN methode_valorisation mv ON mv.id_methode_valorisation = a.id_methode_valorisation WHERE a.id_article = :id LIMIT 1");
				$st->execute([':id' => $article]);
				$row = $st->fetch(\PDO::FETCH_ASSOC) ?: [];
				$articleMethodCode = strtolower($row['methode_code'] ?? '');
				if ($valuation === 'auto' || $valuation === '') {
					$valuation = $articleMethodCode ?: 'cump';
				}
				if ($allocation === 'auto' || $allocation === '') {
					$allocationDefault = strtolower($row['allocation_defaut'] ?? '');
					$allocation = in_array($allocationDefault, ['fifo','lifo','fefo'], true) ? $allocationDefault : 'auto';
				}
			} catch (\Exception $e) {
				// fallback silently if referential not found
			}
			$lotValuationMethods = ['fifo','lifo','fefo'];

			// Check if article's family requires lot traceability (column may not exist on older DBs)
			$requiresLot = false;
			try {
				$lf = $db->prepare("SELECT af.necessite_lot FROM article a LEFT JOIN article_famille af ON a.id_famille_article_famille = af.id_article_famille WHERE a.id_article = :id LIMIT 1");
				$lf->execute([':id' => $article]);
				$r = $lf->fetch(\PDO::FETCH_ASSOC) ?: [];
				$requiresLot = !empty($r['necessite_lot']);
			} catch (\Exception $ignore) {
				// If column/table not present, don't enforce
				$requiresLot = false;
			}

			// If lot is required for this article family and this is a sortie, enforce rules while auto-selecting lots when needed
			if ($requiresLot && (int)$sens !== 1) {
				if (!in_array($valuation, $lotValuationMethods, true)) {
					if (in_array($allocation, $lotValuationMethods, true)) {
						$valuation = $allocation;
					} elseif (in_array($articleMethodCode, $lotValuationMethods, true)) {
						$valuation = $articleMethodCode;
					} else {
						$valuation = 'fifo';
					}
				}
				if (!in_array($allocation, $lotValuationMethods, true)) {
					if (in_array($valuation, $lotValuationMethods, true)) {
						$allocation = $valuation;
					} elseif (in_array($articleMethodCode, $lotValuationMethods, true)) {
						$allocation = $articleMethodCode;
					} else {
						$allocation = 'fifo';
					}
				}
				$previewMethod = in_array($allocation, $lotValuationMethods, true) ? $allocation : $valuation;
				if (!in_array($previewMethod, $lotValuationMethods, true)) {
					$previewMethod = $valuation === 'fefo' ? 'fefo' : ($valuation === 'lifo' ? 'lifo' : 'fifo');
				}
				$movementDate = $mv['date_mouvement'] ?? null;
				if (!empty($mv['id_lot'])) {
					try {
						$lt = $db->prepare("SELECT date_limite_consommation, date_limite_utilisation_optimale FROM lot WHERE id_lot = :id LIMIT 1");
						$lt->execute([':id' => (int)$mv['id_lot']]);
						$lotRow = $lt->fetch(\PDO::FETCH_ASSOC) ?: [];
						$reference = null;
						if ($movementDate) {
							try { $reference = new \DateTime($movementDate); } catch (\Exception $ignore) { $reference = null; }
						}
						if (!$reference) { $reference = new \DateTime('today'); }
						if (!empty($lotRow['date_limite_consommation'])) {
							$dlc = new \DateTime($lotRow['date_limite_consommation']);
							$dlc->setTime(23, 59, 59);
							if ($reference > $dlc) { Flight::json(['success' => false, 'message' => 'Le lot sélectionné est expiré et ne peut pas être utilisé.'], 400); return; }
						}
						if (!empty($lotRow['date_limite_utilisation_optimale'])) {
							$dluo = new \DateTime($lotRow['date_limite_utilisation_optimale']);
							$dluo->setTime(23, 59, 59);
							if ($reference > $dluo) { Flight::json(['success' => false, 'message' => 'Le lot sélectionné est expiré (DLUO dépassée) et ne peut pas être utilisé.'], 400); return; }
						}
					} catch (\Exception $e) {
						// ignore and let allocation logic handle missing lot
					}
				} else {
					try {
						$available = LotModel::getAvailableForAllocation($article, $depot, $previewMethod, $movementDate);
						if (empty($available)) {
							Flight::json(['success' => false, 'message' => 'Aucun lot disponible pour allocation — la traçabilité lot est requise pour cet article.'], 400);
							return;
						}
					} catch (\Exception $e) {
						Flight::json(['success' => false, 'message' => 'Impossible de vérifier la disponibilité des lots.'], 500);
						return;
					}
				}
			}

			$db->beginTransaction();

			$fieldsToUpdate = [];

			if ($sens === 1) {
				// Entrée: si pas de lot, créer; appliquer entrée sur stock courant
				if (empty($mv['id_lot'])) {
					$lotNum = $mv['id_reference'] ? ('REF-' . $mv['id_reference']) : ('LOT-' . date('YmdHis'));
					$lotId = (new LotModel())->insert([
						'id_article' => $article,
						'id_depot' => $depot,
						'lot_numero' => $lotNum,
						'date_entree' => $mv['date_mouvement'],
						'quantite_initiale' => $quantite,
						'cout_unitaire' => (float)($mv['cout_unitaire'] ?? 0),
						'date_limite_utilisation_optimale' => null,
						'date_limite_consommation' => null,
					]);
					$fieldsToUpdate['id_lot'] = $lotId;
				}
				// Met à jour stock courant avec coût unitaire fourni
				$cu = isset($mv['cout_unitaire']) ? (float)$mv['cout_unitaire'] : 0.0;
				StockCourantModel::applyEntry($article, $depot, $quantite, $cu);
				$fieldsToUpdate['date_validation'] = date('Y-m-d H:i:s');
				MouvStockModel::updateFields($id, $fieldsToUpdate);
				$st = $db->prepare("INSERT INTO mouvement_stock_status (id_mouvement_stock, libelle, created_by) VALUES (:id, 'validé', :user)");
				$st->execute([':id' => $id, ':user' => $userId]);
				$db->commit();
				Flight::json(['success' => true, 'message' => 'Entrée validée']);
				return;
			}

			// Sortie: allocation par lots (FIFO/FEFO/LIFO) si valuation lot-based; sinon CUMP
			if (in_array($valuation, $lotValuationMethods, true)) {
				// Delivery: consume reservations linked to reference first
				if ($typeCode === 'VENTE_LIVRAISON') {
					$ref = ($mv['table_reference'] && $mv['id_reference']) ? ($mv['table_reference'] . '#' . $mv['id_reference']) : null;
					StockReservationModel::consumeForReference($article, $depot, $quantite, $ref, $userId);
				}
				$allocMethod = $allocation;
				if (!in_array($allocMethod, $lotValuationMethods, true)) {
					if ($allocMethod === 'auto') {
						$allocMethod = $valuation;
					}
				}
				if (!in_array($allocMethod, $lotValuationMethods, true)) {
					$lotsPreview = LotModel::getAvailableForAllocation($article, $depot, 'fifo', $mv['date_mouvement'] ?? null);
					$hasExpiry = false;
					foreach ($lotsPreview as $lp) {
						if (!empty($lp['date_limite_consommation']) || !empty($lp['date_limite_utilisation_optimale'])) { $hasExpiry = true; break; }
					}
					if ($valuation === 'fefo' || $hasExpiry) {
						$allocMethod = 'fefo';
					} elseif ($valuation === 'lifo') {
						$allocMethod = 'lifo';
					} else {
						$allocMethod = 'fifo';
					}
				}
				try {
					$allocs = LotModel::allocateQuantity($article, $depot, $quantite, $allocMethod, $mv['date_mouvement'] ?? null);
				} catch (\Exception $e) {
					$db->rollBack();
					Flight::json(['success' => false, 'message' => $e->getMessage()], 400);
					return;
				}
				$valeurTotale = 0.0;
				foreach ($allocs as $al) { $valeurTotale += ($al['quantite'] * $al['cout_unitaire']); }
				StockCourantModel::applyExitByValue($article, $depot, $quantite, $valeurTotale);
				// enregistrer les détails de consommation par lot
				MouvStockLotDetailModel::replaceDetails($id, $allocs);
				// si une seule allocation, rattacher le lot
				$coutEffectif = $quantite > 0 ? ($valeurTotale / $quantite) : 0.0;
				if (count($allocs) === 1) { $fieldsToUpdate['id_lot'] = $allocs[0]['id_lot']; $fieldsToUpdate['cout_unitaire'] = $allocs[0]['cout_unitaire']; }
				// Gestion des écarts de valorisation: si opérateur a fourni un coût unitaire, enregistrer l'écart
				try {
					$declaredCu = isset($mv['cout_unitaire']) && $mv['cout_unitaire'] !== null && $mv['cout_unitaire'] !== '' ? (float)$mv['cout_unitaire'] : null;
					if ($declaredCu !== null) {
						$variance = round(($declaredCu - $coutEffectif) * $quantite, 2);
						$fieldsToUpdate['ecart_valorisation'] = $variance;
					}
				} catch (\Exception $e) {
					// ignore variance calculation issues
				}
				$fieldsToUpdate['date_validation'] = date('Y-m-d H:i:s');
				MouvStockModel::updateFields($id, $fieldsToUpdate);
				$st = $db->prepare("INSERT INTO mouvement_stock_status (id_mouvement_stock, libelle, created_by) VALUES (:id, 'validé', :user)");
				$st->execute([':id' => $id, ':user' => $userId]);
				$db->commit();
				Flight::json(['success' => true, 'message' => 'Sortie validée (FIFO/FEFO/LIFO)']);
				return;
			}

			// CUMP par défaut
			// Default CUMP
			if ($typeCode === 'VENTE_LIVRAISON') {
				$ref = ($mv['table_reference'] && $mv['id_reference']) ? ($mv['table_reference'] . '#' . $mv['id_reference']) : null;
				StockReservationModel::consumeForReference($article, $depot, $quantite, $ref, $userId);
			}
			$cu = StockCourantModel::applyExitCUMP($article, $depot, $quantite);
			$fieldsToUpdate['cout_unitaire'] = $cu;
			// Gestion des écarts de valorisation pour CUMP
			try {
				$declaredCu = isset($mv['cout_unitaire']) && $mv['cout_unitaire'] !== null && $mv['cout_unitaire'] !== '' ? (float)$mv['cout_unitaire'] : null;
				if ($declaredCu !== null) {
					$variance = round(($declaredCu - $cu) * $quantite, 2);
					$fieldsToUpdate['ecart_valorisation'] = $variance;
				}
			} catch (\Exception $e) {
				// ignore
			}
			$fieldsToUpdate['date_validation'] = date('Y-m-d H:i:s');
			MouvStockModel::updateFields($id, $fieldsToUpdate);
			$st = $db->prepare("INSERT INTO mouvement_stock_status (id_mouvement_stock, libelle, created_by) VALUES (:id, 'validé', :user)");
			$st->execute([':id' => $id, ':user' => $userId]);
			$db->commit();
			Flight::json(['success' => true, 'message' => 'Sortie validée (CUMP)']);
		} catch (Exception $e) {
			Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
		}
	}

	public static function listTypes() {
		try {
			$db = \Flight::db();
			$stmt = $db->query("SELECT id_type_mouvement_stock, id_categorie_mouvement_stock, code, libelle, impact_valorisation, necessite_validation FROM mouvement_stock_type ORDER BY id_categorie_mouvement_stock, libelle");
			$types = $stmt->fetchAll(\PDO::FETCH_ASSOC);
			\Flight::json(['success' => true, 'data' => $types]);
		} catch (\PDOException $e) {
			\Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
		}
	}

	public static function listDepots() {
		try {
			$db = \Flight::db();
			$stmt = $db->query("SELECT id_depot, code, nom FROM depot ORDER BY nom");
			$depots = $stmt->fetchAll(\PDO::FETCH_ASSOC);
			\Flight::json(['success' => true, 'data' => $depots]);
		} catch (\PDOException $e) {
			\Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
		}
	}

	public static function listArticles() {
		try {
			$db = \Flight::db();
			$q = (string)(\Flight::request()->query->q ?? '');
			$params = [];
			$sql = "SELECT id_article, code, designation FROM article";
			if ($q !== '') {
				$sql .= " WHERE designation LIKE :q OR code LIKE :q";
				$params[':q'] = '%' . $q . '%';
			}
			$sql .= " ORDER BY designation LIMIT 50";
			$st = $db->prepare($sql);
			$st->execute($params);
			$rows = $st->fetchAll(\PDO::FETCH_ASSOC);
			\Flight::json(['success' => true, 'data' => $rows]);
		} catch (\PDOException $e) {
			\Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
		}
	}

	public static function getLots() {
		try {
			$article = (int)(\Flight::request()->query->article ?? 0);
			$depot = (int)(\Flight::request()->query->depot ?? 0);
			if (!$article || !$depot) {
				\Flight::json(['success' => false, 'message' => 'article et depot requis'], 400);
				return;
			}
			$lots = \app\models\AVIS\stock\LotModel::getByArticleDepot($article, $depot);
			\Flight::json(['success' => true, 'data' => $lots]);
		} catch (\Exception $e) {
			\Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
		}
	}

	public static function getStockCourant() {
		try {
			$article = (int)(\Flight::request()->query->article ?? 0);
			$depot = (int)(\Flight::request()->query->depot ?? 0);
			if (!$article || !$depot) {
				\Flight::json(['success' => false, 'message' => 'article et depot requis'], 400);
				return;
			}
			$row = \app\models\AVIS\stock\StockCourantModel::getByArticleDepot($article, $depot) ?: ['quantite' => 0, 'valeur_stock' => 0, 'cout_moyen' => null];
			$reserved = StockReservationModel::getReservedQty($article, $depot);
			$row['reserved'] = $reserved;
			$row['disponible'] = ((float)($row['quantite'] ?? 0)) - $reserved;
			\Flight::json(['success' => true, 'data' => $row]);
		} catch (\Exception $e) {
			\Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
		}
	}

	public static function createLot() {
		try {
			$req = Flight::request();
			$data = json_decode($req->getBody(), true) ?? [];

			$id_article = isset($data['id_article']) ? (int)$data['id_article'] : null;
			$id_depot = isset($data['id_depot']) ? (int)$data['id_depot'] : null;
			$lot_numero = isset($data['lot_numero']) ? trim($data['lot_numero']) : null;
			$cout_unitaire = isset($data['cout_unitaire']) ? (float)$data['cout_unitaire'] : 0;
			$quantite_initiale = isset($data['quantite_initiale']) ? (float)$data['quantite_initiale'] : 0;
			$dluo = isset($data['date_limite_utilisation_optimale']) ? $data['date_limite_utilisation_optimale'] : null;
			$dlc = isset($data['date_limite_consommation']) ? $data['date_limite_consommation'] : null;

			// Validations
			if (!$id_article || !$id_depot || !$lot_numero) {
				Flight::json(['success' => false, 'message' => 'Champs requis manquants (article, depot, numero lot)'], 400);
				return;
			}

			// Vérifier que l'article existe
			$db = Flight::db();
			$chk = $db->prepare("SELECT id_article FROM article WHERE id_article = :id LIMIT 1");
			$chk->execute([':id' => $id_article]);
			if (!$chk->fetch()) {
				Flight::json(['success' => false, 'message' => 'Article introuvable'], 404);
				return;
			}

			// Vérifier que le dépôt existe
			$chk = $db->prepare("SELECT id_depot FROM depot WHERE id_depot = :id LIMIT 1");
			$chk->execute([':id' => $id_depot]);
			if (!$chk->fetch()) {
				Flight::json(['success' => false, 'message' => 'Dépôt introuvable'], 404);
				return;
			}

			// Vérifier l'unicité du lot (article + depot + numero)
			$chk = $db->prepare("SELECT id_lot FROM lot WHERE id_article = :a AND id_depot = :d AND lot_numero = :n LIMIT 1");
			$chk->execute([':a' => $id_article, ':d' => $id_depot, ':n' => $lot_numero]);
			if ($chk->fetch()) {
				Flight::json(['success' => false, 'message' => 'Ce numéro de lot existe déjà pour cet article/dépôt'], 400);
				return;
			}

			// Valider les dates si fournies
			if ($dluo && !self::isValidDate($dluo)) {
				Flight::json(['success' => false, 'message' => 'Format DLUO invalide (YYYY-MM-DD)'], 400);
				return;
			}
			if ($dlc && !self::isValidDate($dlc)) {
				Flight::json(['success' => false, 'message' => 'Format DLC invalide (YYYY-MM-DD)'], 400);
				return;
			}

			// Créer le lot
			$lotModel = new LotModel();
			$id_lot = $lotModel->insert([
				'id_article' => $id_article,
				'id_depot' => $id_depot,
				'lot_numero' => $lot_numero,
				'date_entree' => date('Y-m-d'),
				'quantite_initiale' => $quantite_initiale,
				'cout_unitaire' => $cout_unitaire,
				'date_limite_utilisation_optimale' => $dluo,
				'date_limite_consommation' => $dlc
			]);

			Flight::json(['success' => true, 'message' => 'Lot créé avec succès', 'data' => ['id_lot' => $id_lot]]);
		} catch (Exception $e) {
			Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
		}
	}

	private static function isValidDate($date) {
		if (!is_string($date)) return false;
		$d = \DateTime::createFromFormat('Y-m-d', $date);
		return $d && $d->format('Y-m-d') === $date;
	}
}

