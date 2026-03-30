<?php

function getDB(): PDO
{
	static $pdo = null;

	if ($pdo instanceof PDO) {
		return $pdo;
	}

	$host = getenv('DB_HOST') ?: '127.0.0.1';
	$port = getenv('DB_PORT') ?: '3306';
	$name = getenv('DB_NAME') ?: 'guerre_iran';
	$user = getenv('DB_USER') ?: 'app';
	$pass = getenv('DB_PASSWORD') ?: 'app_password';

	$dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $host, $port, $name);

	$pdo = new PDO($dsn, $user, $pass, [
		PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
		PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
	]);

	return $pdo;
}

