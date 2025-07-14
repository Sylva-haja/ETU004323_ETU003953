<?php
require 'db.php';
session_start();

// Récupérer la catégorie sélectionnée si elle existe
$categorie_filter = $_GET['categorie'] ?? '';

// Récupérer la liste des catégories pour remplir le select
$sql_categories = "SELECT id_categorie, nom_categorie FROM categorie_objet";
$result_categories = mysqli_query($dataBase, $sql_categories);

// Construire la requête selon le filtre
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
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <style>
    body { background-color: #0d1b2a; color: white; }
    .container {
      max-width: 900px;
      margin: 60px auto;
      background-color: #1b263b;
      padding: 30px;
      border-radius: 10px;
    }
    .table thead {
      background-color: #0077b6;
      color: white;
    }
    .table tbody tr {
      background-color: #f8f9fa;
      color: black;
    }
    .filter-form {
      margin-bottom: 20px;
    }
  </style>
</head>
<body>

<nav class="navbar navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="#">TP EXAMEN</a>
    <a class="btn btn-outline-light" href="logout.php">Déconnexion</a>
  </div>
</nav>

<div class="container">
  <h3 class="text-center mb-4">Liste des objets</h3>

  <!-- Formulaire de filtre -->
  <form method="GET" class="filter-form d-flex align-items-center gap-2">
    <label for="categorie" class="form-label mb-0 text-white">Filtrer par catégorie :</label>
    <select name="categorie" id="categorie" class="form-select" style="max-width: 200px;">
      <option value="">Toutes les catégories</option>
      <?php while ($cat = mysqli_fetch_assoc($result_categories)) : ?>
        <option value="<?= $cat['id_categorie'] ?>" <?= ($cat['id_categorie'] == $categorie_filter) ? 'selected' : '' ?>>
          <?= htmlspecialchars($cat['nom_categorie']) ?>
        </option>
      <?php endwhile; ?>
    </select>
    <button type="submit" class="btn btn-primary">Filtrer</button>
    <?php if ($categorie_filter): ?>
      <a href="liste_objet.php" class="btn btn-secondary">Réinitialiser</a>
    <?php endif; ?>
  </form>

  <table class="table table-bordered">
    <thead>
      <tr>
        <th>Nom de l'objet</th>
        <th>Catégorie</th>
        <th>État</th>
      </tr>
    </thead>
    <tbody>
      <?php if (mysqli_num_rows($result) > 0): ?>
        <?php while ($objet = mysqli_fetch_assoc($result)) : ?>
          <tr>
            <td><?= htmlspecialchars($objet['nom_objet']) ?></td>
            <td><?= htmlspecialchars($objet['nom_categorie']) ?></td>
            <td>
              <?= $objet['date_retour'] ? " Emprunté jusqu’au " . htmlspecialchars($objet['date_retour']) : " Disponible" ?>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="3" class="text-center">Aucun objet trouvé pour cette catégorie.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

</body>
</html>
