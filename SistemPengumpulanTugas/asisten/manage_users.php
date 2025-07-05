<?php
$pageTitle = 'Manajemen Pengguna';
$activePage = 'users';
require_once '../config.php';
require_once 'templates/header.php';

$message = '';

// Handle Aksi (Tambah, Edit, Hapus)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Aksi Tambah Pengguna
    if (isset($_POST['add_user'])) {
        $nama = $_POST['nama'];
        $email = $_POST['email'];
        $role = $_POST['role'];
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash password

        $stmt = $conn->prepare("INSERT INTO users (nama, email, password, role) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nama, $email, $password, $role);
        if ($stmt->execute()) {
            $message = "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded'>Pengguna berhasil ditambahkan.</div>";
        } else {
            $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded'>Error: " . htmlspecialchars($stmt->error) . "</div>";
        }
        $stmt->close();
    }
    // Aksi Edit Pengguna
    if (isset($_POST['edit_user'])) {
        $id = $_POST['id'];
        $nama = $_POST['nama'];
        $email = $_POST['email'];
        $role = $_POST['role'];
        
        // Cek jika password diisi untuk diubah
        if (!empty($_POST['password'])) {
            $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
            $stmt = $conn->prepare("UPDATE users SET nama=?, email=?, role=?, password=? WHERE id=?");
            $stmt->bind_param("ssssi", $nama, $email, $role, $password, $id);
        } else {
            // Jika password tidak diubah
            $stmt = $conn->prepare("UPDATE users SET nama=?, email=?, role=? WHERE id=?");
            $stmt->bind_param("sssi", $nama, $email, $role, $id);
        }

        if ($stmt->execute()) {
            $message = "<div class='bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded'>Pengguna berhasil diperbarui.</div>";
        } else {
            $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded'>Error: " . htmlspecialchars($stmt->error) . "</div>";
        }
        $stmt->close();
    }
}

// Handle Hapus Pengguna
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $message = "<div class='bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded'>Pengguna berhasil dihapus.</div>";
    }
    $stmt->close();
}

// Ambil data untuk form edit
$edit_data = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $result = $conn->query("SELECT id, nama, email, role FROM users WHERE id = $id");
    $edit_data = $result->fetch_assoc();
}

// Ambil semua data pengguna
$users = $conn->query("SELECT id, nama, email, role FROM users ORDER BY nama ASC");
?>

<?php echo $message; ?>

<div class="bg-white p-6 rounded-lg shadow-md mb-8">
    <h2 class="text-2xl font-bold mb-4"><?php echo $edit_data ? 'Edit' : 'Tambah'; ?> Pengguna</h2>
    <form action="manage_users.php" method="POST">
        <?php if ($edit_data) : ?>
            <input type="hidden" name="id" value="<?php echo $edit_data['id']; ?>">
        <?php endif; ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
            <div>
                <label for="nama" class="block text-sm font-medium">Nama Lengkap</label>
                <input type="text" name="nama" id="nama" value="<?php echo htmlspecialchars($edit_data['nama'] ?? ''); ?>" required class="mt-1 w-full rounded-md border-gray-300 shadow-sm">
            </div>
            <div>
                <label for="email" class="block text-sm font-medium">Email</label>
                <input type="email" name="email" id="email" value="<?php echo htmlspecialchars($edit_data['email'] ?? ''); ?>" required class="mt-1 w-full rounded-md border-gray-300 shadow-sm">
            </div>
            <div>
                <label for="password" class="block text-sm font-medium">Password</label>
                <input type="password" name="password" id="password" <?php echo !$edit_data ? 'required' : ''; ?> class="mt-1 w-full rounded-md border-gray-300 shadow-sm" placeholder="<?php echo $edit_data ? 'Kosongkan jika tidak diubah' : ''; ?>">
            </div>
            <div>
                <label for="role" class="block text-sm font-medium">Peran</label>
                <select name="role" id="role" required class="mt-1 w-full rounded-md border-gray-300 shadow-sm">
                    <option value="mahasiswa" <?php echo (isset($edit_data['role']) && $edit_data['role'] == 'mahasiswa') ? 'selected' : ''; ?>>Mahasiswa</option>
                    <option value="asisten" <?php echo (isset($edit_data['role']) && $edit_data['role'] == 'asisten') ? 'selected' : ''; ?>>Asisten</option>
                </select>
            </div>
        </div>
        <div>
            <?php if ($edit_data) : ?>
                <button type="submit" name="edit_user" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-md">Update Pengguna</button>
                <a href="manage_users.php" class="ml-2 text-gray-600">Batal</a>
            <?php else : ?>
                <button type="submit" name="add_user" class="bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-md">Tambah Pengguna</button>
            <?php endif; ?>
        </div>
    </form>
</div>

<div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-4">Daftar Semua Pengguna</h2>
    <div class="overflow-x-auto">
        <table class="min-w-full bg-white">
            <thead class="bg-gray-800 text-white">
                <tr>
                    <th class="py-3 px-4 text-left">Nama</th>
                    <th class="py-3 px-4 text-left">Email</th>
                    <th class="py-3 px-4 text-left">Peran</th>
                    <th class="py-3 px-4 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody class="text-gray-700">
                <?php while ($row = $users->fetch_assoc()) : ?>
                    <tr class="border-b">
                        <td class="py-3 px-4"><?php echo htmlspecialchars($row['nama']); ?></td>
                        <td class="py-3 px-4"><?php echo htmlspecialchars($row['email']); ?></td>
                        <td class="py-3 px-4"><?php echo ucfirst(htmlspecialchars($row['role'])); ?></td>
                        <td class="py-3 px-4">
                            <a href="manage_users.php?action=edit&id=<?php echo $row['id']; ?>" class="text-blue-500 hover:text-blue-700">Edit</a>
                            <a href="manage_users.php?action=delete&id=<?php echo $row['id']; ?>" onclick="return confirm('Yakin ingin menghapus pengguna ini?')" class="text-red-500 hover:text-red-700 ml-4">Hapus</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once 'templates/footer.php'; ?>