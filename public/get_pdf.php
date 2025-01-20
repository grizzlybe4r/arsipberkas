<?php
// get_pdf.php
require_once '../includes/auth.php';
require_once '../includes/functions.php';
check_login();

$network_path_kredit = '\\\\172.16.34.5\\ftp\\BERKAS KREDIT\\';
$role = $_SESSION['user']['role'];

if (isset($_GET['norek'])) {
    $norek = trim($_GET['norek']);
    // Search for PDF file
    $pdf_files = glob($network_path_kredit . $norek . "*.pdf");

    if (!empty($pdf_files)) {
        $pdf_file = $pdf_files[0]; // Get the first matching file

        if (file_exists($pdf_file)) {
            // Check if download was requested
            if (isset($_GET['download'])) {
                // Only allow download for admin_kredit and ti_admin
                if ($role === 'adminkredit' || $role === 'ti_admin') {
                    header('Content-Type: application/pdf');
                    header('Content-Disposition: attachment; filename="' . basename($pdf_file) . '"');
                    header('Cache-Control: public, must-revalidate, max-age=0');
                    header('Pragma: public');
                    header('Content-Length: ' . filesize($pdf_file));
                    readfile($pdf_file);
                    exit;
                } else {
                    header('HTTP/1.0 403 Forbidden');
                    echo 'Akses ditolak. Anda tidak memiliki izin untuk mengunduh file.';
                    exit;
                }
            } else {
                // For preview, send file inline
                header('Content-Type: application/pdf');
                header('Content-Disposition: inline; filename="' . basename($pdf_file) . '"');
                header('Cache-Control: public, must-revalidate, max-age=0');
                header('Pragma: public');
                header('Content-Length: ' . filesize($pdf_file));
                readfile($pdf_file);
                exit;
            }
        }
    }
}

// If we get here, file was not found
header('HTTP/1.0 404 Not Found');
echo 'File tidak ditemukan.';
