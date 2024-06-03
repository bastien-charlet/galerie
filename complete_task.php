<?php
require('./connect.php');

if (isset($_GET['id']) && isset($_POST['completed'])) { // Vérifier si l'ID de la tâche et la valeur 'completed' sont là
    $id = $_GET['id'];
    $completed = ($_POST['completed'] == '1') ? 1 : 0; // Convertir la valeur de 'completed'
    $stmt = $db->prepare("UPDATE galerie SET completed=? WHERE id=?"); // Requête SQL pour mettre à jour la tâche
    $stmt->bind_param("ii", $completed, $id);
    
    if ($stmt->execute()) {
        header("Location: index.php"); // Redirige l'utilisateur vers la page principale après l'insertion
        exit();
    } else {
        echo "Erreur lors de la mise à jour de la tâche : " . $db->error;
    }
} else {
    echo "Erreur";
    exit();
}
?>
