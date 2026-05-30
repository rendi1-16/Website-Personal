<?php
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/functions.php';
session_unset();
session_destroy();
session_start();
setFlash('success', 'Kamu berhasil logout.');
header('Location: ' . BASE_URL . '/login.php');
exit;
