<?php
namespace app\models\ressourceHumaine\back;

use PDO;
use PDOException;

class creaAnnonce {
    private $db;

    public function __construct(PDO $db) {
        $this->db = $db;
    }

    public function getAllDiplome(): array {
        try {
            $sql = "SELECT nom FROM diplome";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getAllCompetence(): array {
        try {
            $sql = "SELECT nom FROM competence";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getAllVille(): array {
        try {
            $sql = "SELECT nom FROM ville";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }

    public function getAllProfil(): array {
        try {
            $sql = "SELECT nom FROM Profil";
            $stmt = $this->db->query($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}
