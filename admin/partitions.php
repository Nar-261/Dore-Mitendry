<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    header('Location: ../login.php');
    exit;
}
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/gestion/partition.php';
require_once __DIR__ . '/../config/gestion/cours.php';

$success = '';
$errors = [];

$partModel = new partition($pdo);
$coursModel = new Cours($pdo);

$editPart = null;
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $editPart = $partModel->getById((int)$_GET['id']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['supprimer'])) {
        $partModel->delete((int)$_POST['supprimer']);
        $_SESSION['success'] = 'Partition supprimée.';
        header('Location: partitions.php');
        exit;
    }

    if (isset($_POST['titre'])) {
        $partId = isset($_POST['part_id']) ? (int)$_POST['part_id'] : 0;
        $titre = trim($_POST['titre'] ?? '');
        $fichier = trim($_POST['fichier'] ?? '');
        $cours_id = isset($_POST['cours_id']) && is_numeric($_POST['cours_id']) ? (int)$_POST['cours_id'] : 0;

        if ($titre === '' || $cours_id <= 0) {
            $errors[] = 'Le titre et le cours sont obligatoires.';
        } else {
            if ($partId > 0) {
                $partModel->update($partId, $titre, $fichier, $cours_id);
                $_SESSION['success'] = 'Partition modifiée avec succès.';
            } else {
                $partModel->create($titre, $fichier, $cours_id);
                $_SESSION['success'] = 'Partition ajoutée avec succès.';
            }
            header('Location: partitions.php');
            exit;
        }
    }
}

$partitions = $partModel->getAll();
$cours = $coursModel->getAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Partitions | Admin</title>
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
        <a href="partitions.php" class="active"><i class="fa-solid fa-file-lines"></i> Partitions</a>
        <a href="instruments.php"><i class="fa-solid fa-music"></i> Instruments</a>
        <a href="exercices.php"><i class="fa-solid fa-clipboard-question"></i> Exercices</a>
        <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Déconnexion</a>
      </nav>
    </aside>
    <main class="main">
      <h2 style="margin-top:0;">Gestion des partitions</h2>
      <p class="muted">Ajoutez et modifiez les partitions liées aux cours.</p>

      <?php if (!empty($_SESSION['success'])) { echo '<div class="success">' . htmlspecialchars($_SESSION['success']) . '</div>'; unset($_SESSION['success']); } ?>
      <?php if (!empty($errors)) { foreach ($errors as $e) echo '<div class="alert">' . htmlspecialchars($e) . '</div>'; } ?>

      <section class="card" style="margin-bottom:20px;">
        <div class="section-title">
          <h3><?= $editPart ? 'Modifier la partition' : 'Ajouter une partition' ?></h3>
        </div>
        <form method="post">
          <?php if ($editPart): ?>
            <input type="hidden" name="part_id" value="<?= (int)$editPart['id'] ?>">
          <?php endif; ?>

          <div class="form-group">
            <label>Titre</label>
            <input type="text" name="titre" value="<?= htmlspecialchars($editPart['titre'] ?? '') ?>" required />
          </div>

          <div class="form-group">
            <label>Fichier (chemin/URL)</label>
            <input type="text" name="fichier" value="<?= htmlspecialchars($editPart['fichier'] ?? '') ?>" />
          </div>

          <div class="form-group">
            <label>Cours</label>
            <select name="cours_id" required>
              <option value="">Choisir un cours</option>
              <?php foreach ($cours as $c): ?>
                <option value="<?= (int)$c['id'] ?>" <?= (isset($editPart['cours_id']) && $editPart['cours_id'] == $c['id']) ? 'selected' : '' ?> >
                  <?= htmlspecialchars($c['titre'] ?? ('Cours ' . $c['id'])) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <button class="btn btn-gold" type="submit">
            <?= $editPart ? 'Enregistrer les modifications' : 'Créer la partition' ?>
          </button>
          <?php if ($editPart): ?>
            <a href="partitions.php" class="btn" style="margin-left: 10px;">Annuler</a>
          <?php endif; ?>
        </form>
      </section>

      <section class="card">
        <div class="section-title">
          <h3>Liste des partitions</h3>
        </div>
        <table class="table">
          <thead>
            <tr>
              <th>Titre</th>
              <th>Fichier</th>
              <th>Cours</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($partitions as $p): ?>
              <tr>
                <td><?= htmlspecialchars($p['titre'] ?? '') ?></td>
                <td><?= htmlspecialchars($p['fichier'] ?? '') ?></td>
                <td><?= htmlspecialchars($p['cours_id'] ?? '') ?></td>
                <td class="action-btn">
                  <form method="POST" style="display:inline;">
                    <input type="hidden" name="supprimer" value="<?= (int)$p['id'] ?>">
                    <input type="submit" name="suppr" value="Supprimer" class="btn btn-gold" onclick="return confirm('Confirmer la suppression ?')">
                  </form>
                  <a href="partitions.php?id=<?= $p['id'] ?>" class="btn btn-gold">Modifier</a>
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