<?php
require_once __DIR__ . '/../db.php';

class TypePret {
    public static function getAll() {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM finance_Type_Pret");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM finance_Type_Pret WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data) {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO finance_Type_Pret (libelle, taux, duree_max) VALUES (?, ?, ?)");
        $stmt->execute([$data->libelle, $data->taux, $data->duree_max]);
        return $db->lastInsertId();
    }

    public static function update($id, $data) {
        $db = getDB();
        $stmt = $db->prepare("UPDATE finance_Type_Pret SET libelle = ?, taux = ?, duree_max = ? WHERE id = ?");
        $stmt->execute([$data->libelle, $data->taux, $data->duree_max, $id]);
    }

    public static function delete($id) {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM finance_Type_Pret WHERE id = ?");
        $stmt->execute([$id]);
    }
}