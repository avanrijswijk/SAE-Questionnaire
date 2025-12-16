<?php

require_once(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Models'.DIRECTORY_SEPARATOR.'Database.php');


class Questionnaire {

    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getAllQuestionnaires() {
        $query = "SELECT * FROM questionnaires";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getQuestionnaire($id) {
        $query = "SELECT * FROM questionnaires WHERE id = '$id' ";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getQuestionnaireBy(array $params) {
        $query = "SELECT * FROM qusetionnaires WHERE ". implode(' AND ',array_map(function($key) {
            return "$key = :$key";
        }, array_keys($params)));

        $stmt = $this->conn->prepare($query);

        foreach ($params as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createQuestionnaire($titre, $id_createur, $date_expiration, $code) {
        $query = "INSERT INTO questionnaires (titre, id_createur, date_expiration, date_creation, code) 
        VALUES (:titre, :id_createur, :date_expiration, NOW(), :code)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':titre', $titre);
        $stmt->bindParam(':id_createur', $id_createur);
        $stmt->bindParam(':date_expiration', $date_expiration);
        $stmt->bindParam(':code', $code);

        $stmt->execute();

        return $this->conn->lastInsertId();
    }

    public function update($id, $titre, $date_expiration) {
        $query = "UPDATE questionnaires 
                  SET titre = :titre, date_expiration = :date_expiration 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':titre', $titre);
        $stmt->bindParam(':date_expiration', $date_expiration);
        $stmt->bindParam(':id', $id);

        $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM questionnaires WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }    


}