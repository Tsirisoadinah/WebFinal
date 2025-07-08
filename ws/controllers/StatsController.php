<?php
require_once __DIR__ . '/../models/Statistiques.php';
require_once __DIR__ . '/../helpers/Utils.php';

class StatsController {

    public static function getSommeInterets() {
        $dateDebut = Utils::formatDate(Flight::request()->query['dateDebut']);
        $dateFin = Utils::formatDate(Flight::request()->query['dateFin']);

        if (!$dateDebut || !$dateFin) {
            Flight::halt(400, json_encode(['error' => 'Invalid date format']));
        }

        Flight::json(Statistiques::getSommeInteretsParMois($dateDebut, $dateFin));
    }

    
    public static function getListePrets() {
        $listePrets = Statistiques::getListePrets();
        if (!$listePrets) {
            Flight::halt(404, json_encode(['error' => 'No loans found']));
        }
        Flight::json(Statistiques::getListePrets());
    }


}