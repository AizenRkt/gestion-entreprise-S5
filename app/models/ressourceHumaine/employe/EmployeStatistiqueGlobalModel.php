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
    public function getTauxTurnover($annee = null)
    {
        // Nombre total d'employés
        $queryTotal = "SELECT COUNT(*) as total FROM employe";
        $total = $this->getDb()->query($queryTotal)->fetch()['total'];

        // Nombre d'employés inactifs (dernier statut inactif)
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
        $inactifs = $this->getDb()->query($queryInactifs)->fetch()['inactifs'];

        $taux = $total > 0 ? ($inactifs / $total) * 100 : 0;
        return round($taux, 2);
    }

    public function getTauxAbsentéisme($annee = null)
    {
        $annee = $annee ?? date('Y');
        $debutAnnee = $annee . '-01-01';
        $finAnnee = $annee . '-12-31';

        // Total jours d'absence
        $queryAbsences = "
            SELECT SUM(DATEDIFF(a.date_fin, a.date_debut) + 1) as jours_absence
            FROM absence a
            WHERE a.date_debut >= '$debutAnnee' AND a.date_fin <= '$finAnnee'
        ";
        $joursAbsence = $this->getDb()->query($queryAbsences)->fetch()['jours_absence'] ?? 0;

        // Nombre d'employés actifs
        $queryEmployes = "
            SELECT COUNT(DISTINCT e.id_employe) as employes
            FROM employe e
            JOIN employe_statut es ON e.id_employe = es.id_employe
            WHERE es.activite = 1
        ";
        $employes = $this->getDb()->query($queryEmployes)->fetch()['employes'];

        // Jours travaillés possibles (approximation : 365 jours par employé)
        $joursPossibles = $employes * 365;
        $taux = $joursPossibles > 0 ? ($joursAbsence / $joursPossibles) * 100 : 0;
        return round($taux, 2);
    }

    public function getAncienneteMoyenne()
    {
        $aujourdHui = date('Y-m-d');

        $query = "
            SELECT AVG(
                CASE
                    WHEN es.activite = 0 THEN DATEDIFF(es.date_modification, e.date_embauche)
                    ELSE DATEDIFF('$aujourdHui', e.date_embauche)
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
        ";
        $result = $this->getDb()->query($query)->fetch();
        return round($result['anciennete_annees'] ?? 0, 2);
    }

    public function getStatistiquesGlobales($annee = null)
    {
        return [
            'taux_turnover' => $this->getTauxTurnover($annee),
            'taux_absenteisme' => $this->getTauxAbsentéisme($annee),
            'anciennete_moyenne' => $this->getAncienneteMoyenne(),
            'annee' => $annee ?? date('Y')
        ];
    }
}
