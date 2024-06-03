<?php
require('./connect.php'); // Rend disponible la variable `$db` de connexion à la base de données

// Ajout d'une nouvelle tâche
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];

    // Ajoutez ici le traitement pour les nouvelles colonnes "date" et "image"
    $date = $_POST['date'];

    // Vérifier si un fichier a été téléchargé
    if ($_FILES['image']['name']) {
        $targetDir = "images/"; // Répertoire de destination pour enregistrer les images
        $fileName = basename($_FILES['image']['name']); // Nom de fichier d'origine
        $targetFilePath = $targetDir . $fileName; // Chemin d'accès complet pour enregistrer le fichier

        // Vérifier si le fichier est une image valide
        $imageFileType = strtolower(pathinfo($targetFilePath,PATHINFO_EXTENSION));
        if(!in_array($imageFileType, ['jpg', 'jpeg', 'png', 'gif'])) {
            echo "Désolé, seuls les fichiers JPG, JPEG, PNG et GIF sont autorisés.";
            exit();
        }

        // Déplacer le fichier téléchargé vers le répertoire de destination
        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
            $image = $targetFilePath; // Mettre à jour le chemin de l'image
        } else {
            echo "Une erreur s'est produite lors du téléchargement du fichier.";
            exit();
        }
    } else {
        // Aucun fichier n'a été téléchargé, définir la valeur par défaut de l'image
        $image = 'images/default.png';
    }

    $stmt = $db->prepare("INSERT INTO galerie (title, description, date, completed, image) VALUES (?, ?, ?, 0, ?)");
    $stmt->bind_param("ssss", $title, $description, $date, $image);

    if ($stmt->execute()) {
        header("Location: index.php"); // Redirige l'utilisateur vers la page principale après l'insertion
        exit();
    } else {
        echo "Erreur lors de l'ajout de la tâche : " . $db->error;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galerie de magazines</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Mes magazines</h1>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" enctype="multipart/form-data">
            <label for="title">Titre :</label>
            <input type="text" id="title" name="title" required>
            <br><br>
            <label for="description">Description :</label>
            <input type="text" id="description" name="description"></textarea>
            <br><br>
            <label for="date">Date :</label>
            <input type="date" id="date" name="date" required>
            <br><br>
            <label for="image">Image :</label>
            <input type="file" id="image" name="image" accept="image/*">
            <br><br>
            <input type="submit" name="submit" value="Ajouter un magazine">
        </form>

        <?php
        $result = $db->query("SELECT * FROM galerie ORDER BY date DESC");

        if ($result->num_rows > 0) {
            echo "<div class='galerie-container'>";
            while($galerie = $result->fetch_assoc()) {
                echo "<div class='galerie' style='";
                echo $galerie['completed'] ? "background-color: lightgreen;" : ""; // Applique un fond vert en css par le php
                echo "'>";
                echo "<img src='{$galerie['image']}' alt='Image'>";
                echo "<br>";
                echo "<strong>{$galerie['title']}</strong><br>";
                echo "<p>{$galerie['description']}</p>";

                // Formater la date en "mois année"
                $date = new DateTime($galerie['date']);
                $formattedDate = $date->format('F Y');
                echo "<p>{$formattedDate}</p>";

                echo "<div class='galerie-actions'>";
                echo "<a href='update.php?update=" . $galerie['id'] . "' class='edit'>Modifier</a>";
                echo "<a href='delete.php?delete=" . $galerie['id'] . "' class='delete' onclick='return confirm(\"Êtes-vous sûr de vouloir supprimer cette tâche ?\")'>Supprimer</a>"; // Confirmation en js par le php
                echo "</div>";
                echo "</div>";
            }
            echo "</div>";
        } else {
            echo "Aucune tâche trouvée...";
        }
        ?>

    </div>
</body>
</html>

<?php
$db->close();
?>