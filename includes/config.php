<?php
session_start();

$host = 'localhost';
$db   = 'libreria_progetto';
$user = 'root';       // di default XAMPP/LAMPP usa root senza password
$pass = '';            // se hai impostato una password per root, mettila qui
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
    die('Errore di connessione al database: ' . $e->getMessage());
}
