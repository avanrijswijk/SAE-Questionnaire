<?php


Class Database 
{ 
    private $host = "localhost"; 
    private $db_name = "questionnaire_app"; 
    private $username = "app_user"; 
    private $password = "mot_de_passe_tres_securise_user_87!"; 
    private $conn; 

    public function getConnection() { 
        $this->conn == null;
        try { 
            $this->conn = new PDO("mysql:host=" . $this->host . ";port=3306;dbname=" . $this->db_name, $this->username, $this->password); 
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
            
        } catch (PDOException $exception) { 
            echo "Erreur de connexion : " . $exception->getMessage(); 
        } 
        return $this->conn; 
    } 
}