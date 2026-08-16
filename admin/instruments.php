<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    header('Location: ../login.php');
    exit;
}
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/gestion/instrument.php';

$success = '';
$errors = [];

$inst = new instrument($pdo);

// edition
$editInstrument = null;
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $editInstrument = $inst->getById((int)$_GET['id']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // suppression
    if (isset($_POST['supprimer'])) {
        $inst->delete((int)$_POST['supprimer']);
        $_SESSION['success'] = 'Instrument supprimé.';
        header('Location: instruments.php');
        exit;
    }

    // creation/modification
    if (isset($_POST['nom'])) {
        $instrumentId = isset($_POST['instrument_id']) ? (int)$_POST['instrument_id'] : 0;
        $nom = trim($_POST['nom'] ?? '');
        $image = trim($_POST['image'] ?? '');
        $description = trim($_POST['description'] ?? '');

        if ($nom === '') {
            $errors[] = 'Le nom de l\'instrument est obligatoire.';
        } else {
            if ($instrumentId > 0) {
                $inst->update($instrumentId, $nom, $image, $description);
                $_SESSION['success'] = 'Instrument modifié avec succès.';
            } else {
                $inst->create($nom, $image, $description);
                $_SESSION['success'] = 'Instrument ajouté avec succès.';
            }
            header('Location: instruments.php');
            exit;
        }
    }
}

$instruments = $inst->getAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Instruments | Admin</title>
  <link rel="stylesheet" href="admin.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
</head>
<body>
  <div class="app-shell">
    <aside class="sidebar">
      <div class="brand">
        <div class="brand-mark">D</div>
        <div>
          <h1>DoReMiTendry</h1>
          <p>Back Office</p>
        </div>
      </div>
      <nav>
        <a href="dashboard.php"><i class="fa-solid fa-table-columns"></i> Tableau de bord</a>
        <a href="users.php"><i class="fa-solid fa-users"></i> Utilisateurs</a>
        <a href="courses.php"><i class="fa-solid fa-book-open"></i> Cours</a>
        <a href="modules.php"><i class="fa-solid fa-layer-group"></i> Modules</a>
        <a href="partitions.php"><i class="fa-solid fa-file-lines"></i> Partitions</a>
        <a href="instruments.php" class="active"><i class="fa-solid fa-music"></i> Instruments</a>
        <a href="exercices.php"><i class="fa-solid fa-clipboard-question"></i> Exercices</a>
        <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Déconnexion</a>
      </nav>
    </aside>
    <main class="main">
      <h2 style="margin-top:0;">Gestion des instruments</h2>
      <p class="muted">Ajoutez et modifiez les instruments disponibles.</p>

      <?php if (!empty($_SESSION['success'])) { echo '<div class="success">' . htmlspecialchars($_SESSION['success']) . '</div>'; unset($_SESSION['success']); } ?>
      <?php if (!empty($errors)) { foreach ($errors as $e) echo '<div class="alert">' . htmlspecialchars($e) . '</div>'; } ?>

      <section class="card" style="margin-bottom:20px;">
        <div class="section-title">
          <h3><?= $editInstrument ? 'Modifier l\'instrument' : 'Ajouter un instrument' ?></h3>
        </div>
        <form method="post">
          <?php if ($editInstrument): ?>
            <input type="hidden" name="instrument_id" value="<?= (int)$editInstrument['id'] ?>">
          <?php endif; ?>

          <div class="form-group">
            <label>Nom</label>
            <input type="text" name="nom" value="<?= htmlspecialchars($editInstrument['nom'] ?? '') ?>" required />
          </div>
          <div class="form-group">
            <label>Image (chemin ou URL)</label>
            <input type="text" name="image" value="<?= htmlspecialchars($editInstrument['image'] ?? '') ?>" />
          </div>
          <div class="form-group">
            <label>Description</label>
            <textarea name="description" rows="3"><?= htmlspecialchars($editInstrument['description'] ?? '') ?></textarea>
          </div>
          <button class="btn btn-gold" type="submit">
            <?= $editInstrument ? 'Enregistrer les modifications' : 'Créer l\'instrument' ?>
          </button>
          <?php if ($editInstrument): ?>
            <a href="instruments.php" class="btn" style="margin-left: 10px;">Annuler</a>
          <?php endif; ?>
        </form>
      </section>

      <section class="card">
        <div class="section-title">
          <h3>Liste des instruments</h3>
        </div>
        <table class="table">
          <thead>
            <tr>
              <th>Nom</th>
              <th>Image</th>
              <th>Description</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($instruments as $ins): ?>
              <tr>
                <td><?= htmlspecialchars($ins['nom'] ?? '') ?></td>
                <td><?= htmlspecialchars($ins['image'] ?? '') ?></td>
                <td><?= htmlspecialchars($ins['description'] ?? '') ?></td>
                <td class="action-btn">
                  <form method="POST" style="display:inline;">
                    <input type="hidden" name="supprimer" value="<?= (int)$ins['id'] ?>">
                    <input type="submit" name="suppr" value="Supprimer" class="btn btn-gold" onclick="return confirm('Confirmer la suppression ?')">
                  </form>
                  <a href="instruments.php?id=<?= $ins['id'] ?>" class="btn btn-gold">Modifier</a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </section>
    </main>
  </div>
</body>
</html>