<?php

namespace app\models\ressourceHumaine\employe;

use Flight;

class EmployeStatistiqueGlobalModel
{
    public function __construct()
    {
    }

    private function getDb()
    {
        return Flight::db();
    }
    public function getTauxTurnover($month = null, $year = null)
    {
        if (!$year) $year = date('Y');
        if ($month) {
            // Calcul mensuel
            $startDate = "$year-$month-01";
            $endDate = date('Y-m-t', strtotime($startDate));

            // Nombre total d'employés embauchés avant ou à la fin de la période
            $queryTotal = "SELECT COUNT(*) as total FROM employe WHERE date_embauche <= '$endDate'";
            $total = $this->getDb()->query($queryTotal)->fetch()['total'];

            // Nombre d'employés devenus inactifs dans la période
            $queryInactifs = "
                SELECT COUNT(*) as inactifs
                FROM employe e
                JOIN employe_statut es ON e.id_employe = es.id_employe
                WHERE es.activite = 0 AND es.date_modification >= '$startDate' AND es.date_modification <= '$endDate'
            ";
            $inactifs = $this->getDb()->query($queryInactifs)->fetch()['inactifs'];

            $taux = $total > 0 ? ($inactifs / $total) * 100 : 0;
            return round($taux, 2);
        } else {
            // Calcul annuel : moyenne des taux mensuels
            $totalTaux = 0;
            for ($m = 1; $m <= 12; $m++) {
                $tauxMensuel = $this->getTauxTurnover($m, $year);
                $totalTaux += $tauxMensuel;
            }
            return round($totalTaux / 12, 2);
        }
    }

    public function getTauxAbsentéisme($month = null, $year = null)
    {
        if (!$year) $year = date('Y');
        if ($month) {
            // Calcul mensuel
            $startDate = "$year-$month-01";
            $endDate = date('Y-m-t', strtotime($startDate));

            // Total jours d'absence dans la période
            $queryAbsences = "
                SELECT SUM(DATEDIFF(LEAST(a.date_fin, '$endDate'), GREATEST(a.date_debut, '$startDate')) + 1) as jours_absence
                FROM absence a
                WHERE a.date_debut <= '$endDate' AND a.date_fin >= '$startDate'
            ";
            $joursAbsence = $this->getDb()->query($queryAbsences)->fetch()['jours_absence'] ?? 0;

            // Nombre d'employés actifs à la fin de la période
            $queryEmployes = "
                SELECT COUNT(DISTINCT e.id_employe) as employes
                FROM employe e
                JOIN employe_statut es ON e.id_employe = es.id_employe
                WHERE es.date_modification = (
                    SELECT MAX(es2.date_modification)
                    FROM employe_statut es2
                    WHERE es2.id_employe = e.id_employe AND es2.date_modification <= '$endDate'
                )
                AND es.activite = 1
            ";
            $employes = $this->getDb()->query($queryEmployes)->fetch()['employes'];

            // Jours travaillés possibles dans la période
            $joursPeriode = (strtotime($endDate) - strtotime($startDate)) / (60*60*24) + 1;
            $joursPossibles = $employes * $joursPeriode;
            $taux = $joursPossibles > 0 ? ($joursAbsence / $joursPossibles) * 100 : 0;
            return round($taux, 2);
        } else {
            // Calcul annuel : moyenne des taux mensuels
            $totalTaux = 0;
            for ($m = 1; $m <= 12; $m++) {
                $tauxMensuel = $this->getTauxAbsentéisme($m, $year);
                $totalTaux += $tauxMensuel;
            }
            return round($totalTaux / 12, 2);
        }
    }

    public function getAncienneteMoyenne($month = null, $year = null)
    {
        if (!$year) $year = date('Y');
        $endDate = null;
        if ($year) {
            if ($month) {
                $endDate = date('Y-m-t', strtotime("$year-$month-01"));
            } else {
                $endDate = "$year-12-31";
            }
        } else {
            $endDate = date('Y-m-d');
        }

        $query = "
            SELECT AVG(
                CASE
                    WHEN es.activite = 0 THEN DATEDIFF(es.date_modification, e.date_embauche)
                    ELSE DATEDIFF('$endDate', e.date_embauche)
                END
            ) / 365 as anciennete_annees
            FROM employe e
            LEFT JOIN (
                SELECT id_employe, activite, date_modification
                FROM employe_statut
                WHERE (id_employe, date_modification) IN (
                    SELECT id_employe, MAX(date_modification)
                    FROM employe_statut
                    GROUP BY id_employe
                )
            ) es ON e.id_employe = es.id_employe
            WHERE es.date_modification <= '$endDate'
        ";
        $result = $this->getDb()->query($query)->fetch();
        return round($result['anciennete_annees'] ?? 0, 2);
    }

    public function getStatistiquesGlobales($month = null, $year = null)
    {
        $annee = $year ?? date('Y');
        return [
            'taux_turnover' => $this->getTauxTurnover($month, $year),
            'taux_absenteisme' => $this->getTauxAbsentéisme($month, $year),
            'anciennete_moyenne' => $this->getAncienneteMoyenne($month, $year),
            'annee' => $annee
        ];
    }
}
