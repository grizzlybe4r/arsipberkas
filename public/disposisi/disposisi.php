<?php
require_once '../../includes/config.php';
require_once '../../includes/auth.php';
check_login();
$role = $_SESSION['user']['role'];

// Initialize pagination variables
$rows_per_page = isset($_GET['rows']) ? (int)$_GET['rows'] : 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $rows_per_page;

// Get filter parameters
$selected_bulan = isset($_GET['bulan']) ? $_GET['bulan'] : '';
$selected_tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

try {
    // Build the base query
    $base_query = "FROM disposisi_surat WHERE 1=1";
    $params = [];

    // Add filter conditions
    if (!empty($selected_bulan)) {
        $base_query .= " AND MONTH(tanggal_masuk) = :bulan";
        $params[':bulan'] = $selected_bulan;
    }
    if (!empty($selected_tahun)) {
        $base_query .= " AND YEAR(tanggal_masuk) = :tahun";
        $params[':tahun'] = $selected_tahun;
    }

    // Get total rows for pagination
    $count_query = "SELECT COUNT(*) as count " . $base_query;
    $stmt = $pdo->prepare($count_query);
    $stmt->execute($params);
    $total_rows = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    $total_pages = ceil($total_rows / $rows_per_page);

    // Get the filtered data with pagination
    $query = "SELECT * " . $base_query . " ORDER BY tanggal_masuk DESC, id DESC LIMIT :offset, :rows";
    $stmt = $pdo->prepare($query);

    // Bind all parameters
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
    $stmt->bindParam(':rows', $rows_per_page, PDO::PARAM_INT);

    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Modify export URL to include current filters
    $export_url = "export_excel.php";
    $filter_params = [];
    if (!empty($selected_bulan)) $filter_params[] = "bulan=" . $selected_bulan;
    if (!empty($selected_tahun)) $filter_params[] = "tahun=" . $selected_tahun;
    if (!empty($filter_params)) {
        $export_url .= "?" . implode("&", $filter_params);
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}

// Function to get month name in Indonesian
function getNamaBulan($bulan)
{
    $bulan_list = [
        '01' => 'Januari',
        '02' => 'Februari',
        '03' => 'Maret',
        '04' => 'April',
        '05' => 'Mei',
        '06' => 'Juni',
        '07' => 'Juli',
        '08' => 'Agustus',
        '09' => 'September',
        '10' => 'Oktober',
        '11' => 'November',
        '12' => 'Desember'
    ];
    return isset($bulan_list[$bulan]) ? $bulan_list[$bulan] : '';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Disposisi Surat</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/style.css">

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
                        <?php if ($role === 'sekre'): ?>
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'disposisi.php' ? 'active text-white bg-primary' : 'text-dark'; ?>" href="disposisi.php">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        <?php else: ?>
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active text-white bg-primary' : 'text-dark'; ?>" href="../dashboard.php">
                                <i class="bi bi-speedometer2"></i> Dashboard
                            </a>
                        <?php endif; ?>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle <?php echo (($role !== 'sekre' && basename($_SERVER['PHP_SELF']) == 'disposisi.php') || in_array(basename($_SERVER['PHP_SELF']), ['cek_sk.php', 'cek_sop.php'])) ? 'active text-white bg-primary' : 'text-dark'; ?>" href="#" id="dropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-list-check"></i> Cek Berkas
                        </a>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                            <li><a class="dropdown-item" href="../cek_sk.php">Cek SK</a></li>
                            <li><a class="dropdown-item" href="../cek_sop.php">Cek SOP</a></li>
                            <?php if ($role !== 'sekre'): ?>
                                <li><a class="dropdown-item <?php echo basename($_SERVER['PHP_SELF']) == 'disposisi.php' ? 'active text-white bg-primary' : 'text-dark'; ?>" href="disposisi.php">Disposisi Surat</a></li>
                            <?php endif; ?>
                        </ul>
                    </li>
                    <?php if ($role === 'ti_admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == '../add_user.php' ? 'active text-white bg-primary' : 'text-dark'; ?>" href="../add_user.php">
                                <i class="bi bi-person-plus"></i> Kelola User
                            </a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link text-danger fw-bold" href="../logout.php">
                            <i class="bi bi-box-arrow-right text-danger"></i> Keluar
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Main Content -->
            <div class="col content">
                <div class="container py-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1 class="h2">Disposisi Surat</h1>
                        <div class="d-flex gap-2">
                            <a href="export_excel.php<?= !empty($_GET) ? '?' . http_build_query($_GET) : '' ?>" class="btn btn-success">
                                <i class="fas fa-file-excel me-1"></i> Export Excel
                            </a>
                            <?php if ($role === 'sekre'): ?>
                                <a href="add_disposisi.php" class="btn btn-primary">
                                    <i class="fas fa-plus me-1"></i> Tambah Data
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Rest of your existing content -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <div class="row mb-3 align-items-end">
                                <div class="col-md-4">
                                    <form method="get" class="d-flex align-items-center">
                                        <label for="rows" class="me-2">Tampilkan:</label>
                                        <select name="rows" id="rows" class="form-select w-auto" onchange="this.form.submit()">
                                            <option value="10" <?= $rows_per_page == 10 ? 'selected' : '' ?>>10</option>
                                            <option value="20" <?= $rows_per_page == 20 ? 'selected' : '' ?>>20</option>
                                            <option value="50" <?= $rows_per_page == 50 ? 'selected' : '' ?>>50</option>
                                        </select>
                                        <span class="ms-2">entries</span>
                                    </form>
                                </div>
                                <div class="col-md-4">
                                    <form method="get" id="filterForm" class="d-flex align-items-end gap-2">
                                        <div class="flex-grow-1">
                                            <label for="bulan" class="form-label">Filter Bulan:</label>
                                            <select name="bulan" id="bulan" class="form-select" onchange="this.form.submit()">
                                                <option value="">Semua Bulan</option>
                                                <?php
                                                $bulan_list = [
                                                    '01' => 'Januari',
                                                    '02' => 'Februari',
                                                    '03' => 'Maret',
                                                    '04' => 'April',
                                                    '05' => 'Mei',
                                                    '06' => 'Juni',
                                                    '07' => 'Juli',
                                                    '08' => 'Agustus',
                                                    '09' => 'September',
                                                    '10' => 'Oktober',
                                                    '11' => 'November',
                                                    '12' => 'Desember'
                                                ];
                                                $selected_bulan = isset($_GET['bulan']) ? $_GET['bulan'] : '';

                                                foreach ($bulan_list as $value => $nama) {
                                                    $selected = ($selected_bulan == $value) ? 'selected' : '';
                                                    echo "<option value='{$value}' {$selected}>{$nama}</option>";
                                                }
                                                $current_year = date('Y');

                                                // Get unique years from database
                                                $year_query = "SELECT DISTINCT YEAR(tanggal_masuk) as year FROM disposisi_surat ORDER BY year DESC";
                                                $year_result = $pdo->query($year_query);
                                                $years = $year_result->fetchAll(PDO::FETCH_COLUMN);

                                                if (empty($years)) {
                                                    $years = [$current_year];
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="flex-grow-1">
                                            <label for="tahun" class="form-label">Tahun:</label>
                                            <select name="tahun" id="tahun" class="form-select" onchange="this.form.submit()">
                                                <?php
                                                $selected_tahun = isset($_GET['tahun']) ? $_GET['tahun'] : $current_year;
                                                foreach ($years as $year) {
                                                    echo "<option value='{$year}' " .
                                                        ($selected_tahun == $year ? 'selected' : '') .
                                                        ">{$year}</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-md-4">
                                    <div class="d-flex justify-content-end">
                                        <?php if (!empty($selected_bulan)): ?>
                                            <a href="?tahun=<?= $selected_tahun ?>" class="btn btn-outline-secondary">
                                                <i class="fas fa-times me-1"></i>
                                                Reset Filter
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-hover table-bordered align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-center">No</th>
                                            <th>Kode</th>
                                            <th>Tanggal Surat</th>
                                            <th>Tanggal Masuk</th>
                                            <th>Nomer Surat</th>
                                            <th>Dari</th>
                                            <th>Perihal</th>
                                            <th>Instruksi</th>
                                            <th>Diteruskan</th>
                                            <th class="text-center">File</th>
                                            <?php if ($role === 'sekre' || $role === 'ti_admin'): ?>
                                                <th class="text-center">Aksi</th>
                                            <?php endif; ?>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $nomor = 1; // Inisialisasi nomor urut
                                        foreach ($result as $row):
                                        ?>
                                            <tr>
                                                <td class="text-center"><?= $nomor++ ?></td>
                                                <td><?= htmlspecialchars($row['kode'] ?? '') ?></td>
                                                <td><?= htmlspecialchars($row['tanggal_surat'] ?? '') ?></td>
                                                <td><?= htmlspecialchars($row['tanggal_masuk'] ?? '') ?></td>
                                                <td><?= htmlspecialchars($row['nomer_surat'] ?? '') ?></td>
                                                <td><?= htmlspecialchars($row['dari'] ?? '') ?></td>
                                                <td><?= htmlspecialchars($row['perihal'] ?? '') ?></td>
                                                <td><?= htmlspecialchars($row['instruksi'] ?? '') ?></td>
                                                <td><?= htmlspecialchars($row['diteruskan'] ?? '') ?></td>
                                                <td class="text-center">
                                                    <?php
                                                    if (!empty($row['file_path']) && isValidFile($row['file_path'])) {
                                                        $ext = strtolower(pathinfo($row['file_path'], PATHINFO_EXTENSION));
                                                        $file_url = htmlspecialchars($row['file_path']);

                                                        if ($ext == 'pdf') {
                                                            echo "<a href='{$file_url}' class='btn btn-sm btn-outline-primary' target='_blank'>
                                                    <i class='fas fa-file-pdf'></i> Lihat PDF
                                                </a>";
                                                        } elseif (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                                                            echo "<img src='{$file_url}' alt='Gambar' class='img-thumbnail' 
                                                    onclick='showModal(this)' style='cursor: zoom-in; max-height: 50px;' 
                                                    data-bs-toggle='tooltip' title='Klik untuk memperbesar'>";
                                                        }
                                                    } else {
                                                        echo "<span class='text-muted'><i class='fas fa-times'></i> Tidak tersedia</span>";
                                                    }
                                                    ?>
                                                </td>

                                                <?php if ($role === 'sekre' || $role === 'ti_admin'): ?>
                                                    <td class="text-center">
                                                        <div class="btn-group">
                                                            <a href="edit_disposisi.php?id=<?= htmlspecialchars($row['id'] ?? '') ?>"
                                                                class="btn btn-sm btn-warning me-1" title="Edit">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                            <a href="delete_disposisi.php?id=<?= htmlspecialchars($row['id'] ?? '') ?>"
                                                                class="btn btn-sm btn-danger"
                                                                onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')"
                                                                title="Hapus">
                                                                <i class="fas fa-trash"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                <?php endif; ?>

                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <nav aria-label="Page navigation">
                                <ul class="pagination justify-content-center mb-0">
                                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                                            <a class="page-link" href="?page=<?= $i ?>&rows=<?= $rows_per_page ?>">
                                                <?= $i ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <!-- Image Preview Modal -->
    <div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-labelledby="imagePreviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imagePreviewModalLabel">Preview Gambar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center position-relative p-0">
                    <div class="d-flex justify-content-center align-items-center" style="min-height: 70vh; background-color: #f8f9fa;">
                        <div class="loading-spinner position-absolute" style="display: none;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                        </div>
                        <img id="previewImage" class="img-fluid" style="max-height: 70vh; max-width: 100%; transition: transform 0.3s; object-fit: contain;">
                    </div>
                    <div class="zoom-controls py-3 bg-white border-top">
                        <div class="btn-group">
                            <button class="btn btn-outline-primary" onclick="zoomOut()">
                                <i class="fas fa-minus"></i>
                            </button>
                            <button class="btn btn-outline-primary" disabled id="zoomLevel">100%</button>
                            <button class="btn btn-outline-primary" onclick="zoomIn()">
                                <i class="fas fa-plus"></i>
                            </button>
                            <button class="btn btn-outline-secondary" onclick="resetZoom()">
                                <i class="fas fa-undo"></i> Reset
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Initialize variables
        let currentScale = 1;
        const imagePreviewModal = document.getElementById('imagePreviewModal');
        const previewImage = document.getElementById('previewImage');
        const zoomLevelBtn = document.getElementById('zoomLevel');
        const loadingSpinner = document.querySelector('.loading-spinner');
        const modal = new bootstrap.Modal(imagePreviewModal);

        // Show modal with image
        function showModal(imgElement) {
            loadingSpinner.style.display = 'block';
            previewImage.style.display = 'none';
            previewImage.src = imgElement.src;
            modal.show();

            previewImage.onload = function() {
                loadingSpinner.style.display = 'none';
                previewImage.style.display = 'block';
                resetZoom();
            };
        }

        // Zoom functions
        function updateZoom() {
            previewImage.style.transform = `scale(${currentScale})`;
            zoomLevelBtn.textContent = `${Math.round(currentScale * 100)}%`;
        }

        function zoomIn() {
            currentScale = Math.min(4, currentScale * 1.2); // Max zoom: 400%
            updateZoom();
        }

        function zoomOut() {
            currentScale = Math.max(0.5, currentScale / 1.2); // Min zoom: 50%
            updateZoom();
        }

        function resetZoom() {
            currentScale = 1;
            previewImage.style.transform = '';
            updateZoom();
        }

        // Keyboard controls
        document.addEventListener('keydown', function(e) {
            if (imagePreviewModal.classList.contains('show')) {
                switch (e.key) {
                    case 'Escape':
                        modal.hide();
                        break;
                    case '+':
                    case '=':
                        zoomIn();
                        break;
                    case '-':
                        zoomOut();
                        break;
                    case '0':
                        resetZoom();
                        break;
                }
            }
        });

        // Mouse wheel zoom
        imagePreviewModal.addEventListener('wheel', function(e) {
            if (imagePreviewModal.classList.contains('show')) {
                e.preventDefault();
                if (e.deltaY < 0) {
                    zoomIn();
                } else {
                    zoomOut();
                }
            }
        });

        // Reset zoom when modal is closed
        imagePreviewModal.addEventListener('hidden.bs.modal', function() {
            resetZoom();
        });
        document.addEventListener('DOMContentLoaded', function() {
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
    </script>
</body>

</html>