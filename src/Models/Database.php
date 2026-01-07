<?php

namespace App\Models;

use PDO;
use PDOException;

Class Database 
{ 
    private $host = "localhost"; 
    private $db_name = "questionnaire_app"; 
    private $username = "root"; 
    private $password = ""; 
    private $conn; 

    public function getConnection() { 
        if ($this->conn === null) {
            try {
                $this->conn = new PDO("mysql:host=" . $this->host . ";port=3307;dbname=" . $this->db_name, $this->username, $this->password);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $exception) {
                echo "Erreur de connexion : " . $exception->getMessage();
            }
        }

        return $this->conn;
    } 
}