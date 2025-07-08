<?php
require_once __DIR__ . '/../models/Pret.php';

class PretController {
    // Créer un prêt
    public static function createPret() {
        $data = Flight::request()->data;
        
        // Vérification des données requises
        if (!isset($data->type_pret_id) || !isset($data->montant_pret) || 
            !isset($data->client_id)) {
            Flight::json(['success' => false, 'message' => 'Données manquantes'], 400);
            return;
        }
        
        // Valeurs par défaut pour les paramètres optionnels
        $datePret = isset($data->date_pret) ? $data->date_pret : date('Y-m-d');
        $assurance = isset($data->assurance) ? floatval($data->assurance) : 0;
        $delai = isset($data->delai) ? intval($data->delai) : 0;
        $duree_prevue = isset($data->duree_prevue) ? intval($data->duree_prevue) : 0;
        

        $result = Pret::createPret(
            $data->type_pret_id,
            $data->montant_pret,
            $datePret,
            $assurance,
            $delai,
            $data->client_id,
            $duree_prevue
        );
        
        if ($result['success']) {
            Flight::json($result, 201);
        } else {
            Flight::json($result, 400);
        }
    }
    
    // Simuler un prêt
    public static function simulatePret() {
        $data = Flight::request()->data;
        
        // Vérification des données requises
        if (!isset($data->type_pret_id) || !isset($data->montant_pret) || 
            !isset($data->client_id)) {
            Flight::json(['success' => false, 'message' => 'Données manquantes'], 400);
            return;
        }
        
        // Valeurs par défaut pour les paramètres optionnels
        $datePret = isset($data->date_pret) ? $data->date_pret : date('Y-m-d');
        $assurance = isset($data->assurance) ? floatval($data->assurance) : 0;
        $delai = isset($data->delai) ? intval($data->delai) : 0;
        $duree_prevue = isset($data->duree_prevue) ? intval($data->duree_prevue) : 0;
        
        $result = Pret::simulatePret(
            $data->type_pret_id,
            $data->montant_pret,
            $datePret,
            $assurance,
            $delai,
            $data->client_id,
            $duree_prevue
        );
        
        Flight::json($result);
    }
    
    // Récupérer tous les prêts d'un client
    public static function getPretsByClient($clientId) {
        $prets = Pret::getPretsByClient($clientId);
        Flight::json($prets);
    }
    
    // Récupérer les détails d'un prêt
    public static function getPretDetails($pretId) {
        $details = Pret::getPretDetails($pretId);
        
        if ($details) {
            Flight::json($details);
        } else {
            Flight::json(['message' => 'Prêt non trouvé'], 404);
        }
    }
}
?>

