<?php
// Database connection
$host = 'localhost';
$user = 'root';
$password = 'mypassword';
$database = 'berkas_kredit';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Network path
define('NETWORK_PATH', '\\172.16.34.5\ftp\BERKAS KREDIT');
