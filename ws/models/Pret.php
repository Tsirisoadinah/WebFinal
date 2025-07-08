<?php
require_once __DIR__ . '/../db.php';

class Pret
{
    // Calcule la mensualité selon la formule donnée
    public static function calculMensualite($montantReel, $taux, $duree)
    {
        $tauxMensuel = $taux / 12 / 100; // taux annuel en pourcentage converti en taux mensuel
        $mensualite = ($montantReel * $tauxMensuel) / (1 - pow(1 + $tauxMensuel, -$duree));
        return $mensualite;
    }

    // Calcule le montant de l'assurance à ajouter
    public static function calculMontantAssurance($montantPret, $assurance)
    {
        return $montantPret * ($assurance / 100);
    }

    // Retourne la somme des paiements remboursés avant une date
    public static function getSommeHistoriquePret($date)
    {
        $db = getDB();
        $query = "SELECT SUM(hp.capital + hp.interet) as total
                 FROM finance_Historique_Pret hp
                 JOIN finance_Pret p ON hp.id_pret = p.id
                 WHERE hp.date_paiement < ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$date]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ? floatval($result['total']) : 0;
    }

    // Retourne le capital d'un prêt
    public static function getCapitalFromPret($pretId)
    {
        $db = getDB();
        $query = "SELECT SUM(capital) as total_capital FROM finance_Historique_Pret WHERE id_pret = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$pretId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total_capital'] ? floatval($result['total_capital']) : 0;
    }

    // Retourne l'intérêt d'un prêt
    public static function getInteretFromPret($pretId)
    {
        $db = getDB();
        $query = "SELECT SUM(interet) as total_interet FROM finance_Historique_Pret WHERE id_pret = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$pretId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total_interet'] ? floatval($result['total_interet']) : 0;
    }

    // Calcule la date de fin selon la durée
    public static function getDateFinPret($datePret, $typePretId, $duree)
    {
        $db = getDB();
        $query = "SELECT duree_max FROM finance_Type_Pret WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$typePretId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            return null;
        }

        $dureeMax = intval($result['duree_max']);
        return date('Y-m-d', strtotime($datePret . " + $duree months"));
    }

    // Vérifie si l'établissement a assez de fonds
    public static function verificationFonds($datePret, $montantReel)
    {
        $db = getDB();

        // Récupérer les fonds totaux de l'établissement (somme de tous les fonds)
        $query = "SELECT SUM(fonds) as total_fonds FROM finance_Etablissement_Financier";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            return false;
        }

        $fondsInitial = floatval($result['total_fonds']);

        // Somme des montants des prêts accordés avant la date du nouveau prêt
        $query = "SELECT SUM(montant_pret) as total_prets
                 FROM finance_Pret p
                 WHERE p.date_pret < ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$datePret]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $totalPrets = $result['total_prets'] ? floatval($result['total_prets']) : 0;

        // Somme des remboursements (capital + intérêt) avant la date du prêt
        $totalRemboursements = self::getSommeHistoriquePret($datePret);

        // Calcul des fonds restants
        $fondsRestants = $fondsInitial - $totalPrets + $totalRemboursements;

        return $fondsRestants >= $montantReel;
    }

    // Calcule le mois du premier remboursement
    public static function getDebutRemboursement($datePret, $delai)
    {
        return date('Y-m-d', strtotime($datePret . " + $delai months"));
    }

    // Vérifie si le client est éligible au nouveau prêt
    public static function checkIfPossiblePret($clientId, $montantMensuelSouhaite, $datePret)
    {
        $db = getDB();

        // Récupérer le revenu mensuel du client
        $query = "SELECT revenu_mensuel FROM finance_Clients WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$clientId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result) {
            return false;
        }

        $revenuMensuel = floatval($result['revenu_mensuel']);
        $limiteEndettement = $revenuMensuel * 0.3; // 30% du revenu mensuel

        // Récupérer le total des mensualités des prêts en cours
        $query = "SELECT SUM(p.montant_mensuel) as total_mensualites
                 FROM finance_Pret p
                 JOIN finance_Client_Pret cp ON p.id = cp.id_pret
                 WHERE cp.id_client = ? 
                 AND p.date_pret <= ? 
                 AND p.date_fin >= ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$clientId, $datePret, $datePret]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $totalMensualites = $result['total_mensualites'] ? floatval($result['total_mensualites']) : 0;

        // Vérifier si l'ajout de la nouvelle mensualité dépasse la limite
        return ($totalMensualites + $montantMensuelSouhaite) <= $limiteEndettement;
    }

    // Créer un prêt et l'historique des remboursements
    public static function createPret($typePretId, $montantPret, $datePret, $assurance, $delai, $clientId, $duree)
    {
        $db = getDB();

        try {
            $db->beginTransaction();

            // Récupérer les informations du type de prêt
            $stmtTypePret = $db->prepare("SELECT taux, duree_max FROM finance_Type_Pret WHERE id = ?");
            $stmtTypePret->execute([$typePretId]);
            $typePret = $stmtTypePret->fetch(PDO::FETCH_ASSOC);

            if (!$typePret) {
                throw new Exception("Type de prêt non trouvé");
            }

            $taux = floatval($typePret['taux']);
            $duree_max = intval($typePret['duree_max']);

            if ($duree_max < $duree) {
                throw new Exception("La durée ne peut pas être inférieure à la durée max");
            }
            // Calculer le montant réel avec assurance
            $montantAssurance = self::calculMontantAssurance($montantPret, $assurance);
            $montantReel = $montantPret + $montantAssurance;

            // Vérifier si l'établissement a assez de fonds
            if (!self::verificationFonds($datePret, $montantPret)) {
                throw new Exception("Fonds insuffisants pour accorder ce prêt");
            }

            $nbMoisRemboursement = $duree - $delai;
            $mensualite = self::calculMensualite($montantReel, $taux, $nbMoisRemboursement);

            // Vérifier la capacité d'emprunt du client
            if (!self::checkIfPossiblePret($clientId, $mensualite, $datePret)) {
                throw new Exception("Capacité d'emprunt dépassée pour ce client");
            }

            // Calculer la date de fin du prêt
            $dateFin = self::getDateFinPret($datePret, $typePretId, $duree + $delai);

            // Insérer le prêt
            $stmtPret = $db->prepare("INSERT INTO finance_Pret (id_type_pret, montant_mensuel, montant_pret, date_pret, date_fin, assurance, delai) 
                                     VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmtPret->execute([
                $typePretId,
                $mensualite,
                $montantPret,
                $datePret,
                $dateFin,
                $assurance,
                $delai
            ]);

            $pretId = $db->lastInsertId();

            // Lier le client au prêt
            $stmtClientPret = $db->prepare("INSERT INTO finance_Client_Pret (id_pret, id_client) VALUES (?, ?)");
            $stmtClientPret->execute([$pretId, $clientId]);

            // Générer l'historique des remboursements
            $dateDebut = self::getDebutRemboursement($datePret, $delai);

            $capitalRestant = $montantReel;
            $dateRemboursement = $dateDebut;

            for ($i = 0; $i < $nbMoisRemboursement; $i++) {
                $interet = $capitalRestant * ($taux / 12 / 100);
                $capital = $mensualite - $interet;

                // Ajustement pour le dernier mois
                if ($i == $nbMoisRemboursement- 1) {
                    $capital = $capitalRestant;
                    $mensualite = $capital + $interet;
                }

                $stmtHistorique = $db->prepare("INSERT INTO finance_Historique_Pret (id_pret, capital, interet, date_paiement) 
                                              VALUES (?, ?, ?, ?)");
                $stmtHistorique->execute([
                    $pretId,
                    $capital,
                    $interet,
                    $dateRemboursement
                ]);

                $capitalRestant -= $capital;
                $dateRemboursement = date('Y-m-d', strtotime($dateRemboursement . " +1 month"));
            }

            $db->commit();
            return [
                'success' => true,
                'pret_id' => $pretId,
                'mensualite' => $mensualite,
                'montant_reel' => $montantReel,
                'debut_remboursement' => $dateDebut,
                'fin_remboursement' => $dateFin
            ];
        } catch (Exception $e) {
            $db->rollBack();
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    // Simulation de prêt (sans insertion en base)
    public static function simulatePret($typePretId, $montantPret, $datePret, $assurance, $delai, $clientId, $duree)
    {
        $db = getDB();

        try {
            // Récupérer les informations du type de prêt
            $stmtTypePret = $db->prepare("SELECT taux, duree_max FROM finance_Type_Pret WHERE id = ?");
            $stmtTypePret->execute([$typePretId]);
            $typePret = $stmtTypePret->fetch(PDO::FETCH_ASSOC);

            if (!$typePret) {
                throw new Exception("Type de prêt non trouvé");
            }

            $taux = floatval($typePret['taux']);
            $duree_max = intval($typePret['duree_max']);

            if ($duree_max < $duree) {
                throw new Exception("La durée ne peut pas être inférieure à la durée max");
            }
            // Calculer le montant réel avec assurance
            $montantAssurance = self::calculMontantAssurance($montantPret, $assurance);
            $montantReel = $montantPret + $montantAssurance;

            // Vérifier si l'établissement a assez de fonds
            $fondsOk = self::verificationFonds($datePret, $montantPret);

            $nbMoisRemboursement = $duree - $delai;
            $mensualite = self::calculMensualite($montantReel, $taux, $nbMoisRemboursement);

            // Vérifier la capacité d'emprunt du client
            $capaciteOk = self::checkIfPossiblePret($clientId, $mensualite, $datePret);

            // Calculer la date de fin du prêt
            $dateFin = self::getDateFinPret($datePret, $typePretId, $duree + $delai);

            // Calculer le début des remboursements
            $dateDebut = self::getDebutRemboursement($datePret, $delai);

            // Générer tableau d'amortissement pour simulation
            $tableauAmortissement = [];
            $capitalRestant = $montantReel;
            $dateRemboursement = $dateDebut;
            $totalInteret = 0;
            $totalCapital = 0;

            for ($i = 0; $i < $nbMoisRemboursement; $i++) {
                $interet = $capitalRestant * ($taux / 12 / 100);
                $capital = $mensualite - $interet;

                // Ajustement pour le dernier mois
                if ($i == $nbMoisRemboursement - 1) {
                    $capital = $capitalRestant;
                    $mensualite = $capital + $interet;
                }

                $tableauAmortissement[] = [
                    'mois' => $i + 1,
                    'date' => $dateRemboursement,
                    'mensualite' => $mensualite,
                    'capital' => $capital,
                    'interet' => $interet,
                    'capital_restant' => $capitalRestant - $capital
                ];

                $totalInteret += $interet;
                $totalCapital += $capital;
                $capitalRestant -= $capital;
                $dateRemboursement = date('Y-m-d', strtotime($dateRemboursement . " +1 month"));
            }

            return [
                'success' => true,
                'est_possible' => $fondsOk && $capaciteOk,
                'raisons_rejet' => [
                    'fonds_insuffisants' => !$fondsOk,
                    'capacite_depassee' => !$capaciteOk
                ],
                'mensualite' => $mensualite,
                'montant_pret' => $montantPret,
                'montant_assurance' => $montantAssurance,
                'montant_reel' => $montantReel,
                'taux' => $taux,
                'duree' => $duree,
                'debut_remboursement' => $dateDebut,
                'fin_remboursement' => $dateFin,
                'cout_total' => $montantReel + $totalInteret,
                'total_interet' => $totalInteret,
                'tableau_amortissement' => $tableauAmortissement
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    // Récupérer tous les prêts d'un client
    public static function getPretsByClient($clientId)
    {
        $db = getDB();
        $query = "SELECT p.*, tp.libelle as type_pret, tp.taux
                 FROM finance_Pret p
                 JOIN finance_Type_Pret tp ON p.id_type_pret = tp.id
                 JOIN finance_Client_Pret cp ON p.id = cp.id_pret
                 WHERE cp.id_client = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$clientId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Récupérer les détails d'un prêt avec son historique
    public static function getPretDetails($pretId)
    {
        $db = getDB();

        // Récupérer les informations du prêt
        $query = "SELECT p.*, tp.libelle as type_pret, tp.taux, c.nom as client_nom
                 FROM finance_Pret p
                 JOIN finance_Type_Pret tp ON p.id_type_pret = tp.id
                 JOIN finance_Client_Pret cp ON p.id = cp.id_pret
                 JOIN finance_Clients c ON cp.id_client = c.id
                 WHERE p.id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$pretId]);
        $pret = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$pret) {
            return null;
        }

        // Récupérer l'historique des remboursements
        $query = "SELECT * FROM finance_Historique_Pret WHERE id_pret = ? ORDER BY date_paiement";
        $stmt = $db->prepare($query);
        $stmt->execute([$pretId]);
        $historique = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'pret' => $pret,
            'historique' => $historique
        ];
    }
}
