<?php
/**
 * fichier contenant les configurations de la connexion vers la base de donnees
 */

$host = '127.0.0.1';//serveur
$db   = 'doremitendry';//nom base de donnee
$user = 'root'; //utilisateur base de donnee 'mysl -u root -p
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die('Connexion MySQL impossible : ' . $e->getMessage());
}
?>