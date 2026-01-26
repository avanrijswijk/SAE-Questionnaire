<?php

namespace App\Models;

use PDO;
use PDOException;

Class Database 
{ 
    private $conn;

    public function getConnection()
    {
        if ($this->conn === null) {
            // Lire les variables d'environnement avec des valeurs par défaut.
            // Certaines installations remplissent $_ENV mais getenv() peut être vide,
            // donc on vérifie dans cet ordre : getenv -> $_ENV -> $_SERVER -> default.
            $get = function ($key, $default = null) {
                $val = getenv($key);
                if ($val !== false && $val !== null && $val !== '') {
                    return $val;
                }
                if (isset($_ENV[$key]) && $_ENV[$key] !== '') {
                    return $_ENV[$key];
                }
                if (isset($_SERVER[$key]) && $_SERVER[$key] !== '') {
                    return $_SERVER[$key];
                }
                return $default;
            };

            // Try to read values from environment (may be empty for web workers until dotenv loaded)
            $host = $get('DB_HOST', null);
            $port = $get('DB_PORT', null);
            $dbName = $get('DB_NAME', null);
            $username = $get('DB_USER', null);
            $password = $get('DB_PASS', null);

            // If essential DB vars are missing, attempt to load .env from project root (helpful for web workers)
            if (empty($host) || empty($username) || empty($dbName)) {
                $possibleRoot = realpath(__DIR__ . '/../../');
                if ($possibleRoot && file_exists($possibleRoot . DIRECTORY_SEPARATOR . '.env') && class_exists('Dotenv\\Dotenv')) {
                    try {
                        \Dotenv\Dotenv::createImmutable($possibleRoot)->safeLoad();
                        // repopulate variables from environment
                        $host = $get('DB_HOST', $host);
                        $port = $get('DB_PORT', $port);
                        $dbName = $get('DB_NAME', $dbName);
                        $username = $get('DB_USER', $username);
                        $password = $get('DB_PASS', $password);
                    } catch (\Throwable $e) {
                        // ignore and continue
                    }
                }
            }

            // Apply sensible defaults for local/dev when still missing
            $serverName = isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : null;
            $localServers = array('127.0.0.1', '::1', 'localhost');
            if (empty($host) && in_array($serverName, $localServers, true)) {
                $host = '127.0.0.1';
            }
            $port = $port ?: '3306';
            $dbName = $dbName ?: 'questionnaire_app';
            $username = $username ?: 'root';
            $password = $password ?: '';

            $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $dbName);

            try {
                $this->conn = new PDO($dsn, $username, $password);
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $exception) {
                error_log('Erreur de connexion DB: ' . $exception->getMessage());
                throw new \RuntimeException('Erreur de connexion à la base de données. Voir logs pour détails.');
            }
        }

        return $this->conn;
    }
}