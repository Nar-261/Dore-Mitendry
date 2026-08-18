<?php
session_start();
require_once __DIR__ . '/../config/db.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);

    if ($identifier === '' || $password === '') {
        $errors[] = 'Veuillez saisir votre email ou téléphone et votre mot de passe.';
    } else {
        $stmt = $pdo->prepare("SELECT id, nom, prenom, mot_de_passe, role FROM utilisateurs WHERE  role = 'admin' AND (email = ? OR telephone = ?) LIMIT 1");
        $stmt->execute([$identifier, $identifier]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['mot_de_passe'])) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int)$user['id'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_name'] = $user['prenom'];

            if ($remember) {
                setcookie(session_name(), session_id(), [
                    'expires' => time() + 60 * 60 * 24 * 30,
                    'path' => '/',
                    'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
                    'httponly' => true,
                    'samesite' => 'Lax',
                ]);
            }

            header('Location: dashboard.php');
            exit;
        } else {
            $errors[] = 'Email ou mot de passe incorrect.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Connexion | DoReMiTendry</title>
  <link rel="stylesheet" href="../assets/css/dashboard.css" />
  <style>
    body {
      display: flex;
      align-items: center;
      justify-content: center;
      min-height: 100vh;
      padding: 24px;
    }

    .auth-card {
      width: min(430px, 100%);
      background: rgba(18, 18, 18, .95);
      border: 1px solid rgba(199, 155, 75, .2);
      border-radius: 20px;
      padding: 24px;
      box-shadow: 0 15px 40px rgba(0, 0, 0, .25);
    }

    .auth-card h1 {
      margin-top: 0;
      color: #f5d9a1;
      text-align: center;
    }

    .field {
      display: flex;
      flex-direction: column;
      gap: 8px;
      margin-bottom: 20px
    }

    .field input {
      padding: 12px 14px;
      border-radius: 10px;
      border: 1px solid rgba(255, 255, 255, .1);
      background: #0f0f0f;
      color: #fff
    }

    .btn {
      display: inline-block;
      width: 100%;
      padding: 12px;
      border: none;
      border-radius: 10px;
      background: linear-gradient(135deg, #c79b4b, #f0d29b);
      color: #000;
      font-weight: 700;
      cursor: pointer
    }

    .alert {
      padding: 10px;
      border-radius: 10px;
      background: rgba(176, 0, 32, .15);
      color: #ffb3b3;
      margin-bottom: 12px
    }

    .muted {
      color: #9d9487;
      font-size: .95rem;
      text-align: center;
    }

    a {
      color: #f5d9a1
    }
  </style>
</head>

<body>
  <div class="auth-card">
    <h1>Connexion</h1>
    <p class="muted">Accédez a votre compte administrateur.</p>
    <?php if (!empty($errors)) {
      foreach ($errors as $e) echo '<div class="alert">' . htmlspecialchars($e) . '</div>';
    } ?>
    <form method="post">
      <div class="field">
        <label>Identifiant</label>
        <input type="text" name="identifier" placeholder="email ou telephone" required value="<?= htmlspecialchars($_POST['identifier'] ?? '') ?>" />
      </div>
      <div class="field">
        <label>Mot de passe</label>
        <input type="password" name="password" placeholder="votre mot de passe" required />
      </div>
      <div class="field" style="display:flex;align-items:center;justify-content:space-between;gap:12px;">
        <label style="display:flex;align-items:center;gap:10px;color:#f5d9a1;">
          <input type="checkbox" name="remember" /> Se souvenir de moi
        </label>
      </div>
      <button class="btn" type="submit">Se connecter</button>
    </form>    <p class="muted" style="margin-top:16px;"><a href="../login.php">Retour à l'accueil connexion</a></p>  </div>
</body>

</html>