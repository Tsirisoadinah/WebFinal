<?php
require_once __DIR__ . '/../controllers/EtablissementController.php';

Flight::route('GET /etablissement/fonds', ['EtablissementController', 'getFonds']);
Flight::route('POST /etablissement/fonds', ['EtablissementController', 'ajouterFond']);
Flight::route('GET /etablissement/etat' , ['EtablissementController' , 'getFondsDates'] )

?>