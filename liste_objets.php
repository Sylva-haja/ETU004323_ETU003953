<?php
require 'db.php';
session_start();

$categorie_filter = $_GET['categorie'] ?? '';

$sql_categories = "SELECT id_categorie, nom_categorie FROM categorie_objet";
$result_categories = mysqli_query($dataBase, $sql_categories);

if ($categorie_filter && is_numeric($categorie_filter)) {
    $sql = "SELECT o.nom_objet, c.nom_categorie, e.date_retour 
            FROM objet o 
            JOIN categorie_objet c ON o.id_categorie = c.id_categorie 
            LEFT JOIN emprunt e ON o.id_objet = e.id_objet
            WHERE c.id_categorie = $categorie_filter";
} else {
    $sql = "SELECT o.nom_objet, c.nom_categorie, e.date_retour 
            FROM objet o 
            JOIN categorie_objet c ON o.id_categorie = c.id_categorie 
            LEFT JOIN emprunt e ON o.id_objet = e.id_objet";
}

$result = mysqli_query($dataBase, $sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Liste des objets</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #0d1b2a;
      color: white;
    }
    .container {
      max-width: 1000px;
      margin: 60px auto;
      background-color: #1b263b;
      padding: 30px;
      border-radius: 10px;
    }
    .filter-form {
      margin-bottom: 20px;
    }
    .card {
      background-color: #162447;
      color: white;
      margin-bottom: 20px;
      border-radius: 10px;
      box-shadow: 0 4px 6px rgba(0,0,0,0.3);
      transition: transform 0.2s;
    }
    .card:hover {
      transform: scale(1.03);
    }
    .card-body {
      padding: 15px;
    }
    .status-available {
      color: #38b000; /* vert */
      font-weight: bold;
    }
    .status-borrowed {
      color: #d00000; /* rouge */
      font-weight: bold;
    }
  </style>
</head>
<body>

<nav class="navbar navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">EmpruntObjets</a>
    <a class="btn btn-outline-light" href="logout.php">Déconnexion</a>
  </div>
</nav>

<div class="container">
  <h3 class="text-center mb-4">Liste des objets</h3>

  <!-- Formulaire de filtre -->
  <form method="GET" class="filter-form d-flex align-items-center gap-2 flex-wrap">
    <label for="categorie" class="form-label mb-0 text-white">Filtrer par catégorie :</label>
    <select name="categorie" id="categorie" class="form-select" style="max-width: 250px;">
      <option value="">Toutes les catégories</option>
      <?php while ($cat = mysqli_fetch_assoc($result_categories)) : ?>
        <option value="<?= $cat['id_categorie'] ?>" <?= ($cat['id_categorie'] == $categorie_filter) ? 'selected' : '' ?>>
          <?= htmlspecialchars($cat['nom_categorie']) ?>
        </option>
      <?php endwhile; ?>
    </select>
    <button type="submit" class="btn btn-primary">Filtrer</button>
    <?php if ($categorie_filter): ?>
    <?php endif; ?>
  </form>

  <div class="row">
    <?php if (mysqli_num_rows($result) > 0): ?>
      <?php while ($objet = mysqli_fetch_assoc($result)) : ?>
        <div class="col-md-4">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title"><?= htmlspecialchars($objet['nom_objet']) ?></h5>
              <p class="card-text"><strong>Catégorie :</strong> <?= htmlspecialchars($objet['nom_categorie']) ?></p>
              <p class="card-text">
                <strong>État :</strong>
                <?php if ($objet['date_retour']): ?>
                  <span class="status-borrowed"> Emprunté jusqu’au <?= htmlspecialchars($objet['date_retour']) ?></span>
                <?php else: ?>
                  <span class="status-available"> Disponible</span>
                <?php endif; ?>
              </p>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p class="text-center w-100">Aucun objet trouvé pour cette catégorie.</p>
    <?php endif; ?>
  </div>
</div>

</body>
</html>
