<?php

namespace app\models\ressourceHumaine\pointage;

use Flight;
use PDO;
use DateTime;
use DateInterval;

class PointageModel
{
    /**
     * Vérifie si un employé a déjà fait un check-in aujourd'hui.
     * @param int $id_employe
     * @return bool
     */
    public function hasCheckedInToday($id_employe)
    {
        $db = Flight::db();
        $stmt = $db->prepare("SELECT COUNT(*) FROM checkin WHERE id_employe = ? AND DATE(datetime_checkin) = CURDATE()");
        $stmt->execute([$id_employe]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Vérifie si un employé a déjà fait un check-out aujourd'hui.
     * @param int $id_employe
     * @return bool
     */
    public function hasCheckedOutToday($id_employe)
    {
        $db = Flight::db();
        $stmt = $db->prepare("SELECT COUNT(*) FROM checkout WHERE id_employe = ? AND DATE(datetime_checkout) = CURDATE()");
        $stmt->execute([$id_employe]);
        return $stmt->fetchColumn() > 0;
    }

    /**
     * Enregistre un check-in pour un employé.
     * @param int $id_employe
     * @return int|false L'ID du check-in inséré ou false en cas d'échec.
     */
    public function saveCheckin($id_employe)
    {
        $db = Flight::db();
        $stmt = $db->prepare("INSERT INTO checkin (id_employe, datetime_checkin) VALUES (?, NOW())");
        if ($stmt->execute([$id_employe])) {
            return $db->lastInsertId();
        }
        return false;
    }

    /**
     * Enregistre un check-out pour un employé.
     * @param int $id_employe
     * @return int|false L'ID du check-out inséré ou false en cas d'échec.
     */
    public function saveCheckout($id_employe)
    {
        $db = Flight::db();
        $stmt = $db->prepare("INSERT INTO checkout (id_employe, datetime_checkout) VALUES (?, NOW())");
        if ($stmt->execute([$id_employe])) {
            return $db->lastInsertId();
        }
        return false;
    }
    
    /**
     * Récupère le check-in d'un employé pour une date donnée.
     * @param int $id_employe
     * @param string $date
     * @return mixed
     */
    public function getCheckinForDate($id_employe, $date)
    {
        $db = Flight::db();
        $stmt = $db->prepare("SELECT * FROM checkin WHERE id_employe = ? AND DATE(datetime_checkin) = ? ORDER BY datetime_checkin ASC LIMIT 1");
        $stmt->execute([$id_employe, $date]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère le check-out d'un employé pour une date donnée.
     * @param int $id_employe
     * @param string $date
     * @return mixed
     */
    public function getCheckoutForDate($id_employe, $date)
    {
        $db = Flight::db();
        $stmt = $db->prepare("SELECT * FROM checkout WHERE id_employe = ? AND DATE(datetime_checkout) = ? ORDER BY datetime_checkout DESC LIMIT 1");
        $stmt->execute([$id_employe, $date]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Calcule le retard en minutes.
     * @param DateTime $checkinTime
     * @return int
     */
    private function calculateRetard(DateTime $checkinTime)
    {
        $db = Flight::db();
        $dayOfWeek = $checkinTime->format('N'); // 1 (pour Lundi) à 7 (pour Dimanche)

        $stmt = $db->prepare("SELECT heure, tolerance FROM statut_pointage WHERE jour = ?");
        $stmt->execute([$dayOfWeek]);
        $statut = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$statut) {
            return 0; // Pas de règle de pointage pour ce jour
        }

        $expectedArrivalTime = new DateTime($checkinTime->format('Y-m-d') . ' ' . $statut['heure']);
        $allowedArrivalTime = clone $expectedArrivalTime;
        $allowedArrivalTime->add(new DateInterval('PT' . $statut['tolerance'] . 'M'));

        if ($checkinTime > $allowedArrivalTime) {
            $interval = $expectedArrivalTime->diff($checkinTime);
            return ($interval->h * 60) + $interval->i;
        }

        return 0;
    }

    /**
     * Crée ou met à jour le pointage après un check-in ou un check-out.
     * @param int $id_employe
     * @param string $date
     */
    public function createOrUpdatePointage($id_employe, $date)
    {
        $checkin = $this->getCheckinForDate($id_employe, $date);
        $checkout = $this->getCheckoutForDate($id_employe, $date);

        $id_checkin = $checkin['id'] ?? null;
        $id_checkout = $checkout['id'] ?? null;
        
        $duree_work = null;
        if ($checkin && $checkout) {
            $datetime1 = new DateTime($checkin['datetime_checkin']);
            $datetime2 = new DateTime($checkout['datetime_checkout']);
            $interval = $datetime1->diff($datetime2);
            $duree_work = $interval->format('%H:%I:%S');
        }

        $retard_min = 0;
        if ($checkin) {
            $retard_min = $this->calculateRetard(new DateTime($checkin['datetime_checkin']));
        }
        
        $db = Flight::db();
        // Vérifier si un pointage existe déjà pour cet employé à cette date
        $stmt = $db->prepare("SELECT id_pointage FROM pointage WHERE id_employe = ? AND date_pointage = ?");
        $stmt->execute([$id_employe, $date]);
        $id_pointage = $stmt->fetchColumn();

        if ($id_pointage) {
            // Mettre à jour le pointage existant
            $stmt = $db->prepare("UPDATE pointage SET id_checkin = ?, id_checkout = ?, duree_work = ?, retard_min = ? WHERE id_pointage = ?");
            $stmt->execute([$id_checkin, $id_checkout, $duree_work, $retard_min, $id_pointage]);
        } else {
            // Insérer un nouveau pointage
            $stmt = $db->prepare("INSERT INTO pointage (id_employe, date_pointage, id_checkin, id_checkout, duree_work, retard_min) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$id_employe, $date, $id_checkin, $id_checkout, $duree_work, $retard_min]);
        }
    }
}
