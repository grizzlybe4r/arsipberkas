<?php
require_once '../includes/auth.php';
require_once '../includes/functions.php';
check_login();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="assets/style.css">
</head>

<body>
    <!-- Toggle Button for Mobile -->
    <button class="navbar-toggle" id="sidebarToggle">
        <i class="bi bi-list"></i>
    </button>

    <!-- Backdrop for mobile -->
    <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

    <div class="container-fluid p-0">
        <div class="row g-0">
            <!-- Sidebar -->
            <div class="sidebar bg-white p-3" id="sidebar">
                <div class="d-flex align-items-center mb-4">
                    <i class="bi bi-bank fs-2 text-primary me-2"></i>
                    <h4 class="mb-0">Sistem Informasi Bank Kulon Progo</h4>
                </div>

                <hr>

                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active text-white bg-primary' : 'text-dark'; ?>" href="dashboard.php">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <?php if ($role === 'ti_admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'add_user.php' ? 'active text-white bg-primary' : 'text-dark'; ?>" href="add_user.php">
                                <i class="bi bi-person-plus"></i> Tambah User
                            </a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link text-danger fw-bold" href="logout.php">
                            <i class="bi bi-box-arrow-right text-danger"></i> Keluar
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 content">
                <div class="user-welcome">
                    <h2>Selamat Datang, <?= htmlspecialchars($_SESSION['user']['username']); ?></h2>

                </div>

                <div class="row">
                    <!-- Cek Rekening Card -->
                    <?php if ($role <> 'teller'): ?>
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="card-title mb-0"><i class="bi bi-search"></i> Cek Berkas Kredit</h5>
                                </div>
                                <div class="card-body">
                                    <form method="POST" class="needs-validation" action="#previewCardPDF" novalidate>
                                        <div class="mb-3">
                                            <label class="form-label">Nomor Berkas:</label>
                                            <input type="text" name="norek" class="form-control" required>
                                            <div class="invalid-feedback">
                                                Nomor rekening harus diisi
                                            </div>
                                        </div>
                                        <button type="submit" name="cek" class="btn btn-primary">
                                            Cek
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($role === 'ti_admin' || $role === 'adminkredit'): ?>
                        <!-- Upload Card Berkas Kredit-->
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="card-title mb-0"><i class="bi bi-upload"></i> Upload Berkas Kredit</h5>
                                </div>
                                <div class="card-body">
                                    <form action="" method="post" enctype="multipart/form-data" class="needs-validation" novalidate id="uploadFormBerkas">
                                        <div class="mb-3">
                                            <div class="upload-drop-zone" id="dropZoneBerkas">
                                                <i class="bi bi-cloud-upload fs-2"></i>
                                                <p class="mb-2">Drag & drop file PDF di sini atau klik untuk memilih</p>
                                                <input type="file" name="files[]" class="form-control" accept=".pdf" multiple required id="fileInputBerkas" style="display: none;">
                                                <button type="button" class="btn btn-outline-primary" id="browseButtonBerkas">Pilih File</button>
                                            </div>
                                            <div class="selected-files-list" id="filesListBerkas"></div>
                                        </div>
                                        <button type="submit" name="upload" class="btn btn-primary" id="uploadButtonBerkas" disabled>Unggah</button>

                                    </form>
                                </div>
                            </div>
                        </div>


                    <?php endif; ?>
                </div>

                <!-- Alert Messages -->
                <?php if (isset($message_kredit)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($message_kredit) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if (isset($error_kredit)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill"></i> <?= htmlspecialchars($error_kredit) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Cek & Upload Spesimen TTD -->
                <?php if ($role === 'teller' || $role === 'ti_admin'): ?>
                    <div class="row">

                        <!-- Upload Card Spesimen Tanda Tangan -->
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    <h5 class="card-title mb-0"><i class="bi bi-upload"></i> Upload Berkas Spesimen Tanda Tangan</h5>
                                </div>
                                <div class="card-body">
                                    <form action="" method="post" enctype="multipart/form-data" class="needs-validation" novalidate id="uploadFormSpesimen">
                                        <div class="mb-3">
                                            <div class="upload-drop-zone" id="dropZoneSpesimen">
                                                <i class="bi bi-cloud-upload fs-2"></i>
                                                <p class="mb-2">Drag & drop file JPG di sini atau klik untuk memilih</p>
                                                <input type="file" name="files[]" class="form-control" accept=".jpg,.jpeg,.png" multiple required id="fileInputSpesimen" style="display: none;">
                                                <button type="button" class="btn btn-outline-primary" id="browseButtonSpesimen">Pilih File</button>
                                            </div>
                                            <div class="selected-files-list" id="filesListSpesimen"></div>
                                        </div>
                                        <button type="submit" name="upload_ttd" class="btn btn-success" id="uploadButtonSpesimen" disabled>Unggah</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Cek Spesimen -->
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    <h5 class="card-title mb-0"><i class="bi bi-search"></i> Cek Spesimen Tanda Tangan</h5>
                                </div>
                                <div class="card-body">
                                    <form method="POST" class="needs-validation" action="#previewCardTTD" novalidate>
                                        <div class="mb-3">
                                            <label class="form-label">Nomor Rekening:</label>
                                            <input type="text" name="norek" class="form-control" required>
                                            <div class="invalid-feedback">
                                                Nomor rekening harus diisi
                                            </div>
                                        </div>
                                        <button type="submit" name="cek_ttd" class="btn btn-success">
                                            Cek
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>



                    </div>
                    <!-- Alert Messages Spesimen Tanda Tangan-->
                    <?php if (isset($message_ttd)): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($message_ttd) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($error_ttd)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle-fill"></i> <?= htmlspecialchars($error_ttd) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- JPG Preview Section -->
                    <?php if (isset($jpg_url_ttd)): ?>
                        <div class="card mt-4" id="previewCardTTD">
                            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="bi bi-file-earmark-image"></i> Preview Spesimen Tanda Tangan</h5>
                                <a href="<?= htmlspecialchars($jpg_url_ttd) ?>" target="_blank" class="btn btn-light text-dark btn-sm me-2">
                                    <i class="bi bi-box-arrow-up-right"></i> Buka di Tab Baru
                                </a>
                            </div>
                            <div class="card-body text-center">
                                <img src="<?= htmlspecialchars($jpg_url_ttd) ?>" alt="Preview Spesimen Tanda Tangan" class="img-fluid rounded" style="max-height: 600px;">

                            </div>
                        </div>
                    <?php endif; ?>


                <?php endif; ?>


                <!-- PDF Preview Section -->
                <?php if (isset($pdf_url)): ?>
                    <div class="card mt-4" id="previewCardPDF">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="bi bi-file-earmark-pdf"></i> Preview Berkas Kredit</h5>
                            <div class="btn-group">
                                <a href="<?= htmlspecialchars($pdf_url) ?>" target="_blank" class="btn btn-light btn-sm me-2">
                                    <i class="bi bi-box-arrow-up-right"></i> Buka di Tab Baru
                                </a>
                                <?php if ($role === 'adminkredit' || $role === 'ti_admin'): ?>
                                    <a href="<?= htmlspecialchars($pdf_url) ?>&download=1" class="btn btn-light btn-sm">
                                        <i class="bi bi-download"></i> Unduh PDF
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="card-body">
                            <object
                                data="<?= htmlspecialchars($pdf_url) ?>"
                                type="application/pdf"
                                width="100%"
                                height="600px">
                                <p class="text-center">
                                    Browser Anda tidak mendukung preview PDF.
                                    <br>
                                    <a href="<?= htmlspecialchars($pdf_url) ?>" target="_blank" class="btn btn-primary btn-sm mt-2">
                                        <i class="bi bi-box-arrow-up-right"></i> Buka di Tab Baru
                                    </a>
                                    <?php if ($role === 'adminkredit' || $role === 'ti_admin'): ?>
                                        <a href="<?= htmlspecialchars($pdf_url) ?>&download=1" class="btn btn-primary btn-sm mt-2 ms-2">
                                            <i class="bi bi-download"></i> Unduh PDF
                                        </a>
                                    <?php endif; ?>
                                </p>
                            </object>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            'use strict';

            // Form validation
            var forms = document.querySelectorAll('.needs-validation');
            Array.prototype.slice.call(forms).forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });

            // Handle upload functionality
            function setupUploadHandlers(formId, dropZoneId, fileInputId, filesListId, uploadButtonId, validTypes) {
                const dropZone = document.getElementById(dropZoneId);
                const fileInput = document.getElementById(fileInputId);
                const filesList = document.getElementById(filesListId);
                const uploadButton = document.getElementById(uploadButtonId);

                console.log('Setup handlers for:', {
                    dropZone,
                    fileInput,
                    filesList,
                    uploadButton
                });

                // Pastikan semua elemen ada
                if (!dropZone || !fileInput || !filesList || !uploadButton) {
                    console.error('Some elements are missing for', formId);
                    return;
                }

                // Drag and drop
                dropZone.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    dropZone.classList.add('dragover');
                });

                dropZone.addEventListener('dragleave', () => {
                    dropZone.classList.remove('dragover');
                });

                dropZone.addEventListener('drop', (e) => {
                    e.preventDefault();
                    dropZone.classList.remove('dragover');
                    const files = e.dataTransfer.files;
                    handleFiles(files);
                });

                // Browse button
                const browseButton = dropZone.querySelector('.btn-outline-primary');
                browseButton.addEventListener('click', () => {
                    fileInput.click();
                });

                // File input change
                fileInput.addEventListener('change', (e) => {
                    handleFiles(e.target.files);
                });

                // Handle files and validation
                function handleFiles(files) {
                    filesList.innerHTML = '';
                    let validFiles = true;

                    Array.from(files).forEach(file => {
                        const div = document.createElement('div');
                        div.className = 'selected-file-item';

                        // Validate file type
                        if (!validTypes.includes(file.type)) {
                            div.innerHTML = `
                        <span class="text-danger">${file.name} (Format tidak sesuai!)</span>
                        <i class="bi bi-x-circle remove-file"></i>
                    `;
                            validFiles = false;
                        } else {
                            div.innerHTML = `
                        <span>${file.name}</span>
                        <i class="bi bi-x-circle remove-file"></i>
                    `;
                        }

                        filesList.appendChild(div);
                    });

                    // Enable upload button if all files are valid
                    uploadButton.disabled = !validFiles || files.length === 0;
                }

                // Remove file
                filesList.addEventListener('click', (e) => {
                    if (e.target.classList.contains('remove-file')) {
                        const dt = new DataTransfer();
                        const files = fileInput.files;
                        const parent = e.target.parentElement;
                        const index = Array.from(filesList.children).indexOf(parent);

                        for (let i = 0; i < files.length; i++) {
                            if (i !== index) {
                                dt.items.add(files[i]);
                            }
                        }

                        fileInput.files = dt.files;
                        parent.remove();
                        uploadButton.disabled = fileInput.files.length === 0;
                    }
                });
            }

            // Setup handlers for both forms
            setupUploadHandlers(
                'uploadFormSpesimen',
                'dropZoneSpesimen',
                'fileInputSpesimen',
                'filesListSpesimen',
                'uploadButtonSpesimen',
                ['image/jpeg', 'image/png']
            );

            setupUploadHandlers(
                'uploadFormBerkas',
                'dropZoneBerkas',
                'fileInputBerkas',
                'filesListBerkas',
                'uploadButtonBerkas',
                ['application/pdf']
            );

            // Sidebar toggle functionality
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarBackdrop = document.getElementById('sidebarBackdrop');

            function toggleSidebar() {
                sidebar.classList.toggle('show');
                sidebarBackdrop.classList.toggle('show');
            }

            sidebarToggle.addEventListener('click', toggleSidebar);
            sidebarBackdrop.addEventListener('click', toggleSidebar);

            // Close sidebar when window is resized to desktop view
            window.addEventListener('resize', function() {
                if (window.innerWidth > 768) {
                    sidebar.classList.remove('show');
                    sidebarBackdrop.classList.remove('show');
                }
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const hash = window.location.hash;
            if (hash === '#previewCardTTD' || hash === '#previewCardPDF') {
                const target = document.querySelector(hash);
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth'
                    });
                }
            }
        });
    </script>
</body>

</html>