<?php
require_once __DIR__ . '/../controllers/SimulationController.php';

Flight::route('POST /simulations', ['SimulationController', 'saveSimulation']);
Flight::route('GET /simulations', ['SimulationController', 'getAllSimulations']);
Flight::route('GET /simulations/@id', ['SimulationController', 'getSimulationById']);
Flight::route('GET /clients/@id/simulations', ['SimulationController', 'getSimulationsByClient']);
?>
