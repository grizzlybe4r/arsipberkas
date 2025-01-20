<?php
session_start();

function check_login($role = null)
{
    // Waktu timeout dalam detik (3 jam = 3 * 60 * 60)
    $timeout_duration = 3 * 60 * 60;

    // Periksa apakah user sudah login
    if (!isset($_SESSION['user'])) {
        header("Location: login.php");
        exit();
    }

    // Periksa apakah sesi sudah melebihi batas waktu
    if (isset($_SESSION['login_time'])) {
        $elapsed_time = time() - $_SESSION['login_time'];
        if ($elapsed_time > $timeout_duration) {
            // Jika sudah timeout, hapus semua session dan redirect ke login
            session_unset();
            session_destroy();
            header("Location: login.php?timeout=1");
            exit();
        }
    }

    // Perbarui waktu login setiap ada aktivitas pengguna
    $_SESSION['login_time'] = time();

    // Periksa role jika diberikan parameter
    if ($role && $_SESSION['user']['role'] !== $role) {
        header("Location: dashboard.php");
        exit();
    }
}
