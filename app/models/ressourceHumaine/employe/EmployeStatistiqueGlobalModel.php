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
        $endDate = null;
        if ($year) {
            if ($month) {
                $endDate = date('Y-m-t', strtotime("$year-$month-01"));
            } else {
                $endDate = "$year-12-31";
            }
        }

        // Nombre total d'employés (ceux embauchés avant ou à la date)
        $queryTotal = "SELECT COUNT(*) as total FROM employe";
        if ($endDate) {
            $queryTotal .= " WHERE date_embauche <= '$endDate'";
        }
        $total = $this->getDb()->query($queryTotal)->fetch()['total'];

        // Nombre d'employés inactifs (dernier statut inactif et date <= endDate)
        $queryInactifs = "
            SELECT COUNT(*) as inactifs
            FROM employe e
            JOIN employe_statut es ON e.id_employe = es.id_employe
            WHERE es.date_modification = (
                SELECT MAX(es2.date_modification)
                FROM employe_statut es2
                WHERE es2.id_employe = e.id_employe
            )
            AND es.activite = 0
        ";
        if ($endDate) {
            $queryInactifs .= " AND es.date_modification <= '$endDate'";
        }
        $inactifs = $this->getDb()->query($queryInactifs)->fetch()['inactifs'];

        $taux = $total > 0 ? ($inactifs / $total) * 100 : 0;
        return round($taux, 2);
    }

    public function getTauxAbsentéisme($month = null, $year = null)
    {
        $endDate = null;
        if ($year) {
            if ($month) {
                $endDate = date('Y-m-t', strtotime("$year-$month-01"));
            } else {
                $endDate = "$year-12-31";
            }
        } else {
            $endDate = date('Y-12-31');
        }

        $debutAnnee = date('Y', strtotime($endDate)) . '-01-01';

        // Total jours d'absence jusqu'à endDate
        $queryAbsences = "
            SELECT SUM(DATEDIFF(LEAST(a.date_fin, '$endDate'), a.date_debut) + 1) as jours_absence
            FROM absence a
            WHERE a.date_debut <= '$endDate' AND a.date_fin >= '$debutAnnee'
        ";
        $joursAbsence = $this->getDb()->query($queryAbsences)->fetch()['jours_absence'] ?? 0;

        // Nombre d'employés actifs à endDate
        $queryEmployes = "
            SELECT COUNT(DISTINCT e.id_employe) as employes
            FROM employe e
            JOIN employe_statut es ON e.id_employe = es.id_employe
            WHERE es.date_modification = (
                SELECT MAX(es2.date_modification)
                FROM employe_statut es2
                WHERE es2.id_employe = e.id_employe
            )
            AND es.activite = 1
            AND es.date_modification <= '$endDate'
        ";
        $employes = $this->getDb()->query($queryEmployes)->fetch()['employes'];

        // Jours travaillés possibles (du début de l'année à endDate)
        $joursAnnee = (strtotime($endDate) - strtotime($debutAnnee)) / (60*60*24) + 1;
        $joursPossibles = $employes * $joursAnnee;
        $taux = $joursPossibles > 0 ? ($joursAbsence / $joursPossibles) * 100 : 0;
        return round($taux, 2);
    }

    public function getAncienneteMoyenne($month = null, $year = null)
    {
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
