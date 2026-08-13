<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

$userName = htmlspecialchars($_SESSION['user_name'] ?? 'Apprenant');
$userRole = ucfirst(htmlspecialchars($_SESSION['user_role'] ?? 'apprenant'));
$avatarLetter = htmlspecialchars(strtoupper(substr($userName, 0, 1)) ?: 'A');
?><!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>DoReMiTendry | Tableau de bord</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="../assets/css/dashboard.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
</head>
<body>
  <div class="app-shell">
    <aside class="sidebar">
      <div class="brand">
        <div class="brand-logos">
          <img class="logo-static" src="../IMAGES/logo version fn.jpg" alt="Logo DoReMiTendry">
          <div class="logo-overlay" aria-hidden="true">
            <img class="logo-slide" src="../IMAGES/logo-ispm.png" alt="Logo ISPM">
          </div>
        </div>
        <div>
          <h1>DoReMiTendry</h1>
          <p>Apprendre la musique, vivre la passion</p>
        </div>
      </div>

      <nav>
        <a href="#" class="active"><i class="fa-solid fa-table-columns"></i> Tableau de bord</a>
        <a href="#"><i class="fa-solid fa-book-open"></i> Mes cours</a>
        <a href="#"><i class="fa-solid fa-guitar"></i> Instruments</a>
        <a href="#"><i class="fa-solid fa-route"></i> Parcours d'apprentissage</a>
        <a href="#"><i class="fa-solid fa-dumbbell"></i> Exercices</a>
        <a href="#"><i class="fa-solid fa-music"></i> Partitions</a>
        <a href="#"><i class="fa-solid fa-heart"></i> Favoris</a>
        <a href="#"><i class="fa-solid fa-certificate"></i> Certificats</a>
        <a href="#"><i class="fa-solid fa-message"></i> Messages</a>
        <a href="#"><i class="fa-solid fa-gear"></i> Paramètres</a>
        <a href="../logout.php"><i class="fa-solid fa-right-from-bracket"></i> Déconnexion</a>
      </nav>
    </aside>

    <main class="main">
      <header class="topbar">
        <div class="topbar-left">
          <button class="icon-btn"><i class="fa-solid fa-bars"></i></button>
          <div class="search">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" placeholder="Rechercher un cours, une leçon, un exercice..." />
          </div>
        </div>
        <div class="userbox">
          <button class="icon-btn"><i class="fa-solid fa-bell"></i></button>
          <button class="icon-btn"><i class="fa-solid fa-envelope"></i></button>
          <div class="avatar"><?= $avatarLetter ?></div>
          <div>
            <strong><?= $userName ?></strong><br />
            <small><?= $userRole ?></small>
          </div>
        </div>
      </header>

      <section class="hero">
        <div>
          <p class="badge">Bienvenue</p>
          <h2>Bienvenue, <?= $userName ?> 👋</h2>
          <p>Continuez votre apprentissage et progressez à votre rythme.</p>
        </div>
        <div class="progress-ring"><span>68%</span></div>
      </section>

      <div class="grid grid-2">
        <section class="card">
          <div class="section-title">
            <h3>Reprendre là où vous vous êtes arrêté</h3>
            <span class="badge">En cours</span>
          </div>
          <div class="grid grid-3">
            <article class="course-card">
              <img src="../assets/images/piano.jpg" alt="Piano" />
              <div class="badge">Piano</div>
              <h4>Accords et rythmes - Niveau 1</h4>
              <p>Leçon actuelle : Techniques de main gauche</p>
              <div class="progress-bar"><span style="width:72%"></span></div>
              <a href="#" class="btn btn-gold">Lecture</a>
            </article>
            <article class="course-card">
              <img src="../assets/images/guitar.jpg" alt="Guitare" />
              <div class="badge">Guitare</div>
              <h4>Rythmes et accompagnement</h4>
              <p>Leçon actuelle : Accords de base</p>
              <div class="progress-bar"><span style="width:58%"></span></div>
              <a href="#" class="btn btn-dark">Lecture</a>
            </article>
            <article class="course-card">
              <img src="../assets/images/flute.jpg" alt="Flûte" />
              <div class="badge">Flûte</div>
              <h4>Solfège - Lecture de notes</h4>
              <p>Leçon actuelle : Lecture de portée</p>
              <div class="progress-bar"><span style="width:40%"></span></div>
              <a href="#" class="btn btn-dark">Lecture</a>
            </article>
          </div>
        </section>

        <section class="card">
          <div class="section-title">
            <h3>Statistiques apprenant</h3>
            <span class="badge">Aujourd'hui</span>
          </div>
          <div class="stat-grid">
            <div class="stat-card">
              <h4>12</h4>
              <p>Série d'apprentissage</p>
            </div>
            <div class="stat-card">
              <h4>18h</h4>
              <p>Temps total</p>
            </div>
            <div class="stat-card">
              <h4>46</h4>
              <p>Leçons terminées</p>
            </div>
            <div class="stat-card">
              <h4>3</h4>
              <p>Certificats</p>
            </div>
          </div>
        </section>
      </div>

      <div class="grid grid-2" style="margin-top:20px;">
        <section class="card">
          <div class="section-title">
            <h3>Parcours d'apprentissage</h3>
            <span class="badge">3 parcours</span>
          </div>
          <div class="grid grid-3">
            <article class="course-card">
              <img src="../assets/images/piano.jpg" alt="Parcours Piano" />
              <h4>Piano</h4>
              <p>Débutant • 12 modules • 48 leçons</p>
              <div class="progress-bar"><span style="width:68%"></span></div>
              <a href="#" class="btn btn-gold">Continuer</a>
            </article>
            <article class="course-card">
              <img src="../assets/images/guitar.jpg" alt="Parcours Guitare" />
              <h4>Guitare</h4>
              <p>Débutant • 10 modules • 40 leçons</p>
              <div class="progress-bar"><span style="width:55%"></span></div>
              <a href="#" class="btn btn-dark">Continuer</a>
            </article>
            <article class="course-card">
              <img src="../assets/images/flute.jpg" alt="Parcours Flûte" />
              <h4>Flûte</h4>
              <p>Débutant • 8 modules • 32 leçons</p>
              <div class="progress-bar"><span style="width:30%"></span></div>
              <a href="#" class="btn btn-dark">Continuer</a>
            </article>
          </div>
        </section>

        <section class="card">
          <div class="section-title">
            <h3>Derniers badges obtenus</h3>
            <span class="badge">Nouveau</span>
          </div>
          <div class="badge-list">
            <div class="badge-item">
              <div class="badge-ico"><i class="fa-solid fa-award"></i></div>
              <div><strong>Persévérant</strong><br /><small>10 jours d'apprentissage</small></div>
            </div>
            <div class="badge-item">
              <div class="badge-ico"><i class="fa-solid fa-drum"></i></div>
              <div><strong>Rythme Master</strong><br /><small>Maîtrise les rythmes</small></div>
            </div>
            <div class="badge-item">
              <div class="badge-ico"><i class="fa-solid fa-book"></i></div>
              <div><strong>Lecteur assidu</strong><br /><small>20 leçons complétées</small></div>
            </div>
          </div>
        </section>
      </div>

      <section class="card promo" style="margin-top:20px;">
        <h3>Pass Premium</h3>
        <p>Accédez à tous les cours, ressources et avantages exclusifs.</p>
        <a href="#" class="btn btn-gold">Découvrir</a>
      </section>
    </main>
  </div>
</body>
</html>

