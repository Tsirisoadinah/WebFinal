<?php
require_once __DIR__ . '/../db.php';

class Statistiques
{


    public static function getAllTotalInterets($dateDebut, $dateFin)
    {
        $db = getDB();

        // year - day - month (format americain)
        $dateDebut = date('Y-d-m', strtotime($dateDebut));
        $dateFin = date('Y-d-m', strtotime($dateFin));

        $stmt = $db->prepare("SELECT * FROM finance_Historique_Pret WHERE date_paiement BETWEEN :dateDebut AND :dateFin");
        $stmt->bindParam(':dateDebut', $dateDebut);
        $stmt->bindParam(':dateFin', $dateFin);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getSommeInteretsParMois($dateDebut, $dateFin)
    {
        $db = getDB();

        // year - day - month (format americain)
        $dateDebut = date('Y-d-m', strtotime($dateDebut));
        $dateFin = date('Y-d-m', strtotime($dateFin));
        if (!$dateDebut || !$dateFin) {
            throw new InvalidArgumentException('Invalid date format');
        }

        // Requête pour obtenir la somme des intérêts et du capital par mois

        $query = "SELECT 
                    YEAR(date_paiement) AS annee,
                    MONTH(date_paiement) AS mois,
                    SUM(interet) AS total_interets,
                    SUM(capital) AS total_capital
                FROM finance_Historique_Pret
                WHERE date_paiement BETWEEN :dateDebut AND :dateFin
                GROUP BY YEAR(date_paiement), MONTH(date_paiement)
                ORDER BY annee DESC, mois DESC";
        $stmt = $db->prepare($query);
        $stmt->bindParam(':dateDebut', $dateDebut, PDO::PARAM_STR);
        $stmt->bindParam(':dateFin', $dateFin, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getListePrets()
    {
        $db = getDB();

        $query = "SELECT p.id AS pret_id,c.nom AS client_nom,tp.libelle AS type_pret,p.montant_pret,tp.taux,p.date_pret,p.date_fin
              FROM finance_Pret p
              JOIN finance_Type_Pret tp ON p.id_type_pret = tp.id
              JOIN finance_Client_Pret cp ON p.id = cp.id_pret
              JOIN finance_Clients c ON cp.id_client = c.id
              ORDER BY p.date_pret DESC";

        $stmt = $db->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public static function getDetailsPret($loanId)
    {
        $db = getDB();

        $query = "SELECT 
                p.id AS id_pret,
                tp.libelle AS type_pret,
                p.montant_pret,
                tp.taux,
                p.delai,
                p.date_pret AS date_debut,
                p.date_fin,
                TIMESTAMPDIFF(MONTH, p.date_pret, p.date_fin) AS duree,
                p.delai,
                p.assurance,
                (p.montant_pret * (1 + (tp.taux/100))) AS montant_rembourser,
                p.montant_mensuel,
                c.id AS client_id,
                c.nom AS client_nom,
                c.email AS client_email,
                c.revenu_mensuel,
                c.score_credit
              FROM finance_Pret p
              JOIN finance_Type_Pret tp ON p.id_type_pret = tp.id
              JOIN finance_Client_Pret cp ON p.id = cp.id_pret
              JOIN finance_Clients c ON cp.id_client = c.id
              WHERE p.id = :loanId";

        $stmt = $db->prepare($query);
        $stmt->bindParam(':loanId', $loanId, PDO::PARAM_INT);
        $stmt->execute();

        $data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        return [
                'Identifiant du pret' => $data['id_pret'],
                'Type du pret' => $data['type_pret'],
                'Montant du pret' => $data['montant_pret'],
                'Taux d\'interet' => $data['taux'],
                'Duree du pret' => $data['duree'],
                'delai' => $data['delai'],
                'assurance' => $data['assurance'],
                'Date de debut' => $data['date_debut'],
                'Date de fin' => $data['date_fin'],
                'Montant a rembourser' => $data['montant_rembourser'],
                'Montant mensuel' => $data['montant_mensuel'],
                'Identifiant client' => $data['client_id'],
                'nom' => $data['client_nom'],
                'email' => $data['client_email']
        ];
    }


public static function getDetailsPDFByPret($id_pret)
{
    $db = getDB();

    $query = "
        SELECT 
            date_paiement,
            capital,
            interet
        FROM finance_Historique_Pret
        WHERE id_pret = ?
        ORDER BY date_paiement ASC
    ";

    $stmt = $db->prepare($query);
    $stmt->execute([$id_pret]);
    $resultats = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $details = [];

    // Calculer le capital total à rembourser
    $capitalTotal = 0;
    foreach ($resultats as $row) {
        $capitalTotal += floatval($row['capital']);
    }
    $capitalRestant = $capitalTotal;

    // Préparer IntlDateFormatter pour le nom du mois en français
    $fmt = null;
    if (class_exists('IntlDateFormatter')) {
        $fmt = new IntlDateFormatter('fr_FR', IntlDateFormatter::NONE, IntlDateFormatter::NONE);
        $fmt->setPattern('MMMM'); // Nom complet du mois
    }

    foreach ($resultats as $row) {
        try {
            $datePaiement = DateTime::createFromFormat('Y-m-d', $row['date_paiement'], new DateTimeZone('UTC'));
            if ($datePaiement === false) {
                throw new Exception("Date invalide: " . $row['date_paiement']);
            }
        } catch (Exception $e) {
            error_log("Erreur de date pour id_pret {$id_pret}: " . $e->getMessage());
            continue;
        }

        $capital = floatval($row['capital']);
        $interet = floatval($row['interet']);
        $mensualite = $capital + $interet;
        $capitalRestant -= $capital;

        if ($fmt !== null) {
            $moisNom = ucfirst($fmt->format($datePaiement));
        } else {
            // Fallback
            $moisNom = ucfirst(
                (function_exists('strftime')) 
                ? strftime('%B', $datePaiement->getTimestamp()) 
                : $datePaiement->format('F')
            );
        }

        $details[] = [
            'mois' => $moisNom,
            'annee' => (int) $datePaiement->format('Y'),
            'date' => $datePaiement->format('d/m/Y'),
            'mensualite' => round($mensualite, 2),
            'capital' => round($capital, 2),
            'interets' => round($interet, 2),
            'capital_restant' => round(max($capitalRestant, 0), 2),
        ];
    }

    return $details;
}

}
