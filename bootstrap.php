<?php

declare(strict_types=1);

// Charge les variables d'environnement depuis le fichier .env (si présent)
if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . '.env')) {
	$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
	$dotenv->safeLoad();
}
