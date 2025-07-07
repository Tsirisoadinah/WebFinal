<?php
require_once __DIR__ . '/../models/Etablissement.php';
require_once __DIR__ . '/../helpers/Utils.php';


class EtablissementController {

    public static function getFonds () {
        $fonds = Etablissement::getFondsByIdEtablissement(1);
        Flight::json($fonds);
    }


    public static function ajouterFond() {
        // Get the raw POST data (form-urlencoded)
        $request = Flight::request();
        $montant = (float) $request->data->montant; // Access the 'montant' field
        
        Etablissement::updateFonds(1, $montant);
        Flight::json(["success" => true, "message" => "Fonds inséré avec succès"]);
    }
}
