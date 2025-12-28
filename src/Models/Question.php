<?php

require_once(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'Models'.DIRECTORY_SEPARATOR.'Database.php');


class Question {

    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getAllQuestions() {
        $query = "SELECT * FROM questions";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getQuestion($id) {
        $query = "SELECT * FROM questions WHERE id = '$id' ";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getQuestionBy(array $params) {
        $query = "SELECT * FROM questions WHERE ". implode(' AND ',array_map(function($key) {
            return "$key = :$key";
        }, array_keys($params)));

        $stmt = $this->conn->prepare($query);

        foreach ($params as $key => $value) {
            $stmt->bindValue(":$key", $value);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createQuestion($id_questionnaire, $intitule, $id_type, $position, $est_obligatoire) {
        $query = "INSERT INTO questions (id_questionnaire, intitule, id_type, position, est_obligatoire) 
        VALUES (:id_questionnaire, :intitule, :id_type, :position, :est_obligatoire)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id_questionnaire', $id_questionnaire);
        $stmt->bindParam(':intitule', $intitule);
        $stmt->bindParam(':id_type', $id_type);
        $stmt->bindParam(':position', $position);
        $stmt->bindParam(':est_obligatoire', $est_obligatoire);

        $stmt->execute();

        return $this->conn->lastInsertId();
    }

    public function update($id, $intitule, $id_type, $position, $est_obligatoire) {
        $query = "UPDATE questions 
                  SET intitule = :intitule, id_type = :id_type, position = :position, est_obligatoire = :est_obligatoire 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':intitule', $intitule);
        $stmt->bindParam(':id_type', $id_type);
        $stmt->bindParam(':position', $position);
        $stmt->bindParam(':est_obligatoire', $est_obligatoire);
        $stmt->bindParam(':id', $id);

        $stmt->execute();
    }

    public function delete($id) {
        $query = "DELETE FROM questions WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }    


}