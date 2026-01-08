<?php

declare(strict_types=1);

// Charge les variables d'environnement depuis le fichier .env (si présent)
if (file_exists(__DIR__ . DIRECTORY_SEPARATOR . '.env')) {
	$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
	$dotenv->safeLoad();
}

// Contrôle simple du comportement d'affichage des erreurs via APP_DEBUG (true/false)
$appEnv = getenv('APP_ENV') ?: 'production';
$appDebug = getenv('APP_DEBUG');
if ($appDebug === false) {
	// si non défini, autorise l'affichage en local uniquement
	$appDebug = ($appEnv === 'local') ? 'true' : 'false';
}
if (strtolower($appDebug) === '1' || strtolower($appDebug) === 'true') {
	ini_set('display_errors', '1');
	ini_set('display_startup_errors', '1');
	error_reporting(E_ALL);
} else {
	ini_set('display_errors', '0');
	error_reporting(0);
}
