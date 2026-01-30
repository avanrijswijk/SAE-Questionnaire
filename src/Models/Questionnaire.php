<?php

namespace App\Models;

use PDO;

class Questionnaire {

    private $conn;

    /**
     * Constructeur de la classe Questionnaire.
     * Initialise la connexion à la base de données.
     */
    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    /**
     * Récupère tous les questionnaires de la base de données.
     *
     * @return array Liste de tous les questionnaires.
     */
    public function getAllQuestionnaires() {
        $query = "SELECT * FROM questionnaires";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère un questionnaire par son ID.
     *
     * @param int $id ID du questionnaire.
     * @return array|false Les données du questionnaire ou false si non trouvé.
     */
    public function getQuestionnaire($id) {
        $query = "SELECT * FROM questionnaires WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère des questionnaires selon des paramètres donnés.
     *
     * @param array $params Tableau associatif des paramètres de recherche.
     * @return array Liste des questionnaires correspondants.
     */
    public function getQuestionnaireBy(array $params) {
        $query = "SELECT * FROM questionnaires WHERE ". implode(' AND ',array_map(function($key) {
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
     * Crée un nouveau questionnaire dans la base de données.
     * Génère un code unique si nécessaire.
     *
     * @param string $titre Titre du questionnaire.
     * @param int|null $id_createur ID du créateur (défaut 1 si null).
     * @param string $date_expiration Date d'expiration.
     * @param string|null $code Code du questionnaire (généré si null).
     * @return string Dernier ID inséré.
     */
    public function createQuestionnaire($titre, $id_createur, $date_expiration, $code) {
        $query = "INSERT INTO questionnaires (titre, id_createur, date_expiration, date_creation, code) 
        VALUES (:titre, :id_createur, :date_expiration, NOW(), :code)";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':titre', $titre);
        if (is_null($id_createur)) {
            $id_createur = 1; // utilisateur par défaut en attendant la posibiliter de gérer les utilisateurs
        } 
        $stmt->bindParam(':id_createur', $id_createur);
        $stmt->bindParam(':date_expiration', $date_expiration);
        if (is_null($code)) {
            $code = 'ACDC';
        }
        while ($this->existsCode($code)) {
            // génère un code aléatoire de 4 lettres meme si la colonne 'code' n'est pas limité à 4 en vu de potentielles extentions
            $code = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 4); 
        }
        $stmt->bindParam(':code', $code);

        $stmt->execute();

        return $this->conn->lastInsertId();
    }

    /**
     * Met à jour un questionnaire existant.
     *
     * @param int $id ID du questionnaire à mettre à jour.
     * @param string $titre Nouveau titre.
     * @param string $date_expiration Nouvelle date d'expiration.
     */
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

    /**
     * Supprime un questionnaire de la base de données.
     *
     * @param int $id ID du questionnaire à supprimer.
     * @return bool True si la suppression a affecté au moins une ligne, false sinon.
     */
    public function delete($id) {
        $query = "DELETE FROM questionnaires WHERE id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }    

    /**
     * Vérifie si un code de questionnaire existe déjà.
     *
     * @param string $code Code à vérifier.
     * @return bool True si le code existe, false sinon.
     */
    public function existsCode($code) { //true = code exists, false = code n'existe pas
        $query = "SELECT COUNT(*) as count FROM questionnaires WHERE code = :code";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':code', $code);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }

    /**
     * Récupère les questionnaires acceptés par un utilisateur.
     *
     * @param int $id_utilisateur ID de l'utilisateur.
     * @return array Liste des questionnaires acceptés.
     */
    public function getQuestionnairesByUserId($id_utilisateur) {
        $query = "SELECT * FROM questionnaires WHERE id IN (SELECT id_questionnaire FROM acceptes WHERE id_utilisateur = :id_utilisateur)";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_utilisateur', $id_utilisateur);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Récupère les résultats d'un questionnaire (questions et réponses).
     *
     * @param int $id_questionnaire ID du questionnaire.
     * @return array Liste des résultats groupés par question et réponse.
     */
    public function getResults($id_questionnaire) {
        $query = "SELECT qt.intitule ,r.reponse
                  FROM questionnaires qtnaire
                  where qtnaire.id = :id_questionnaire
                  LEFT JOIN questions qt ON qt.id_questionnaire = qtnaire.id
                  left JOIN choix_possible c ON c.id_question = qt.id
                  left JOIN reponses r ON r.id_choix = c.id
                  group BY qt.id, r.id
                  ORDER BY qt.id, r.id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_questionnaire', $id_questionnaire);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Retourne le dernier ID inséré dans la base de données.
     *
     * @return string Dernier ID inséré.
     */
    public function lastInsertId() {
        return $this->conn->lastInsertId();
    }

    /**
     * Récupère le titre d'un questionnaire en remplaçant les espaces par des tirets.
     *
     * @param int $id_questionnaire ID du questionnaire.
     * @return string|null Titre modifié ou null si introuvable.
     */
    public function getTitreWithTireDuSixByID($id_questionnaire) {
        $query = "SELECT titre FROM questionnaires WHERE id = :id_questionnaire";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_questionnaire', $id_questionnaire);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!isset($result['titre'])) {
            echo "Questionnaire introuvable.";
            return;
        } else {
            $titre = $result['titre'];
            $titreDuSix = str_replace(' ', '-', $titre);
            return $titreDuSix;
        }
    }

    /**
     * Récupère l'ID du questionnaire à partir d'un ID de choix de réponse.
     *
     * @param int $id_choix ID du choix.
     * @return int ID du questionnaire ou -1 si non trouvé.
     */
    public function getQuestionnaireFromIdReponse($id_choix) {
        $query = "SELECT q.id_questionnaire
                FROM choix_possible cp, questions q 
                WHERE cp.id = :id_choix 
                AND cp.id_question = q.id
                LIMIT 1;";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_choix', $id_choix, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $id_questionnaire = isset($result['id_questionnaire']) ? (int) $result['id_questionnaire'] : -1;

        return $id_questionnaire;
    }

}