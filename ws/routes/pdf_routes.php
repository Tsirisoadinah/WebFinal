<?php 

require_once __DIR__ . '/../controllers/PDFController.php';
Flight::route('GET /create-pdf', ['PDFController', 'generateClientPDF']);

?>