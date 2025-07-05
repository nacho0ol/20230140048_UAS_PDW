<?php
$pageTitle = 'Manajemen Modul';
$activePage = 'modules';
require_once '../config.php';
require_once 'templates/header.php';

// Pastikan ada ID praktikum yang dipilih
if (!isset($_GET['course_id'])) {
    echo "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded'>Pilih mata praktikum terlebih dahulu dari halaman <a href='manage_courses.php' class='font-bold underline'>Manajemen Praktikum</a>.</div>";
    require_once 'templates/footer.php';
    exit();
}

$course_id = $_GET['course_id'];
$message = '';

// Handle Penambahan Modul
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_module'])) {
    $nama_modul = $_POST['nama_modul'];
    $deskripsi = $_POST['deskripsi'];
    
    // Proses Upload File Materi
    $file_materi = '';
    if (isset($_FILES['file_materi']) && $_FILES['file_materi']['error'] == 0) {
        $target_dir = "../uploads/materi/";
        // Buat folder jika belum ada
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $file_name = time() . '_' . basename($_FILES["file_materi"]["name"]);
        $target_file = $target_dir . $file_name;

        if (move_uploaded_file($_FILES["file_materi"]["tmp_name"], $target_file)) {
            $file_materi = $file_name; // Simpan nama file ke DB
        } else {
            $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded'>Gagal mengunggah file.</div>";
        }
    }

    if (empty($message)) {
        $stmt = $conn->prepare("INSERT INTO modul (id_praktikum, nama_modul, deskripsi, file_materi) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $course_id, $nama_modul, $deskripsi, $file_materi);
        if ($stmt->execute()) {
            $message = "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded'>Modul berhasil ditambahkan.</div>";
        } else {
            $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded'>Error: " . $stmt->error . "</div>";
        }
        $stmt->close();
    }
}

// Handle Hapus Modul
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['module_id'])) {
    $module_id = $_GET['module_id'];
    // Hapus juga file fisiknya
    $result = $conn->query("SELECT file_materi FROM modul WHERE id = $module_id");
    if($row = $result->fetch_assoc()){
        if(!empty($row['file_materi']) && file_exists("../uploads/materi/" . $row['file_materi'])){
            unlink("../uploads/materi/" . $row['file_materi']);
        }
    }

    $stmt = $conn->prepare("DELETE FROM modul WHERE id = ?");
    $stmt->bind_param("i", $module_id);
    if ($stmt->execute()) {
        $message = "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded'>Modul berhasil dihapus.</div>";
    }
    $stmt->close();
}


// Ambil data praktikum dan modul
$course_info = $conn->query("SELECT nama_praktikum FROM mata_praktikum WHERE id = $course_id")->fetch_assoc();
$modules = $conn->query("SELECT * FROM modul WHERE id_praktikum = $course_id ORDER BY created_at ASC");
?>

<?php echo $message; ?>

<div class="bg-white p-6 rounded-lg shadow-md mb-8">
    <h2 class="text-2xl font-bold mb-1">Manajemen Modul</h2>
    <h3 class="text-xl text-gray-600 mb-4">Praktikum: <?php echo htmlspecialchars($course_info['nama_praktikum']); ?></h3>
    <form action="manage_modules.php?course_id=<?php echo $course_id; ?>" method="POST" enctype="multipart/form-data">
        <div class="mb-4">
            <label for="nama_modul" class="block text-sm font-medium text-gray-700">Nama Modul</label>
            <input type="text" name="nama_modul" id="nama_modul" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
        </div>
        <div class="mb-4">
            <label for="deskripsi" class="block text-sm font-medium text-gray-700">Deskripsi Singkat</label>
            <textarea name="deskripsi" id="deskripsi" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"></textarea>
        </div>
        <div class="mb-4">
            <label for="file_materi" class="block text-sm font-medium text-gray-700">Upload File Materi (PDF/DOCX)</label>
            <input type="file" name="file_materi" id="file_materi" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
        </div>
        <button type="submit" name="add_module" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-md">Tambah Modul</button>
    </form>
</div>

<div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-4">Daftar Modul</h2>
    <div class="space-y-4">
        <?php if ($modules->num_rows > 0): ?>
            <?php while($row = $modules->fetch_assoc()): ?>
            <div class="border rounded-lg p-4 flex items-center justify-between">
                <div>
                    <h4 class="font-bold"><?php echo htmlspecialchars($row['nama_modul']); ?></h4>
                    <p class="text-sm text-gray-600"><?php echo htmlspecialchars($row['deskripsi']); ?></p>
                    <?php if(!empty($row['file_materi'])): ?>
                        <p class="text-sm text-blue-600 mt-1">File: <?php echo htmlspecialchars($row['file_materi']); ?></p>
                    <?php endif; ?>
                </div>
                <a href="manage_modules.php?course_id=<?php echo $course_id; ?>&action=delete&module_id=<?php echo $row['id']; ?>" onclick="return confirm('Yakin ingin menghapus modul ini?')" class="text-red-500 hover:text-red-700 font-medium">Hapus</a>
            </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-center text-gray-500">Belum ada modul untuk praktikum ini.</p>
        <?php endif; ?>
    </div>
</div>

<?php
require_once 'templates/footer.php';
?>