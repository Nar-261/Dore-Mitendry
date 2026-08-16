<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    header('Location: ../login.php');
    exit;
}
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/gestion/lecon.php';
require_once __DIR__ . '/../config/gestion/module.php';

$success = '';
$errors = [];

$lecModel = new lecon($pdo);
$modModel = new module($pdo);

$editLec = null;
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $editLec = $lecModel->getById((int)$_GET['id']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['supprimer'])) {
        $lecModel->delete((int)$_POST['supprimer']);
        $_SESSION['success'] = 'Leçon supprimée.';
        header('Location: lecons.php');
        exit;
    }

    if (isset($_POST['titre'])) {
        $lecId = isset($_POST['lec_id']) ? (int)$_POST['lec_id'] : 0;
        $module_id = isset($_POST['module_id']) && is_numeric($_POST['module_id']) ? (int)$_POST['module_id'] : 0;
        $titre = trim($_POST['titre'] ?? '');
        $contenu = trim($_POST['contenu'] ?? '');

        if ($module_id <= 0 || $titre === '') {
            $errors[] = 'Le module et le titre sont obligatoires.';
        } else {
            if ($lecId > 0) {
                $lecModel->update($lecId, $module_id, $titre, $contenu);
                $_SESSION['success'] = 'Leçon modifiée avec succès.';
            } else {
                $lecModel->create($module_id, $titre, $contenu);
                $_SESSION['success'] = 'Leçon ajoutée avec succès.';
            }
            header('Location: lecons.php');
            exit;
        }
    }
}

$lecons = $lecModel->getAll();
$modules = $modModel->getAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Leçons | Admin</title>
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
        <a href="lecons.php" class="active"><i class="fa-solid fa-video"></i> Leçons</a>
        <a href="partitions.php"><i class="fa-solid fa-file-lines"></i> Partitions</a>
        <a href="instruments.php"><i class="fa-solid fa-music"></i> Instruments</a>
        <a href="exercices.php"><i class="fa-solid fa-clipboard-question"></i> Exercices</a>
        <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Déconnexion</a>
      </nav>
    </aside>
    <main class="main">
      <h2 style="margin-top:0;">Gestion des leçons</h2>
      <p class="muted">Ajoutez et modifiez les leçons liées aux modules.</p>

      <?php if (!empty($_SESSION['success'])) { echo '<div class="success">' . htmlspecialchars($_SESSION['success']) . '</div>'; unset($_SESSION['success']); } ?>
      <?php if (!empty($errors)) { foreach ($errors as $e) echo '<div class="alert">' . htmlspecialchars($e) . '</div>'; } ?>

      <section class="card" style="margin-bottom:20px;">
        <div class="section-title">
          <h3><?= $editLec ? 'Modifier la leçon' : 'Ajouter une leçon' ?></h3>
        </div>
        <form method="post">
          <?php if ($editLec): ?>
            <input type="hidden" name="lec_id" value="<?= (int)$editLec['id'] ?>">
          <?php endif; ?>

          <div class="form-group">
            <label>Module</label>
            <select name="module_id" required>
              <option value="">Choisir un module</option>
              <?php foreach ($modules as $m): ?>
                <option value="<?= (int)$m['id'] ?>" <?= (isset($editLec['module_id']) && $editLec['module_id'] == $m['id']) ? 'selected' : '' ?> >
                  <?= htmlspecialchars($m['titre'] ?? ('Module ' . $m['id'])) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label>Titre</label>
            <input type="text" name="titre" value="<?= htmlspecialchars($editLec['titre'] ?? '') ?>" required />
          </div>

          <div class="form-group">
            <label>Contenu</label>
            <textarea name="contenu" rows="4"><?= htmlspecialchars($editLec['contenu'] ?? '') ?></textarea>
          </div>

          <button class="btn btn-gold" type="submit">
            <?= $editLec ? 'Enregistrer les modifications' : 'Créer la leçon' ?>
          </button>
          <?php if ($editLec): ?>
            <a href="lecons.php" class="btn" style="margin-left: 10px;">Annuler</a>
          <?php endif; ?>
        </form>
      </section>

      <section class="card">
        <div class="section-title">
          <h3>Liste des leçons</h3>
        </div>
        <table class="table">
          <thead>
            <tr>
              <th>Module</th>
              <th>Titre</th>
              <th>Contenu</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($lecons as $l): ?>
              <tr>
                <td><?= htmlspecialchars($l['module_id'] ?? '') ?></td>
                <td><?= htmlspecialchars($l['titre'] ?? '') ?></td>
                <td><?= htmlspecialchars(substr($l['contenu'] ?? '', 0, 50)) . '...' ?></td>
                <td class="action-btn">
                  <form method="POST" style="display:inline;">
                    <input type="hidden" name="supprimer" value="<?= (int)$l['id'] ?>">
                    <input type="submit" name="suppr" value="Supprimer" class="btn btn-gold" onclick="return confirm('Confirmer la suppression ?')">
                  </form>
                  <a href="lecons.php?id=<?= $l['id'] ?>" class="btn btn-gold">Modifier</a>
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