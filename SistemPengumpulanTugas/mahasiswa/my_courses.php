<?php
$pageTitle = 'Praktikum Saya';
$activePage = 'my_courses';
require_once '../config.php';
require_once 'templates/header_mahasiswa.php';

// Ambil ID mahasiswa dari session
$id_mahasiswa = $_SESSION['user_id'];

// Query untuk mengambil praktikum yang diikuti mahasiswa
$sql = "SELECT mp.id, mp.nama_praktikum, mp.deskripsi 
        FROM mata_praktikum mp
        JOIN pendaftaran_praktikum pp ON mp.id = pp.id_praktikum
        WHERE pp.id_mahasiswa = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_mahasiswa);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="bg-white p-8 rounded-xl shadow-md">
    <h2 class="text-3xl font-bold text-gray-800 mb-6">Daftar Praktikum yang Anda Ikuti</h2>
    <div class="space-y-4">
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
            <div class="border rounded-lg p-6 flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-xl"><?php echo htmlspecialchars($row['nama_praktikum']); ?></h3>
                    <p class="text-gray-600 text-sm mt-1"><?php echo htmlspecialchars($row['deskripsi']); ?></p>
                </div>
                <a href="course_detail.php?course_id=<?php echo $row['id']; ?>" class="bg-gray-800 text-white font-bold py-2 px-4 rounded-lg hover:bg-gray-900">
                Lihat Detail & Tugas
                </a>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-center">Anda belum mendaftar praktikum apapun. Silakan <a href="courses.php" class="text-blue-600 hover:underline">cari praktikum</a> yang tersedia.</p>
        <?php endif; ?>
    </div>
</div>

<?php
$stmt->close();
require_once 'templates/footer_mahasiswa.php';
?>