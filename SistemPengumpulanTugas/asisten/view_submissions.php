<?php
$pageTitle = 'Laporan Masuk';
$activePage = 'submissions';
require_once '../config.php';
require_once 'templates/header.php';

$message = '';

// ... (logika pemberian nilai yang sudah ada) ...
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_grade'])) {
    $submission_id = $_POST['submission_id'];
    $nilai = $_POST['nilai'];
    $feedback = $_POST['feedback'];

    $stmt = $conn->prepare("UPDATE pengumpulan_laporan SET nilai = ?, feedback = ? WHERE id = ?");
    $stmt->bind_param("isi", $nilai, $feedback, $submission_id);
    if ($stmt->execute()) {
        $message = "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4'>Nilai berhasil disimpan.</div>";
    } else {
        $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4'>Error: " . $stmt->error . "</div>";
    }
    $stmt->close();
}


// Logika untuk filter
$filter_course_id = $_GET['filter_course'] ?? 'all';

$sql = "SELECT pl.id, pl.tanggal_kumpul, pl.file_laporan, pl.nilai,
               u.nama as nama_mahasiswa,
               m.nama_modul,
               p.nama_praktikum
        FROM pengumpulan_laporan pl
        JOIN users u ON pl.id_mahasiswa = u.id
        JOIN modul m ON pl.id_modul = m.id
        JOIN mata_praktikum p ON m.id_praktikum = p.id";

if ($filter_course_id != 'all') {
    $sql .= " WHERE p.id = ?";
}
$sql .= " ORDER BY pl.tanggal_kumpul DESC";

$stmt = $conn->prepare($sql);
if ($filter_course_id != 'all') {
    $stmt->bind_param("i", $filter_course_id);
}
$stmt->execute();
$submissions = $stmt->get_result();

// Ambil daftar praktikum untuk dropdown filter
$courses = $conn->query("SELECT id, nama_praktikum FROM mata_praktikum");
?>

<?php echo $message; ?>

<div class="bg-white p-6 rounded-lg shadow-md">
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold">Daftar Laporan Masuk</h2>
        <form action="view_submissions.php" method="GET">
            <select name="filter_course" onchange="this.form.submit()" class="rounded-md border-gray-300 shadow-sm">
                <option value="all">Semua Praktikum</option>
                <?php while ($course = $courses->fetch_assoc()) : ?>
                    <option value="<?php echo $course['id']; ?>" <?php echo ($filter_course_id == $course['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($course['nama_praktikum']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </form>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white">
            <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="py-3 px-4 text-left">Mahasiswa</th>
                    <th class="py-3 px-4 text-left">Praktikum / Modul</th>
                    <th class="py-3 px-4 text-left">Tgl Kumpul</th>
                    <th class="py-3 px-4 text-left">Laporan</th>
                    <th class="py-3 px-4 text-left">Penilaian</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                <?php if ($submissions->num_rows > 0) : ?>
                    <?php while ($row = $submissions->fetch_assoc()) : ?>
                        <tr class="border-b">
                            <td class="py-3 px-4"><?php echo htmlspecialchars($row['nama_mahasiswa']); ?></td>
                            <td class="py-3 px-4">
                                <p class="font-semibold"><?php echo htmlspecialchars($row['nama_praktikum']); ?></p>
                                <p class="text-sm text-gray-600"><?php echo htmlspecialchars($row['nama_modul']); ?></p>
                            </td>
                            <td class="py-3 px-4"><?php echo date('d M Y, H:i', strtotime($row['tanggal_kumpul'])); ?></td>
                            <td class="py-3 px-4">
                                <a href="../uploads/laporan/<?php echo htmlspecialchars($row['file_laporan']); ?>" download class="text-blue-500 hover:underline">Unduh File</a>
                            </td>
                            <td class="py-3 px-4">
                                <form action="view_submissions.php?filter_course=<?php echo $filter_course_id; ?>" method="POST">
                                    <input type="hidden" name="submission_id" value="<?php echo $row['id']; ?>">
                                    <div class="flex items-center gap-2">
                                        <input type="number" name="nilai" placeholder="Nilai" value="<?php echo htmlspecialchars($row['nilai'] ?? ''); ?>" class="w-20 border-gray-300 rounded-md shadow-sm">
                                        <textarea name="feedback" placeholder="Feedback" rows="1" class="w-full border-gray-300 rounded-md shadow-sm"></textarea>
                                        <button type="submit" name="submit_grade" class="bg-green-500 text-white p-2 rounded-md hover:bg-green-600">Simpan</button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="5" class="text-center py-4">Belum ada laporan yang dikumpulkan untuk filter ini.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'templates/footer.php'; ?>