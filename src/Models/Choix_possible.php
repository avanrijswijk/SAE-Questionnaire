<?php

namespace App\Models;

use PDO;

class Choix_possible {

    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getAllChoix() {
        $query = "SELECT * FROM choix_possible";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getChoixDeQuestion($id_question) {
        $query = "SELECT * FROM choix_possible WHERE id_question = :id_question";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_question', $id_question);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getChoixBy(array $params) {
        $query = "SELECT * FROM choix_possible WHERE ". implode(' AND ',array_map(function($key) {
            return "$key = :$key";
        }, array_keys($params)));

        $stmt = $this->conn->prepare($query);

        foreach ($params as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createChoix($id_question, $texte) {
        $query = "INSERT INTO choix_possible (id_question, texte) 
        VALUES (:id_question, :texte)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id_question', $id_question);
        $stmt->bindParam(':texte', $texte);

        $stmt->execute();

        return $this->conn->lastInsertId();
    }

    public function update($id, $texte) {
        $query = "UPDATE choix_possible SET texte = :texte WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':texte', $texte);

        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function delete($id) {
        $query = "DELETE FROM choix_possible WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }

    public function countChoix($id_question) {
        $query = "SELECT COUNT(*) as count FROM choix_possible WHERE id_question = :id_question";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_question', $id_question);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }

    public function getLastInsertId() {
        return $this->conn->lastInsertId();
    }
}