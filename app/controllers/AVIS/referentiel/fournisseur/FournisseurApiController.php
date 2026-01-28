<?php

namespace app\controllers\AVIS\referentiel\fournisseur;

use app\models\AVIS\referentiel\fournisseur\FournisseurModel;
use app\models\AVIS\referentiel\fournisseur\FournisseurArticleModel;
use Exception;
use Flight;

class FournisseurApiController {

    // ============ FOURNISSEUR ============

    public static function getAllFournisseurs() {
        try {
            $fournisseurs = FournisseurModel::getAll();
            Flight::json(['success' => true, 'data' => $fournisseurs]);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function getFournisseurById($id_fournisseur) {
        try {
            $fournisseur = FournisseurModel::getById($id_fournisseur);
            if ($fournisseur) {
                Flight::json(['success' => true, 'data' => $fournisseur]);
            } else {
                Flight::json(['success' => false, 'message' => 'Fournisseur introuvable'], 404);
            }
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function createFournisseur() {
        try {
            $data = Flight::request()->data;
            $model = new FournisseurModel();
            $id = $model->insert(
                $data->nom ?? '',
                $data->adresse ?? null,
                $data->telephone ?? null,
                $data->email ?? null
            );
            Flight::json(['success' => true, 'message' => 'Fournisseur créé', 'id' => $id]);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function updateFournisseur($id_fournisseur) {
        try {
            $data = Flight::request()->data;
            FournisseurModel::update(
                $id_fournisseur,
                $data->nom ?? '',
                $data->adresse ?? null,
                $data->telephone ?? null,
                $data->email ?? null
            );
            Flight::json(['success' => true, 'message' => 'Fournisseur modifié']);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function deleteFournisseur($id_fournisseur) {
        try {
            FournisseurModel::delete($id_fournisseur);
            Flight::json(['success' => true, 'message' => 'Fournisseur supprimé']);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function searchFournisseur() {
        try {
            $search_term = Flight::request()->query->search ?? '';
            $results = FournisseurModel::search($search_term);
            Flight::json(['success' => true, 'data' => $results]);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ============ FOURNISSEUR ARTICLE ============

    public static function getAllFournisseurArticles() {
        try {
            $fournisseurArticles = FournisseurArticleModel::getAll();
            Flight::json(['success' => true, 'data' => $fournisseurArticles]);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function getFournisseurArticleById($id_fournisseur_article) {
        try {
            $fournisseurArticle = FournisseurArticleModel::getById($id_fournisseur_article);
            if ($fournisseurArticle) {
                Flight::json(['success' => true, 'data' => $fournisseurArticle]);
            } else {
                Flight::json(['success' => false, 'message' => 'Association fournisseur-article introuvable'], 404);
            }
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function getFournisseurArticlesByFournisseur($id_fournisseur) {
        try {
            $articles = FournisseurArticleModel::getByFournisseur($id_fournisseur);
            Flight::json(['success' => true, 'data' => $articles]);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function getFournisseurArticlesByArticle($id_article) {
        try {
            $fournisseurs = FournisseurArticleModel::getByArticle($id_article);
            Flight::json(['success' => true, 'data' => $fournisseurs]);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function createFournisseurArticle() {
        try {
            $data = Flight::request()->data;
            $model = new FournisseurArticleModel();
            $id = $model->insert(
                $data->id_fournisseur ?? '',
                $data->id_article ?? '',
                $data->prix_achat ?? null,
                $data->delai_livraison ?? null
            );
            Flight::json(['success' => true, 'message' => 'Association fournisseur-article créée', 'id' => $id]);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function updateFournisseurArticle($id_fournisseur_article) {
        try {
            $data = Flight::request()->data;
            FournisseurArticleModel::update(
                $id_fournisseur_article,
                $data->prix_achat ?? null,
                $data->delai_livraison ?? null
            );
            Flight::json(['success' => true, 'message' => 'Association fournisseur-article modifiée']);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function deleteFournisseurArticle($id_fournisseur_article) {
        try {
            FournisseurArticleModel::delete($id_fournisseur_article);
            Flight::json(['success' => true, 'message' => 'Association fournisseur-article supprimée']);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
