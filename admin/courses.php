<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    header('Location: login.php');
    exit;
}
require_once __DIR__ . '/../includes/db.php';

$success = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $niveau = trim($_POST['niveau'] ?? 'Débutant');
    $instrumentId = isset($_POST['instrument_id']) ? (int)$_POST['instrument_id'] : 0;

    if ($titre === '') {
        $errors[] = 'Le titre du cours est obligatoire.';
    } else {
        $stmt = $pdo->prepare('INSERT INTO cours (titre, description, niveau, instrument_id) VALUES (?, ?, ?, ?)');
        $stmt->execute([$titre, $description, $niveau, $instrumentId ?: null]);
        $success = 'Cours ajouté avec succès.';
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
        <div><h1>DoReMiTendry</h1><p>Back Office</p></div>
      </div>
      <nav>
        <a href="dashboard.php"><i class="fa-solid fa-table-columns"></i> Tableau de bord</a>
        <a href="users.php"><i class="fa-solid fa-users"></i> Utilisateurs</a>
        <a href="courses.php" class="active"><i class="fa-solid fa-book-open"></i> Cours</a>
        <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Déconnexion</a>
      </nav>
    </aside>
    <main class="main">
      <h2 style="margin-top:0;">Gestion des cours</h2>
      <p class="muted">Ajoutez et consultez les contenus pédagogiques.</p>
      <?php if (!empty($success)) echo '<div class="success">' . htmlspecialchars($success) . '</div>'; ?>
      <?php if (!empty($errors)) { foreach ($errors as $e) echo '<div class="alert">' . htmlspecialchars($e) . '</div>'; } ?>

      <section class="card" style="margin-bottom:20px;">
        <div class="section-title"><h3>Ajouter un cours</h3></div>
        <form method="post">
          <div class="form-group">
            <label>Titre</label>
            <input type="text" name="titre" required />
          </div>
          <div class="form-group">
            <label>Description</label>
            <textarea name="description" rows="3"></textarea>
          </div>
          <div class="form-group">
            <label>Niveau</label>
            <input type="text" name="niveau" value="Débutant" />
          </div>
          <div class="form-group">
            <label>Instrument</label>
            <select name="instrument_id">
              <option value="">Aucun</option>
              <?php foreach ($instruments as $instrument): ?>
                <option value="<?= (int)$instrument['id'] ?>"><?= htmlspecialchars($instrument['nom']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <button class="btn btn-gold" type="submit">Créer le cours</button>
        </form>
      </section>

      <section class="card">
        <div class="section-title"><h3>Liste des cours</h3></div>
        <table class="table">
          <thead>
            <tr><th>Titre</th><th>Instrument</th><th>Niveau</th><th>Description</th></tr>
          </thead>
          <tbody>
            <?php foreach ($courses as $course): ?>
              <tr>
                <td><?= htmlspecialchars($course['titre'] ?? '') ?></td>
                <td><?= htmlspecialchars($course['instrument'] ?? '-') ?></td>
                <td><?= htmlspecialchars($course['niveau'] ?? '') ?></td>
                <td><?= htmlspecialchars($course['description'] ?? '') ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </section>
    </main>
  </div>
</body>
</html>
