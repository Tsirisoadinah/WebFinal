<?php
require('fpdf186/fpdf.php');

class PDF extends FPDF
{
    // En-tete
    function Header()
    {
        $this->SetFont('Helvetica', 'B', 14);
        $this->SetTextColor(44, 82, 130); // Bleu professionnel
        $this->Cell(0, 10, 'Etablissement Financier XYZ', 0, 1, 'L');
        $this->SetFont('Helvetica', '', 10);
        $this->SetTextColor(80, 80, 80);
        $this->Cell(0, 6, 'Antaninarenina, Antananarivo, Madagascar | contact@xyzfinance.mg', 0, 1, 'L');
        $this->SetLineWidth(0.5);
        $this->SetDrawColor(200, 200, 200);
        $this->Line(10, 30, 200, 30);
        $this->Ln(10);
    }

    // Pied de page
    function Footer()
    {
        $this->SetY(-20);
        $this->SetFont('Helvetica', 'I', 8);
        $this->SetTextColor(100, 100, 100);
        $this->Cell(0, 6, 'Page ' . $this->PageNo() . ' / {nb}', 0, 0, 'C');
        $this->Ln(5);
        $this->Cell(0, 6, 'emis le ' . date('d/m/Y'), 0, 0, 'C');
    }

    // Informations du client
    function ClientInfo($data)
    {
        $this->SetFont('Helvetica', 'B', 12);
        $this->SetTextColor(44, 82, 130);
        $this->Cell(0, 10, 'Informations du Client', 0, 1, 'L');
        $this->SetFont('Helvetica', '', 10);
        $this->SetTextColor(0, 0, 0);

        $this->SetX(15);
        $this->Cell(40, 7, 'Nom :', 0, 0, 'L');
        $this->Cell(0, 7, $data['nom'], 0, 1, 'L');
        $this->SetX(15);
        $this->Cell(40, 7, 'E-mail :', 0, 0, 'L');
        $this->Cell(0, 7, $data['email'], 0, 1, 'L');
        $this->Ln(10);
    }

    // Details du pret (tableau)
    function LoanDetails($data)
    {
        $this->SetFont('Helvetica', 'B', 12);
        $this->SetTextColor(44, 82, 130);
        $this->Cell(0, 10, 'Details du Pret', 0, 1, 'L');
        $this->SetFont('Helvetica', '', 10);
        $this->SetTextColor(0, 0, 0);

        $this->Ln(5);

        // Tableau des details
        $this->SetFillColor(247, 250, 252); // Fond gris clair
        $this->SetDrawColor(200, 200, 200);
        $this->SetX(15);
        $this->Cell(80, 8, 'Description', 1, 0, 'L', true);
        $this->Cell(80, 8, 'Valeur', 1, 1, 'L', true);

        $loanInfo = [
            'Identifiant du pret' => $data['Identifiant du pret'],
            'Type du pret' => $data['Type du pret'],
            'Delai de remboursement' => (isset($data['delai']) && $data['delai'] !== '') ? $data['delai'] . ' mois' : '0 mois',
            'Assurance' => (isset($data['assurance']) && $data['assurance'] !== '') ? $data['assurance'] . ' %' : '0 %',
            'Montant du pret' => number_format($data['Montant du pret'], 2, ',', ' ') . ' Ar',
            'Taux d\'interet' => $data['Taux d\'interet'] . ' %',
            'Duree du pret' => $data['Duree du pret'] . ' mois',
            'Date de debut' => $data['Date de debut'],
            'Date de fin' => $data['Date de fin'],
            'Montant a rembourser' => number_format($data['Montant a rembourser'], 2, ',', ' ') . ' Ar',
            'Montant mensuel' => number_format($data['Montant mensuel'], 2, ',', ' ') . ' Ar',
        ];


        foreach ($loanInfo as $label => $value) {
            $this->SetX(15);
            $this->Cell(80, 8, $label, 1, 0, 'L');
            $this->Cell(80, 8, $value, 1, 1, 'L');
        }
        $this->Ln(10);
    }


    function PaymentSummary($payments)
    {
        $this->SetFont('Arial', 'B', 12);
        $this->SetTextColor(44, 82, 130);
        $this->Cell(0, 10, 'Resume des Paiements', 0, 1, 'L');
        $this->SetFont('Arial', '', 10);
        $this->SetTextColor(0, 0, 0);

        $this->SetX(15);
        $this->SetFillColor(247, 250, 252);
        $this->SetDrawColor(200, 200, 200);
        $this->Cell(30, 8, 'Annee', 1, 0, 'C', true);
        $this->Cell(40, 8, 'Mois', 1, 0, 'C', true);
        $this->Cell(40, 8, 'Interets (Ar)', 1, 0, 'C', true);
        $this->Cell(40, 8, 'Capital (Ar)', 1, 1, 'C', true);

        $totalInterets = 0;
        $totalCapital = 0;

        foreach ($payments as $payment) {
            $this->SetX(15);
            $this->Cell(30, 8, $payment['annee'], 1, 0, 'C');
            $this->Cell(40, 8, $payment['mois'], 1, 0, 'C');
            $this->Cell(40, 8, number_format($payment['interets'], 2, ',', ' '), 1, 0, 'R');
            $this->Cell(40, 8, number_format($payment['capital'], 2, ',', ' '), 1, 1, 'R');

            $totalInterets += $payment['interets'];
            $totalCapital += $payment['capital'];
        }
    }


    // Notes et signature
    function NotesAndSignature()
    {
        $this->SetFont('Helvetica', 'I', 9);
        $this->SetTextColor(100, 100, 100);
        $this->MultiCell(0, 6, "Ce document est un recapitulatif officiel de votre pret. Pour toute question, veuillez contacter notre service client a contact@xyzfinance.mg ou au +261 34 77 755 55.", 0, 'L');
        $this->Ln(10);

        $this->SetFont('Helvetica', '', 10);
        $this->SetTextColor(0, 0, 0);
        $this->Cell(0, 6, 'Fait a Antananarivo, le ' . date('d/m/Y'), 0, 1, 'R');
        $this->SetFont('Helvetica', 'B', 10);
        $this->Cell(0, 6, 'Le Directeur de l Etablissement', 0, 1, 'R');
    }
}

function createPDFClients($dataClients, $data_prets)
{
    $pdf = new PDF();
    $pdf->AliasNbPages();
    $pdf->AddPage();
    $pdf->ClientInfo($dataClients);
    $pdf->LoanDetails($dataClients);
    $pdf->PaymentSummary($data_prets);
    $pdf->NotesAndSignature();
    $pdf->Output();
}
