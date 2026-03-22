<?php

namespace App\Models;

use PDO;

class Question {

    private $conn;

    /**
     * Constructeur de la classe Question.
     * Initialise la connexion à la base de données.
     */
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Récupère toutes les questions de la base de données.
     *
     * @return array|false Les données de la première question ou false si aucune.
     */
    public function getTousLesQuestions() {
        $query = "SELECT * FROM questions";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère une question par son ID.
     *
     * @param int $id ID de la question.
     * @return array|false Les données de la question ou false si non trouvée.
     */
    public function getQuestion($id) {
        $query = "SELECT * FROM questions WHERE id = '$id' ";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * vérifie si une question existe dans la base de données en fonction de son ID.
     * 
     * @param int $id ID de la question à vérifier.
     * @return bool|string True si la question existe, une chaîne d'erreur si plusieurs
     */
    public function existant($id){
        $liste = $this->getQuestion($id);
        $valideur = false;
        if (count($liste) > 1) {
            $valideur = "erreur : plusieurs questions ont le même ID, ce qui ne devrait pas arriver.";
        } else { if (count($liste) == 1) {
            $valideur = true;
        }}
        return $valideur;
    }

    /**
     * Récupère des questions selon des paramètres donnés.
     *
     * @param array $params Tableau associatif des paramètres de recherche.
     * @return array Liste des questions correspondantes.
     */
    public function getQuestionPar(array $params) {
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

    /**
     * Crée une nouvelle question dans la base de données.
     *
     * @param string $intitule Intitulé de la question.
     * @param string $type Type de la question.
     * @param int $position Position de la question.
     * @param bool $est_obligatoire Si la question est obligatoire.
     * @return bool True si la création réussit, false sinon.
     */
    public function creerQuestion( $id_questionnaire, $intitule, $type, $position, $est_obligatoire) {
        $position = $position + 1; // Décalage de position pour éviter les conflits d'affichage
        $query = "INSERT INTO questions (intitule, type, position, est_obligatoire, id_questionnaire)
                VALUES (:intitule, :type, :position, :est_obligatoire, :id_questionnaire)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':intitule', $intitule);
        $stmt->bindParam(':type', $type);
        $stmt->bindParam(':position', $position);
        $stmt->bindParam(':est_obligatoire', $est_obligatoire);
        $stmt->bindParam(':id_questionnaire', $id_questionnaire);

        $stmt->execute();

        return $this->conn->lastInsertId();
    }

    /**
     * Met à jour une question existante.
     *
     * @param int $id ID de la question à mettre à jour.
     * @param string $intitule Nouvel intitulé.
     * @param int $id_type Nouvel ID de type.
     * @param int $position Nouvelle position.
     * @param bool $est_obligatoire Si obligatoire.
     */
    public function modifier($id, $intitule, $id_type, $position, $est_obligatoire) {
        $position = $position + 1; // Décalage de position pour éviter les conflits d'affichage
        $query = "UPDATE questions 
                  SET intitule = :intitule, type = :id_type, position = :position, est_obligatoire = :est_obligatoire 
                  WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':intitule', $intitule);
        $stmt->bindParam(':id_type', $id_type);
        $stmt->bindParam(':position', $position );
        $stmt->bindParam(':est_obligatoire', $est_obligatoire);
        $stmt->bindParam(':id', $id);

        $stmt->execute();
    }

    /**
     * Supprime une question de la base de données.
     *
     * @param int $id ID de la question à supprimer.
     * @return bool True si la suppression a affecté au moins une ligne, false sinon.
     */
    public function supprimer($id) {
        $query = "DELETE FROM questions WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }    
    /**
     * Retourne le dernier ID inséré dans la base de données.
     *
     * @return string Dernier ID inséré.
     */
    public function getIdDerniereInsertion() {
        return $this->conn->lastInsertId();
    }

    /**
     * Supprime toutes les questions d’un questionnaire.
     * La suppression en cascade gère automatiquement les choix et réponses.
     *
     * @param int $id_questionnaire
     */
    public function supprimerQuestionsSansquestionnaire($id_questionnaire) {
        $query = "DELETE FROM questions WHERE id_questionnaire = :id_questionnaire";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_questionnaire', $id_questionnaire, PDO::PARAM_INT);
        $stmt->execute();
    }

}