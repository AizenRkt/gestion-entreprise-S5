<?php

namespace app\controllers\AVIS\referentiel\site;

use app\models\AVIS\referentiel\site\DepotModel;
use app\models\AVIS\referentiel\site\SiteModel;
use app\models\AVIS\referentiel\site\SiteDepotModel;
use Exception;
use Flight;

class SiteApiController {

    // ============ DEPOT ============

    public static function getAllDepots() {
        try {
            $depots = DepotModel::getAll();
            Flight::json(['success' => true, 'data' => $depots]);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function getDepotById($id_depot) {
        try {
            $depot = DepotModel::getById($id_depot);
            if ($depot) {
                Flight::json(['success' => true, 'data' => $depot]);
            } else {
                Flight::json(['success' => false, 'message' => 'Dépôt introuvable'], 404);
            }
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function createDepot() {
        try {
            $data = Flight::request()->data;
            $model = new DepotModel();
            $id = $model->insert(
                $data->code ?? '',
                $data->nom ?? '',
                $data->adresse ?? null
            );
            Flight::json(['success' => true, 'message' => 'Dépôt créé', 'id' => $id]);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function updateDepot($id_depot) {
        try {
            $data = Flight::request()->data;
            DepotModel::update(
                $id_depot,
                $data->code ?? '',
                $data->nom ?? '',
                $data->adresse ?? null
            );
            Flight::json(['success' => true, 'message' => 'Dépôt modifié']);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function deleteDepot($id_depot) {
        try {
            DepotModel::delete($id_depot);
            Flight::json(['success' => true, 'message' => 'Dépôt supprimé']);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ============ SITE ============

    public static function getAllSites() {
        try {
            $sites = SiteModel::getAll();
            Flight::json(['success' => true, 'data' => $sites]);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function getSiteById($id_site) {
        try {
            $site = SiteModel::getById($id_site);
            if ($site) {
                Flight::json(['success' => true, 'data' => $site]);
            } else {
                Flight::json(['success' => false, 'message' => 'Site introuvable'], 404);
            }
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function createSite() {
        try {
            $data = Flight::request()->data;
            $model = new SiteModel();
            $id = $model->insert(
                $data->code ?? '',
                $data->nom ?? null
            );
            Flight::json(['success' => true, 'message' => 'Site créé', 'id' => $id]);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function updateSite($id_site) {
        try {
            $data = Flight::request()->data;
            SiteModel::update(
                $id_site,
                $data->code ?? '',
                $data->nom ?? null
            );
            Flight::json(['success' => true, 'message' => 'Site modifié']);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function deleteSite($id_site) {
        try {
            SiteModel::delete($id_site);
            Flight::json(['success' => true, 'message' => 'Site supprimé']);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function getSiteWithDepots($id_site) {
        try {
            $siteDepots = SiteModel::getSiteWithDepots($id_site);
            Flight::json(['success' => true, 'data' => $siteDepots]);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // ============ SITE DEPOT ============

    public static function getAllSiteDepots() {
        try {
            $siteDepots = SiteDepotModel::getAll();
            Flight::json(['success' => true, 'data' => $siteDepots]);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function getSiteDepotById($id_site_depot) {
        try {
            $siteDepot = SiteDepotModel::getById($id_site_depot);
            if ($siteDepot) {
                Flight::json(['success' => true, 'data' => $siteDepot]);
            } else {
                Flight::json(['success' => false, 'message' => 'Association site-dépôt introuvable'], 404);
            }
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function getSiteDepotByIdSite($id_site) {
        try {
            $siteDepots = SiteDepotModel::getByIdSite($id_site);
            Flight::json(['success' => true, 'data' => $siteDepots]);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function createSiteDepot() {
        try {
            $data = Flight::request()->data;
            $model = new SiteDepotModel();
            $id = $model->insert(
                $data->id_site ?? '',
                $data->id_depot ?? ''
            );
            Flight::json(['success' => true, 'message' => 'Association site-dépôt créée', 'id' => $id]);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public static function deleteSiteDepot($id_site_depot) {
        try {
            SiteDepotModel::delete($id_site_depot);
            Flight::json(['success' => true, 'message' => 'Association site-dépôt supprimée']);
        } catch (Exception $e) {
            Flight::json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
