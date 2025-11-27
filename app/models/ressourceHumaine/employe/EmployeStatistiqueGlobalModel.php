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
        $annee = $annee ?? date('Y');
        $debutAnnee = $annee . '-01-01';
        $finAnnee = $annee . '-12-31';

        // Nombre de départs (employés devenus inactifs dans l'année)
        $queryDeparts = "
            SELECT COUNT(DISTINCT es.id_employe) as departs
            FROM employe_statut es
            WHERE es.activite = 0
            AND es.date_modification BETWEEN '$debutAnnee' AND '$finAnnee'
        ";
        $departs = $this->getDb()->query($queryDeparts)->fetch()['departs'];

        // Nombre moyen d'employés actifs (approximation : employés actifs au milieu de l'année)
        $queryMoyenneEmployes = "
            SELECT AVG(actifs) as moyenne
            FROM (
                SELECT COUNT(DISTINCT es.id_employe) as actifs
                FROM employe_statut es
                WHERE es.activite = 1
                AND es.date_modification <= '$annee-06-30'
                UNION ALL
                SELECT COUNT(DISTINCT es.id_employe) as actifs
                FROM employe_statut es
                WHERE es.activite = 1
                AND es.date_modification <= '$annee-12-31'
            ) as sub
        ";
        $moyenneEmployes = $this->getDb()->query($queryMoyenneEmployes)->fetch()['moyenne'] ?? 1;

        $taux = $moyenneEmployes > 0 ? ($departs / $moyenneEmployes) * 100 : 0;
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
