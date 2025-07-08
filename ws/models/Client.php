<?php
require_once __DIR__ . '/../db.php';

class Client
{
    public static function getAll() {
        $db = getDB();
        $stmt = $db->query("SELECT * FROM finance_Clients");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getById($id) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM finance_Clients WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function create($data) {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO finance_Clients (nom, email, revenu_mensuel, score_credit) VALUES (?, ?, ?, ?)");
        $stmt->execute([$data->nom, $data->email, $data->revenu_mensuel, $data->score_credit]);
        return $db->lastInsertId();
    }

    public static function update($id, $data) {
        $db = getDB();
        $stmt = $db->prepare("UPDATE finance_Clients SET nom = ?, email = ?, revenu_mensuel = ?, score_credit = ? WHERE id = ?");
        $stmt->execute([$data->nom, $data->email, $data->revenu_mensuel, $data->score_credit, $id]);
    }

    public static function delete($id) {
        $db = getDB();
        $stmt = $db->prepare("DELETE FROM finance_Clients WHERE id = ?");
        $stmt->execute([$id]);
    }
}
?>
