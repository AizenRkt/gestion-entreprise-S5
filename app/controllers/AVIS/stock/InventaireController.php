<?php
namespace app\controllers\AVIS\stock;

use Flight;

class InventaireController {

    public function list() {
        Flight::render('AVIS/stock/inventaireList', ['title' => 'Liste des Inventaires']);
    }

    public function saisie($id = null) {
        Flight::render('AVIS/stock/inventaireSaisie', [
            'title' => ($id ? 'Édition' : 'Nouveau') . ' Inventaire',
            'id_inventaire' => $id
        ]);
    }
}
