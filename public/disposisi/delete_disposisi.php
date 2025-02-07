<?php
require_once '../../includes/config.php';
require_once 'update_nomor.php';

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];

    // Mulai transaction
    $pdo->beginTransaction();

    try {
        // Hapus data
        $query = "DELETE FROM disposisi_surat WHERE id = :id";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            // Update nomor urut
            if (updateNomorUrut($pdo)) {
                $pdo->commit();
                header("Location: disposisi.php");
                exit;
            } else {
                throw new Exception("Gagal mengupdate nomor urut");
            }
        } else {
            throw new Exception("Gagal menghapus data");
        }
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "Error: " . $e->getMessage();
    }
}
