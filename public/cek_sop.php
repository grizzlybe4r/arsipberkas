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

                <!-- Cek SOP -->
                <div class="row">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-primary text-white d-flex align-items-center">
                                <i class="bi bi-search me-2"></i>
                                <h5 class="card-title mb-0">Cek Status SOP</h5>
                            </div>
                            <div class="card-body p-4">
                                <div class="search-container">
                                    <input
                                        type="text"
                                        id="searchInputSOP"
                                        class="form-control form-control-lg"
                                        placeholder="Ketik kata kunci..."
                                        style="font-size: 14px;"
                                        autocomplete="off">
                                    <div id="searchSuggestionsSOP" class="search-suggestions"></div>
                                </div>
                                <div id="searchResultsSOP" class="mt-4"></div>
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


            </div>



        </div>
    </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const searchInputSOP = document.getElementById('searchInputSOP');
            const searchSuggestionsSOP = document.getElementById('searchSuggestionsSOP');
            const searchResultsSOP = document.getElementById('searchResultsSOP');
            const paginationContainer = document.createElement('div');
            paginationContainer.id = 'paginationContainer';
            searchResultsSOP.parentNode.insertBefore(paginationContainer, searchResultsSOP.nextSibling);
            let typingTimerSOP;
            let currentPage = 1;
            let totalPages = 1;

            // Fungsi untuk membuat card SOP
            function createSopCard(sop) {
                return `
                <div class="card border-0 shadow-sm mb-3">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-start">
                            <div class="me-3">
                                <i class="bi bi-file-text text-primary" style="font-size: 2rem;"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div>
                                        <span class="text-primary">${sop.nomor_sop}</span> | 
                                        <span>${sop.tahun_disahkan}</span>
                                    </div>
                                </div>
                                <h5 class="mb-3">${sop.judul_sop}</h5>
                                <div>
                                    <a href="detail_sop.php?id=${sop.id}" class="btn btn-primary">
                                        <i class="bi bi-search me-1"></i> Selengkapnya
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            }

            // Fungsi untuk membuat pagination
            function createPagination(currentPage, totalPages) {
                let paginationHTML = '<nav><ul class="pagination justify-content-center">';
                if (currentPage > 1) {
                    paginationHTML += `<li class="page-item"><a class="page-link" href="#" data-page="${currentPage - 1}"> << </a></li>`;
                }
                for (let i = 1; i <= totalPages; i++) {
                    paginationHTML += `<li class="page-item ${i === currentPage ? 'active' : ''}"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
                }
                if (currentPage < totalPages) {
                    paginationHTML += `<li class="page-item"><a class="page-link" href="#" data-page="${currentPage + 1}"> >> </a></li>`;
                }
                paginationHTML += '</ul></nav>';
                return paginationHTML;
            }

            // Fungsi untuk memuat data SOP
            function loadSOP(page, searchTerm = '') {
                const url = searchTerm ? `search_sop.php?term=${encodeURIComponent(searchTerm)}&page=${page}` : `get_all_sop.php?page=${page}`;
                fetch(url)
                    .then(response => response.json())
                    .then(data => {
                        if (data.results.length > 0) {
                            searchResultsSOP.innerHTML = data.results.map(sop => createSopCard(sop)).join('');
                            paginationContainer.innerHTML = createPagination(data.currentPage, data.totalPages);
                        } else {
                            searchResultsSOP.innerHTML = '<div class="alert alert-info">Tidak ditemukan SOP yang sesuai dengan kata kunci.</div>';
                            paginationContainer.innerHTML = '';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        searchResultsSOP.innerHTML = '<div class="alert alert-danger">Terjadi kesalahan saat memuat data.</div>';
                        paginationContainer.innerHTML = '';
                    });
            }

            // Handle input pencarian
            searchInputSOP.addEventListener('input', function() {
                clearTimeout(typingTimerSOP);
                typingTimerSOP = setTimeout(() => {
                    const searchTerm = this.value.trim();
                    if (searchTerm.length > 2) {
                        loadSOP(1, searchTerm);
                    } else {
                        loadSOP(1);
                    }
                }, 500);
            });

            // Handle klik pagination
            paginationContainer.addEventListener('click', function(e) {
                if (e.target.tagName === 'A') {
                    e.preventDefault();
                    const page = e.target.getAttribute('data-page');
                    loadSOP(page, searchInputSOP.value.trim());
                }
            });

            // Load semua SOP saat pertama kali
            loadSOP(1);
        });
    </script>
</body>

</html>