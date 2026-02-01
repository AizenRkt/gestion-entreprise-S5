<?php

namespace app\models\AVIS\stock;

use Flight;
use PDO;

class StockReservationModel
{
    public static function getReservedQty(int $article, int $depot): float
    {
        $db = Flight::db();
        $st = $db->prepare("SELECT COALESCE(SUM(quantite),0) AS reserved
            FROM stock_reservation
            WHERE id_article = :a
              AND id_depot = :d
              AND (date_expiration IS NULL OR date_expiration >= NOW() OR quantite < 0)");
        $st->execute([':a' => $article, ':d' => $depot]);
        $row = $st->fetch(PDO::FETCH_ASSOC);
        return isset($row['reserved']) ? (float)$row['reserved'] : 0.0;
    }

    public static function reserve(int $article, int $depot, float $quantite, ?string $reference, int $userId, ?string $expiresAt = null, ?int $clientId = null): void
    {
        if ($quantite <= 0) { throw new \InvalidArgumentException('Quantité de réservation invalide'); }
        $db = Flight::db();
        // Check available
        $stock = StockCourantModel::getByArticleDepot($article, $depot) ?: ['quantite' => 0];
        $reserved = self::getReservedQty($article, $depot);
        $available = ((float)($stock['quantite'] ?? 0)) - $reserved;
        if ($available < $quantite) { throw new \RuntimeException('Quantité disponible insuffisante pour réservation'); }
        // Insert reservation (+quantite)
        $st = $db->prepare("INSERT INTO stock_reservation(id_article, id_depot, id_client, quantite, date_expiration, reference, created_by)
            VALUES (:a, :d, :c, :q, :e, :r, :u)");
        $st->execute([
            ':a' => $article,
            ':d' => $depot,
            ':c' => $clientId,
            ':q' => $quantite,
            ':e' => $expiresAt,
            ':r' => $reference,
            ':u' => $userId
        ]);
    }

    public static function cancel(int $article, int $depot, float $quantite, ?string $reference, int $userId, ?int $clientId = null): void
    {
        if ($quantite <= 0) { throw new \InvalidArgumentException('Quantité d\'annulation invalide'); }
        $db = Flight::db();
        // Insert negative reservation to cancel
        $st = $db->prepare("INSERT INTO stock_reservation(id_article, id_depot, id_client, quantite, date_expiration, reference, created_by)
            VALUES (:a, :d, :c, :q, NULL, :r, :u)");
        $st->execute([
            ':a' => $article,
            ':d' => $depot,
            ':c' => $clientId,
            ':q' => -$quantite,
            ':r' => $reference,
            ':u' => $userId
        ]);
    }

    public static function consumeForReference(int $article, int $depot, float $quantite, ?string $reference, int $userId): void
    {
        if ($quantite <= 0) { return; }
        $db = Flight::db();
        // If reference is provided, ensure not over-consuming reserved against that reference
        if ($reference) {
            $st = $db->prepare("SELECT COALESCE(SUM(quantite),0) AS reserved_ref
                FROM stock_reservation
                WHERE id_article=:a AND id_depot=:d AND reference=:r
                  AND (date_expiration IS NULL OR date_expiration >= NOW() OR quantite < 0)");
            $st->execute([':a' => $article, ':d' => $depot, ':r' => $reference]);
            $rr = (float)($st->fetch(PDO::FETCH_ASSOC)['reserved_ref'] ?? 0);
            $consume = min($quantite, $rr);
        } else {
            // Fallback: consume up to global reserved amount
            $reserved = self::getReservedQty($article, $depot);
            $consume = min($quantite, $reserved);
        }
        if ($consume <= 0) { return; }
        $ins = $db->prepare("INSERT INTO stock_reservation(id_article, id_depot, id_client, quantite, date_expiration, reference, created_by)
            VALUES (:a, :d, NULL, :q, NULL, :r, :u)");
        $ins->execute([':a' => $article, ':d' => $depot, ':q' => -$consume, ':r' => $reference, ':u' => $userId]);
    }
}
