<?php
$pageTitle = 'Detail Praktikum';
$activePage = 'my_courses';
require_once '../config.php';
require_once 'templates/header_mahasiswa.php';

$id_mahasiswa = $_SESSION['user_id'];

if (!isset($_GET['course_id'])) {
    echo "ID Praktikum tidak ditemukan.";
    exit();
}
$course_id = $_GET['course_id'];

// Tampilkan pesan status
if (isset($_GET['status'])) {
    if ($_GET['status'] == 'success') {
        echo "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4'>Laporan berhasil diunggah.</div>";
    } elseif ($_GET['status'] == 'error') {
        echo "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4'>Terjadi kesalahan saat mengunggah laporan.</div>";
    }
}

// Ambil info praktikum dan modul
$course_info = $conn->query("SELECT nama_praktikum, deskripsi FROM mata_praktikum WHERE id = $course_id")->fetch_assoc();
$modules = $conn->query("SELECT * FROM modul WHERE id_praktikum = $course_id ORDER BY created_at ASC");
?>

<div class="bg-white p-8 rounded-xl shadow-md">
    <h2 class="text-3xl font-bold text-gray-800"><?php echo htmlspecialchars($course_info['nama_praktikum']); ?></h2>
    <p class="text-gray-600 mt-2 mb-8"><?php echo htmlspecialchars($course_info['deskripsi']); ?></p>

    <h3 class="text-2xl font-bold text-gray-800 mb-4 border-t pt-6">Daftar Modul & Tugas</h3>
    <div class="space-y-6">
        <?php if ($modules->num_rows > 0) : ?>
            <?php while ($modul = $modules->fetch_assoc()) : ?>
                <?php
                // Cek status pengumpulan untuk modul ini
                $id_modul = $modul['id'];
                $submission_stmt = $conn->prepare("SELECT nilai, feedback, tanggal_kumpul FROM pengumpulan_laporan WHERE id_modul = ? AND id_mahasiswa = ?");
                $submission_stmt->bind_param("ii", $id_modul, $id_mahasiswa);
                $submission_stmt->execute();
                $submission_result = $submission_stmt->get_result();
                $submission = $submission_result->fetch_assoc();
                $submission_stmt->close();
                ?>
                <div class="border rounded-lg p-6">
                    <div class="md:flex justify-between items-start">
                        <div>
                            <h4 class="font-bold text-xl mb-2"><?php echo htmlspecialchars($modul['nama_modul']); ?></h4>
                            <p class="text-gray-700 mb-4"><?php echo htmlspecialchars($modul['deskripsi']); ?></p>
                        </div>
                        <?php if ($submission) : ?>
                            <div class="text-sm text-right flex-shrink-0 mt-2 md:mt-0 md:ml-6">
                                <p class="font-semibold text-green-600">Sudah Mengumpulkan</p>
                                <p class="text-gray-500"><?php echo date('d M Y, H:i', strtotime($submission['tanggal_kumpul'])); ?></p>
                                
                                <?php if (isset($submission['nilai'])) : ?>
                                    <div class="mt-2 border-t pt-2">
                                        <p class="font-bold text-lg">Nilai: <?php echo htmlspecialchars($submission['nilai']); ?></p>
                                        
                                        <?php if (!empty($submission['feedback'])) : ?>
                                            <p class="text-gray-600 mt-1 text-left">
                                                <strong>Feedback:</strong> <?php echo nl2br(htmlspecialchars($submission['feedback'])); ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="flex flex-wrap gap-4 mt-4 border-t pt-4">
                        <?php if (!empty($modul['file_materi'])) : ?>
                            <a href="../uploads/materi/<?php echo htmlspecialchars($modul['file_materi']); ?>" download class="bg-blue-600 text-white font-bold py-2 px-4 rounded-lg hover:bg-blue-700 inline-flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                Unduh Materi
                            </a>
                        <?php endif; ?>

                        <form action="submit_report.php" method="post" enctype="multipart/form-data" class="flex items-center gap-2">
                            <input type="hidden" name="module_id" value="<?php echo $id_modul; ?>">
                            <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                            <input type="file" name="file_laporan" required class="text-sm text-gray-500 file:mr-2 file:py-2 file:px-3 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                            <button type="submit" class="bg-green-500 text-white font-bold py-2 px-4 rounded-lg hover:bg-green-600 inline-flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                <?php echo $submission ? 'Kumpul Ulang' : 'Kumpul'; ?>
                            </button>
                        </form>
                    </div>

                </div>
            <?php endwhile; ?>
        <?php else : ?>
            <p class="text-center text-gray-500">Belum ada modul yang ditambahkan untuk praktikum ini.</p>
        <?php endif; ?>
    </div>
</div>

<?php
require_once 'templates/footer_mahasiswa.php';
?>