<?php

namespace App\Models;

use PDO;

class Reponses_utilisateur {

    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getAllReponses() {
        $query = "SELECT * FROM reponses_utilisateur";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getReponse($id_utilisateur, $id_choix) {
        $query = "SELECT * FROM reponses_utilisateur WHERE id_utilisateur = :id_utilisateur AND id_choix = :id_choix";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_utilisateur', $id_utilisateur);
        $stmt->bindParam(':id_choix', $id_choix);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getReponseBy(array $params) {
        $query = "SELECT * FROM reponses_utilisateur WHERE ". implode(' AND ',array_map(function($key) {
            return "$key = :$key";
        }, array_keys($params)));

        $stmt = $this->conn->prepare($query);

        foreach ($params as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createReponse($id_utilisateur, $id_choix, $reponse) {
        $query = "INSERT INTO reponses_utilisateur (id_utilisateur, id_choix, reponse) 
        VALUES (:id_utilisateur, :id_choix, :reponse)";
       
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id_utilisateur', $id_utilisateur);
        $stmt->bindParam(':id_choix', $id_choix);
        $stmt->bindParam(':reponse', $reponse);

        return $stmt->execute();
    }

    public function delete($id_utilisateur, $id_choix) {
        $query = "DELETE FROM reponses_utilisateur WHERE id_utilisateur = :id_utilisateur AND id_choix = :id_choix";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_utilisateur', $id_utilisateur);
        $stmt->bindParam(':id_choix', $id_choix);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }    

    public function getReponseByQuestionnaryId($id_questionnaire) {
        $query = "SELECT rep.* FROM questionnaires qtnaire WHERE id = :id_questionnaire
                  LEFT JOIN questions q ON q.id_questionnaire = qtnaire.id
                  LEFT JOIN choix_possible c ON c.id_question = q.id
                  LEFT JOIN reponses_utilisateur rep ON rep.id_choix = c.id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_questionnaire', $id_questionnaire);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function lastInsertId() {
        return $this->conn->lastInsertId();
    }

}