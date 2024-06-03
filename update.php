<?php
require('./connect.php');

if (isset($_GET['update'])) {
    $stmt = $db->prepare("SELECT * FROM galerie WHERE id=?"); // Requête SQL pour récupérer la tâche par son 'id'
    $stmt->bind_param("i", $_GET['update']);
    $stmt->execute();

    $result = $stmt->get_result(); // Récupère la tâche correspondante
    if ($result->num_rows > 0) {
        $galerie = $result->fetch_assoc(); // Stocke la tâche dans la variable 'galerie'
    } else {
        echo "Tâche non trouvée.";
        exit();
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
        $title = $_POST['title'];
        $description = $_POST['description'];
        $completed = isset($_POST['completed']) ? 1 : 0;
        $date = $_POST['date'];
        $imagePath = $galerie['image'];

        // Vérifier si un nouveau fichier a été téléchargé
        if ($_FILES['image']['name']) {
            $targetDir = "images/"; // Répertoire de destination pour enregistrer les images
            $fileName = basename($_FILES['image']['name']); // Nom de fichier d'origine
            $targetFilePath = $targetDir . $fileName; // Chemin d'accès complet pour enregistrer le fichier

            // Déplacer le fichier téléchargé vers le répertoire de destination
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
                $imagePath = $targetFilePath; // Mettre à jour le chemin de l'image
            } else {
                echo "Une erreur s'est produite lors du téléchargement du fichier.";
            }
        }

        $stmt = $db->prepare("UPDATE galerie SET title=?, description=?, completed=?, date=?, image=? WHERE id=?"); // Requête SQL pour modifier la tâche
        $stmt->bind_param("ssisss", $title, $description, $completed, $date, $imagePath, $_GET['update']);

        if ($stmt->execute()) {
            header("Location: index.php"); // Redirige l'utilisateur vers la page principale après l'insertion
            exit();
        } else {
            echo "Erreur lors de la mise à jour de la tâche : " . $db->error;
        }
    }
} else {
    echo "Erreur";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier une tâche</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Modifier une tâche</h1>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?update=" . $_GET['update']); ?>" enctype="multipart/form-data">
            <label for="title">Titre :</label>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($galerie['title']); ?>" required>
            <br><br>
            <label for="description">Description :</label>
            <input type="text" id="description" name="description" value="<?php echo htmlspecialchars($galerie['description']); ?>">
            <br><br>
            <label for="date">Date :</label>
            <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($galerie['date']); ?>" required>
            <br><br>
            <label for="completed">Acheté :</label>
            <input type="checkbox" id="completed" name="completed" <?php echo $galerie['completed'] ? 'checked' : ''; ?>>
            <br><br>
            <label for="image">Image :</label>
            <input type="file" id="image" name="image">
            <br><br>
            <input type="submit" name="submit" value="Mettre à jour la tâche">
        </form>
    </div>
</body>
</html>