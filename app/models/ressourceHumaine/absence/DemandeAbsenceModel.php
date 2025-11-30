<?php

namespace app\models\ressourceHumaine\absence;

use Flight;
use PDO;

class DemandeAbsenceModel
{
    public function getTypesAbsence(): array
    {
        try {
            $db = Flight::db();
            $stmt = $db->query("SELECT id_type_absence, nom, description FROM type_absence WHERE isAutorise = 1");
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Debug: vérifier ce qui est récupéré
            error_log("Types d'absence récupérés: " . print_r($result, true));

            return $result;
        } catch (\PDOException $e) {
            error_log("Erreur getTypesAbsence: " . $e->getMessage());
            return [];
        }
    }

    public function creerDemandeAbsence($data): array
    {
        try {
            // Convertir Collection en array si nécessaire
            if (is_object($data) && method_exists($data, 'getData')) {
                $data = $data->getData();
            }

            $db = Flight::db();
            $db->beginTransaction();

            // Valider les données
            if (empty($data['id_type_absence']) || empty($data['date_debut']) || empty($data['date_fin']) || empty($data['motif'])) {
                return ['success' => false, 'message' => 'Tous les champs obligatoires doivent être remplis'];
            }

            // Vérifier que la date de fin est après la date de début
            $dateDebut = new \DateTime($data['date_debut']);
            $dateFin = new \DateTime($data['date_fin']);

            if ($dateFin < $dateDebut) {
                return ['success' => false, 'message' => 'La date de fin doit être après la date de début'];
            }

            // Récupérer l'ID de l'employé connecté
            $id_employe = $this->getEmployeConnecte();
            if (!$id_employe) {
                return ['success' => false, 'message' => 'Employé non identifié'];
            }

            // 1. Créer l'absence
            $stmt_absence = $db->prepare(
                "INSERT INTO absence (id_type_absence, id_employe, date_debut, date_fin) 
             VALUES (:id_type_absence, :id_employe, :date_debut, :date_fin)"
            );

            $stmt_absence->execute([
                'id_type_absence' => $data['id_type_absence'],
                'id_employe' => $id_employe,
                'date_debut' => $data['date_debut'],
                'date_fin' => $data['date_fin']
            ]);

            $id_absence = $db->lastInsertId();

            // 2. Créer la documentation SANS la lier immédiatement (pas de validation)
            $stmt_doc = $db->prepare(
                "INSERT INTO documentation_absence (type_documentation, id_employe, motif, date_debut, date_fin, date_documentation) 
             VALUES ('demande', :id_employe, :motif, :date_debut, :date_fin, CURDATE())"
            );

            $stmt_doc->execute([
                'id_employe' => $id_employe,
                'motif' => $data['motif'],
                'date_debut' => $data['date_debut'],
                'date_fin' => $data['date_fin']
            ]);

            // NE PAS créer d'entrée dans validation_documentation_absence
            // La demande reste en attente de validation

            $db->commit();
            return ['success' => true, 'message' => 'Demande d\'absence créée avec succès. En attente de validation.'];

        } catch (\PDOException $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            error_log("Erreur dans creerDemandeAbsence: " . $e->getMessage());
            return ['success' => false, 'message' => 'Erreur lors de la création de la demande: ' . $e->getMessage()];
        }
    }

    private function getEmployeConnecte()
    {
        // À adapter selon votre système d'authentification
        // Exemple : récupérer depuis la session
        return $_SESSION['user']['id_employe'] ?? 1; // Exemple avec ID 1 en dur
    }
}