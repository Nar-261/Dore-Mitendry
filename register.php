<?php
session_start();
require_once __DIR__ . '/includes/db.php';

function respondJson(array $payload)
{
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload);
    exit;
}

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['fullName'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $password = $_POST['registerPassword'] ?? '';
    $confirm = $_POST['confirmPassword'] ?? '';

    $nom = '';
    $prenom = '';
    if ($fullName !== '') {
        $parts = preg_split('/\s+/', $fullName);
        if (count($parts) > 1) {
            $nom = array_pop($parts);
            $prenom = implode(' ', $parts);
        } else {
            $prenom = $fullName;
        }
    }

    if ($fullName === '' || $email === '' || $password === '') {
        $errors[] = 'Tous les champs obligatoires doivent être renseignés.';
    }
    if ($password !== $confirm) {
        $errors[] = 'Les mots de passe ne correspondent pas.';
    }
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Adresse email invalide.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare('SELECT id FROM utilisateurs WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'Cet email est déjà utilisé.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare('INSERT INTO utilisateurs (nom, prenom, email, telephone, mot_de_passe, role) VALUES (?, ?, ?, ?, ?, ?)');
            $stmt->execute([$nom, $prenom, $email, $telephone, $hash, 'apprenant']);
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int)$pdo->lastInsertId();
            $_SESSION['user_role'] = 'apprenant';
            $_SESSION['user_name'] = $prenom ?: $fullName;

            if ($isAjax) {
                respondJson(['success' => true]);
            }

            header('Location: utilisateur/dashboard.php');
            exit;
        }
    }

    if ($isAjax) {
        respondJson(['success' => false, 'errors' => $errors]);
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Inscription | DoReMiTendry</title>
  <link rel="stylesheet" href="assets/css/dashboard.css" />
  <style>
    body{display:flex;align-items:center;justify-content:center;min-height:100vh;padding:24px;}
    .auth-card{width:min(460px,100%);background:rgba(18,18,18,.95);border:1px solid rgba(199,155,75,.2);border-radius:20px;padding:24px;box-shadow:0 15px 40px rgba(0,0,0,.25)}
    .auth-card h1{margin-top:0;color:#f5d9a1}
    .field{display:flex;flex-direction:column;gap:6px;margin-bottom:14px}
    .field input{padding:12px 14px;border-radius:10px;border:1px solid rgba(255,255,255,.1);background:#0f0f0f;color:#fff}
    .btn{display:inline-block;width:100%;padding:12px;border:none;border-radius:10px;background:linear-gradient(135deg,#c79b4b,#f0d29b);color:#000;font-weight:700;cursor:pointer}
    .alert{padding:10px;border-radius:10px;background:rgba(176,0,32,.15);color:#ffb3b3;margin-bottom:12px}
    .muted{color:#9d9487;font-size:.95rem}
    a{color:#f5d9a1}
  </style>
</head>
<body>
  <div class="auth-card">
    <h1>Créer un compte</h1>
    <p class="muted">Rejoignez DoReMiTendry et débutez votre parcours musical.</p>
    <?php if (!empty($errors)) { foreach ($errors as $e) echo '<div class="alert">' . htmlspecialchars($e) . '</div>'; } ?>
    <form method="post">
      <div class="field">
        <label>Nom complet</label>
        <input type="text" name="fullName" required value="<?= htmlspecialchars($_POST['fullName'] ?? '') ?>" />
      </div>
      <div class="field">
        <label>Email</label>
        <input type="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" />
      </div>
      <div class="field">
        <label>Mot de passe</label>
        <input type="password" name="registerPassword" required />
      </div>
      <div class="field">
        <label>Confirmer le mot de passe</label>
        <input type="password" name="confirmPassword" required />
      </div>
      <button class="btn" type="submit">Créer mon compte</button>
    </form>
    <p class="muted" style="margin-top:12px;">Vous avez déjà un compte ? <a href="login.php">Se connecter</a></p>
  </div>
</body>
</html>
