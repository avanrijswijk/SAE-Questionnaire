<?php

declare(strict_types=1);

// Load environment variables with precedence: .env.<APP_ENV> -> .env -> .env.local
// Strategy: if .env exists and contains APP_ENV, prefer .env.<APP_ENV> when present.
$root = __DIR__;
$envToLoad = null;
if (file_exists($root . DIRECTORY_SEPARATOR . '.env')) {
	// try to read APP_ENV from .env
	$contents = @file_get_contents($root . DIRECTORY_SEPARATOR . '.env');
	if ($contents !== false) {
		if (preg_match('/^\s*APP_ENV\s*=\s*(\w+)\s*$/m', $contents, $m)) {
			$candidate = $m[1];
			$candidatePath = $root . DIRECTORY_SEPARATOR . '.env.' . $candidate;
			if (file_exists($candidatePath)) {
				$envToLoad = $candidatePath;
			}
		}
	}
}
// fallback order if nothing chosen yet
if ($envToLoad === null) {
	$local = $root . DIRECTORY_SEPARATOR . '.env.local';
	$prod = $root . DIRECTORY_SEPARATOR . '.env.production';
	$default = $root . DIRECTORY_SEPARATOR . '.env';
	if (file_exists($local)) {
		$envToLoad = $local;
	} elseif (file_exists($prod)) {
		$envToLoad = $prod;
	} elseif (file_exists($default)) {
		$envToLoad = $default;
	}
}

if ($envToLoad !== null) {
	$dotenv = Dotenv\Dotenv::createImmutable($root, basename($envToLoad));
	$dotenv->safeLoad();
	// Propagate to getenv()/\$_SERVER to cover different SAPIs
	foreach ($_ENV as $k => $v) {
		if (!is_string($v) && !is_numeric($v)) {
			continue;
		}
		putenv(sprintf('%s=%s', $k, $v));
		if (!isset($_SERVER[$k])) {
			$_SERVER[$k] = $v;
		}
	}
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
