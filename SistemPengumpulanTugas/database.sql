CREATE TABLE `users` (

  `id` int(11) NOT NULL AUTO_INCREMENT,

  `nama` varchar(100) NOT NULL,

  `email` varchar(100) NOT NULL,

  `password` varchar(255) NOT NULL,

  `role` enum('mahasiswa','asisten') NOT NULL,

  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),

  PRIMARY KEY (`id`),

  UNIQUE KEY `email` (`email`)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



-- Tabel untuk menyimpan data mata praktikum

CREATE TABLE `mata_praktikum` (

  `id` INT(11) NOT NULL AUTO_INCREMENT,

  `kode_praktikum` VARCHAR(20) NOT NULL,

  `nama_praktikum` VARCHAR(100) NOT NULL,

  `deskripsi` TEXT,

  PRIMARY KEY (`id`),

  UNIQUE KEY `kode_praktikum` (`kode_praktikum`)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



-- Tabel untuk menyimpan modul/pertemuan dari setiap mata praktikum

CREATE TABLE `modul` (

  `id` INT(11) NOT NULL AUTO_INCREMENT,

  `id_praktikum` INT(11) NOT NULL,

  `nama_modul` VARCHAR(100) NOT NULL,

  `deskripsi` TEXT,

  `file_materi` VARCHAR(255),

  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (`id`),

  FOREIGN KEY (`id_praktikum`) REFERENCES `mata_praktikum`(`id`) ON DELETE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



-- Tabel untuk mencatat pendaftaran mahasiswa ke mata praktikum

CREATE TABLE `pendaftaran_praktikum` (

  `id` INT(11) NOT NULL AUTO_INCREMENT,

  `id_mahasiswa` INT(11) NOT NULL,

  `id_praktikum` INT(11) NOT NULL,

  `tanggal_daftar` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

  PRIMARY KEY (`id`),

  UNIQUE KEY `mahasiswa_praktikum` (`id_mahasiswa`, `id_praktikum`),

  FOREIGN KEY (`id_mahasiswa`) REFERENCES `users`(`id`) ON DELETE CASCADE,

  FOREIGN KEY (`id_praktikum`) REFERENCES `mata_praktikum`(`id`) ON DELETE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



-- Tabel untuk menyimpan laporan/tugas yang dikumpulkan mahasiswa

CREATE TABLE `pengumpulan_laporan` (

  `id` INT(11) NOT NULL AUTO_INCREMENT,

  `id_modul` INT(11) NOT NULL,

  `id_mahasiswa` INT(11) NOT NULL,

  `file_laporan` VARCHAR(255) NOT NULL,

  `tanggal_kumpul` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

  `nilai` INT(3),

  `feedback` TEXT,

  PRIMARY KEY (`id`),

  FOREIGN KEY (`id_modul`) REFERENCES `modul`(`id`) ON DELETE CASCADE,

  FOREIGN KEY (`id_mahasiswa`) REFERENCES `users`(`id`) ON DELETE CASCADE

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
