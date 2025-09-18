<?php
namespace app\controllers\ressourceHumaine\back;

use app\models\ressourceHumaine\back\creaAnnonce as creaAnnonceModel;
use Flight;
use PDO;

class creaAnnonce {
    private $model;

    public function __construct() {
        $db = Flight::db();
        $this->model = new creaAnnonceModel($db);
    }

    public function getAllDiplome() {
        $data = $this->model->getAllDiplome();
        Flight::json($data);
    }

    public function getAllCompetence() {
        $data = $this->model->getAllCompetence();
        Flight::json($data);
    }

    public static function create() {
        $db = Flight::db();
        $data = json_decode(file_get_contents("php://input"), true);

        try {
            $db->beginTransaction();

            $stmt = $db->prepare("SELECT id_profil FROM profil WHERE nom = ?");
            $stmt->execute([$data['profil']]);
            $profil = $stmt->fetchColumn();

            if (!$profil) {
                $stmt = $db->prepare("INSERT INTO profil (nom) VALUES (?)");
                $stmt->execute([$data['profil']]);
                $profil = $db->lastInsertId();
            }

            $stmt = $db->prepare("SELECT id_ville FROM ville WHERE nom = ?");
            $stmt->execute([$data['lieu']]);
            $ville = $stmt->fetchColumn();

            if (!$ville) {
                $stmt = $db->prepare("INSERT INTO ville (nom) VALUES (?)");
                $stmt->execute([$data['lieu']]);
                $ville = $db->lastInsertId();
            }

            $stmt = $db->prepare("
                INSERT INTO annonce (id_profil, titre, date_debut, date_fin, age_min, age_max, experience, objectif, qualite)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $profil,
                $data['titre'],
                $data['date_debut'],
                $data['date_fin'],
                $data['age_min'],
                $data['age_max'],
                $data['experience'],
                $data['objectif'],
                $data['qualites']
            ]);

            $id_annonce = $db->lastInsertId();

            $stmt = $db->prepare("INSERT INTO detail_annonce (id_annonce, type, id_item) VALUES (?, 'ville', ?)");
            $stmt->execute([$id_annonce, $ville]);

            $stmtDiplome = $db->prepare("SELECT id_diplome FROM diplome WHERE nom = ?");
            $insertDetail = $db->prepare("INSERT INTO detail_annonce (id_annonce, type, id_item) VALUES (?, 'diplome', ?)");
            foreach ($data['diplomes'] as $d) {
                $stmtDiplome->execute([$d]);
                $id = $stmtDiplome->fetchColumn();
                if ($id) {
                    $insertDetail->execute([$id_annonce, $id]);
                }
            }

            $stmtComp = $db->prepare("SELECT id_competence FROM competence WHERE nom = ?");
            $insertDetail = $db->prepare("INSERT INTO detail_annonce (id_annonce, type, id_item) VALUES (?, 'competence', ?)");
            foreach ($data['competences'] as $c) {
                $stmtComp->execute([$c]);
                $id = $stmtComp->fetchColumn();
                if ($id) {
                    $insertDetail->execute([$id_annonce, $id]);
                }
            }

            $db->commit();

            Flight::json(["success" => true, "message" => "Annonce créée avec succès", "id_annonce" => $id_annonce]);

        } catch (\Exception $e) {
            $db->rollBack();
            Flight::json(["success" => false, "message" => $e->getMessage()]);
        }
    }
}
