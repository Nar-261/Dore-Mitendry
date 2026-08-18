<?php
session_start();

if (!empty($_SESSION['user_id'])) {
    if (($_SESSION['user_role'] ?? '') === 'admin') {
        header('Location: admin/dashboard.php');
    } else {
        header('Location: utilisateur/interface.php');
    }
} else {
    header('Location: login.php');
}
exit;
?>
