<?php

namespace app\models\AVIS\stock;

use Flight;

class StockClosureModel
{
    /**
     * Returns latest closed period key (YYYYMM) or null
     */
    public static function getLastClosedPeriodKey(): ?int
    {
        $db = Flight::db();
        $stmt = $db->query("SELECT MAX(annee*100 + mois) AS last_key FROM stock_cloture_periode WHERE statut='CLOTURE'");
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        if (!$row || $row['last_key'] === null) return null;
        return (int)$row['last_key'];
    }

    /**
     * Checks if the given date falls into any closed period.
     */
    public static function isMonthClosed(int $year, int $month): bool
    {
        $db = Flight::db();
        $st = $db->prepare("SELECT 1 FROM stock_cloture_periode WHERE annee = :y AND mois = :m AND statut='CLOTURE' LIMIT 1");
        $st->execute([':y' => $year, ':m' => $month]);
        return (bool)$st->fetchColumn();
    }

    /**
     * Returns whether a movement date is allowed (not in or before closed period).
     */
    public static function isDateAllowed(string $date): bool
    {
        $ts = strtotime($date);
        if ($ts === false) return false; // invalid date denied
        $year = (int)date('Y', $ts);
        $month = (int)date('n', $ts);
        $currentKey = $year * 100 + $month;

        $lastClosed = self::getLastClosedPeriodKey();
        if ($lastClosed !== null && $currentKey <= $lastClosed) {
            return false; // retrodated or in closed period
        }
        // explicit month closed check (redundant but safe)
        if (self::isMonthClosed($year, $month)) {
            return false;
        }
        return true;
    }
}
