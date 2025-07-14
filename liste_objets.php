<?php
require 'db.php';
session_start();

// Filtres
$categorie_filter = isset($_GET['categorie']) ? intval($_GET['categorie']) : '';
$search_filter = $_GET['recherche'] ?? '';
$disponible_filter = isset($_GET['disponible']);

// Récupération des catégories
$sql_categories = "SELECT id_categorie, nom_categorie FROM categorie_objet";
$result_categories = mysqli_query($dataBase, $sql_categories);

// Requête principale : on récupère une seule image principale par objet (LEFT JOIN)
$sql = "
    SELECT o.id_objet, o.nom_objet, c.nom_categorie, e.date_retour, i.nom_image AS image_principale
    FROM objet o
    JOIN categorie_objet c ON o.id_categorie = c.id_categorie
    LEFT JOIN emprunt e ON o.id_objet = e.id_objet AND e.date_retour IS NULL
    LEFT JOIN images_objet i ON o.id_objet = i.id_objet AND i.is_principale = 1
    WHERE 1
";

if ($categorie_filter) {
    $sql .= " AND c.id_categorie = $categorie_filter";
}

if (!empty($search_filter)) {
    $safe_search = mysqli_real_escape_string($dataBase, $search_filter);
    $sql .= " AND o.nom_objet LIKE '%$safe_search%'";
}

if ($disponible_filter) {
    $sql .= " AND e.id_emprunt IS NULL"; // e.date_retour IS NULL et emprunt en cours
}

$sql .= " ORDER BY o.nom_objet ASC";

$result = mysqli_query($dataBase, $sql);
if (!$result) {
    die("Erreur SQL : " . mysqli_error($dataBase));
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Liste des objets</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        body {
            background-color: #0d1b2a;
            color: white;
        }
        .container {
            max-width: 1100px;
            margin: 50px auto;
            background-color: #1b263b;
            padding: 25px;
            border-radius: 12px;
        }
        .card {
            background-color: #162447;
            color: white;
            border-radius: 15px;
            box-shadow: 0 6px 12px rgba(0,0,0,0.35);
            transition: transform 0.3s;
            cursor: pointer;
        }
        .card:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 20px rgba(0,0,0,0.6);
        }
        .card-img-top {
            height: 200px;
            object-fit: cover;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
        }
        .status-available {
            color: #38b000;
            font-weight: 700;
        }
        .status-borrowed {
            color: #d00000;
            font-weight: 700;
        }
        form.filter-form .form-control,
        form.filter-form .form-select {
            background-color: #0d1b2a;
            color: white;
            border: 1px solid #38b000;
        }
        form.filter-form label {
            font-weight: 600;
        }
        .btn-primary {
            background-color: #38b000;
            border: none;
        }
        .btn-primary:hover {
            background-color: #2f6c00;
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

    <!-- Formulaire filtres -->
    <form method="GET" class="row g-3 align-items-end mb-4 filter-form">
        <div class="col-md-5">
            <label for="recherche" class="form-label">Nom de l’objet</label>
            <input type="text" id="recherche" name="recherche" class="form-control" value="<?= htmlspecialchars($search_filter) ?>" placeholder="Ex : Perceuse...">
        </div>
        <div class="col-md-4">
            <label for="categorie" class="form-label">Catégorie</label>
            <select id="categorie" name="categorie" class="form-select">
                <option value="">Toutes les catégories</option>
                <?php while ($cat = mysqli_fetch_assoc($result_categories)) : ?>
                    <option value="<?= $cat['id_categorie'] ?>" <?= ($cat['id_categorie'] == $categorie_filter) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['nom_categorie']) ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="col-md-2">
            <div class="form-check mt-4">
                <input type="checkbox" id="disponible" name="disponible" class="form-check-input" <?= $disponible_filter ? 'checked' : '' ?>>
                <label for="disponible" class="form-check-label">Disponible uniquement</label>
            </div>
        </div>
        <div class="col-md-1 d-grid">
            <button type="submit" class="btn btn-primary">Filtrer</button>
        </div>
    </form>

    <!-- Liste des cartes objets -->
    <div class="row g-4">
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($objet = mysqli_fetch_assoc($result)) : ?>
                <?php $image = $objet['image_principale'] ?: 'default.jpg'; ?>
                <div class="col-md-4">
                    <a href="fiche_objet.php?id=<?= $objet['id_objet'] ?>" style="text-decoration:none;">
                    <div class="card h-100">
                        <img src="uploads/<?= htmlspecialchars($image) ?>" class="card-img-top" alt="Image de l'objet <?= htmlspecialchars($objet['nom_objet']) ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($objet['nom_objet']) ?></h5>
                            <p class="card-text"><strong>Catégorie :</strong> <?= htmlspecialchars($objet['nom_categorie']) ?></p>
                            <p class="card-text">
                                <strong>État :</strong>
                                <?= $objet['date_retour'] 
                                    ? '<span class="status-borrowed">⛔ Emprunté jusqu’au ' . htmlspecialchars($objet['date_retour']) . '</span>' 
                                    : '<span class="status-available">✅ Disponible</span>' 
                                ?>
                            </p>
                        </div>
                    </div>
                    </a>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-center w-100">Aucun objet trouvé.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
