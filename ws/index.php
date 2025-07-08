<?php
require 'vendor/autoload.php';
require 'db.php';
require 'routes/etudiant_routes.php';
require 'routes/TypePret_routes.php';
require 'routes/etablissement_routes.php';
require 'routes/pret_routes.php';
require 'routes/client_routes.php';
require 'routes/type_pret_routes.php';
require 'routes/stats_routes.php';
require 'routes/pdf_routes.php';
require 'routes/simulation_routes.php';

// Activer CORS pour permettre les requêtes depuis le frontend
Flight::route('*', function(){
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type");
    
    if (Flight::request()->method == 'OPTIONS') {
        Flight::halt(200);
    } else {
        return true;
    }
});

Flight::start();