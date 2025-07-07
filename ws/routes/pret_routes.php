<?php
require_once __DIR__ . '/../controllers/PretController.php';

Flight::route('POST /prets/create', ['PretController', 'createPret']);
Flight::route('POST /prets/simuler', ['PretController', 'simulatePret']);
Flight::route('GET /clients/@id/prets', ['PretController', 'getPretsByClient']);
Flight::route('GET /prets/@id', ['PretController', 'getPretDetails']);
?>
