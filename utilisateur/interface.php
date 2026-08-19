<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>do re miTEndry | Accueil</title>
    <link rel="stylesheet" href="interface.css">
</head>


<body>
    <div class="scrolling-bg">
        <header class="navbar">
            <div class="logo">
                <h3>do re mi<span>TEndry</span></h3>
            </div>

            <input type="checkbox" id="menu-toggle" class="menu-toggle">
            <label for="menu-toggle" class="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </label>

            <nav>
                <ul>
                    <li><a href="instrument.html">Instruments</a></li>
                    <li><a href="cours.html">Cours</a></li>
                    <li><a href="Apropos.html">À propos</a></li>
                    <li class="mobile-only"><a href="deconnexion.html">Déconnexion</a></li>
                </ul>
            </nav>

            <a href="../logout.php" class="btn-logout">Déconnexion</a>
        </header>

        <main>
            <section class="hero">
                <div class="hero-text">
                    <div class="hero-title-row">
                        <h1>Apprends la musique <span>facilement</span></h1>
                    </div>
                    <p>Des cours simples et des exercices pratiques pour progresser à ton rythme.</p>

                </div>

                <div class="hero-image">
                    <img src="doremitendrylogo.jpeg" alt="Logo do re miTEndry">
                </div>
            </section>
        </main>

        <footer class="site-footer">
            <p class="footer-contact">
                <a href="tel:0343863935">034 38 639 35</a>
                <span class="footer-sep">•</span>
                <a href="https://facebook.com" target="_blank" rel="noopener" class="fb-link">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" aria-hidden="true">
                        <path
                            d="M22 12.06C22 6.5 17.52 2 12 2S2 6.5 2 12.06c0 5 3.66 9.15 8.44 9.94v-7.03H7.9v-2.91h2.54V9.86c0-2.5 1.49-3.89 3.78-3.89 1.1 0 2.24.2 2.24.2v2.46h-1.26c-1.24 0-1.63.77-1.63 1.56v1.87h2.78l-.44 2.91h-2.34V22c4.78-.79 8.44-4.94 8.44-9.94z" />
                    </svg>
                    Ra Mah
                </a>
                <a href="https://facebook.com" target="_blank" rel="noopener" class="fb-link">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" aria-hidden="true">
                        <path
                            d="M22 12.06C22 6.5 17.52 2 12 2S2 6.5 2 12.06c0 5 3.66 9.15 8.44 9.94v-7.03H7.9v-2.91h2.54V9.86c0-2.5 1.49-3.89 3.78-3.89 1.1 0 2.24.2 2.24.2v2.46h-1.26c-1.24 0-1.63.77-1.63 1.56v1.87h2.78l-.44 2.91h-2.34V22c4.78-.79 8.44-4.94 8.44-9.94z" />
                    </svg>
                    Fa'Ntsuh Tamby
                </a>

                <span class="footer-sep">•</span>
                <span>&copy; 2026 do re miTEndry</span>
            </p>
        </footer>
    </div>

</body>

</html>