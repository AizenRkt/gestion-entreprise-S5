<?php

namespace app\controllers\AVIS\referentiel\article;

use app\models\AVIS\referentiel\article\ArticleFamilleModel;
use app\models\AVIS\referentiel\article\ArticleModel;
use app\models\AVIS\referentiel\article\ArticleStatusModel;
use app\models\AVIS\referentiel\article\ArticleFamilleStatusModel;
use app\models\AVIS\referentiel\article\ArticlePrixHistoriqueModel;
use Exception;
use Flight;

class ArticleApiController {

    // ============ ARTICLE FAMILLE ============

    public static function getAllArticleFamilles() {
        try {
            $familles = ArticleFamilleModel::getAll();
            Flight::json(['success' => true, 'data' => $familles]);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function getArticleFamilleById($id_article_famille) {
        try {
            $famille = ArticleFamilleModel::getById($id_article_famille);
            if ($famille) {
                Flight::json(['success' => true, 'data' => $famille]);
            } else {
                Flight::json(['success' => false, 'message' => 'Famille d\'article introuvable'], 404);
            }
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function createArticleFamille() {
        try {
            $data = Flight::request()->data;
            $model = new ArticleFamilleModel();
            $id = $model->insert(
                $data->code ?? '',
                $data->nom ?? '',
                $data->description ?? null
            );
            Flight::json(['success' => true, 'message' => 'Famille d\'article créée', 'id' => $id]);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function updateArticleFamille($id_article_famille) {
        try {
            $data = Flight::request()->data;
            ArticleFamilleModel::update(
                $id_article_famille,
                $data->code ?? '',
                $data->nom ?? '',
                $data->description ?? null
            );
            Flight::json(['success' => true, 'message' => 'Famille d\'article modifiée']);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function deleteArticleFamille($id_article_famille) {
        try {
            ArticleFamilleModel::delete($id_article_famille);
            Flight::json(['success' => true, 'message' => 'Famille d\'article supprimée']);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function searchArticleFamille() {
        try {
            $search_term = Flight::request()->query->search ?? '';
            $results = ArticleFamilleModel::search($search_term);
            Flight::json(['success' => true, 'data' => $results]);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ============ ARTICLE ============

    public static function getAllArticles() {
        try {
            $articles = ArticleModel::getAll();
            Flight::json(['success' => true, 'data' => $articles]);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function getArticleById($id_article) {
        try {
            $article = ArticleModel::getById($id_article);
            if ($article) {
                Flight::json(['success' => true, 'data' => $article]);
            } else {
                Flight::json(['success' => false, 'message' => 'Article introuvable'], 404);
            }
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function createArticle() {
        try {
            $data = Flight::request()->data;
            $model = new ArticleModel();
            $id = $model->insert(
                $data->code ?? '',
                $data->designation ?? '',
                $data->id_famille_article_famille ?? null,
                $data->id_methode_valorisation ?? null,
                $data->unite ?? null,
                $data->prix_achat ?? null,
                $data->prix_vente ?? null,
                $data->stock_min ?? 0,
                $data->actif ?? true
            );
            Flight::json(['success' => true, 'message' => 'Article créé', 'id' => $id]);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function updateArticle($id_article) {
        try {
            $data = Flight::request()->data;
            ArticleModel::update(
                $id_article,
                $data->code ?? '',
                $data->designation ?? '',
                $data->id_famille_article_famille ?? null,
                $data->id_methode_valorisation ?? null,
                $data->unite ?? null,
                $data->prix_achat ?? null,
                $data->prix_vente ?? null,
                $data->stock_min ?? 0,
                $data->actif ?? true
            );
            Flight::json(['success' => true, 'message' => 'Article modifié']);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function deleteArticle($id_article) {
        try {
            ArticleModel::delete($id_article);
            Flight::json(['success' => true, 'message' => 'Article supprimé']);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function searchArticle() {
        try {
            $search_term = Flight::request()->query->search ?? '';
            $results = ArticleModel::search($search_term);
            Flight::json(['success' => true, 'data' => $results]);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function getArticleByFamille($id_famille) {
        try {
            $articles = ArticleModel::getByFamille($id_famille);
            Flight::json(['success' => true, 'data' => $articles]);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function getActiveArticles() {
        try {
            $articles = ArticleModel::getActive();
            Flight::json(['success' => true, 'data' => $articles]);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ============ ARTICLE STATUS ============

    public static function getAllArticleStatus() {
        try {
            $status = ArticleStatusModel::getAll();
            Flight::json(['success' => true, 'data' => $status]);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function getArticleStatusById($id_article_status) {
        try {
            $status = ArticleStatusModel::getById($id_article_status);
            if ($status) {
                Flight::json(['success' => true, 'data' => $status]);
            } else {
                Flight::json(['success' => false, 'message' => 'Statut article introuvable'], 404);
            }
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ============ ARTICLE PRIX HISTORIQUE ============

    public static function getArticlePrixHistorique($id_article) {
        try {
            $historique = ArticlePrixHistoriqueModel::getByArticle($id_article);
            Flight::json(['success' => true, 'data' => $historique]);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function getLastPriceArticle($id_article) {
        try {
            $price = ArticlePrixHistoriqueModel::getLastPriceByArticle($id_article);
            Flight::json(['success' => true, 'data' => $price]);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
