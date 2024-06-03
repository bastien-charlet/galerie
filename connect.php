<?php
$servername = "localhost"; // On travaille en local
$username = "root"; // "root" par défaut sous MAMP, XAMPP et WAMP
$password = ""; // "" sous WAMP
$dbname = "galerie"; // Nom de la base de données MyS

// Créé la connexion à la base
$db = new mysqli($servername, $username, $password, $dbname);

// Vérifie la connexion
if ($db->connect_error) {
    die("La connexion à la base de données a échoué : " . $db->connect_error);
}
?>
