<?php
session_start();
require_once __DIR__ . '/config/db.php';


if (!empty($_SESSION['user_id'])) {
  header('Location: utilisateur/dashboard.php');
  exit;
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $identifier = trim($_POST['identifier'] ?? '');
  $password = $_POST['password'] ?? '';
  $remember = isset($_POST['remember']);

  if ($identifier === '' || $password === '') {
    $errors[] = 'Veuillez saisir votre email ou téléphone et votre mot de passe.';
  } else {
    $stmt = $pdo->prepare("SELECT id, nom, prenom, mot_de_passe, role FROM utilisateurs WHERE role = 'apprenant' AND (email = ? OR telephone = ?) LIMIT 1");
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

      header('Location: utilisateur/dashboard.php');
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
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Connexion - DoRe-Mitendry</title>
  <style>
    :root {
      --brown: #7a3b2e;
      --brown-dark: #5c2c22;
      --bg: #f4efe9;
      --white: #ffffff;
      --text-light: #ece2dc;
      --radius: 12px;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: 'Franklin Gothic Medium', 'Arial Narrow', Arial, sans-serif;
    }

    html,
    body {
      height: 100%;
    }

    body {
      background-color: var(--bg);
      display: flex;
      flex-direction: column;
      min-height: 100dvh;
    }

    /* ---------- Header ---------- */
    header nav {
      width: 100%;
      min-height: clamp(48px, 8vh, 60px);
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 0 24px;
      background-color: var(--brown);
      flex-shrink: 0;
    }

    .alert {
      padding: 10px;
      border-radius: 10px;
      background: rgba(176, 0, 32, .15);
      color: #ffb3b3;
      margin-bottom: 12px
    }
    .brand {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    nav .logo-circle {
      height: 40px;
      width: 40px;
      border-radius: 50%;
      background-color: var(--white);
      flex-shrink: 0;
    }

    nav .brand-name {
      color: var(--white);
      font-size: 1.15rem;
      font-weight: 600;
    }

    nav .admin {
      height: 36px;
      padding: 0 16px;
      background-color: var(--brown-dark);
      border: none;
      border-radius: 6px;
      cursor: pointer;
      transition: background-color 0.2s ease;
      text-decoration: none;
      color: var(--white);
      font-size: 0.9rem;
      display: inline-flex;
      align-items: center;
    }

    .admin:hover,
    .admin:focus-visible {
      background-color: #47201a;
    }

    .admin:focus-visible {
      outline: 2px solid var(--white);
      outline-offset: 2px;
    }

    /* ---------- Main ---------- */
    main {
      flex: 1;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      padding: clamp(8px, 2vh, 24px) 16px;
      overflow-y: auto;
      /* safety net on very short viewports, never clips content */
    }

    .con {
      text-align: center;
      font-size: clamp(1.3rem, 3vw, 1.8rem);
      color: var(--brown-dark);
      margin-bottom: clamp(8px, 2vh, 16px);
    }

    /* ---------- Bloc connexion ---------- */
    .connex {
      display: flex;
      flex-direction: column;
      align-items: center;
      width: 100%;
      max-width: 380px;
    }

    .ispm {
      height: 72px;
      width: 72px;
      border-radius: 50%;
      overflow: hidden;
      background-color: var(--white);
      box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
      border: 3px solid var(--white);
      z-index: 2;
      margin-bottom: -36px;
      /* pulls the form up underneath it, badge effect */
    }

    .ispm img {
      height: 100%;
      width: 100%;
      object-fit: cover;
      display: block;
    }

    .form {
      width: 100%;
      padding: clamp(28px, 5vh, 36px) 24px clamp(16px, 3vh, 24px);
      display: flex;
      flex-direction: column;
      background-color: var(--brown);
      border-radius: var(--radius);
      box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
    }

    .form form {
      display: flex;
      flex-direction: column;
      gap: clamp(10px, 1.6vh, 16px);
    }

    .field {
      display: flex;
      flex-direction: column;
      text-align: left;
    }

    .field label {
      color: var(--text-light);
      font-size: 0.85rem;
      margin-bottom: 4px;
    }

    .form input {
      height: 38px;
      width: 100%;
      padding: 0 12px;
      border-radius: 8px;
      border: 2px solid transparent;
      outline: none;
      font-size: 0.95rem;
      transition: border-color 0.2s ease, box-shadow 0.2s ease;
    }

    .form input:focus-visible {
      border-color: var(--white);
      box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.25);
    }

    .submit-btn {
      margin-top: 4px;
      height: 42px;
      border: none;
      border-radius: 8px;
      background-color: var(--brown-dark);
      color: var(--white);
      font-size: 1rem;
      font-weight: 600;
      cursor: pointer;
      transition: background-color 0.2s ease, transform 0.1s ease;
    }

    .submit-btn:hover {
      background-color: #47201a;
    }

    .submit-btn:focus-visible {
      outline: 2px solid var(--white);
      outline-offset: 2px;
    }

    .submit-btn:active {
      transform: scale(0.98);
    }

    /* ---------- Liens sous le formulaire ---------- */
    .connecter {
      display: flex;
      align-items: center;
      gap: 10px;
      color: #ffffff;
      font-size: 0.85rem;
      margin-top: clamp(10px, 2vh, 16px);
      flex-wrap: wrap;
      justify-content: center;
    }

    .connecter a {
      color:#ffffff;
      text-decoration: none;
      font-weight: 600;
    }

    .connecter a:hover,
    .connecter a:focus-visible {
      text-decoration: underline;
    }

    .connecter .sep {
      color: #999;
    }

    /* ---------- Responsive ---------- */
    @media (max-width: 480px) {
      nav .brand-name {
        font-size: 1rem;
      }

      .form {
        padding-left: 18px;
        padding-right: 18px;
      }
    }

    @media (max-height: 560px) {
      .ispm {
        height: 56px;
        width: 56px;
        margin-bottom: -28px;
      }

      .con {
        font-size: 1.1rem;
        margin-bottom: 6px;
      }
    }

    /* ---------- Footer ---------- */
    footer {
      flex-shrink: 0;
      text-align: center;
      padding: 10px 16px;
      background-color: var(--brown);
      color: var(--text-light);
      font-size: 0.78rem;
    }

    footer a {
      color: var(--white);
      text-decoration: none;
      font-weight: 600;
    }

    footer a:hover,
    footer a:focus-visible {
      text-decoration: underline;
    }
  </style>
</head>

<body>
  <header>
    <nav>
      <div class="brand">
        <div class="logo-circle"></div>
        <span class="brand-name">DoRe-Mitendry</span>
      </div>
      <a class="admin" href="/admin/index.php">Admin</a>
    </nav>
  </header>

  <main>
    <h1 class="con">Connexion</h1>

    <div class="connex">
      <div class="ispm"><img src="/IMAGES/logo-ispm.png" alt="Logo ISPM"></div>

      <div class="form">
        <form method="post">
          <?php if (!empty($errors)) {
            foreach ($errors as $e) echo '<div class="alert">' . htmlspecialchars($e) . '</div>';
          } ?>

          <div class="field">
            <label for="email">Email</label>
            <input type="email" placeholder="nom@email.com" required id="email" name="identifier" autocomplete="username">
          </div>

          <div class="field">
            <label for="password">Mot de passe</label>
            <input type="password" placeholder="Mot de passe" required id="password" name="password" autocomplete="current-password">
          </div>
          <label style="display:flex;align-items:center;color:#f5d9a1;">
            <input type="checkbox" name="remember" style="height: 10px;width:10px;margin:10px" /> Se souvenir de moi
          </label>

          <button type="submit" class="submit-btn">Se connecter</button>
        </form>

        <div class="connecter">
          pas de compte?
          <a href="register.php">Créer un compte</a>
        </div>
      </div>
    </div>
  </main>

  <footer>
    <p>&copy; <span id="year">2026</span> DoRe-Mitendry &middot; ISPM &middot; <a href="#">Contact</a></p>
  </footer>

  <script>
    document.getElementById('year').textContent = new Date().getFullYear();
  </script>
</body>

</html>