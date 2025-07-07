<?php

require_once __DIR__ . '/../controllers/StatsController.php';

Flight::route('GET /stats/somme-interets', ['StatsController', 'getSommeInterets']);
Flight::route('GET /stats/liste-prets', ['StatsController', 'getListePrets']);