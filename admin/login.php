<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

$errors = [];

$adminEmail = 'admin@doremitendry.com';
$adminPassword = 'Admin123!';
$checkAdmin = $pdo->prepare('SELECT id FROM utilisateurs WHERE role = ? LIMIT 1');
$checkAdmin->execute(['admin']);
if (!$checkAdmin->fetch()) {
    $hash = password_hash($adminPassword, PASSWORD_DEFAULT);
    $createAdmin = $pdo->prepare('INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, role) VALUES (?, ?, ?, ?, ?)');
    $createAdmin->execute(['Admin', 'Principal', $adminEmail, $hash, 'admin']);
}

if (!empty($_SESSION['user_id']) && ($_SESSION['user_role'] ?? '') === 'admin') {
    header('Location: dashboard.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['mot_de_passe'] ?? '';

    if ($email === '' || $password === '') {
        $errors[] = 'Veuillez saisir votre email et votre mot de passe.';
    } else {
        $stmt = $pdo->prepare('SELECT id, nom, prenom, mot_de_passe, role FROM utilisateurs WHERE email = ? AND role = ?');
        $stmt->execute([$email, 'admin']);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['mot_de_passe'])) {
            $_SESSION['user_id'] = (int)$admin['id'];
            $_SESSION['user_role'] = $admin['role'];
            $_SESSION['user_name'] = $admin['prenom'];
            header('Location: dashboard.php');
            exit;
        } else {
            $errors[] = 'Identifiants administrateur incorrects.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Connexion admin | DoReMiTendry</title>
  <link rel="stylesheet" href="admin.css" />
</head>
<body style="display:flex;align-items:center;justify-content:center;min-height:100vh;padding:24px;">
  <div class="auth-card">
    <h1>Back Office Admin</h1>
    <p class="muted">Accédez à la gestion de la plateforme DoReMiTendry.</p>
    <?php if (!empty($errors)) { foreach ($errors as $e) echo '<div class="alert">' . htmlspecialchars($e) . '</div>'; } ?>
    <form method="post">
      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" />
      </div>
      <div class="form-group">
        <label>Mot de passe</label>
        <input type="password" name="mot_de_passe" required />
      </div>
      <button class="btn btn-gold" type="submit" style="width:100%;">Se connecter</button>
    </form>
    <p class="muted" style="margin-top:12px;">
  </div>
</body>
</html>
