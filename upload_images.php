<?php
require 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Méthode non autorisée.");
}

if (!isset($_POST['id_objet']) || !is_numeric($_POST['id_objet'])) {
    die("ID d'objet invalide.");
}
$id_objet = intval($_POST['id_objet']);
$set_principale = isset($_POST['set_principale']);

$upload_dir = __DIR__ . "/uploads/";

// Vérification des fichiers
if (!isset($_FILES['images'])) {
    die("Aucun fichier uploadé.");
}

$errors = [];
for ($i = 0; $i < count($_FILES['images']['name']); $i++) {
    $error = $_FILES['images']['error'][$i];
    if ($error !== UPLOAD_ERR_OK) {
        $errors[] = "Erreur à l'upload du fichier n°" . ($i+1) . " (code $error)";
    }
}

if ($errors) {
    foreach ($errors as $e) {
        echo "<p>$e</p>";
    }
    exit;
}

// Si image principale doit être changée, on la "désactive"
if ($set_principale) {
    $sql_reset = "UPDATE images_objet SET is_principale = 0 WHERE id_objet = ?";
    $stmt_reset = $dataBase->prepare($sql_reset);
    $stmt_reset->bind_param("i", $id_objet);
    $stmt_reset->execute();
    $stmt_reset->close();
}

for ($i = 0; $i < count($_FILES['images']['name']); $i++) {
    $tmp_name = $_FILES['images']['tmp_name'][$i];
    $original_name = basename($_FILES['images']['name'][$i]);

    // Générer un nom unique pour éviter écrasement
    $ext = pathinfo($original_name, PATHINFO_EXTENSION);
    $new_name = uniqid('img_') . '.' . $ext;
    $target_path = $upload_dir . $new_name;

    if (move_uploaded_file($tmp_name, $target_path)) {
        $is_principale = ($set_principale && $i === 0) ? 1 : 0;

        $sql_insert = "INSERT INTO images_objet (id_objet, nom_image, is_principale) VALUES (?, ?, ?)";
        $stmt = $dataBase->prepare($sql_insert);
        $stmt->bind_param("isi", $id_objet, $new_name, $is_principale);
        $stmt->execute();
        $stmt->close();
    } else {
        echo "<p>Erreur lors du déplacement du fichier $original_name</p>";
    }
}

header("Location: fiche_objet.php?id=$id_objet");
exit;
