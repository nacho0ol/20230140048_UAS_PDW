<?php
session_start();
require_once '../config.php';

// Pastikan mahasiswa sudah login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'mahasiswa') {
    header("Location: ../login.php");
    exit();
}

// Pastikan ada course_id
if (!isset($_GET['course_id'])) {
    header("Location: courses.php");
    exit();
}

$id_mahasiswa = $_SESSION['user_id'];
$id_praktikum = $_GET['course_id'];

// Cek apakah sudah terdaftar
$check_stmt = $conn->prepare("SELECT id FROM pendaftaran_praktikum WHERE id_mahasiswa = ? AND id_praktikum = ?");
$check_stmt->bind_param("ii", $id_mahasiswa, $id_praktikum);
$check_stmt->execute();
$check_stmt->store_result();

if ($check_stmt->num_rows > 0) {
    // Jika sudah terdaftar
    header("Location: courses.php?status=already_enrolled");
} else {
    // Jika belum, daftarkan
    $insert_stmt = $conn->prepare("INSERT INTO pendaftaran_praktikum (id_mahasiswa, id_praktikum) VALUES (?, ?)");
    $insert_stmt->bind_param("ii", $id_mahasiswa, $id_praktikum);
    if ($insert_stmt->execute()) {
        header("Location: courses.php?status=enrolled");
    } else {
        header("Location: courses.php?status=error");
    }
    $insert_stmt->close();
}

$check_stmt->close();
$conn->close();
exit();