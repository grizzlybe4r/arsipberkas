<?php
require_once '../includes/config.php'; // Pastikan file ini sudah menginisialisasi $pdo
require_once '../includes/auth.php';
check_login('ti_admin'); // Hanya bisa diakses oleh admin TI

// Tentukan role user yang login
$role = 'ti_admin';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    // Hash password untuk keamanan
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    try {
        // Simpan user ke database menggunakan PDO
        $query = "INSERT INTO users (username, password, role) VALUES (:username, :password, :role)";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':password', $hashed_password, PDO::PARAM_STR);
        $stmt->bindParam(':role', $role, PDO::PARAM_STR);

        if ($stmt->execute()) {
            $message = "User berhasil ditambahkan!";
        } else {
            $message = "Terjadi kesalahan saat menambahkan user.";
        }
    } catch (PDOException $e) {
        $message = "Terjadi kesalahan: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/style.css">
    <style>
        .card {
            max-width: 600px;
            /* Set a max width for the card */
            margin: auto;
            /* Center the card */
        }

        /* Mobile Responsiveness */
        @media (max-width: 768px) {
            .sidebar {
                width: 200px;
            }

            .nav-link {
                font-size: 14px;
                padding: 8px 10px;
            }
        }
    </style>
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
                        <a class="nav-link dropdown-toggle text-dark" href="#" id="dropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="bi bi-list-check"></i>
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

            <div class="card mt-3">
                <div class="card-header bg-light text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 text-dark">Tambah User Baru</h5>

                </div>

                <div class="card-body">
                    <?php if (isset($message)): ?>
                        <div class="alert <?php echo strpos($message, 'berhasil') !== false ? 'alert-success' : 'alert-danger'; ?> alert-dismissible fade show" role="alert">
                            <?php echo $message; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-person"></i></span>
                                <input type="text" class="form-control" id="username" name="username" required>
                                <div class="invalid-feedback">
                                    Username harus diisi
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-key"></i></span>
                                <input type="password" class="form-control" id="password" name="password" required>
                                <div class="invalid-feedback">
                                    Password harus diisi
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="role" class="form-label">Role</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
                                <select class="form-select" id="role" name="role" required>
                                    <option value="" selected disabled>Pilih Role</option>
                                    <option value="adminkredit">Admin Kredit</option>
                                    <option value="marketing">Marketing</option>
                                    <option value="ti_admin">TI (Admin)</option>
                                    <option value="teller">Teller</option>
                                    <option value="admin_dok">Admin Dokumen</option>
                                </select>
                                <div class="invalid-feedback">
                                    Role harus dipilih
                                </div>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-person-plus"></i> Tambah User
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>



        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            const sidebar = document.getElementById('sidebar');
            const contentWrapper = document.getElementById('contentWrapper');
            const sidebarToggle = document.getElementById('sidebarToggle');

            sidebarToggle.addEventListener('click', () => {
                sidebar.classList.toggle('sidebar-hidden');
                contentWrapper.style.marginLeft = sidebar.classList.contains('sidebar-hidden') ? '0' : '250px';
            });
        </script>
</body>

</html>