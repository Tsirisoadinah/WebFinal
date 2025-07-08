<?php
require_once __DIR__ . '/../models/Etablissement.php';
require_once __DIR__ . '/../helpers/Utils.php';


class EtablissementController
{

    public static function getFonds()
    {
        $fonds = Etablissement::getFondsByIdEtablissement(1);
        Flight::json($fonds);
    }


    public static function ajouterFond()
    {
        // Get the raw POST data (form-urlencoded)
        $request = Flight::request();
        $montant = (float) $request->data->montant; // Access the 'montant' field

        Etablissement::updateFonds(1, $montant);
        Flight::json(["success" => true, "message" => "Fonds inséré avec succès"]);
    }


    public static function getFondsDates()
    {
        $dateDebut = Flight::request()->query['dateDebut'];
        $dateFin = Flight::request()->query['dateFin'];

        if (!$dateDebut || !$dateFin) {
            Flight::halt(400, json_encode(['error' => 'Invalid date format']));
        }
        Flight::json(Etablissement::getSituationMensuelleParDate($dateDebut, $dateFin,1));
    }
}
