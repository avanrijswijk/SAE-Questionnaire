<?php

namespace App\Models;

use PDO;

class Reponses_utilisateur {

    private $conn;

    /**
     * Constructeur de la classe Reponses_utilisateur.
     * Initialise la connexion à la base de données.
     */
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Récupère toutes les réponses des utilisateurs.
     *
     * @return array Liste de toutes les réponses.
     */
    public function getAllReponses() {
        $query = "SELECT * FROM reponses_utilisateur";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère une réponse spécifique d'un utilisateur pour un choix.
     *
     * @param int $id_utilisateur L'identifiant de l'utilisateur.
     * @param int $id_choix L'identifiant du choix.
     * @return array Les données de la réponse.
     */
    public function getReponse($id_utilisateur, $id_choix) {
        $query = "SELECT * FROM reponses_utilisateur WHERE id_utilisateur = :id_utilisateur AND id_choix = :id_choix";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_utilisateur', $id_utilisateur);
        $stmt->bindParam(':id_choix', $id_choix);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les réponses basées sur des paramètres donnés.
     *
     * @param array $params Tableau associatif des paramètres de recherche.
     * @return array Liste des réponses correspondant aux paramètres.
     */
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

    /**
     * Crée une nouvelle réponse d'utilisateur pour un choix.
     *
     * @param int $id_utilisateur L'identifiant de l'utilisateur.
     * @param int $id_choix L'identifiant du choix.
     * @param string $reponse La réponse donnée.
     * @return bool Vrai si l'insertion a réussi, faux sinon.
     */
    public function createReponse($id_utilisateur, $id_choix, $reponse) {
        $query = "INSERT INTO reponses_utilisateur (id_utilisateur, id_choix, reponse) 
        VALUES (:id_utilisateur, :id_choix, :reponse)";
       
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id_utilisateur', $id_utilisateur);
        $stmt->bindParam(':id_choix', $id_choix);
        $stmt->bindParam(':reponse', $reponse);
        return $stmt->execute();
    }

    /**
     * Supprime une réponse d'utilisateur pour un choix.
     *
     * @param int $id_utilisateur L'identifiant de l'utilisateur.
     * @param int $id_choix L'identifiant du choix.
     * @return bool Vrai si la suppression a réussi, faux sinon.
     */
    public function delete($id_utilisateur, $id_choix) {
        $query = "DELETE FROM reponses_utilisateur WHERE id_utilisateur = :id_utilisateur AND id_choix = :id_choix";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_utilisateur', $id_utilisateur);
        $stmt->bindParam(':id_choix', $id_choix);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }

    /**
     * Récupère les réponses pour un questionnaire au format CSV.
     * Joint les tables pour obtenir les questions, choix et réponses.
     *
     * @param int $id_questionnaire L'identifiant du questionnaire.
     * @return array Liste des réponses formatées pour CSV.
     */
    public function getReponseForCSV($id_questionnaire) {
        $query = "SELECT qt.intitule as question, c.texte as choix, r.reponse
                FROM questionnaires qtnaire
                JOIN questions qt ON qt.id_questionnaire = qtnaire.id 
                JOIN choix_possible c ON c.id_question = qt.id
                JOIN reponses_utilisateur r ON r.id_choix = c.id
                WHERE qtnaire.id = :id_questionnaire
                ORDER BY r.id_choix, r.id_utilisateur";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_questionnaire', $id_questionnaire);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère toutes les réponses pour un questionnaire spécifique.
     * Joint les tables pour obtenir les réponses liées au questionnaire.
     *
     * @param int $id_questionnaire L'identifiant du questionnaire.
     * @return array Liste des réponses pour le questionnaire.
     */
    public function getReponseByQuestionnaryId($id_questionnaire) {
        $query = "SELECT r.* 
                FROM questionnaires qtnaire
                JOIN questions qt ON qt.id_questionnaire = qtnaire.id 
                JOIN choix_possible c ON c.id_question = qt.id
                JOIN reponses_utilisateur r ON r.id_choix = c.id
                WHERE qtnaire.id = :id_questionnaire
                ORDER BY r.id_choix, r.id_utilisateur";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_questionnaire', $id_questionnaire);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère l'identifiant de la dernière insertion.
     *
     * @return int L'identifiant de la dernière insertion.
     */
    public function lastInsertId() {
        return $this->conn->lastInsertId();
    }

}