<?php
require_once __DIR__ . '/../models/Client.php';

class ClientController {
    public static function getAll() {
        $clients = Client::getAll();
        Flight::json($clients);
    }

    public static function getById($id) {
        $client = Client::getById($id);
        if ($client) {
            Flight::json($client);
        } else {
            Flight::json(['message' => 'Client non trouvé'], 404);
        }
    }

    public static function create() {
        $data = Flight::request()->data;
        $id = Client::create($data);
        Flight::json(['message' => 'Client ajouté', 'id' => $id], 201);
    }

    public static function update($id) {
        $data = Flight::request()->data;
        Client::update($id, $data);
        Flight::json(['message' => 'Client modifié']);
    }

    public static function delete($id) {
        Client::delete($id);
        Flight::json(['message' => 'Client supprimé']);
    }
}
?>
