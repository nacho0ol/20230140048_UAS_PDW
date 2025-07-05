<?php
session_start();
require_once '../config.php';

// Cek jika pengguna belum login atau bukan mahasiswa
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'mahasiswa') {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_mahasiswa = $_SESSION['user_id'];
    $id_modul = $_POST['module_id'];
    $course_id = $_POST['course_id']; // Untuk redirect kembali
    $redirect_url = "course_detail.php?course_id=" . $course_id;

    // Cek apakah file sudah diupload
    if (isset($_FILES['file_laporan']) && $_FILES['file_laporan']['error'] == 0) {
        $target_dir = "../uploads/laporan/";
        // Buat folder jika belum ada
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        // Buat nama file yang unik
        $original_name = basename($_FILES["file_laporan"]["name"]);
        $file_extension = pathinfo($original_name, PATHINFO_EXTENSION);
        $file_name = time() . "_" . $id_mahasiswa . "_" . $id_modul . "." . $file_extension;
        $target_file = $target_dir . $file_name;

        // Pindahkan file ke folder uploads
        if (move_uploaded_file($_FILES["file_laporan"]["tmp_name"], $target_file)) {
            // Cek apakah sudah ada pengumpulan sebelumnya
            $stmt_check = $conn->prepare("SELECT id, file_laporan FROM pengumpulan_laporan WHERE id_modul = ? AND id_mahasiswa = ?");
            $stmt_check->bind_param("ii", $id_modul, $id_mahasiswa);
            $stmt_check->execute();
            $result = $stmt_check->get_result();
            $existing_submission = $result->fetch_assoc();
            $stmt_check->close();

            if ($existing_submission) {
                // Jika ada, UPDATE data yang ada (termasuk hapus file lama)
                $old_file = $target_dir . $existing_submission['file_laporan'];
                if (file_exists($old_file)) {
                    unlink($old_file);
                }
                $stmt_update = $conn->prepare("UPDATE pengumpulan_laporan SET file_laporan = ?, tanggal_kumpul = NOW() WHERE id = ?");
                $stmt_update->bind_param("si", $file_name, $existing_submission['id']);
                $stmt_update->execute();
                $stmt_update->close();
            } else {
                // Jika belum ada, INSERT data baru
                $stmt_insert = $conn->prepare("INSERT INTO pengumpulan_laporan (id_modul, id_mahasiswa, file_laporan) VALUES (?, ?, ?)");
                $stmt_insert->bind_param("iis", $id_modul, $id_mahasiswa, $file_name);
                $stmt_insert->execute();
                $stmt_insert->close();
            }
            header("Location: " . $redirect_url . "&status=success");
            exit();
        }
    }
}
// Jika terjadi error
header("Location: " . $redirect_url . "&status=error");
exit();
?>