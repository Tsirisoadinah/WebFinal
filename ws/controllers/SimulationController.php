<?php
require_once __DIR__ . '/../models/Pret.php';

class SimulationController {
    
    // Sauvegarder une simulation
    public static function saveSimulation() {
        $data = Flight::request()->data;
        
        // Vérification des données requises
        if (!isset($data->nom) || !isset($data->type_pret_id) || !isset($data->montant_pret) || 
            !isset($data->client_id)) {
            Flight::json(['success' => false, 'message' => 'Données manquantes'], 400);
            return;
        }
        
        // Valeurs par défaut pour les paramètres optionnels
        $datePret = isset($data->date_pret) ? $data->date_pret : date('Y-m-d');
        $assurance = isset($data->assurance) ? floatval($data->assurance) : 0;
        $delai = isset($data->delai) ? intval($data->delai) : 0;
        $duree = isset($data->duree_prevue) ? intval($data->duree_prevue) : 0;
        
        $result = Pret::saveSimulation(
            $data->nom,
            $data->client_id,
            $data->type_pret_id,
            $data->montant_pret,
            $datePret,
            $assurance,
            $delai,
            $duree
        );
        
        if ($result['success']) {
            Flight::json($result, 201);
        } else {
            Flight::json($result, 400);
        }
    }
    
    // Récupérer toutes les simulations
    public static function getAllSimulations() {
        $result = Pret::getAllSimulations();
        
        if ($result['success']) {
            Flight::json($result['simulations']);
        } else {
            Flight::json(['message' => $result['message']], 500);
        }
    }
    
    // Récupérer les simulations d'un client
    public static function getSimulationsByClient($clientId) {
        $result = Pret::getSimulationsByClient($clientId);
        
        if ($result['success']) {
            Flight::json($result['simulations']);
        } else {
            Flight::json(['message' => $result['message']], 500);
        }
    }
    
    // Récupérer une simulation spécifique
    public static function getSimulationById($simulationId) {
        $result = Pret::getSimulationById($simulationId);
        
        if ($result['success']) {
            Flight::json($result['simulation']);
        } else {
            Flight::json(['message' => $result['message']], $result['message'] === 'Simulation non trouvée' ? 404 : 500);
        }
    }
}
?>
