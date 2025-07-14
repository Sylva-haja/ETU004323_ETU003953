<?php
require 'db.php';
session_start();
$id_objet = $_GET['id']; // l’objet auquel on ajoute des images
?>

<h3>Ajouter des images à l’objet</h3>
<form action="upload_image.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="id_objet" value="<?= $id_objet ?>">
    <input type="file" name="images[]" multiple required>
    <label>
        <input type="checkbox" name="set_principale" value="1">
        Définir la première image comme principale
    </label>
    <br>
    <button type="submit">Envoyer</button>
</form>
