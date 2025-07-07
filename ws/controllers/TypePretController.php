<?php
require_once __DIR__ . '/../models/TypePret.php';
require_once __DIR__ . '/../helpers/Utils.php';

class TypePretController {
    public static function getAll() {
        $types = TypePret::getAll();
        Flight::json($types);
    }

    public static function getById($id) {
        $type = TypePret::getById($id);
        if (!$type) {
            Flight::halt(404, 'Type de prêt non trouvé');
        }
        Flight::json($type);
    }

    public static function create() {
        $data = Flight::request()->data;
        if (empty($data->libelle) || !isset($data->taux) || !isset($data->duree_max)) {
            Flight::halt(400, 'Données manquantes');
        }
        $id = TypePret::create($data);
        Flight::json(['message' => 'Type de prêt ajouté', 'id' => $id], 201);
    }

    public static function update($id) {
        $request = Flight::request();
        
        // Pour les requêtes PUT, nous devons parser le corps manuellement
        if ($request->method == 'PUT') {
            parse_str($request->getBody(), $data);
            $data = (object)$data; // Convertir en objet pour compatibilité
        } else {
            $data = $request->data;
        }

        if (empty($data->libelle) || !isset($data->taux) || !isset($data->duree_max)) {
            Flight::halt(400, 'Données manquantes');
        }
        
        TypePret::update($id, $data);
        Flight::json(['message' => 'Type de prêt modifié']);
    }

    public static function delete($id) {
        TypePret::delete($id);
        Flight::json(['message' => 'Type de prêt supprimé']);
    }
}