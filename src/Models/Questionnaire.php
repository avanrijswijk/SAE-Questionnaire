<?php

namespace App\Models;

use PDO;
use Exception;

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
    public function getTousLesQuestionnaires() {
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
     * Vérifie si un questionnaire existe déjà par son ID.
     * 
     * @param int $id ID du questionnaire à vérifier.
     * @return bool True si le questionnaire existe, false sinon.
     */
    public function existant($id) {
        $result = $this->getQuestionnaire($id);
        return !empty($result);
    }

    /**
     * Récupère des questionnaires selon des paramètres donnés.
     *
     * @param array $params Tableau associatif des paramètres de recherche.
     * @return array Liste des questionnaires correspondants.
     */
    public function getQuestionnairePar(array $params) {
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
     * @param string|null $id_createur Identifiant du créateur (session/login).
     * @param string $date_expiration Date d'expiration.
     * @param string|null $code Code du questionnaire (généré si null).
     * @param string $groupes_autorises Règles d'accès en JSON.
     * @param int|null $brouillon Nouveau mode (0->brouillon ; 1->publier) Vaut null par defaut
     * @return string Dernier ID inséré.
     */
    public function creerQuestionnaire($titre, $id_createur, $date_expiration, $code, $groupes_autorises, $brouillon=null) {
        $query = "INSERT INTO questionnaires (titre, id_createur, date_expiration, date_creation, code, groupes_autorises". (isset($brouillon) ? ", brouillon" : "") . ") 
        VALUES (:titre, :id_createur, :date_expiration, NOW(), :code, :groupes_autorises" .( isset($brouillon) ? ", :brouillon" : "" ) . ")";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':titre', $titre);
        if (is_null($id_createur)) {
            $id_createur = '';
        }
        $stmt->bindParam(':id_createur', $id_createur);
        $stmt->bindParam(':date_expiration', $date_expiration);
        if (is_null($code)) {
            $code = 'ACDC';
        }
        while ($this->codeExistant($code)) {
            // génère un code aléatoire de 4 lettres meme si la colonne 'code' n'est pas limité à 4 en vu de potentielles extentions
            $code = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 4); 
        }
        $stmt->bindParam(':code', $code);
        $stmt->bindParam(':groupes_autorises', $groupes_autorises);
        if (isset($brouillon)) {
            $stmt->bindParam(':brouillon', $brouillon);
        }
        $stmt->execute();

        return $this->conn->lastInsertId();
    }

    /**
     * Met à jour un questionnaire existant.
     *
     * @param int $id ID du questionnaire à mettre à jour.
     * @param string $titre Nouveau titre.
     * @param string $date_expiration Nouvelle date d'expiration.
     * @param string $groupes_autorises Nouveaux groupes autorisés.
     * @param int|null $brouillon Nouveau mode (0->brouillon ; 1->publier) Vaut null par defaut
     * @return bool Un boolean en fonction de la réussite de la modification
     */
    public function modifier($id, $titre, $date_expiration, $groupes_autorises, $brouillon=null) {
        $query = "UPDATE questionnaires 
                  SET titre = :titre, date_expiration = :date_expiration, groupes_autorises = :groupes_autorises " . (!is_null($brouillon) ? ", brouillon = :brouillon" : "")
                  ." WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':titre', $titre);
        $stmt->bindParam(':date_expiration', $date_expiration);
        $stmt->bindParam(':groupes_autorises', $groupes_autorises);
        if (!is_null($brouillon)) {
            $stmt->bindParam(':brouillon', $brouillon);
        }
        $stmt->bindParam(':id', $id);

        return $stmt->execute();
    }

    /**
     * Supprime un questionnaire de la base de données.
     *
     * @param int $id ID du questionnaire à supprimer.
     * @return bool True si la suppression a affecté au moins une ligne, false sinon.
     */
    public function supprimer($id) {
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
    public function codeExistant($code) { //true = code existe, false = code n'existe pas
        $query = "SELECT COUNT(*) as compteur FROM questionnaires WHERE code = :code";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':code', $code);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['compteur'] > 0;
    }

    /**
     * Récupère les questionnaires acceptés par un utilisateur.
     *
     * @param int $id_utilisateur ID de l'utilisateur.
     * @return array Liste des questionnaires acceptés.
     */
    public function getQuestionnairesParIdUtilisateur($id_utilisateur) {
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
    public function getResultats($id_questionnaire) {
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
     * Récupère le nombre de répondants pour un questionnaire.
     *
     * @param int $id_questionnaire ID du questionnaire.
     * @return int Nombre total de répondants.
     */
    public function getNombreRepondants($id_questionnaire) {
        $query = "SELECT COUNT(DISTINCT r.id_utilisateur) as total_repondants
                  FROM questionnaires
                  JOIN questions qt ON qt.id_questionnaire = questionnaires.id
                  JOIN choix_possible c ON c.id_question = qt.id
                  JOIN reponses_utilisateur r ON r.id_choix = c.id
                  WHERE questionnaires.id = :id_questionnaire";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_questionnaire', $id_questionnaire);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total_repondants'] ?? 0;
    }


    /**
     * Récupère le nombre total de questions pour un questionnaire.
     *
     * @param int $id_questionnaire ID du questionnaire.
     * @return int Nombre total de questions.
     */
    public function getNombreQuestions($id_questionnaire) {
        $query = "SELECT COUNT(*) as total_questions
                  FROM questions
                  WHERE id_questionnaire = :id_questionnaire";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id_questionnaire', $id_questionnaire);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total_questions'] ?? 0;
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
     * Récupère le titre d'un questionnaire en remplaçant les espaces par des tirets.
     *
     * @param int $id_questionnaire ID du questionnaire.
     * @return string|null Titre modifié ou null si introuvable.
     */
    public function getTitreAvecTireParID($id_questionnaire) {
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
     * Récupère un utilisateur par son ID (nom, prénom).
     *
     * @param int|string $id_utilisateur ID de l'utilisateur.
     * @return array|null Données utilisateur ou null si introuvable.
     */
    public function getUtilisateurParId($id_utilisateur) {
        if ($id_utilisateur === null || $id_utilisateur === '') {
            return null;
        }

        $query = "SELECT identifiant, nom, prenom FROM utilisateurs WHERE identifiant = :id_utilisateur";
        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id_utilisateur', $id_utilisateur);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        return $result ?: null;
    }

    /**
     * Récupère l'ID du questionnaire à partir d'un ID de choix de réponse.
     *
     * @param int $id_choix ID du choix.
     * @return int ID du questionnaire ou -1 si non trouvé.
     */
    public function getQuestionnaireParIdReponse($id_choix) {
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


    /**
     * Récupère un questionnaire par son code.
     *
     * @param string $code Code du questionnaire.
     * @return array|null Données du questionnaire ou null si introuvable.
     */
    public function getQuestionnaireParCode($code) {
        $sql = "SELECT * FROM questionnaires WHERE code = :code";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':code', $code);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function dupliquerQuestionnaireComplet($id_original, $nouveauTitre, $estBrouillon = 0) {
        try {
            $this->conn->beginTransaction();

            $stmt = $this->conn->prepare("SELECT * FROM questionnaires WHERE id = :id");
            $stmt->execute(['id' => $id_original]);
            $qOriginal = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$qOriginal) {
                $this->conn->rollBack();
                return false;
            }

            $nouveauCode = substr(str_shuffle("ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, 4);
            while ($this->codeExistant($nouveauCode)) {
                $nouveauCode = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, 4); 
            }

            $sqlInsertQ = "INSERT INTO questionnaires (titre, id_createur, date_expiration, date_creation, groupes_autorises, code, brouillon) 
                           VALUES (:titre, :id_createur, :date_expiration, NOW(), :groupes_autorises, :code, :brouillon)";
            $stmtInsertQ = $this->conn->prepare($sqlInsertQ);
            
            $stmtInsertQ->execute([
                ':titre' => $nouveauTitre,
                ':id_createur' => $qOriginal['id_createur'],
                ':date_expiration' => $qOriginal['date_expiration'],
                ':groupes_autorises' => $qOriginal['groupes_autorises'],
                ':code' => $nouveauCode,
                ':brouillon' => $estBrouillon
            ]);

            $nouvelIdQuestionnaire = $this->conn->lastInsertId();

            $stmtQuestions = $this->conn->prepare("SELECT * FROM questions WHERE id_questionnaire = :id_q");
            $stmtQuestions->execute(['id_q' => $id_original]);
            $questions = $stmtQuestions->fetchAll(PDO::FETCH_ASSOC);

            $sqlInsertQuestion = "INSERT INTO questions (id_questionnaire, intitule, type, est_obligatoire, position) 
                                  VALUES (:id_q, :intitule, :type, :est_obligatoire, :position)";
            $stmtInsertQuestion = $this->conn->prepare($sqlInsertQuestion);

            $sqlSelectChoix = "SELECT * FROM choix_possible WHERE id_question = :id_question";
            $stmtSelectChoix = $this->conn->prepare($sqlSelectChoix);

            $sqlInsertChoix = "INSERT INTO choix_possible (id_question, texte) VALUES (:id_q, :texte)";
            $stmtInsertChoix = $this->conn->prepare($sqlInsertChoix);

            foreach ($questions as $question) {
                
                $stmtInsertQuestion->execute([
                    ':id_q' => $nouvelIdQuestionnaire,
                    ':intitule' => $question['intitule'],
                    ':type' => $question['type'],
                    ':est_obligatoire' => $question['est_obligatoire'] ?? 0,
                    ':position' => $question['position'] ?? 0
                ]);

                $nouvelIdQuestion = $this->conn->lastInsertId();

                $stmtSelectChoix->execute(['id_question' => $question['id']]);
                $choixPossibles = $stmtSelectChoix->fetchAll(PDO::FETCH_ASSOC);

                foreach ($choixPossibles as $choix) {
                    $stmtInsertChoix->execute([
                        ':id_q' => $nouvelIdQuestion,
                        ':texte' => $choix['texte']
                    ]);
                }
            }
            $this->conn->commit();
            return true;

        } catch (Exception $e) {
            $this->conn->rollBack();
            return false;
        }
    }

}