<?php
require_once __DIR__ . '/../models/TypePret.php';
require_once __DIR__ . '/../helpers/Utils.php';


class TypePretController {
    public static function getAll() {
        $typesPret = TypePret::getAll();
        Flight::json($typesPret);
    }

    public static function getById($id) {
        $typePret = TypePret::getById($id);
        if ($typePret) {
            Flight::json($typePret);
        } else {
            Flight::json(['message' => 'Type de prêt non trouvé'], 404);
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
        $data = Flight::request()->data;
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
?>
