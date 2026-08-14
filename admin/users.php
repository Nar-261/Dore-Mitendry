<?php
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] ?? '') !== 'admin') {
    header('Location: ../login.php');
    exit;
}
require_once __DIR__ . '/../includes/db.php';

$success = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'], $_POST['role'])) {
    $userId = (int)$_POST['user_id'];
    $role = trim($_POST['role']);
    if ($role !== 'admin' && $role !== 'apprenant') {
        $errors[] = 'Rôle invalide.';
    } else {
        $stmt = $pdo->prepare('UPDATE utilisateurs SET role = ? WHERE id = ?');
        $stmt->execute([$role, $userId]);
        $success = 'Rôle mis à jour avec succès.';
    }
}

$users = $pdo->query('SELECT id, nom, prenom, email, role, date_inscription FROM utilisateurs ORDER BY date_inscription DESC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Utilisateurs | Admin</title>
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
        <a href="users.php" class="active"><i class="fa-solid fa-users"></i> Utilisateurs</a>
        <a href="courses.php"><i class="fa-solid fa-book-open"></i> Cours</a>
        <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Déconnexion</a>
      </nav>
    </aside>
    <main class="main">
      <h2 style="margin-top:0;">Gestion des utilisateurs</h2>
      <p class="muted">Consultez et modifiez les rôles des comptes.</p>
      <?php if (!empty($success)) echo '<div class="success">' . htmlspecialchars($success) . '</div>'; ?>
      <?php if (!empty($errors)) { foreach ($errors as $e) echo '<div class="alert">' . htmlspecialchars($e) . '</div>'; } ?>
      <section class="card">
        <table class="table">
          <thead>
            <tr><th>Nom</th><th>Email</th><th>Rôle</th><th>Action</th></tr>
          </thead>
          <tbody>
            <?php foreach ($users as $user): ?>
              <tr>
                <td><?= htmlspecialchars(($user['prenom'] ?? '') . ' ' . ($user['nom'] ?? '')) ?></td>
                <td><?= htmlspecialchars($user['email'] ?? '') ?></td>
                <td><?= htmlspecialchars($user['role'] ?? '') ?></td>
                <td>
                  <form method="post" style="display:flex;gap:8px;align-items:center;">
                    <input type="hidden" name="user_id" value="<?= (int)$user['id'] ?>" />
                    <select name="role">
                      <option value="apprenant" <?= ($user['role'] ?? '') === 'apprenant' ? 'selected' : '' ?>>Apprenant</option>
                      <option value="admin" <?= ($user['role'] ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
                    </select>
                    <button class="btn btn-dark" type="submit">Enregistrer</button>
                  </form>
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
