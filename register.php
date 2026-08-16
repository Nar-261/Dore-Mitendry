<?php
session_start();
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/config/gestion/utilisateur.php';

function respondJson(array $payload)
{
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload);
    exit;
}

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
$errors = [];
// Ensure a CSRF token exists for the form
if (empty($_SESSION['csrf_token'])) {
    try {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    } catch (Exception $e) {
        // Fallback to less strong token if random_bytes is unavailable
        $_SESSION['csrf_token'] = bin2hex(openssl_random_pseudo_bytes(32));
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Use the actual form field names (nom, prenom, email, password, password2)
    $nom = trim($_POST['nom'] ?? '');
    $prenom = trim($_POST['prenom'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telephone = trim($_POST['telephone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['password2'] ?? '';
    $token = $_POST['csrf_token'] ?? '';

    // CSRF token check
    if (!hash_equals((string)($_SESSION['csrf_token'] ?? ''), (string)$token)) {
        $errors[] = 'Requête invalide. Veuillez réessayer.';
    }

    // Basic validations
    if ($nom === '' || $prenom === '' || $email === '' || $password === '') {
        $errors[] = 'Tous les champs obligatoires doivent être renseignés.';
    }
    if ($password !== $confirm) {
        $errors[] = 'Les mots de passe ne correspondent pas.';
    }
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Adresse email invalide.';
    }
    if ($password !== '' && strlen($password) < 8) {
        $errors[] = 'Le mot de passe doit contenir au moins 8 caractères.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare('SELECT id FROM utilisateurs WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'Cet email est déjà utilisé.';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $user = new utilisateur($pdo);
            $user->create($nom, $prenom, $email, $telephone, $hash);
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int)$pdo->lastInsertId();
            $_SESSION['user_role'] = 'apprenant';
            $_SESSION['user_name'] = $prenom ?: $nom;

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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un compte - DoRe-Mitendry</title>
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

        /* ---------- Bloc formulaire ---------- */
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
            gap: clamp(8px, 1.4vh, 14px);
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

        /* ---------- Lien sous le formulaire ---------- */
        .connecter {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 0.85rem;
            margin-top: clamp(10px, 2vh, 16px);
            flex-wrap: wrap;
            justify-content: center;
        }

        .connecter a {
            color: var(--brown-dark);
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

        @media (max-height: 620px) {
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
            <a class="admin" href="#">Admin</a>
        </nav>
    </header>

    <main>
        <h1 class="con">Créer un compte</h1>

        <div class="connex">
            <div class="ispm"><img src="/IMAGES/logo-ispm.png" alt="Logo ISPM"></div>

            <div class="form">
                <?php if (!empty($errors) && !$isAjax): ?>
                    <div style="background:#fff0f0;color:#5c2c22;padding:10px;border-radius:8px;margin-bottom:12px;">
                        <ul style="margin:0;padding-left:18px;">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
                <form action="" method="post">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                    <div class="field">
                        <label for="nom">Nom</label>
                        <input type="text" placeholder="Nom" required id="nom" name="nom" autocomplete="family-name">
                    </div>

                    <div class="field">
                        <label for="prenom">Prénom</label>
                        <input type="text" placeholder="Prénom" required id="prenom" name="prenom" autocomplete="given-name">
                    </div>

                    <div class="field">
                        <label for="email">Email</label>
                        <input type="email" placeholder="nom@email.com" required id="email" name="email" autocomplete="username">
                    </div>

                    <div class="field">
                        <label for="password">Mot de passe</label>
                        <input type="password" placeholder="Mot de passe" required id="password" name="password" minlength="8" autocomplete="new-password">
                    </div>

                    <div class="field">
                        <label for="password2">Confirmer le mot de passe</label>
                        <input type="password" placeholder="Confirmer le mot de passe" required id="password2" name="password2" minlength="8" autocomplete="new-password">
                    </div>

                    <button type="submit" class="submit-btn">Créer mon compte</button>
                </form>

                <div class="connecter">
                    <span>Déjà un compte ?</span>
                    <a href="login.php">Se connecter</a>
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