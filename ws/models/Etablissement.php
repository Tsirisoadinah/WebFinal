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
