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
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-dark <?php echo basename($_SERVER['PHP_SELF']) == 'cek_sk.php' || 'cek_sop.php' ? 'active text-white bg-primary' : 'text-dark'; ?>" href="#" id="dropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-list-check"></i>
                            Cek Berkas
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                            <li><a class="dropdown-item <?php echo basename($_SERVER['PHP_SELF']) == 'cek_sk.php' ? 'active text-white bg-primary' : 'text-dark'; ?>" href="cek_sk.php">Cek SK</a></li>
                            <li><a class="dropdown-item <?php echo basename($_SERVER['PHP_SELF']) == 'cek_sop.php' ? 'active text-white bg-primary' : 'text-dark'; ?>" href="cek_sop.php">Cek SOP</a></li>
                        </ul>
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
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-primary text-white d-flex align-items-center">
                                <i class="bi bi-search me-2"></i>
                                <h5 class="card-title mb-0">Cek Status SOP</h5>
                            </div>
                            <div class="card-body p-4">
                                <form method="POST" class="needs-validation" action="#previewCardSOP" novalidate>
                                    <div class="input-group input-group-lg">
                                        <input
                                            type="text"
                                            name="norek"
                                            class="form-control form-control-lg"
                                            placeholder="Ketik kata kunci..."
                                            aria-label="Nomor Rekening"
                                            aria-describedby="button-cek-sop"
                                            required>
                                        <button
                                            class="btn btn-success"
                                            type="submit"
                                            name="cek_sop"
                                            id="button-cek-sop">
                                            <i class="bi bi-search me-2"></i> Cek SOP
                                        </button>
                                        <div class="invalid-feedback">
                                            Nomor SOP harus diisi
                                        </div>
                                    </div>
                                </form>
                                <small class="text-muted mt-2 d-block text-center">
                                    Masukkan kata kunci untuk memeriksa SOP yang terkait.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Alert Modal -->
                <div class="modal fade" id="alertModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content border-0 shadow">
                            <div class="modal-header border-0 py-3">
                                <h5 class="modal-title fw-bold"></h5>
                                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body px-4 py-4">
                                <div class="text-center mb-4">
                                    <div class="alert-icon mb-3">
                                        <i class="bi" style="font-size: 3rem;"></i>
                                    </div>
                                    <div class="alert-message fs-5"></div>
                                </div>
                            </div>
                            <div class="modal-footer border-0 pt-0 pb-4">
                                <button type="button" class="btn btn-lg px-4 rounded-3" data-bs-dismiss="modal">Tutup</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PHP Alert Handler -->
                <?php foreach (['sk', 'sop', 'ttd', 'kredit'] as $type): ?>
                    <?php if (isset(${"message_$type"})): ?>
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const modal = new bootstrap.Modal(document.getElementById('alertModal'));
                                const alertModal = document.getElementById('alertModal');

                                alertModal.querySelector('.modal-header').className = 'modal-header border-0 py-3 bg-success-subtle';
                                alertModal.querySelector('.modal-title').textContent = 'Berhasil!';
                                alertModal.querySelector('.alert-icon i').className = 'bi bi-check-circle-fill text-success';
                                alertModal.querySelector('.alert-message').innerHTML = '<?= htmlspecialchars(${"message_$type"}) ?>';
                                alertModal.querySelector('.modal-footer .btn').className = 'btn btn-success btn-lg px-4 rounded-3';

                                modal.show();
                            });
                        </script>
                    <?php endif; ?>

                    <?php if (isset(${"error_$type"})): ?>
                        <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                const modal = new bootstrap.Modal(document.getElementById('alertModal'));
                                const alertModal = document.getElementById('alertModal');

                                alertModal.querySelector('.modal-header').className = 'modal-header border-0 py-3 bg-danger-subtle';
                                alertModal.querySelector('.modal-title').textContent = 'Gagal!';
                                alertModal.querySelector('.alert-icon i').className = 'bi bi-exclamation-circle-fill text-danger';
                                alertModal.querySelector('.alert-message').innerHTML = '<?= htmlspecialchars(${"error_$type"}) ?>';
                                alertModal.querySelector('.modal-footer .btn').className = 'btn btn-danger btn-lg px-4 rounded-3';

                                modal.show();
                            });
                        </script>
                    <?php endif; ?>
                <?php endforeach; ?>

                <!-- SOP Preview Section -->
                <?php if (isset($pdf_url_sop)): ?>
                    <div class="card mt-4" id="previewCardPDF">
                        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="bi bi-file-earmark-pdf"></i> Preview Berkas SK</h5>
                            <div class="btn-group">
                                <a href="<?= htmlspecialchars($pdf_url_sop) ?>" target="_blank" class="btn btn-light btn-sm me-2">
                                    <i class="bi bi-box-arrow-up-right"></i> Buka di Tab Baru
                                </a>
                                <?php if ($role === 'admin_dok' || $role === 'ti_admin'): ?>
                                    <a href="<?= htmlspecialchars($pdf_url_sop) ?>&download=1" class="btn btn-light btn-sm">
                                        <i class="bi bi-download"></i> Unduh PDF
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="card-body">
                            <object
                                data="<?= htmlspecialchars($pdf_url_sop) ?>"
                                type="application/pdf"
                                width="100%"
                                height="600px">
                                <p class="text-center">
                                    Browser Anda tidak mendukung preview PDF.
                                    <br>
                                    <a href="<?= htmlspecialchars($pdf_url_sop) ?>" target="_blank" class="btn btn-primary btn-sm mt-2">
                                        <i class="bi bi-box-arrow-up-right"></i> Buka di Tab Baru
                                    </a>
                                    <?php if ($role === 'admin_dok' || $role === 'ti_admin'): ?>
                                        <a href="<?= htmlspecialchars($pdf_url_sop) ?>&download=1" class="btn btn-primary btn-sm mt-2 ms-2">
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

            setupUploadHandlers(
                'uploadFormSK',
                'dropZoneSK',
                'fileInputSK',
                'filesListSK',
                'uploadButtonSK',
                ['application/pdf']
            );

            setupUploadHandlers(
                'uploadFormSOP',
                'dropZoneSOP',
                'fileInputSOP',
                'filesListSOP',
                'uploadButtonSOP',
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