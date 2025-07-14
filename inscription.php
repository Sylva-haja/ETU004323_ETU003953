<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = $_POST['nom'];
    $email = $_POST['email'];
    $mdp = $_POST['mot_de_passe'];

    $sql = "INSERT INTO membre (nom, date_naissance, genre, email, ville, mdp) 
            VALUES ('$nom', '2000-01-01', 'H', '$email', 'Antananarivo', '$mdp')";
    mysqli_query($dataBase, $sql);

    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Inscription</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <style>
    body { background-color: #0d1b2a; color: white; }
    .form-box {
      max-width: 400px;
      margin: 100px auto;
      padding: 30px;
      background-color: #1b263b;
      border-radius: 10px;
    }
    .btn-custom { background-color: #0077b6; color: white; width: 100%; }
    .btn-custom:hover { background-color: #0096c7; }
    .form-control { background-color: #f8f9fa; }
  </style>
</head>
<body>

<nav class="navbar navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">TP EXAMEN</a>
  </div>
</nav>

<div class="form-box">
  <h3 class="text-center">Inscription</h3>
  <form method="POST">
    <input class="form-control mb-2" type="text" name="nom" placeholder="Nom" required>
    <input class="form-control mb-2" type="email" name="email" placeholder="Email" required>
    <input class="form-control mb-2" type="password" name="mot_de_passe" placeholder="Mot de passe" required>
    <button class="btn btn-custom" type="submit">S'inscrire</button>
  </form>
  <div class="text-center mt-3">
    <a href="index.php">Déjà un compte ? Se connecter</a>
  </div>
</div>

</body>
</html>
