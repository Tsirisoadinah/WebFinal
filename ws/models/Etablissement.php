<?php
require_once __DIR__ . '/../db.php';

class Etablissement
{

    public static function insertFondsIntoTransaction($id, $id_type_transactions, $montant)
    {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO finance_Transaction (id_ef, id_type_transaction, date_transaction,montant) VALUES (?, ?, NOW(),?)");
        $stmt->execute([$id, $id_type_transactions, $montant]);
    }


    public static function getFondsByIdEtablissement($id_ef)
    {
        $db = getDB();
        $stmt = $db->prepare("SELECT fonds FROM finance_Etablissement_Financier WHERE id = ?");
        $stmt->execute([$id_ef]);

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result || !isset($result['fonds'])) {
            return 0;
        }

        return (float) $result['fonds']; // Cast explicite pour éviter les objets ou erreurs
    }


    public static function getPretsEntreDates($dateDebut, $dateFin)
    {
        $db = getDB();

        $query = "SELECT p.id, p.montant_pret, p.montant_mensuel, p.date_pret, p.date_fin, 
                     p.assurance, p.delai, tp.libelle AS type_pret
              FROM finance_Pret p
              JOIN finance_Type_Pret tp ON p.id_type_pret = tp.id
              WHERE p.date_pret >= ? AND p.date_pret <= ?
              ORDER BY p.date_pret ASC";

        $stmt = $db->prepare($query);
        $stmt->execute([$dateDebut, $dateFin]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public static function getSommeHistoriquePretEntreDates($dateDebut, $dateFin)
    {
        $db = getDB();
        $query = "SELECT SUM(hp.capital + hp.interet) as total
                FROM finance_Historique_Pret hp
                JOIN finance_Pret p ON hp.id_pret = p.id
                WHERE hp.date_paiement >= ? AND hp.date_paiement <= ?";

        $stmt = $db->prepare($query);
        $stmt->execute([$dateDebut, $dateFin]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result['total'] ? floatval($result['total']) : 0;
    }


    public static function getSituationMensuelleParDate($dateDebut, $dateFin, $id_ef)
    {
        $db = getDB();
        $fondsInitial = self::getFondsByIdEtablissement($id_ef);
        $resultats = [];

        $moisCourant = new DateTime($dateDebut);
        $fin = new DateTime($dateFin);
        $fin->modify('last day of this month');

        $fondsRestant = $fondsInitial;

        while ($moisCourant <= $fin) {
            $debutMois = $moisCourant->format('Y-m-01');
            $finMois = $moisCourant->format('Y-m-t');

            // Prêts accordés ce mois
            $prets = self::getPretsEntreDates($debutMois, $finMois);
            $sommePrets = 0;
            foreach ($prets as $pret) {
                $sommePrets += floatval($pret['montant_pret']);
            }

            // Remboursements effectués ce mois (capital + intérêt)
            $stmt = $db->prepare("
            SELECT SUM(capital) AS total_capital, SUM(interet) AS total_interet
            FROM finance_Historique_Pret
            WHERE date_paiement >= ? AND date_paiement <= ?
        ");
            $stmt->execute([$debutMois, $finMois]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            $capital = floatval($row['total_capital'] ?? 0);
            $interet = floatval($row['total_interet'] ?? 0);

            $fondsRestant += $capital + $interet;
            $fondsRestant -= $sommePrets;

            $resultats[] = [
                'annee' => $moisCourant->format('Y'),
                'mois' => $moisCourant->format('m'),
                'fonds_initial' => $fondsInitial,
                'prets_accordes' => $sommePrets,
                'total_interet' => $interet,
                'total_capital' => $capital,
                'reste_non_emprunte' => max(0, $fondsRestant),
                'montant_total' => $capital + $interet,
            ];

            $moisCourant->modify('+1 month');
        }

        return $resultats;
    }



    public static function updateFonds($id_ef, $montant)
    {
        $db = getDB();
        $montant_actuel = self::getFondsByIdEtablissement($id_ef);

        if ($montant_actuel == 0) {
            // Nouvelle entrée
            $stmt = $db->prepare("INSERT INTO finance_Etablissement_Financier (id, fonds) VALUES (?, ?)");
            $stmt->execute([$id_ef, $montant]);
        } else {
            // Mise à jour du fonds existant
            $nouveau_fonds = $montant_actuel + $montant;
            $stmt = $db->prepare("UPDATE finance_Etablissement_Financier SET fonds = ? WHERE id = ?");
            $stmt->execute([$nouveau_fonds, $id_ef]);
        }

        // Enregistrement de la transaction après mise à jour
        self::insertFondsIntoTransaction($id_ef, 1, $montant);
    }
}
