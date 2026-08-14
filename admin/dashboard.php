<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    header('Location: ../login.php');
    exit;
}
require_once __DIR__ . '/../includes/db.php';

$stats = [
    'users' => (int)$pdo->query('SELECT COUNT(*) FROM utilisateurs')->fetchColumn(),
    'courses' => (int)$pdo->query('SELECT COUNT(*) FROM cours')->fetchColumn(),
    'instruments' => (int)$pdo->query('SELECT COUNT(*) FROM instruments')->fetchColumn(),
    'messages' => (int)$pdo->query('SELECT COUNT(*) FROM messages')->fetchColumn(),
];

$latestUsers = $pdo->query('SELECT nom, prenom, email, role, date_inscription FROM utilisateurs ORDER BY date_inscription DESC LIMIT 5')->fetchAll();
$latestCourses = $pdo->query('SELECT c.titre, i.nom AS instrument, c.niveau FROM cours c LEFT JOIN instruments i ON i.id = c.instrument_id ORDER BY c.id DESC LIMIT 5')->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin | DoReMiTendry</title>
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
          <p>Back Office Administrateur</p>
        </div>
      </div>
      <nav>
        <a href="dashboard.php" class="active"><i class="fa-solid fa-table-columns"></i> Tableau de bord</a>
        <a href="users.php"><i class="fa-solid fa-users"></i> Utilisateurs</a>
        <a href="courses.php"><i class="fa-solid fa-book-open"></i> Cours</a>
        <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Déconnexion</a>
      </nav>
    </aside>

    <main class="main">
      <header class="topbar">
        <div class="topbar-left">
          <button class="icon-btn"><i class="fa-solid fa-bars"></i></button>
          <div>
            <h2 style="margin:0;">Bienvenue, <?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?> 👋</h2>
            <p class="muted" style="margin:4px 0 0;">Voici un aperçu rapide de votre plateforme.</p>
          </div>
        </div>
        <div class="userbox" style="display:flex;align-items:center;gap:12px;">
          <div class="avatar">A</div>
          <div>
            <strong>Administrateur</strong><br />
            <small>Gestion</small>
          </div>
        </div>
      </header>

      <section class="hero">
        <div>
          <p class="badge">Administration</p>
          <h2>Gérez votre école de musique</h2>
          <p>Suivez les utilisateurs, les cours et les contenus depuis un seul espace.</p>
        </div>
      </section>

      <div class="grid grid-3" style="margin-bottom:20px;">
        <div class="stat-card"><h4><?= $stats['users'] ?></h4><p>Utilisateurs</p></div>
        <div class="stat-card"><h4><?= $stats['courses'] ?></h4><p>Cours</p></div>
        <div class="stat-card"><h4><?= $stats['instruments'] ?></h4><p>Instruments</p></div>
        <div class="stat-card"><h4><?= $stats['messages'] ?></h4><p>Messages</p></div>
      </div>

      <div class="grid grid-2">
        <section class="card">
          <div class="section-title">
            <h3>Derniers inscrits</h3>
            <span class="badge">Récents</span>
          </div>
          <table class="table">
            <thead>
              <tr><th>Nom</th><th>Email</th><th>Rôle</th></tr>
            </thead>
            <tbody>
              <?php foreach ($latestUsers as $user): ?>
                <tr>
                  <td><?= htmlspecialchars(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? '')) ?></td>
                  <td><?= htmlspecialchars($user['email'] ?? '') ?></td>
                  <td><?= htmlspecialchars($user['role'] ?? '') ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </section>

        <section class="card">
          <div class="section-title">
            <h3>Derniers cours ajoutés</h3>
            <span class="badge">Contenus</span>
          </div>
          <table class="table">
            <thead>
              <tr><th>Titre</th><th>Instrument</th><th>Niveau</th></tr>
            </thead>
            <tbody>
              <?php foreach ($latestCourses as $course): ?>
                <tr>
                  <td><?= htmlspecialchars($course['titre'] ?? '') ?></td>
                  <td><?= htmlspecialchars($course['instrument'] ?? '-') ?></td>
                  <td><?= htmlspecialchars($course['niveau'] ?? '') ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </section>
      </div>
    </main>
  </div>
</body>
</html>
