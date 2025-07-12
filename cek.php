<?php
// untuk memastikan session_start() hanya dijalankan sekali agar menghindari warning duplikat session.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}
