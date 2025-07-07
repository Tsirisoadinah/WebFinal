<?php
require_once __DIR__ . '/../controllers/TypePretController.php';

Flight::route('GET /types-pret', ['TypePretController', 'getAll']);
Flight::route('GET /types-pret/@id', ['TypePretController', 'getById']);
Flight::route('POST /types-pret', ['TypePretController', 'create']);
Flight::route('PUT /types-pret/@id', ['TypePretController', 'update']);
Flight::route('DELETE /types-pret/@id', ['TypePretController', 'delete']);
?>
