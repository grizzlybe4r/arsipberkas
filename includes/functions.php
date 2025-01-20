<?php
require_once 'config.php';

$network_path_kredit = '\\\\172.16.34.5\\ftp\\BERKAS KREDIT\\';
$network_path_ttd = '\\\\172.16.34.5\\ftp\\TTD\\';
$role = $_SESSION['user']['role'];

// Handle multiple file upload berkas kredit
if (isset($_POST['upload']) && ($role === 'ti_admin' || $role === 'adminkredit')) {
    if (isset($_FILES['files'])) {
        $success_count = 0;
        $error_files = [];

        // Loop through each uploaded file
        for ($i = 0; $i < count($_FILES['files']['name']); $i++) {
            $file_name = $_FILES['files']['name'][$i];
            $temp_file = $_FILES['files']['tmp_name'][$i];

            // Sanitize filename and ensure it's a PDF
            if (strtolower(pathinfo($file_name, PATHINFO_EXTENSION)) !== 'pdf') {
                $error_files[] = $file_name . " (Bukan file PDF)";
                continue;
            }

            $safe_filename = preg_replace("/[^a-zA-Z0-9.-]/", "_", $file_name);
            $target_path = $network_path_kredit . $safe_filename;

            try {
                if (move_uploaded_file($temp_file, $target_path)) {
                    $success_count++;
                } else {
                    $error_files[] = $file_name;
                }
            } catch (Exception $e) {
                $error_files[] = $file_name . " (Error: " . $e->getMessage() . ")";
            }
        }

        // Set messages based on results
        if ($success_count > 0) {
            $message_kredit = "Berhasil mengupload " . $success_count . " file.";
        }
        if (!empty($error_files)) {
            $error_kredit = "Gagal mengupload file berikut: " . implode(", ", $error_files);
        }
    }
}

// Handle file check
if (isset($_POST['cek']) && !empty($_POST['norek'])) {
    $norek = trim($_POST['norek']);
    // Search for PDF file in network share
    $pdf_files = glob($network_path_kredit . $norek . "*.pdf");
    if (!empty($pdf_files)) {
        $pdf_file = $pdf_files[0]; // Get the first matching file
        // Convert Windows path to URL format for browser access
        $pdf_url = "get_pdf.php?norek=" . urlencode($norek);

        // Verify file exists and is readable
        if (!is_readable($pdf_file)) {
            $error_kredit = "File ditemukan tapi tidak dapat diakses. Hubungi administrator.";
            unset($pdf_url);
        }
    } else {
        $error_kredit = "File PDF Berkas Kredit untuk nomor berkas tersebut tidak ditemukan.";
    }
}

// Handle multiple file upload untuk spesimen tanda tangan
if (isset($_POST['upload_ttd']) && ($role === 'teller' || $role === 'ti_admin')) {
    if (isset($_FILES['files'])) {
        $success_count = 0;
        $error_files = [];

        // Daftar ekstensi yang diperbolehkan
        $allowed_extensions = ['jpg', 'jpeg', 'png'];

        // Loop melalui setiap file yang diupload
        for ($i = 0; $i < count($_FILES['files']['name']); $i++) {
            $file_name = $_FILES['files']['name'][$i];
            $temp_file = $_FILES['files']['tmp_name'][$i];

            // Periksa ekstensi file
            $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            if (!in_array($file_extension, $allowed_extensions)) {
                $error_files[] = $file_name . " (Bukan file dengan ekstensi yang diizinkan)";
                continue;
            }

            // Sanitasi nama file
            $safe_filename = preg_replace("/[^a-zA-Z0-9.-]/", "_", $file_name);
            $target_path = $network_path_ttd . $safe_filename;

            try {
                if (move_uploaded_file($temp_file, $target_path)) {
                    $success_count++;
                } else {
                    $error_files[] = $file_name;
                }
            } catch (Exception $e) {
                $error_files[] = $file_name . " (Error: " . $e->getMessage() . ")";
            }
        }

        // Set pesan berdasarkan hasil
        if ($success_count > 0) {
            $message_ttd = "Berhasil mengupload " . $success_count . " file spesimen tanda tangan.";
        }
        if (!empty($error_files)) {
            $error_ttd = "Gagal mengupload file berikut: " . implode(", ", $error_files);
        }
    }
}


// Handle file check untuk spesimen tanda tangan dalam format JPG
if (isset($_POST['cek_ttd']) && !empty($_POST['norek'])) {
    $norek = trim($_POST['norek']);
    // Search for JPG file in network share
    $jpg_files_ttd = glob($network_path_ttd . $norek . "*.jpg");
    if (!empty($jpg_files_ttd)) {
        $jpg_file_ttd = $jpg_files_ttd[0]; // Get the first matching file
        // Convert Windows path to URL format for browser access
        $jpg_url_ttd = "get_image_ttd.php?norek=" . urlencode($norek);

        // Verify file exists and is readable
        if (!is_readable($jpg_file_ttd)) {
            $error_ttd = "File ditemukan tapi tidak dapat diakses. Hubungi administrator.";
            unset($jpg_url_ttd);
        }
    } else {
        $error_ttd = "File JPG spesimen tanda tangan untuk nomor rekening tersebut tidak ditemukan.";
    }
}
