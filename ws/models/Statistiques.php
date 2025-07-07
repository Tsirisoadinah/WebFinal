<?php
require_once __DIR__ . '/../db.php';

class Statistiques
{


    public static function getAllTotalInterets($dateDebut, $dateFin)
    {
        $db = getDB();
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
}
