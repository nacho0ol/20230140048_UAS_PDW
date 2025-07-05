<?php
$pageTitle = 'Cari Praktikum';
$activePage = 'courses';
require_once '../config.php';
require_once 'templates/header_mahasiswa.php';

$result = $conn->query("SELECT * FROM mata_praktikum ORDER BY nama_praktikum ASC");
?>

<div class="bg-white p-8 rounded-xl shadow-md">
    <h2 class="text-3xl font-bold text-gray-800 mb-6">Katalog Mata Praktikum</h2>
    <?php
    if (isset($_GET['status'])) {
        if ($_GET['status'] == 'enrolled') {
            echo "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4'>Anda berhasil mendaftar praktikum.</div>";
        } elseif ($_GET['status'] == 'already_enrolled') {
            echo "<div class='bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded relative mb-4'>Anda sudah terdaftar pada praktikum ini.</div>";
        }
    }
    ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
            <div class="border rounded-lg p-6 flex flex-col justify-between">
                <div>
                    <h3 class="font-bold text-xl mb-2"><?php echo htmlspecialchars($row['nama_praktikum']); ?></h3>
                    <p class="text-gray-600 text-sm mb-4"><?php echo htmlspecialchars($row['deskripsi']); ?></p>
                </div>
                <a href="enroll.php?course_id=<?php echo $row['id']; ?>" class="bg-blue-600 text-white text-center font-bold py-2 px-4 rounded-lg hover:bg-blue-700 mt-4 block">
                    Daftar Praktikum
                </a>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-center col-span-3">Saat ini belum ada praktikum yang tersedia.</p>
        <?php endif; ?>
    </div>
</div>

<?php
require_once 'templates/footer_mahasiswa.php';
?>