<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    header('Location: ../login.php');
    exit;
}
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/gestion/module.php';
require_once __DIR__ . '/../config/gestion/cours.php';

$success = '';
$errors = [];

$mod = new module($pdo);
$coursModel = new Cours($pdo);

$editModule = null;
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $editModule = $mod->getById((int)$_GET['id']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['supprimer'])) {
        $mod->delete((int)$_POST['supprimer']);
        $_SESSION['success'] = 'Module supprimé.';
        header('Location: modules.php');
        exit;
    }

    if (isset($_POST['titre'])) {
        $moduleId = isset($_POST['module_id']) ? (int)$_POST['module_id'] : 0;
        $cours_id = isset($_POST['cours_id']) && is_numeric($_POST['cours_id']) ? (int)$_POST['cours_id'] : 0;
        $titre = trim($_POST['titre'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $ordre = isset($_POST['ordre']) ? (int)$_POST['ordre'] : 1;
        $image_hero = trim($_POST['image_hero'] ?? '');
        if ($image_hero === '') $image_hero = null;

        if ($cours_id <= 0 || $titre === '') {
            $errors[] = 'Le cours et le titre sont obligatoires.';
        } else {
            if ($moduleId > 0) {
                $mod->update($moduleId, $cours_id, $titre, $description, $ordre, $image_hero);
                $_SESSION['success'] = 'Module modifié avec succès.';
            } else {
                $mod->create($cours_id, $titre, $description, $ordre, $image_hero);
                $_SESSION['success'] = 'Module ajouté avec succès.';
            }
            header('Location: modules.php');
            exit;
        }
    }
}

$modules = $mod->getAll();
$cours = $coursModel->getAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Modules | Admin</title>
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
        <a href="modules.php" class="active"><i class="fa-solid fa-layer-group"></i> Modules</a>
        <a href="lecons.php"><i class="fa-solid fa-video"></i> Leçons</a>
        <a href="partitions.php"><i class="fa-solid fa-file-lines"></i> Partitions</a>
        <a href="instruments.php"><i class="fa-solid fa-music"></i> Instruments</a>
        <a href="exercices.php"><i class="fa-solid fa-clipboard-question"></i> Exercices</a>
        <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Déconnexion</a>
      </nav>
    </aside>
    <main class="main">
      <h2 style="margin-top:0;">Gestion des modules</h2>
      <p class="muted">Ajoutez et modifiez les modules liés aux cours.</p>

      <?php if (!empty($_SESSION['success'])) { echo '<div class="success">' . htmlspecialchars($_SESSION['success']) . '</div>'; unset($_SESSION['success']); } ?>
      <?php if (!empty($errors)) { foreach ($errors as $e) echo '<div class="alert">' . htmlspecialchars($e) . '</div>'; } ?>

      <section class="card" style="margin-bottom:20px;">
        <div class="section-title">
          <h3><?= $editModule ? 'Modifier le module' : 'Ajouter un module' ?></h3>
        </div>
        <form method="post">
          <?php if ($editModule): ?>
            <input type="hidden" name="module_id" value="<?= (int)$editModule['id'] ?>">
          <?php endif; ?>

          <div class="form-group">
            <label>Cours</label>
            <select name="cours_id" required>
              <option value="">Choisir un cours</option>
              <?php foreach ($cours as $c): ?>
                <option value="<?= (int)$c['id'] ?>" <?= (isset($editModule['cours_id']) && $editModule['cours_id'] == $c['id']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($c['titre'] ?? ('Cours ' . $c['id'])) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label>Titre</label>
            <input type="text" name="titre" value="<?= htmlspecialchars($editModule['titre'] ?? '') ?>" required />
          </div>

          <div class="form-group">
            <label>Description</label>
            <textarea name="description" rows="3"><?= htmlspecialchars($editModule['description'] ?? '') ?></textarea>
          </div>

          <div class="form-group">
            <label>Ordre</label>
            <input type="number" name="ordre" value="<?= htmlspecialchars($editModule['ordre'] ?? 1) ?>" min="1" />
          </div>

          <div class="form-group">
            <label>Image hero (chemin ou URL)</label>
            <input type="text" name="image_hero" value="<?= htmlspecialchars($editModule['image_hero'] ?? '') ?>" />
          </div>

          <button class="btn btn-gold" type="submit">
            <?= $editModule ? 'Enregistrer les modifications' : 'Créer le module' ?>
          </button>
          <?php if ($editModule): ?>
            <a href="modules.php" class="btn" style="margin-left: 10px;">Annuler</a>
          <?php endif; ?>
        </form>
      </section>

      <section class="card">
        <div class="section-title">
          <h3>Liste des modules</h3>
        </div>
        <table class="table">
          <thead>
            <tr>
              <th>Cours</th>
              <th>Titre</th>
              <th>Ordre</th>
              <th>Description</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($modules as $m): ?>
              <tr>
                <td><?= htmlspecialchars($m['cours_titre'] ?? ('Cours ' . ($m['cours_id'] ?? ''))) ?></td>
                <td><?= htmlspecialchars($m['titre'] ?? '') ?></td>
                <td><?= htmlspecialchars($m['ordre'] ?? '') ?></td>
                <td><?= htmlspecialchars($m['description'] ?? '') ?></td>
                <td class="action-btn">
                  <form method="POST" style="display:inline;">
                    <input type="hidden" name="supprimer" value="<?= (int)$m['id'] ?>">
                    <input type="submit" name="suppr" value="Supprimer" class="btn btn-gold" onclick="return confirm('Confirmer la suppression ?')">
                  </form>
                  <a href="modules.php?id=<?= $m['id'] ?>" class="btn btn-gold">Modifier</a>
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