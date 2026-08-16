<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    header('Location: ../login.php');
    exit;
}
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/gestion/exercice.php';
require_once __DIR__ . '/../config/gestion/lecon.php';

$success = '';
$errors = [];

$ex = new exercice($pdo);
$leconModel = new lecon($pdo);

// edition
$editEx = null;
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $editEx = $ex->getById((int)$_GET['id']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // suppression
    if (isset($_POST['supprimer'])) {
        $ex->delete((int)$_POST['supprimer']);
        $_SESSION['success'] = 'Exercice supprimé.';
        header('Location: exercices.php');
        exit;
    }

    // creation/modification
    if (isset($_POST['question'])) {
        $exerciseId = isset($_POST['exercise_id']) ? (int)$_POST['exercise_id'] : 0;
        $lecon_id = isset($_POST['lecon_id']) && is_numeric($_POST['lecon_id']) ? (int)$_POST['lecon_id'] : null;
        $question = trim($_POST['question'] ?? '');
        $correction = trim($_POST['correction'] ?? '');

        if ($lecon_id === null || $question === '') {
            $errors[] = 'La leçon et la question sont obligatoires.';
        } else {
            if ($exerciseId > 0) {
                $ex->update($exerciseId, $lecon_id, $question, $correction);
                $_SESSION['success'] = 'Exercice modifié avec succès.';
            } else {
                $ex->create($lecon_id, $question, $correction);
                $_SESSION['success'] = 'Exercice ajouté avec succès.';
            }
            header('Location: exercices.php');
            exit;
        }
    }
}

$exercices = $ex->getAll();
$lecons = $leconModel->getAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Exercices | Admin</title>
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
        <a href="instruments.php"><i class="fa-solid fa-music"></i> Instruments</a>
        <a href="exercices.php" class="active"><i class="fa-solid fa-clipboard-question"></i> Exercices</a>
        <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Déconnexion</a>
      </nav>
    </aside>
    <main class="main">
      <h2 style="margin-top:0;">Gestion des exercices</h2>
      <p class="muted">Ajoutez et modifiez les exercices liés aux leçons.</p>

      <?php if (!empty($_SESSION['success'])) { echo '<div class="success">' . htmlspecialchars($_SESSION['success']) . '</div>'; unset($_SESSION['success']); } ?>
      <?php if (!empty($errors)) { foreach ($errors as $e) echo '<div class="alert">' . htmlspecialchars($e) . '</div>'; } ?>

      <section class="card" style="margin-bottom:20px;">
        <div class="section-title">
          <h3><?= $editEx ? 'Modifier l\'exercice' : 'Ajouter un exercice' ?></h3>
        </div>
        <form method="post">
          <?php if ($editEx): ?>
            <input type="hidden" name="exercise_id" value="<?= (int)$editEx['id'] ?>">
          <?php endif; ?>

          <div class="form-group">
            <label>Leçon</label>
            <select name="lecon_id" required>
              <option value="">Choisir une leçon</option>
              <?php foreach ($lecons as $l): ?>
                <option value="<?= (int)$l['id'] ?>" <?= (isset($editEx['lecon_id']) && $editEx['lecon_id'] == $l['id']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($l['titre'] ?? ('Leçon ' . $l['id'])) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label>Question</label>
            <textarea name="question" rows="3" required><?= htmlspecialchars($editEx['question'] ?? '') ?></textarea>
          </div>

          <div class="form-group">
            <label>Correction</label>
            <textarea name="correction" rows="3"><?= htmlspecialchars($editEx['correction'] ?? '') ?></textarea>
          </div>

          <button class="btn btn-gold" type="submit">
            <?= $editEx ? 'Enregistrer les modifications' : 'Créer l\'exercice' ?>
          </button>
          <?php if ($editEx): ?>
            <a href="exercices.php" class="btn" style="margin-left: 10px;">Annuler</a>
          <?php endif; ?>
        </form>
      </section>

      <section class="card">
        <div class="section-title">
          <h3>Liste des exercices</h3>
        </div>
        <table class="table">
          <thead>
            <tr>
              <th>Leçon</th>
              <th>Question</th>
              <th>Correction</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($exercices as $item): ?>
              <tr>
                <td><?= htmlspecialchars($item['lecon_id'] ?? '') ?></td>
                <td><?= htmlspecialchars($item['question'] ?? '') ?></td>
                <td><?= htmlspecialchars($item['correction'] ?? '') ?></td>
                <td class="action-btn">
                  <form method="POST" style="display:inline;">
                    <input type="hidden" name="supprimer" value="<?= (int)$item['id'] ?>">
                    <input type="submit" name="suppr" value="Supprimer" class="btn btn-gold" onclick="return confirm('Confirmer la suppression ?')">
                  </form>
                  <a href="exercices.php?id=<?= $item['id'] ?>" class="btn btn-gold">Modifier</a>
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