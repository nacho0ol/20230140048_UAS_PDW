<?php
$pageTitle = 'Dashboard';
$activePage = 'dashboard';
require_once '../config.php';
require_once 'templates/header.php';

// 1. Query untuk mengambil data statistik
$total_modul = $conn->query("SELECT COUNT(id) as total FROM modul")->fetch_assoc()['total'];
$total_laporan = $conn->query("SELECT COUNT(id) as total FROM pengumpulan_laporan")->fetch_assoc()['total'];
$laporan_belum_dinilai = $conn->query("SELECT COUNT(id) as total FROM pengumpulan_laporan WHERE nilai IS NULL")->fetch_assoc()['total'];

// 2. Query untuk aktivitas laporan terbaru
$aktivitas_terbaru = $conn->query(
    "SELECT u.nama as nama_mahasiswa, m.nama_modul, pl.tanggal_kumpul
     FROM pengumpulan_laporan pl
     JOIN users u ON pl.id_mahasiswa = u.id
     JOIN modul m ON pl.id_modul = m.id
     ORDER BY pl.tanggal_kumpul DESC
     LIMIT 2"
);

?>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    
    <div class="bg-white p-6 rounded-lg shadow-md flex items-center space-x-4">
        <div class="bg-blue-100 p-3 rounded-full">
            <svg class="w-6 h-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 006 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 016 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 016-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0018 18a8.967 8.967 0 00-6 2.292m0-14.25v14.25" /></svg>
        </div>
        <div>
            <p class="text-sm text-gray-500">Total Modul Diajarkan</p>
            <p class="text-2xl font-bold text-gray-800"><?php echo $total_modul; ?></p>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md flex items-center space-x-4">
        <div class="bg-green-100 p-3 rounded-full">
            <svg class="w-6 h-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        </div>
        <div>
            <p class="text-sm text-gray-500">Total Laporan Masuk</p>
            <p class="text-2xl font-bold text-gray-800"><?php echo $total_laporan; ?></p>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md flex items-center space-x-4">
        <div class="bg-yellow-100 p-3 rounded-full">
            <svg class="w-6 h-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        </div>
        <div>
            <p class="text-sm text-gray-500">Laporan Belum Dinilai</p>
            <p class="text-2xl font-bold text-gray-800"><?php echo $laporan_belum_dinilai; ?></p>
        </div>
    </div>
</div>

<div class="bg-white p-6 rounded-lg shadow-md mt-8">
    <h3 class="text-xl font-bold text-gray-800 mb-4">Aktivitas Laporan Terbaru</h3>
    <div class="space-y-4">
        <?php if ($aktivitas_terbaru->num_rows > 0) : ?>
            <?php while($row = $aktivitas_terbaru->fetch_assoc()): ?>
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center mr-4">
                        <span class="font-bold text-gray-500"><?php echo strtoupper(substr($row['nama_mahasiswa'], 0, 2)); ?></span>
                    </div>
                    <div>
                        <p class="text-gray-800"><strong><?php echo htmlspecialchars($row['nama_mahasiswa']); ?></strong> mengumpulkan laporan untuk <strong><?php echo htmlspecialchars($row['nama_modul']); ?></strong></p>
                        <p class="text-sm text-gray-500"><?php echo date('d M Y, H:i', strtotime($row['tanggal_kumpul'])); ?></p>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-gray-500">Belum ada aktivitas laporan.</p>
        <?php endif; ?>
    </div>
</div>

<?php
require_once 'templates/footer.php';
?>