<?php
require('./connect.php'); // Rend disponible la variable `$db` de connexion à la base de données  

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $stmt = $db->prepare("DELETE FROM galerie WHERE id=?"); // Requête SQL pour supprimer la tâche
    $stmt->bind_param("i", $_GET['delete']);

    if ($stmt->execute()) {
        header("Location: index.php"); // Redirige l'utilisateur vers la page principale après l'insertion
        exit();
    } else {
        echo "Erreur lors de la suppression de la tâche : " . $db->error;
    }
} else {
    echo "Erreur";
    exit();
}
?>
