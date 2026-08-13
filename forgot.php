<?php
session_start();
require_once __DIR__ . '/includes/db.php';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if ($email === '') {
        $errors[] = 'Veuillez saisir votre adresse email.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Adresse email invalide.';
    } else {
        $stmt = $pdo->prepare('SELECT id FROM utilisateurs WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {
            // Ici vous pouvez générer un token et envoyer un email.
            $success = 'Un lien de réinitialisation a été envoyé à votre adresse email.';
        } else {
            $errors[] = 'Aucun compte trouvé avec cette adresse email.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Mot de passe oublié | DoReMiTendry</title>
  <link rel="stylesheet" href="assets/css/dashboard.css" />
  <style>
    body{display:flex;align-items:center;justify-content:center;min-height:100vh;padding:24px;}
    .auth-card{width:min(430px,100%);background:rgba(18,18,18,.95);border:1px solid rgba(199,155,75,.2);border-radius:20px;padding:24px;box-shadow:0 15px 40px rgba(0,0,0,.25)}
    .auth-card h1{margin-top:0;color:#f5d9a1}
    .field{display:flex;flex-direction:column;gap:6px;margin-bottom:14px}
    .field input{padding:12px 14px;border-radius:10px;border:1px solid rgba(255,255,255,.1);background:#0f0f0f;color:#fff}
    .btn{display:inline-block;width:100%;padding:12px;border:none;border-radius:10px;background:linear-gradient(135deg,#c79b4b,#f0d29b);color:#000;font-weight:700;cursor:pointer}
    .alert{padding:10px;border-radius:10px;background:rgba(176,0,32,.15);color:#ffb3b3;margin-bottom:12px}
    .success{padding:10px;border-radius:10px;background:rgba(56,142,60,.15);color:#b9f6ca;margin-bottom:12px}
    .muted{color:#9d9487;font-size:.95rem}
    a{color:#f5d9a1}
  </style>
</head>
<body>
  <div class="auth-card">
    <h1>Mot de passe oublié</h1>
    <p class="muted">Entrez votre email pour recevoir les instructions de réinitialisation.</p>
    <?php if (!empty($success)) { echo '<div class="success">' . htmlspecialchars($success) . '</div>'; } ?>
    <?php if (!empty($errors)) { foreach ($errors as $e) echo '<div class="alert">' . htmlspecialchars($e) . '</div>'; } ?>
    <form method="post">
      <div class="field">
        <label>Email</label>
        <input type="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" />
      </div>
      <button class="btn" type="submit">Envoyer le lien</button>
    </form>
    <p class="muted" style="margin-top:12px;">Retour à la connexion ? <a href="login.php">Se connecter</a></p>
  </div>
</body>
</html>
