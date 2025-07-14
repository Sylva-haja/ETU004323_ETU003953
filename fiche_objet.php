<?php
require 'db.php';
session_start();

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Objet invalide.");
}
$id_objet = intval($_GET['id']);

// Détails de l'objet
$sql_objet = "
    SELECT o.nom_objet, c.nom_categorie, m.nom AS nom_membre,
           (SELECT date_retour FROM emprunt WHERE id_objet = o.id_objet AND date_retour IS NULL LIMIT 1) AS en_cours
    FROM objet o
    JOIN categorie_objet c ON o.id_categorie = c.id_categorie
    JOIN membre m ON o.id_membre = m.id_membre
    WHERE o.id_objet = $id_objet
";
$result_objet = mysqli_query($dataBase, $sql_objet);
$objet = mysqli_fetch_assoc($result_objet);
if (!$objet) die("Objet introuvable.");

// Images
$sql_images = "SELECT nom_image, is_principale FROM images_objet WHERE id_objet = $id_objet ORDER BY is_principale DESC, id_image ASC";
$result_images = mysqli_query($dataBase, $sql_images);

// Historique des emprunts
$sql_historique = "
    SELECT m.nom, e.date_emprunt, e.date_retour
    FROM emprunt e
    JOIN membre m ON e.id_membre = m.id_membre
    WHERE e.id_objet = $id_objet
    ORDER BY e.date_emprunt DESC
";
$result_historique = mysqli_query($dataBase, $sql_historique);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <title>Fiche de l'objet</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    body {
      background-color: #0d1b2a;
      color: white;
      min-height: 100vh;
    }
    .container {
      max-width: 900px;
      margin: 50px auto;
      background-color: #1b263b;
      padding: 25px;
      border-radius: 15px;
      box-shadow: 0 6px 12px rgba(0,0,0,0.5);
    }
    .badge-available {
      background-color: #38b000;
    }
    .badge-borrowed {
      background-color: #d00000;
    }
    .image-main {
      width: 100%;
      height: 350px;
      object-fit: cover;
      border-radius: 15px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.4);
      margin-bottom: 20px;
    }
    .images-thumbs {
      gap: 15px;
      margin-bottom: 30px;
    }
    .images-thumbs img {
      width: 120px;
      height: 80px;
      object-fit: cover;
      border-radius: 10px;
      cursor: pointer;
      border: 2px solid transparent;
      transition: border-color 0.3s ease;
    }
    .images-thumbs img:hover,
    .images-thumbs img.active {
      border-color: #38b000;
    }
    .history-list li {
      background-color: #162447;
      margin-bottom: 8px;
      border-radius: 8px;
    }
  </style>
</head>
<body>

<div class="container">
  <a href="liste_objet.php" class="btn btn-outline-light mb-4">← Retour à la liste</a>

  <h2 class="mb-3"><?= htmlspecialchars($objet['nom_objet']) ?></h2>
  <p><strong>Catégorie :</strong> <?= htmlspecialchars($objet['nom_categorie']) ?></p>
  <p><strong>Propriétaire :</strong> <?= htmlspecialchars($objet['nom_membre']) ?></p>
  <p>
    <strong>État :</strong>
    <?php if ($objet['en_cours']): ?>
      <span class="badge badge-borrowed">⛔ Emprunté jusqu’à <?= htmlspecialchars($objet['en_cours']) ?></span>
    <?php else: ?>
      <span class="badge badge-available">✅ Disponible</span>
    <?php endif; ?>
  </p>

  <!-- Images principales et miniatures -->
  <div>
    <?php
      $images = [];
      while ($img = mysqli_fetch_assoc($result_images)) {
          $images[] = $img['nom_image'];
      }
      $main_image = $images[0] ?? 'default.jpg';
    ?>
    <img src="uploads/<?= htmlspecialchars($main_image) ?>" id="mainImage" class="image-main" alt="Image principale">
    <?php if (count($images) > 1): ?>
      <div class="d-flex images-thumbs">
        <?php foreach ($images as $img): ?>
          <img src="uploads/<?= htmlspecialchars($img) ?>" alt="Miniature" onclick="showImage(this)" />
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

  <!-- Historique des emprunts -->
  <h4>Historique des emprunts</h4>
  <?php if (mysqli_num_rows($result_historique) > 0): ?>
    <ul class="list-unstyled history-list p-0">
      <?php while ($hist = mysqli_fetch_assoc($result_historique)) : ?>
        <li class="p-2">
          <strong><?= htmlspecialchars($hist['nom']) ?></strong>
          — du <?= htmlspecialchars($hist['date_emprunt']) ?>
          au <?= $hist['date_retour'] ? htmlspecialchars($hist['date_retour']) : '<em>en cours</em>' ?>
        </li>
      <?php endwhile; ?>
    </ul>
  <?php else: ?>
    <p class="fst-italic text-muted">Aucun historique d’emprunt pour cet objet.</p>
  <?php endif; ?>
</div>

<script>
  function showImage(img) {
    document.getElementById('mainImage').src = img.src;
    // Gestion active sur miniatures
    const thumbs = img.parentElement.querySelectorAll('img');
    thumbs.forEach(t => t.classList.remove('active'));
    img.classList.add('active');
  }
  // Active la première miniature au chargement si plusieurs images
  window.onload = () => {
    const thumbs = document.querySelectorAll('.images-thumbs img');
    if (thumbs.length > 0) {
      thumbs[0].classList.add('active');
    }
  };
