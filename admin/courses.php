<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
  header('Location: ../login.php');
  exit;
}
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../config/gestion/cours.php';


$success = '';
$errors = [];

$cours = new cours($pdo);

// 1. Récupération des informations du cours à modifier si un ID est passé dans l'URL
$editCourse = null;
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
  $editCourse = $cours->getById((int)$_GET['id']);  
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Suppression d'un cours
  if (isset($_POST['supprimer'])) {
    $cours->delete((int)$_POST['supprimer']);
    header('Location: courses.php');
    exit;
  }
  
  // Enregistrement (Création OU Modification)
  if (isset($_POST['titre'])) {
    $courseId = isset($_POST['course_id']) ? (int)$_POST['course_id'] : 0;
    $titre = trim($_POST['titre'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $niveau = trim($_POST['niveau'] ?? 'Débutant');
    $instrumentId = isset($_POST['instrument_id']) ? (int)$_POST['instrument_id'] : 0;

    if ($titre === '') {
      $errors[] = 'Le titre du cours est obligatoire.';
    } else {
      if ($courseId > 0) {
        // MODIFICATION (UPDATE)
        $cours->update($courseId, $titre, $description, $instrumentId ?: null, $niveau);
        $_SESSION['success'] = 'Cours modifié avec succès.';
      } else {
        // CRÉATION (INSERT)
        $cours->create($titre, $description, $instrumentId ?: null, $niveau);
        $_SESSION['success'] = 'Cours ajouté avec succès.';
      }
      header('Location: courses.php');
      exit;
    }
  }
}

$instruments = $pdo->query('SELECT id, nom FROM instruments ORDER BY nom')->fetchAll();
$courses = $pdo->query('SELECT c.id, c.titre, c.description, c.niveau, i.nom AS instrument FROM cours c LEFT JOIN instruments i ON i.id = c.instrument_id ORDER BY c.id DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Cours | Admin</title>
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
        <a href="courses.php" class="active"><i class="fa-solid fa-book-open"></i> Cours</a>
        <a href="modules.php"><i class="fa-solid fa-layer-group"></i> Modules</a>
        <a href="lecons.php"><i class="fa-solid fa-video"></i> Leçons</a>
        <a href="partitions.php"><i class="fa-solid fa-file-lines"></i> Partitions</a>
        <a href="instruments.php"><i class="fa-solid fa-music"></i> Instruments</a>
        <a href="exercices.php"><i class="fa-solid fa-clipboard-question"></i> Exercices</a>
        <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Déconnexion</a>
      </nav>
    </aside>
    <main class="main">
      <h2 style="margin-top:0;">Gestion des cours</h2>
      <p class="muted">Ajoutez et consultez les contenus pédagogiques.</p>
      
      <?php if (!empty($errors)) {
        foreach ($errors as $e) echo '<div class="alert">' . htmlspecialchars($e) . '</div>';
      } ?>

      <section class="card" style="margin-bottom:20px;">
        <div class="section-title">
          <h3><?= $editCourse ? 'Modifier le cours' : 'Ajouter un cours' ?></h3>
        </div>
        <form method="post">
          <!-- Champ caché contenant l'ID s'il s'agit d'une modification -->
          <?php if ($editCourse): ?>
            <input type="hidden" name="course_id" value="<?= $editCourse['id'] ?>">
          <?php endif; ?>

          <div class="form-group">
            <label>Titre</label>
            <input type="text" name="titre" value="<?= htmlspecialchars($editCourse['titre'] ?? '') ?>" required />
          </div>
          <div class="form-group">
            <label>Description</label>
            <textarea name="description" rows="3"><?= htmlspecialchars($editCourse['description'] ?? '') ?></textarea>
          </div>
          <div class="form-group">
            <label>Niveau</label>
            <input type="text" name="niveau" value="<?= htmlspecialchars($editCourse['niveau'] ?? 'Débutant') ?>" />
          </div>
          <div class="form-group">
            <label>Instrument</label>
            <select name="instrument_id">
              <option value="">Aucun</option>
              <?php foreach ($instruments as $instrument): ?>
                <option value="<?= (int)$instrument['id'] ?>" <?= (isset($editCourse['instrument_id']) && $editCourse['instrument_id'] == $instrument['id']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($instrument['nom']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <button class="btn btn-gold" type="submit">
            <?= $editCourse ? 'Enregistrer les modifications' : 'Créer le cours' ?>
          </button>
          <?php if ($editCourse): ?>
            <a href="courses.php" class="btn" style="margin-left: 10px;">Annuler</a>
          <?php endif; ?>
        </form>
      </section>

      <!-- Liste des cours -->
      <section class="card">
        <div class="section-title">
          <h3>Liste des cours</h3>
        </div>
        <table class="table">
          <thead>
            <tr>
              <th>Titre</th>
              <th>Instrument</th>
              <th>Niveau</th>
              <th>Description</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($courses as $course): ?>
              <tr>
                <td><?= htmlspecialchars($course['titre'] ?? '') ?></td>
                <td><?= htmlspecialchars($course['instrument'] ?? '-') ?></td>
                <td><?= htmlspecialchars($course['niveau'] ?? '') ?></td>
                <td><?= htmlspecialchars($course['description'] ?? '') ?></td>
                <td class="action-btn">
                  <!-- Bouton de suppression -->
                  <form method="POST" style="display:inline;">
                    <input type="hidden" name="supprimer" value="<?= $course['id'] ?>">
                    <input type="submit" name="suppr" value="Supprimer" class="btn btn-gold" onclick="return confirm('Confirmer la suppression ?')">
                  </form>
                  <!-- Bouton de modification : redirige vers la même page avec l'ID du cours -->
                  <a href="courses.php?id=<?= $course['id'] ?>" class="btn btn-gold">Modifier</a>
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