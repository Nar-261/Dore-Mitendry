<?php
session_start();

// Si l'utilisateur est connecté, on le redirige vers son tableau de bord respectif
if (!empty($_SESSION['user_id'])) {
    if (($_SESSION['user_role'] ?? '') === 'admin') {
        header('Location: admin/dashboard.php');
    } else {
        header('Location: utilisateur');
    }
} else {
    // Sinon, on le redirige vers la page de connexion
    header('Location: login.php');
}
exit;
?>
