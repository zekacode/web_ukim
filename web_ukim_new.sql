-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: May 28, 2025 at 09:13 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `web_ukim_new`
--
CREATE DATABASE IF NOT EXISTS `web_ukim_new` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `web_ukim_new`;

-- --------------------------------------------------------

--
-- Table structure for table `blog_ukim`
--

CREATE TABLE `blog_ukim` (
  `id_blog` int UNSIGNED NOT NULL,
  `id_dept` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED DEFAULT NULL,
  `judul` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `slug` varchar(270) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `gambar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `isi` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `status` enum('published','draft','archived') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'published',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `blog_ukim`
--

INSERT INTO `blog_ukim` (`id_blog`, `id_dept`, `user_id`, `judul`, `slug`, `gambar`, `isi`, `status`, `created_at`, `updated_at`) VALUES
(1, 4, 2, 'PENGABDIAN MASYARAKAT SEBAGAI WUJUD IMPLEMENTASI TRI DHARMA PERGURUAN TINGGI OLEH UKM UKIM UNESA 2024', 'pengabdian-masyarakat-sebagai-wujud-implementasi-tri-dharma-perguruan-tinggi-oleh-ukm-ukim-unesa-2024', './asset/gbr_blog/file_6822f754d67887.21230141.webp', 'PENDAHULUAN\r\n\r\nUnit Kegiatan Ilmiah Mahasiswa (UKIM) merupakan salah satu UKM yang terdapat di Universitas Negeri Surabaya. UKM UKIM mewadahi seluruh mahasiswa Unesa untuk menyalurkan potensi serta membentuk karakter berupa pengimplementasian keempat roh, yaitu roh peneliti, roh penulis, roh aktivis, dan roh wirausaha. Selain bergerak dalam penalaran, UKM UKIM juga membekali mahasiswa Unesa untuk memiliki soft skills seperti kepemimpinan, teamwork, dan berkomunikasi secara efektif. Sebagai pengimplentasian beberapa roh, yaitu roh aktivis dan roh wirausaha, maka diadakannya kegiatan berupa Pengabdian Masyarakat 2024.\r\n\r\nPengabdian Masyarakat 2024 merupakan program kerja tahunan yang diselenggarakan UKM UKIM sebagai bentuk kepedulian UKM UKIM terhadap permasalahan-permasalahan yang terdapat di masyarakat. Pengabdian Masyarakat 2024 dilakukan di Desa Sumbersari, Kecamatan Sambeng, Lamongan. Di desa tersebut telah memiliki usaha desa, namun belum memiliki pengetahuan cukup untuk memasarkan produk usaha desa ke ranah digital. Selain itu, permasalahan sampah masih menjadi suatu hal yang harus diselesaikan di desa tersebut karena pengelolaan sampah di desa tersebut masih kurang optimal. Oleh karena itu, UKIM UNESA melakukan Pengabdian Masyarakat 2024 yang meliputi kegiatan seminar kewirausahaan, diharapkan masyarakat Desa Sumbersari dapat memanfaatkan potensial yang terdapat di sekitar desa seperti pemasaran produk secara online yang akan memberikan benefit lebih besar terkait pemasaran produk. Selain itu, melalui kegiatan seminar pengelolaan sampah yang disampaikan oleh Duta Lingkungan Provinsi Jawa Timur, diharapkan masyarakat Desa Sumbersari dapat sadar akan kebersihan lingkungan dan pemanfaatan produk daur ulang dari limbah rumah tangga. Pada kedua seminar tersebut, diadakan sesi tanya jawab serta diskusi untuk menanggulangi permasalahan-permasalahan yang terjadi.\r\n\r\nPEMBAHASAN\r\n\r\nHari pertama Pengabdian Masyarakat 2024 dibuka dengan mengundang seluruh perangkat desa dan lapisan masyarakat untuk menyaksikan opening ceremony yang ditandai dengan beberapa sambutan dan pemotongan pita serta makan bersama. Dilanjutkan dengan istirahat dan rapat evaluasi untuk kelanjutan agenda besok.\r\n\r\nPada hari kedua diawali dengan sholat subuh berjamaah di masjid terdekat bersama warga sekitar. Dilanjutkan dengan senam seluruh panitia Pengabdian Masyarakat 2024. Setelah senam bersama, berikutnya adalah Sosialisasi Kewirausahaan yang diharapkan warga Desa Sumbersari dapat mengoptimalkan promosi produk melalui digital. Tidak hanya itu, terdapat tips dan diskusi antara pemateri dan peserta dengan tujuan tercapainya pemahaman yang mendalam tentang wirausaha berbasis digital. Selain Sosialisasi Kewirausahaan, sore hari dilanjutkan dengan lomba-lomba 17 Agustus yang berkolaborasi dengan Karang Taruna setempat guna memaksimalkan keberhasilan perlombaan. Sebagai penutup agenda hari kedua, dilakukan rapat evaluasi dan briefing untuk agenda hari berikutnya.\r\n\r\nHari ketiga Pengabdian Masyarakat 2024 dibuka dengan sholat subuh berjamaah, lalu dilanjutkan dengan mengajar dan sosialisasi pengelolaan sampah. Dalam mengajar memuat materi tentang tanaman toga yang diharapkan dapat mencetak peserta didik yang dapat membudidayakan tanaman toga. Sedangkan pada sosialisasi pengelolaan sampah diharapkan dapat meminimalisir pencemaran sampah. Setelah sosialisasi pengelolaan sampah dilanjutkan dengan mengajar di Taman Pendidikan al-Qur’an (TPQ) terdekat di Desa Sumbersari. Proses mengajar dilaksanakan dengan antusias anak-anak untuk belajar agama Islam. Sebagai penutup agenda hari ketiga, dilakukan rapat evaluasi harian dan briefing untuk agenda hari berikutnya. Untuk hari keempat pagi hari melakukan kegiatan yang sama dengan hari sebelumnya, namun di sore hari dilakukan gladi pentas seni dan malam penutupan ditutup dengan pentas seni yang digelar di Lapangan Voli Desa Sumbersari yang dihadiri oleh sekretaris desa dan perangkat desa lainnya serta warga Desa Sumbersari. Dalam pentas seni diis sambutan-sambutan dari ketua pelaksana, ketua umum UKIM UNESA 2024 dan kepala desa yang diwakilkan oleh sekretaris desa. Kemudian dilanjutkan oleh penampilan anak-anak Desa Sumbersari, pembagian hadiah, penyerahan sertifikat kepada pihak desa, sekolah, dan TPQ, serta penyerahan doorprize hasil undian.\r\n\r\nHari terakhir, diawali dengan kegiatan kerja bakti sekitar jalan Desa Sumbersari. Tidak hanya itu, perwakilan panitia UKIM UNESA 2024 juga berpamitan kepada kepala desa dan perangkat desa lainnya.\r\n\r\nPENUTUP\r\n\r\nBerdasarkan kegiatan Pengabdian Masyarakat 2024 yang telah dilakukan UKIM UNESA 2024, telah tercapai yang dirangkum sebagai berikut:\r\n\r\nDapat memberikan manfaat kepada warga Desa Sumbersari yang ditandai dengan melakukan kegiatan kerja bakti, memberikan fasilitas berupa seminar kewirausahan, sosialisasi pengelolaan sampah, mengajar SD dan TPQ.\r\n\r\nMeningkatkan jiwa sosial dan kepedulian Pengurus dan Anggota UKIM UNESA 2024 ditandai dengan terlaksananya kerja bakti bersama warga.\r\n\r\nMembantu mengurangi masalah yang ada di Desa Sumbersari ditandai dengan terlaksananya seluruh rangkaian acara yang terdapat di Pengabdian Masyarakat 2024.', 'published', '2025-05-13 07:40:04', '2025-05-13 07:40:53');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id_dept` int UNSIGNED NOT NULL,
  `kode_dept` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nama_dept` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `deskripsi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id_dept`, `kode_dept`, `nama_dept`, `deskripsi`, `created_at`, `updated_at`) VALUES
(1, 'BPH', 'Badan Pengurus Harian', NULL, '2025-05-13 04:27:04', '2025-05-13 04:27:04'),
(2, 'DPO', 'Departemen Pengembangan Organisasi', NULL, '2025-05-13 04:27:04', '2025-05-13 04:27:04'),
(3, 'DPR', 'Departemen Penelitian dan Riset', NULL, '2025-05-13 04:27:04', '2025-05-13 04:27:04'),
(4, 'DHM', 'Departemen Hubungan Masyarakat', NULL, '2025-05-13 04:27:04', '2025-05-13 04:27:04'),
(5, 'DPE', 'Departemen Pengembangan Ekonomi', NULL, '2025-05-13 04:27:04', '2025-05-13 04:27:04');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id_event` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED DEFAULT NULL,
  `judul` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `slug` varchar(270) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tema` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `deskripsi` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tgl_event_mulai` datetime NOT NULL,
  `tgl_event_selesai` datetime DEFAULT NULL,
  `lokasi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `gambar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `link_pendaftaran` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `link_info` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('upcoming','ongoing','past','cancelled') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'upcoming',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id_event`, `user_id`, `judul`, `slug`, `tema`, `deskripsi`, `tgl_event_mulai`, `tgl_event_selesai`, `lokasi`, `gambar`, `link_pendaftaran`, `link_info`, `status`, `created_at`, `updated_at`) VALUES
(1, 3, 'Galaksi 2024', 'galaksi-2024', 'Eksplorasi dan Optimalisasi Peran Generasi Muda Menuju Indonesia Emas 2045', '[𝐔𝐍𝐈𝐓 𝐊𝐄𝐆𝐈𝐀𝐓𝐀𝐍 𝐈𝐋𝐌𝐈𝐀𝐇 𝐌𝐀𝐇𝐀𝐒𝐈𝐒𝐖𝐀 𝐔𝐍𝐄𝐒𝐀]\r\n\r\n🔥𝗚𝗲𝗯𝘆𝗮𝗿 𝗟𝗼𝗺𝗯𝗮 𝗞𝗮𝗿𝘆𝗮 𝗧𝘂𝗹𝗶𝘀 𝗜𝗹𝗺𝗶𝗮𝗵 𝟮𝟬𝟮𝟰🔥\r\n\r\nHalo Sobat Ilmiah!!👋🏻\r\n\r\nGebyar Lomba Karya Tulis Ilmiah (GALAKSI) 2024 kini hadir kembali dengan lomba-lomba yang menarik dan bergengsi, salah satunya yaitu Lomba Esai. Ini adalah kesempatan emas bagi kalian yang memiliki minat menulis dan ingin menyuarakan ide-ide brilian kalian.\r\n\r\n🎖️𝗟𝗢𝗠𝗕𝗔 𝗘𝗦𝗔𝗜 (𝗠𝗮𝗵𝗮𝘀𝗶𝘀𝘄𝗮 𝗗𝟯/𝗗𝟰/𝗦𝟭)\r\n • Gelombang 1: Rp50.000,00\r\n      🗓️ 13 Mei 2024 - 4 Juni 2024\r\n • Gelombang 2: Rp60.000,00\r\n      🗓️ 5 Juni 2024 - 7 Agustus 2024 \r\n\r\n💡[𝐓𝐄𝐌𝐀 𝐆𝐀𝐋𝐀𝐊𝐒𝐈 𝟐𝟎𝟐𝟒]\r\n\"Eksplorasi dan Optimalisasi Peran Generasi Muda Menuju Indonesia Emas 2045\"\r\n\r\n🔬 [𝐒𝐔𝐁 𝐓𝐄𝐌𝐀]\r\n1. Pendidikan\r\n2. Sosial Budaya\r\n3. Ekonomi\r\n4. Kesehatan dan Lingkungan\r\n5. Teknologi\r\n6. Hukum dan Politik\r\n\r\n🏆 [𝐇𝐀𝐃𝐈𝐀𝐇]\r\n1. Uang Pembinaan \r\n2. Sertifikat \r\n3. Trophy\r\n4. Free National Talkshow\r\n\r\n💶 [𝐌𝐄𝐓𝐎𝐃𝐄 𝐏𝐄𝐌𝐁𝐀𝐘𝐀𝐑𝐀𝐍]\r\na.n/ Nur Faizah Fidaroaini\r\nBRI: 002301123369507\r\nBTN: 0037701610406166\r\nHana Bank: 10595811340\r\nDana: 087717362101\r\nGopay: 087717362101\r\n\r\n📲 [𝐊𝐎𝐍𝐅𝐈𝐑𝐌𝐀𝐒𝐈 𝐏𝐄𝐌𝐁𝐀𝐘𝐀𝐑𝐀𝐍]\r\n1. Faizah (087717362101)\r\n\r\n📞 [ 𝐂𝐎𝐍𝐓𝐀𝐂𝐓 𝐏𝐄𝐑𝐒𝐎𝐍 ]\r\n1. Lisa (085708827959)\r\n2. Rendy (089515520970)\r\n\r\n𝗟𝗶𝗻𝗸 𝗣𝗲𝗻𝗱𝗮𝗳𝘁𝗮𝗿𝗮𝗻 𝗱𝗮𝗻 𝗕𝘂𝗸𝘂 𝗣𝗮𝗻𝗱𝘂𝗮𝗻:\r\nhttps://linktr.ee/BerkasGALAKSI2024\r\n\r\nCheck our social media for further Information:\r\n𝐈𝐧𝐬𝐭𝐚𝐠𝐫𝐚𝐦: @galaksiunesa_2024 & @ukim_unesa\r\n𝐓𝐢𝐤𝐓𝐨𝐤: galaksiukimunesa \r\n𝐅𝐚𝐜𝐞𝐛𝐨𝐨𝐤: Galaksi Unesa \r\n𝐗: galaksi_unesa \r\n𝐘𝐨𝐮𝐓𝐮𝐛𝐞: UKIM UNESA', '2025-06-25 23:59:00', NULL, 'Universitas Negeri Surabaya', './asset/gbr_event/file_682d6089e115b4.31237185.png', 'https://linktr.ee/BerkasGALAKSI2024', '', 'upcoming', '2025-05-21 05:11:37', '2025-05-21 05:11:37');

-- --------------------------------------------------------

--
-- Table structure for table `karya_categories`
--

CREATE TABLE `karya_categories` (
  `id_category` int UNSIGNED NOT NULL,
  `nama_category` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `karya_categories`
--

INSERT INTO `karya_categories` (`id_category`, `nama_category`, `created_at`, `updated_at`) VALUES
(1, 'Ilmiah', '2025-05-13 04:27:04', '2025-05-13 04:27:04'),
(2, 'Non Ilmiah', '2025-05-13 04:27:04', '2025-05-13 04:27:04');

-- --------------------------------------------------------

--
-- Table structure for table `karya_cipta`
--

CREATE TABLE `karya_cipta` (
  `id_karya` int UNSIGNED NOT NULL,
  `id_category` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED DEFAULT NULL,
  `judul` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `slug` varchar(270) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `isi` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `file_pendukung` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` enum('published','pending','rejected') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `karya_cipta`
--

INSERT INTO `karya_cipta` (`id_karya`, `id_category`, `user_id`, `judul`, `slug`, `isi`, `file_pendukung`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 3, 'Perempuan Harus Lebih Menutup Pakaian Agar Bisa Disebut Menjaga Diri | [Kajian Aktivis]', 'perempuan-harus-lebih-menutup-pakaian-agar-bisa-disebut-menjaga-diri-kajian-aktivis', 'Tidak hanya tingkah laku dan tutur kata, pakaian dapat menggambarkan citra diri atau self image seseorang. Tingkat citra diri dalam diri seseorang dapat rendah maupun tinggi. Individu yang memiliki citra diri yang tinggi dapat mengembangkan dirinya karena menyadari aset yang dimilikinya harus dijaga dan dihormati. Cara berpakaian juga merupakan salah satu bentuk komunikasi, antara lain bagaimana kita ingin dilihat oleh orang lain dan seperti apa kita ingin diperlakukan. Bahkan suatu ungkapan Jawa mengatakan “ Ajining raga ana ing busana” yang artinya pakaian juga berperan penting bagi seseorang. Orang dengan busana atau pakaian yang rapi tentunya dapat menaikkan martabat. Dengan kata lain, busana atau pakaian secara fisik mencerminkan siapa diri kita sebenarnya.\r\n\r\n  Dalam relasi sosial, citra diri wanita semakin jelas dilihat tempatnya. Seorang perempuan yang berpakaian tertutup dianggap lebih menghargai kenyamanan pemakainya namun juga nyaman di mata orang lain. Lebih nyaman dikarenakan perempuan yang memakai pakaian tertutup tentunya merasa melindungi bagian-bagian atau aset penting yang dimilikinya tanpa khawatir orang lain melihat atau bahkan menjamah bagian-bagian atau aset penting ini. \r\n\r\nPakaian tertutup menjadi upaya preventif agar tidak terjadi objektifikasi. Hal ini selaras dengan jurnal edukasi yang dilakukan oleh UIN Jakarta tahun 2019 dimana meneliti bahwa salah satu upaya preventif untuk menangani objektifikasi tubuh adalah melalui dengan busana yang berfungsi untuk menutup bagian tubuh tertentu. Segi lainnya menurut analisis survei BBC tahun 2020 tentang hasil survei terbaru mengenai pelecehan seksual di ruang publik. Dalam temuan survei, mayoritas korban pelecehan seksual di ruang publik mengenakan baju terbuka, dengan presentasi lebih dari 82%, sedangkan bagi mereka yang menggunakan pakaian tertutup dengan prsentasi terendah mulai dari 16-18% saja. Sementara itu, meninjau psikologi pria dengan penelitian yang dilakukan pada pria di inggris menunjukkan bahwa lebih dari 50 persen pria mengaku akan lebih menghormati wanita yang berpakaian tertutup, sementara sekitar 25 persen mengaku hal itu bergantung pada wanitanya. Hanya 22 persen pria yang mengatakan dia akan menghargai wanita yang berpakaian terbuka. Artinya salah satu upaya preventif agar tidak terjadi objektifikasi adalah dengan modifikasi pakaian dengan tertutup.\r\n\r\nSelain itu, adat ketimuran di Indonesia identik dengan pakaian yang tertutup. Standard tertutup masyarakat Indonesia bukan hanya dari sudut pandang agama Islam, tetapi juga mengacu pada nilai kesopanan dan etika masyarakat Indonesia. Dapat dikatakan pakaian tertutup bukan hanya yang memakai hijab saja, mengingat ideologi Indonesia adalah Pancasila, beragam agama, suku bangsa hingga budaya. Berpakaian tertutup ini artinya berpakaian yang sopan dan sesuai dengan kondisi dan situasi. Sehingga perlu disikapi bagaimana standard pakaian tertutup tersebut. Pakaian yang baik tidak hanya terbatas berdasarkan potensi yang dimiliki, menghargai diri sendiri, dan percaya diri yang akan memudahkan individu melakukan berbagai hal namun dapat menutupi bagian-bagian atau aset penting yang dimiliki.\r\n\r\nSeorang perempuan harus berupaya untuk menutup diri agar bisa dianggap menjaga diri. Dengan cara menutup diri tersebut sama saja dengan melindungi diri dari hal-hal yang dapat menimbulkan kejahatan. Beda halnya dengan seorang yang tidak menutup diri, seorang perempuan yang tidak menutup diri akan berpotensi  menjadi korban kejahatan seperti pemerkosaan ataupun tindak kriminal lainnya. Hal ini akan berbanding terbalik dengan perempuan yang senantiasa menutup dirinya. Orang yang jahat merasa tidak tertarik dengan wanita yang serba tertutup karena menampilan mereka yang misterius membuat pelaku kejahatan menjadi enggan menjahatinya.\r\n\r\nOleh : Tim Pro 2 Kajian Aktivis Season 1 (Nadi Harvita, dkk.)', NULL, 'published', '2025-05-21 05:23:40', '2025-05-21 05:23:40');

-- --------------------------------------------------------

--
-- Table structure for table `prestasi`
--

CREATE TABLE `prestasi` (
  `id_prestasi` int UNSIGNED NOT NULL,
  `user_id_pencatat` int UNSIGNED DEFAULT NULL,
  `nama_lomba` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `tingkat` enum('Internal Kampus','Kota/Kabupaten','Provinsi','Regional','Nasional','Internasional') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `juara` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Contoh: Juara 1, Medali Emas, Finalis, Peserta Terbaik',
  `tahun` year NOT NULL,
  `penyelenggara` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `peserta_nama` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Nama peserta/tim (jika perorangan/tim)',
  `is_tim` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0: Perorangan, 1: Tim',
  `deskripsi` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci COMMENT 'Detail tambahan tentang prestasi',
  `bukti` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Path/URL file sertifikat/foto',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `prestasi`
--

INSERT INTO `prestasi` (`id_prestasi`, `user_id_pencatat`, `nama_lomba`, `tingkat`, `juara`, `tahun`, `penyelenggara`, `peserta_nama`, `is_tim`, `deskripsi`, `bukti`, `created_at`, `updated_at`) VALUES
(1, 2, 'rsthv', 'Nasional', 'Juara 1', 2025, 'ahdbdh', '1. aiudfi\r\n2. kahsdk\r\n3. jasdvkf', 1, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Mauris porta ullamcorper enim eget dignissim. In vel orci sodales, accumsan nisl sed, sollicitudin neque. Etiam volutpat faucibus lobortis. Nam lacinia sit amet lacus et suscipit. Morbi nec laoreet arcu. Aenean leo magna, rhoncus sed egestas sed, maximus at odio. Aenean imperdiet sodales nisi. Integer ornare felis ac mi varius imperdiet. Aenean pellentesque pulvinar eleifend. Praesent ornare velit quis sollicitudin consequat. Quisque blandit, urna eget maximus mattis, lectus purus mattis ex, et volutpat nibh risus sit amet lectus. Sed feugiat, eros pulvinar sodales venenatis, lacus arcu tristique ipsum, at tempus massa purus ut augue. In hac habitasse platea dictumst.', NULL, '2025-05-24 03:13:50', '2025-05-24 03:13:50');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int UNSIGNED NOT NULL,
  `nama_lengkap` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `nim` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `jurusan` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `angkatan` year DEFAULT NULL,
  `email` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `no_telepon` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `username` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('admin','pengurus','anggota','demisioner','alumni') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'anggota',
  `status_keanggotaan` enum('aktif','pasif','alumni') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'aktif',
  `foto_profil` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `nama_lengkap`, `nim`, `jurusan`, `angkatan`, `email`, `no_telepon`, `username`, `password`, `role`, `status_keanggotaan`, `foto_profil`, `created_at`, `updated_at`) VALUES
(1, 'Administrator Web', NULL, NULL, NULL, 'admin@ukim.unesa.ac.id', NULL, 'admin', '$2y$10$xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx', 'admin', 'aktif', NULL, '2025-05-13 04:27:05', '2025-05-13 04:27:05'),
(2, 'Admin', '222', '-', 2000, 'admin_ukim@ukim.unesa.ac.id', NULL, 'admin_ukim', '$2y$10$q7uwEZ3OWXxsZRCCgX8qlu3l7DIR/mv8gH3HgPamQ7DhYqIrIvwf6', 'anggota', 'aktif', NULL, '2025-05-13 06:12:51', '2025-05-13 06:12:51'),
(3, 'aadf', NULL, NULL, NULL, 'zekagm91@gmail.com', NULL, 'adminzeka', '$2y$10$guSq2x40s7rr9XjQ8J8tZOBdtOOADnmp./xFXE6cBIwBohpV0wnei', 'admin', 'aktif', NULL, '2025-05-21 05:05:13', '2025-05-28 09:01:57');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `blog_ukim`
--
ALTER TABLE `blog_ukim`
  ADD PRIMARY KEY (`id_blog`),
  ADD UNIQUE KEY `slug_unique` (`slug`),
  ADD KEY `id_dept_index` (`id_dept`),
  ADD KEY `user_id_index` (`user_id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id_dept`),
  ADD UNIQUE KEY `kode_dept_unique` (`kode_dept`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id_event`),
  ADD UNIQUE KEY `slug_unique` (`slug`),
  ADD KEY `user_id_index` (`user_id`),
  ADD KEY `tgl_event_mulai_index` (`tgl_event_mulai`);

--
-- Indexes for table `karya_categories`
--
ALTER TABLE `karya_categories`
  ADD PRIMARY KEY (`id_category`),
  ADD UNIQUE KEY `nama_category_unique` (`nama_category`);

--
-- Indexes for table `karya_cipta`
--
ALTER TABLE `karya_cipta`
  ADD PRIMARY KEY (`id_karya`),
  ADD UNIQUE KEY `slug_unique` (`slug`),
  ADD KEY `id_category_index` (`id_category`),
  ADD KEY `user_id_index` (`user_id`);

--
-- Indexes for table `prestasi`
--
ALTER TABLE `prestasi`
  ADD PRIMARY KEY (`id_prestasi`),
  ADD KEY `user_id_pencatat_index` (`user_id_pencatat`),
  ADD KEY `tahun_index` (`tahun`),
  ADD KEY `tingkat_index` (`tingkat`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `email_unique` (`email`),
  ADD UNIQUE KEY `username_unique` (`username`),
  ADD KEY `nim_index` (`nim`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `blog_ukim`
--
ALTER TABLE `blog_ukim`
  MODIFY `id_blog` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id_dept` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id_event` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `karya_categories`
--
ALTER TABLE `karya_categories`
  MODIFY `id_category` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `karya_cipta`
--
ALTER TABLE `karya_cipta`
  MODIFY `id_karya` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `prestasi`
--
ALTER TABLE `prestasi`
  MODIFY `id_prestasi` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `blog_ukim`
--
ALTER TABLE `blog_ukim`
  ADD CONSTRAINT `fk_blog_department` FOREIGN KEY (`id_dept`) REFERENCES `departments` (`id_dept`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_blog_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `fk_event_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `karya_cipta`
--
ALTER TABLE `karya_cipta`
  ADD CONSTRAINT `fk_karya_category` FOREIGN KEY (`id_category`) REFERENCES `karya_categories` (`id_category`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_karya_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `prestasi`
--
ALTER TABLE `prestasi`
  ADD CONSTRAINT `fk_prestasi_user_pencatat` FOREIGN KEY (`user_id_pencatat`) REFERENCES `users` (`id_user`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
