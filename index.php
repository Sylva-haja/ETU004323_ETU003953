<?php
require 'db.php';
session_start();

$erreur = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $mdp = $_POST['mdp'];

    $sql = "SELECT * FROM membre WHERE email = '$email' AND mdp = '$mdp'";
    $result = mysqli_query($dataBase, $sql);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['user_id'] = $user['id_membre'];
        header("Location: liste_objets.php");
        exit;
    } else {
        $erreur = "Email ou mot de passe incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Connexion</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <style>
    body { background-color: #0d1b2a; color: white; }
    .login-box {
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

<div class="login-box">
  <h3 class="text-center">Connexion</h3>
  <?php if ($erreur): ?>
    <div class="alert alert-danger text-center"><?= $erreur ?></div>
  <?php endif; ?>

  <form method="POST">
    <input class="form-control mb-2" type="email" name="email" placeholder="Email" required>
    <input class="form-control mb-2" type="password" name="mdp" placeholder="Mot de passe" required>
    <button class="btn btn-custom" type="submit">Se connecter</button>
  </form>
  <div class="text-center mt-3">
    <a href="inscription.php">Pas encore de compte ? Inscris-toi</a>
  </div>
</div>

</body>
</html>
