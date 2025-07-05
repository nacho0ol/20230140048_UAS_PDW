<?php
// Pastikan session sudah dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek jika pengguna belum login atau bukan asisten
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'asisten') {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Panel Asisten - <?php echo $pageTitle ?? 'Dashboard'; ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 font-sans">

    <nav class="bg-blue-600 shadow-lg text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">

                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <span class="text-2xl font-bold">SIMPRAK - Asisten</span>
                    </div>
                    <div class="hidden md:block">
                        <div class="ml-10 flex items-baseline space-x-4">
                            <?php
                                // Kelas untuk menandai link aktif
                                $activeClass = 'bg-cyan-600 text-white';
                                $inactiveClass = 'text-gray-100 hover:bg-cyan-600 hover:text-white';
                            ?>
                            <a href="dashboard.php" class="<?php echo ($activePage == 'dashboard') ? $activeClass : $inactiveClass; ?> px-3 py-2 rounded-md text-sm font-medium">Dashboard</a>
                            <a href="manage_courses.php" class="<?php echo ($activePage == 'courses') ? $activeClass : $inactiveClass; ?> px-3 py-2 rounded-md text-sm font-medium">Manajemen Praktikum</a>
                            <a href="view_submissions.php" class="<?php echo ($activePage == 'submissions') ? $activeClass : $inactiveClass; ?> px-3 py-2 rounded-md text-sm font-medium">Laporan Masuk</a>
                            <a href="manage_users.php" class="<?php echo ($activePage == 'users') ? $activeClass : $inactiveClass; ?> px-3 py-2 rounded-md text-sm font-medium">Manajemen Pengguna</a>
                        </div>
                    </div>
                </div>

                <div class="hidden md:block">
                    <div class="ml-4 flex items-center md:ml-6">
                        <span class="text-gray-200 mr-4">Halo, <?php echo htmlspecialchars($_SESSION['nama']); ?></span>
                        <a href="../logout.php" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-md transition-colors duration-300">
                            Logout
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </nav>

    <header class="bg-white shadow">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <h1 class="text-3xl font-bold tracking-tight text-gray-900"><?php echo $pageTitle ?? 'Dashboard'; ?></h1>
        </div>
    </header>

    <main>
        <div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
            <div class="px-4 py-6 sm:px-0">