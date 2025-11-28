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
    public function createOrUpdatePointage($id_employe, $date, $forceCheckinId = null, $forceCheckoutId = null)
    {
        $db = Flight::db();
        $checkin = null;
        $checkout = null;
        $effectiveDate = null;

        if ($forceCheckinId) {
            // A check-in just happened. The check-in itself is the source of truth.
            $stmt = $db->prepare("SELECT * FROM checkin WHERE id = ? LIMIT 1");
            $stmt->execute([$forceCheckinId]);
            $checkin = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$checkin) return; // Should not happen

            $effectiveDate = date('Y-m-d', strtotime($checkin['datetime_checkin']));
            // No checkout exists yet for this new pointage.
            $checkout = null;

        } elseif ($forceCheckoutId) {
            // A check-out just happened. Find its details first.
            $stmt = $db->prepare("SELECT * FROM checkout WHERE id = ? LIMIT 1");
            $stmt->execute([$forceCheckoutId]);
            $checkout = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$checkout) return; // Should not happen

            // Now, find the corresponding check-in. It must be the latest one that occurred before this checkout.
            // This correctly handles overnight work.
            $stmt = $db->prepare(
                "SELECT * FROM checkin
                 WHERE id_employe = ? AND datetime_checkin < ?
                 ORDER BY datetime_checkin DESC LIMIT 1"
            );
            $stmt->execute([$id_employe, $checkout['datetime_checkout']]);
            $checkin = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$checkin) return; // Cannot find a check-in to associate with this check-out.

            // The effective date is the date of the check-in.
            $effectiveDate = date('Y-m-d', strtotime($checkin['datetime_checkin']));
        } else {
            // This function should not be called without a check-in or check-out ID.
            return;
        }

        // Using the determined effective date, find if a pointage record already exists.
        $stmt = $db->prepare("SELECT id_pointage FROM pointage WHERE id_employe = ? AND date_pointage = ?");
        $stmt->execute([$id_employe, $effectiveDate]);
        $id_pointage = $stmt->fetchColumn();

        // Calculate metrics
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
        
        $statut = 'A l\'heure';
        if ($retard_min > 0) {
            $statut = 'Retard';
        }

        // Insert or Update the pointage table
        if ($id_pointage) {
            $stmt = $db->prepare("UPDATE pointage SET id_checkin = ?, id_checkout = ?, duree_work = ?, retard_min = ?, statut = ? WHERE id_pointage = ?");
            $stmt->execute([$id_checkin, $id_checkout, $duree_work, $retard_min, $statut, $id_pointage]);
        } else {
            $stmt = $db->prepare("INSERT INTO pointage (id_employe, date_pointage, id_checkin, id_checkout, duree_work, retard_min, statut) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$id_employe, $effectiveDate, $id_checkin, $id_checkout, $duree_work, $retard_min, $statut]);
        }
    }
    /**
     * Remplit les enregistrements de pointage manquants pour un employé à partir d'une date donnée.
     * @param int $id_employe
     */
    public function fillMissingPointages($id_employe)
    {
        $db = Flight::db();

        // 1. Trouver la date d'activation de l'employé
        $stmt_start_date = $db->prepare(
            "SELECT MIN(date_modification) 
             FROM employe_statut 
             WHERE id_employe = :id_employe AND activite = 1"
        );
        $stmt_start_date->execute(['id_employe' => $id_employe]);
        $startDateStr = $stmt_start_date->fetchColumn();

        if (!$startDateStr) {
            return; // Pas de date d'activation trouvée
        }
        
        $startDate = new DateTime($startDateStr);
        $endDate = new DateTime();
        $endDate->modify('-1 day'); // Jusqu'à hier

        if ($startDate > $endDate) {
            return; // Pas de jours à vérifier
        }

        // Itérer sur la plage de dates
        $currentDate = clone $startDate;
        while ($currentDate <= $endDate) {
            $dateStr = $currentDate->format('Y-m-d');
            $dayOfWeek = $currentDate->format('N'); // 1 pour Lundi, 7 pour Dimanche

            // Vérifier si c'est un jour ouvrable selon la table statut_pointage
            $stmt = $db->prepare("SELECT COUNT(*) FROM statut_pointage WHERE jour = ?");
            $stmt->execute([$dayOfWeek]);
            $isWorkingDay = $stmt->fetchColumn() > 0;

            if ($isWorkingDay) {
                // Vérifier si un pointage existe déjà pour ce jour
                $stmt = $db->prepare("SELECT COUNT(*) FROM pointage WHERE id_employe = ? AND date_pointage = ?");
                $stmt->execute([$id_employe, $dateStr]);
                $pointageExists = $stmt->fetchColumn() > 0;

                if (!$pointageExists) {
                    // Insérer un enregistrement d'absence
                    $stmt_insert = $db->prepare(
                        "INSERT INTO pointage (id_employe, date_pointage, duree_work, retard_min, statut) VALUES (?, ?, '00:00:00', 0, 'Absent')"
                    );
                    $stmt_insert->execute([$id_employe, $dateStr]);
                }
            }

            $currentDate->modify('+1 day');
        }
    }
    /**
     * Récupère l'historique de pointage pour un employé.
     * @param int $id_employe
     * @return array
     */
    public function getHistoriqueByEmployeId($id_employe)
    {
        $db = Flight::db();
        $stmt = $db->prepare(
            "SELECT
                p.id_pointage,
                p.date_pointage,
                p.duree_work,
                p.retard_min,
                p.statut,
                ci.datetime_checkin,
                co.datetime_checkout
            FROM pointage p
            LEFT JOIN checkin ci ON p.id_checkin = ci.id
            LEFT JOIN checkout co ON p.id_checkout = co.id
            WHERE p.id_employe = ?
            ORDER BY p.date_pointage DESC"
        );
        $stmt->execute([$id_employe]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère l'historique de pointage pour tous les employés.
     * @return array
     */
    public function getAllHistorique()
    {
        $db = Flight::db();
        $stmt = $db->prepare(
            "SELECT
                p.id_pointage,
                p.id_employe,
                p.date_pointage,
                p.duree_work,
                p.retard_min,
                p.statut,
                ci.datetime_checkin,
                co.datetime_checkout,
                e.nom,
                e.prenom
            FROM pointage p
            JOIN employe e ON p.id_employe = e.id_employe
            LEFT JOIN checkin ci ON p.id_checkin = ci.id
            LEFT JOIN checkout co ON p.id_checkout = co.id
            ORDER BY p.date_pointage DESC, e.nom ASC"
        );
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllEmployeIds()
    {
        $db = Flight::db();
        $stmt = $db->prepare("SELECT id_employe FROM employe");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    public function updatePointageRecord($id_pointage, $datetime_checkin, $datetime_checkout)
    {
        $db = Flight::db();
        $db->beginTransaction();
        try {
            // Récupérer le pointage
            $stmt = $db->prepare("SELECT id_checkin, id_checkout, id_employe, date_pointage FROM pointage WHERE id_pointage = ?");
            $stmt->execute([$id_pointage]);
            $pointage = $stmt->fetch(PDO::FETCH_ASSOC);
            if (!$pointage) {
                $db->rollBack();
                return false;
            }

            $id_checkin = $pointage['id_checkin'];
            $id_checkout = $pointage['id_checkout'];
            $id_employe = $pointage['id_employe'];
            $date_pointage = $pointage['date_pointage'];

            // Update or insert checkin
            if ($datetime_checkin) {
                if ($id_checkin) {
                    $stmt = $db->prepare("UPDATE checkin SET datetime_checkin = ? WHERE id = ?");
                    $stmt->execute([$datetime_checkin, $id_checkin]);
                } else {
                    $stmt = $db->prepare("INSERT INTO checkin (id_employe, datetime_checkin) VALUES (?, ?)");
                    $stmt->execute([$id_employe, $datetime_checkin]);
                    $id_checkin = $db->lastInsertId();
                }
            }

            // Update or insert checkout
            if ($datetime_checkout) {
                if ($id_checkout) {
                    $stmt = $db->prepare("UPDATE checkout SET datetime_checkout = ? WHERE id = ?");
                    $stmt->execute([$datetime_checkout, $id_checkout]);
                } else {
                    $stmt = $db->prepare("INSERT INTO checkout (id_employe, datetime_checkout) VALUES (?, ?)");
                    $stmt->execute([$id_employe, $datetime_checkout]);
                    $id_checkout = $db->lastInsertId();
                }
            }

            // Recalculer duree_work et retard_min
            $duree_work = null;
            if ($datetime_checkin && $datetime_checkout) {
                $dt1 = new DateTime($datetime_checkin);
                $dt2 = new DateTime($datetime_checkout);
                $interval = $dt1->diff($dt2);
                $duree_work = $interval->format('%H:%I:%S');
            }

            $retard_min = 0;
            if ($datetime_checkin) {
                $retard_min = $this->calculateRetard(new DateTime($datetime_checkin));
            }

            $statut = 'A l\'heure';
            if (!$datetime_checkin) {
                $statut = 'Absent';
            } elseif ($retard_min > 0) {
                $statut = 'Retard';
            }

            // Mettre à jour la table pointage
            $stmt = $db->prepare("UPDATE pointage SET id_checkin = ?, id_checkout = ?, duree_work = ?, retard_min = ?, statut = ? WHERE id_pointage = ?");
            $stmt->execute([$id_checkin, $id_checkout, $duree_work, $retard_min, $statut, $id_pointage]);

            $db->commit();

            // Récupérer les valeurs mises à jour pour renvoyer au client
            $stmt = $db->prepare(
                "SELECT p.duree_work, p.retard_min, p.statut, ci.datetime_checkin, co.datetime_checkout
                 FROM pointage p
                 LEFT JOIN checkin ci ON p.id_checkin = ci.id
                 LEFT JOIN checkout co ON p.id_checkout = co.id
                 WHERE p.id_pointage = ?"
            );
            $stmt->execute([$id_pointage]);
            $updated = $stmt->fetch(PDO::FETCH_ASSOC);

            return $updated ?: true;
        } catch (\Exception $e) {
            $db->rollBack();
            return false;
        }
    }

    /**
     * Updates the pointage status for a given employee over a date range.
     * Creates pointage records if they do not exist.
     * @param int $id_employe
     * @param string $startDate
     * @param string $endDate
     * @param string $status
     * @return bool
     */
    public function updatePointageStatusForDateRange(int $id_employe, string $startDate, string $endDate, string $status): bool
    {
        $db = Flight::db();
        
        $currentDate = new DateTime($startDate);
        $lastDate = new DateTime($endDate);

        while ($currentDate <= $lastDate) {
            $dateStr = $currentDate->format('Y-m-d');

            // Check if a pointage record exists
            $stmt_check = $db->prepare("SELECT id_pointage, id_checkin, id_checkout FROM pointage WHERE id_employe = :id_employe AND date_pointage = :date_pointage");
            $stmt_check->execute([
                'id_employe' => $id_employe,
                'date_pointage' => $dateStr
            ]);
            $pointage = $stmt_check->fetch(PDO::FETCH_ASSOC);

            if ($pointage) {
                // Update existing record
                $stmt_update = $db->prepare("UPDATE pointage SET statut = :statut, duree_work = '00:00:00', retard_min = 0, id_checkin = NULL, id_checkout = NULL WHERE id_pointage = :id_pointage");
                $stmt_update->execute([
                    'statut' => $status,
                    'id_pointage' => $pointage['id_pointage']
                ]);
                
                if ($pointage['id_checkin']) {
                    $stmt_delete_checkin = $db->prepare("DELETE FROM checkin WHERE id = :id_checkin");
                    $stmt_delete_checkin->execute(['id_checkin' => $pointage['id_checkin']]);
                }
                if ($pointage['id_checkout']) {
                    $stmt_delete_checkout = $db->prepare("DELETE FROM checkout WHERE id = :id_checkout");
                    $stmt_delete_checkout->execute(['id_checkout' => $pointage['id_checkout']]);
                }

            } else {
                // Insert new record
                $stmt_insert = $db->prepare(
                    "INSERT INTO pointage (id_employe, date_pointage, duree_work, retard_min, statut) VALUES (:id_employe, :date_pointage, '00:00:00', 0, :statut)"
                );
                $stmt_insert->execute([
                    'id_employe' => $id_employe,
                    'date_pointage' => $dateStr,
                    'statut' => $status
                ]);
            }

            $currentDate->modify('+1 day');
        }
        
        return true;
    }
}