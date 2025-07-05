<?php
$pageTitle = 'Manajemen Praktikum';
$activePage = 'courses';
require_once '../config.php';
require_once 'templates/header.php';

$message = '';

// Handle Aksi (Tambah, Edit, Hapus)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Aksi Tambah
    if (isset($_POST['add_course'])) {
        $kode = $_POST['kode_praktikum'];
        $nama = $_POST['nama_praktikum'];
        $deskripsi = $_POST['deskripsi'];
        $stmt = $conn->prepare("INSERT INTO mata_praktikum (kode_praktikum, nama_praktikum, deskripsi) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $kode, $nama, $deskripsi);
        if ($stmt->execute()) {
            $message = "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4'>Praktikum berhasil ditambahkan.</div>";
        } else {
            $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4'>Error: " . htmlspecialchars($stmt->error) . "</div>";
        }
        $stmt->close();
    }
    // Aksi Edit
    if (isset($_POST['edit_course'])) {
        $id = $_POST['id'];
        $kode = $_POST['kode_praktikum'];
        $nama = $_POST['nama_praktikum'];
        $deskripsi = $_POST['deskripsi'];
        $stmt = $conn->prepare("UPDATE mata_praktikum SET kode_praktikum = ?, nama_praktikum = ?, deskripsi = ? WHERE id = ?");
        $stmt->bind_param("sssi", $kode, $nama, $deskripsi, $id);
        if ($stmt->execute()) {
            $message = "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4'>Praktikum berhasil diperbarui.</div>";
        } else {
            $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4'>Error: " . htmlspecialchars($stmt->error) . "</div>";
        }
        $stmt->close();
    }
}

// Handle Hapus
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM mata_praktikum WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $message = "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4'>Praktikum berhasil dihapus.</div>";
    } else {
        $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4'>Error: " . htmlspecialchars($stmt->error) . "</div>";
    }
    $stmt->close();
}

// Ambil data untuk form edit jika ada
$edit_data = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $result = $conn->query("SELECT * FROM mata_praktikum WHERE id = $id");
    $edit_data = $result->fetch_assoc();
}

// Ambil semua data praktikum untuk ditampilkan di tabel
$courses = $conn->query("SELECT * FROM mata_praktikum ORDER BY kode_praktikum ASC");
?>

<?php echo $message; ?>

<div class="bg-white p-6 rounded-lg shadow-md mb-8">
    <h2 class="text-2xl font-bold mb-4"><?php echo $edit_data ? 'Edit' : 'Tambah'; ?> Mata Praktikum</h2>
    <form action="manage_courses.php" method="POST">
        <?php if ($edit_data) : ?>
            <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
        <?php endif; ?>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
                <label for="kode_praktikum" class="block text-sm font-medium text-gray-700">Kode Praktikum</label>
                <input type="text" name="kode_praktikum" id="kode_praktikum" value="<?php echo htmlspecialchars($edit_data['kode_praktikum'] ?? ''); ?>" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div>
                <label for="nama_praktikum" class="block text-sm font-medium text-gray-700">Nama Praktikum</label>
                <input type="text" name="nama_praktikum" id="nama_praktikum" value="<?php echo htmlspecialchars($edit_data['nama_praktikum'] ?? ''); ?>" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div class="md:col-span-3">
                <label for="deskripsi" class="block text-sm font-medium text-gray-700">Deskripsi</label>
                <textarea name="deskripsi" id="deskripsi" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"><?php echo htmlspecialchars($edit_data['deskripsi'] ?? ''); ?></textarea>
            </div>
        </div>
        <div>
            <?php if ($edit_data) : ?>
                <button type="submit" name="edit_course" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-md">Update Praktikum</button>
                <a href="manage_courses.php" class="ml-2 text-gray-600 hover:text-gray-900">Batal</a>
            <?php else : ?>
                <button type="submit" name="add_course" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-md">Tambah Praktikum</button>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-4">Daftar Mata Praktikum</h2>
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white">
            <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Kode</th>
                    <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Nama Praktikum</th>
                    <th class="py-3 px-4 uppercase font-semibold text-sm text-left">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                <?php if ($courses->num_rows > 0) : ?>
                    <?php while ($row = $courses->fetch_assoc()) : ?>
                        <tr class="border-b">
                            <td class="py-3 px-4"><?php echo htmlspecialchars($row['kode_praktikum']); ?></td>
                            <td class="py-3 px-4"><?php echo htmlspecialchars($row['nama_praktikum']); ?></td>
                            <td class="py-3 px-4">
                                <a href="manage_modules.php?course_id=<?php echo $row['id']; ?>" class="text-green-500 hover:text-green-700 font-medium">Kelola Modul</a>
                                <a href="manage_courses.php?action=edit&id=<?php echo $row['id']; ?>" class="text-blue-500 hover:text-blue-700 font-medium ml-4">Edit</a>
                                <a href="manage_courses.php?action=delete&id=<?php echo $row['id']; ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus praktikum ini?')" class="text-red-500 hover:text-red-700 font-medium ml-4">Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="3" class="text-center py-4">Belum ada data praktikum.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php
require_once 'templates/footer.php';
?>