<?php 


require_once __DIR__ . '/../models/Statistiques.php';
// This controller handles PDF generation for clients
// It uses the createPDFClients function to generate a PDF for a specific client
// The client ID is hardcoded to 1 for demonstration purposes

class PDFController {
public static function generateClientPDF() {
        // Désactive toute sortie bufferisée
        while (ob_get_level()) ob_end_clean();
        
        $pretId = Flight::request()->query['pretId'] ?? null;

        // Configure les headers PDF
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="clients.pdf"');
        
        // Charge et exécute la génération PDF
        require_once __DIR__ . '/../inc/createPDFClients.php';

        $dataPret = Statistiques::getDetailsPret($pretId);
        //Flight::json($dataPret);


        if (!$dataPret) {
            Flight::halt(404, json_encode(['error' => 'Loan not found']));
        }

        createPDFClients($dataPret);
        // Termine l'exécution après génération du PDF
        exit;
    }
    
}