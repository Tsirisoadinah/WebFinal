<?php
require_once __DIR__ . '/../controllers/TypePretController.php';

Flight::route('GET /typepret', ['TypePretController', 'getAll']);
Flight::route('GET /typepret/@id', ['TypePretController', 'getById']);
Flight::route('POST /typepret', ['TypePretController', 'create']);
Flight::route('PUT /typepret/@id', ['TypePretController', 'update']);
Flight::route('DELETE /typepret/@id', ['TypePretController', 'delete']);
