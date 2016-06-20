-- phpMyAdmin SQL Dump
-- version 4.1.6
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jun 20, 2016 at 09:50 AM
-- Server version: 5.6.16
-- PHP Version: 5.5.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `praktikum`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `current`(OUT `result` VARCHAR(10), IN `nom` VARCHAR(10), IN `sem` INT)
BEGIN
	DECLARE maks INT;

	SELECT MAX(`semester`) INTO maks
	FROM `rapor`
	WHERE `nim` = nom;
	
    IF maks = sem
	THEN
	SET result = 'True';
	ELSE
	SET result = 'False';
	END IF;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `tanpaE`(OUT `result` VARCHAR(10), IN `nom` VARCHAR(10))
BEGIN
	DECLARE x VARCHAR(10);
	
	SELECT `nim` INTO x
	FROM `mengambil` 
	WHERE `indeks` = 'E' and `nim` = nom;

	IF x IS NOT NULL
	THEN
	SET `result` = 'True';
	ELSE
	SET `result` = 'False';
	END IF;
	
END$$

--
-- Functions
--
CREATE DEFINER=`root`@`localhost` FUNCTION `recent`(sem INT) RETURNS varchar(10) CHARSET latin1
BEGIN
	DECLARE maks INT;
	DECLARE result VARCHAR(10);

	SELECT MAX(`semester`) INTO maks
	FROM `rapor`
	WHERE `nim` = '13512075';

	IF maks = sem
	THEN
	SET result = 'True';
	ELSE
	SET result = 'False';
	END IF;
RETURN result;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `daftar_ulang`
--

CREATE TABLE IF NOT EXISTS `daftar_ulang` (
  `id_registrasi` int(5) NOT NULL AUTO_INCREMENT,
  `nim` varchar(20) NOT NULL,
  `semester` int(5) NOT NULL,
  `status` varchar(30) NOT NULL,
  `sks` int(5) NOT NULL,
  PRIMARY KEY (`id_registrasi`),
  KEY `nim` (`nim`),
  KEY `nim_2` (`nim`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `daftar_ulang`
--

INSERT INTO `daftar_ulang` (`id_registrasi`, `nim`, `semester`, `status`, `sks`) VALUES
(1, '13512001', 3, 'boleh', 19),
(2, '13512023', 3, 'tidak boleh', 21),
(3, '13512005', 4, 'boleh', 23),
(4, '13512022', 4, 'tidak boleh', 23),
(5, '13512015', 4, 'boleh', 24);

-- --------------------------------------------------------

--
-- Table structure for table `dosen`
--

CREATE TABLE IF NOT EXISTS `dosen` (
  `nip` varchar(20) NOT NULL DEFAULT '',
  `nama` varchar(255) NOT NULL,
  `tahun_masuk` int(11) NOT NULL,
  `jenis_kelamin` varchar(1) NOT NULL,
  PRIMARY KEY (`nip`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `dosen`
--

INSERT INTO `dosen` (`nip`, `nama`, `tahun_masuk`, `jenis_kelamin`) VALUES
('195201091985032001', 'Hira Laksmiwati Soemitro', 1985, 'P'),
('195711231984032001', 'Harlili', 1984, 'P'),
('196512101994021001', 'Rinaldi', 1994, 'L'),
('197109071997022001', 'Tricya Esterina Widagdo', 1997, 'P'),
('197604292008122001', 'Masayu Leylia Khodra', 2008, 'P'),
('197701272008012011', 'Ayu Purwarianti', 2008, 'P'),
('197902102009122001', 'Fazat Nur Azizah', 2009, 'P');

-- --------------------------------------------------------

--
-- Table structure for table `kelas`
--

CREATE TABLE IF NOT EXISTS `kelas` (
  `id_kelas` int(11) NOT NULL AUTO_INCREMENT,
  `nomor_kelas` int(2) NOT NULL,
  `kode_kuliah` varchar(10) NOT NULL,
  `nip_dosen` varchar(20) NOT NULL,
  PRIMARY KEY (`id_kelas`),
  KEY `kelas_dosen` (`nip_dosen`),
  KEY `kelas_kuliah` (`kode_kuliah`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=135224003 ;

--
-- Dumping data for table `kelas`
--

INSERT INTO `kelas` (`id_kelas`, `nomor_kelas`, `kode_kuliah`, `nip_dosen`) VALUES
(135121001, 1, 'IF1210', '197902102009122001'),
(135121002, 2, 'IF1210', '197109071997022001'),
(135220001, 1, 'IF2200', '197701272008012011'),
(135220002, 2, 'IF2200', '195711231984032001'),
(135221101, 1, 'IF2211', '197604292008122001'),
(135221102, 2, 'IF2211', '196512101994021001'),
(135224001, 1, 'IF2240', '195201091985032001'),
(135224002, 2, 'IF2240', '197109071997022001');

-- --------------------------------------------------------

--
-- Table structure for table `kuliah`
--

CREATE TABLE IF NOT EXISTS `kuliah` (
  `kode_kuliah` varchar(10) NOT NULL DEFAULT '',
  `nama` varchar(255) NOT NULL,
  `sks` int(2) NOT NULL DEFAULT '0',
  `deskripsi` text NOT NULL,
  PRIMARY KEY (`kode_kuliah`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `kuliah`
--

INSERT INTO `kuliah` (`kode_kuliah`, `nama`, `sks`, `deskripsi`) VALUES
('IF1210', 'Dasar Pemprograman', 2, 'Mata kuliah ini mengenalkan tentang konsep fundamental pemrograman: abstraksi, dekomposisi problem, modularisasi, rekurens; skill/praktek pemrograman skala kecil (aspek koding); dan memberikan peta dunia pemrograman untuk dapat mempelajari pemrograman secara lebih mendalam pada tahap berikutnya.'),
('IF2200', 'Teori Bahasa Formal dan Otomata', 3, 'Mata kuliah TBFO memberikan pengetahuan pendukung dan keahlian dalam merancang Finite Automata, Regular Expression, dan Pushdown Automata serta pengantar Turing Machine'),
('IF2211', 'Strategi Algoritma', 3, 'Kompleksitas algoritma, Brute Force Algorithms, Greedy Algorithms, Divide and Conquer Algorithms, DFS, BFS, Backtracking Algorithms, Branch and Bound Algorithms, Dynamic Programming String Matching, NP Theory'),
('IF2240', 'Basis Data', 3, 'Mata kuliah ini memberikan pengetahuan mengenai sistem basis data secara umum, mencakup arsitektur sistem basis data, pemodelan data, perancangan skema basis data relasional, pemanfaatan dan pengelolaan data.');

-- --------------------------------------------------------

--
-- Table structure for table `mahasiswa`
--

CREATE TABLE IF NOT EXISTS `mahasiswa` (
  `nim` varchar(20) NOT NULL DEFAULT '',
  `strata` varchar(10) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `jenis_kelamin` varchar(1) NOT NULL,
  `alamat` varchar(255) NOT NULL,
  `no_telepon` varchar(80) NOT NULL,
  `golongan_darah` varchar(5) NOT NULL,
  `nip_dosen_wali` varchar(20) NOT NULL,
  PRIMARY KEY (`nim`),
  KEY `dosen_wali` (`nip_dosen_wali`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `mahasiswa`
--

INSERT INTO `mahasiswa` (`nim`, `strata`, `nama`, `jenis_kelamin`, `alamat`, `no_telepon`, `golongan_darah`, `nip_dosen_wali`) VALUES
('13512001', 'S2', 'Darwin Prasetio', 'L', 'Jl.Ciumbuleuit 66 daerah Ciumbuleuit', '082165113030', 'O', '197109071997022001'),
('13512002', 'S1', 'Eldwin Christian', 'L', 'Jl. Kebon Bibit Barat I No. 52 Kel. Tamansari', '085795156576', 'O', '197109071997022001'),
('13512003', 'S1', 'Fauzan Hilmi Ramadhian', 'L', 'Hunian Kost Sangkuriang Jl. CIsitu Lama No.45B', '085795630596', 'A', '197109071997022001'),
('13512004', 'S1', 'Tirta Wening Rachman', 'L', 'Cicaheum', '085795980141', 'B', '197109071997022001'),
('13512005', 'S1', 'Chrestella Stephanie', 'P', 'Taman Holis Indah', '081312117979', 'O', '197109071997022001'),
('13512006', 'S1', 'Arina Listyarini Dwiastuti', 'P', 'Cimahi', '085722773269', 'O', '197109071997022001'),
('13512007', 'S1', 'Mamat Rahmat', 'L', 'Jalan Cisitu Indah 7 Perumahan Kampung Dago 2 No 12A', '085721247847', 'B', '197109071997022001'),
('13512009', 'S1', 'Rita Sarah', 'P', 'Kompleks Alamanda', '081315706300', 'O', '197109071997022001'),
('13512010', 'S1', 'Kevin Yudi Utama', 'L', 'Jl. Brigdjen Katamso Dalam No. 130', '087731802871', 'O', '197109071997022001'),
('13512011', 'S1', 'Denny Astika Herdioso', 'L', 'Jl. Sadang Luhur no 12 blok 13', '085725792688', 'O', '197109071997022001'),
('13512012', 'S1', 'Fahziar Riesad Wutono', 'L', 'Jl. Abadi Regency Raya No. 9 Komplek Abadi Regency Gegerkalong Girang', '08562390272', 'B', '197109071997022001'),
('13512013', 'S1', 'Joshua Bezaleel Abednego', 'L', 'Jl. Cisitu Lama 9 no 32', '081936063113', 'B', '197109071997022001'),
('13512014', 'S1', 'Muhammad Yafi', 'L', 'Tubagus Ismail', '085729592442', 'O', '197109071997022001'),
('13512015', 'S1', 'Jan Wira Gotama Putra', 'L', 'Dago Asri blok b 30', '08563734273', 'A', '197109071997022001'),
('13512016', 'S1', 'Mario Tressa Juzar', 'L', 'Jl. Cisitu lama III no.24A/154C', '082268175686', 'A', '197109071997022001'),
('13512017', 'S1', 'Arieza Nadya Sekariani', 'P', 'Jalan dago asri blok A 14', '081295032052', 'O', '197109071997022001'),
('13512018', 'S1', 'Tony', 'L', 'Jl. Cisitu Baru Dalam no.72', '085717566229', 'B', '197109071997022001'),
('13512019', 'S1', 'Christ Angga Saputra', 'L', 'Taman Hijau', '08997170052', 'A', '197109071997022001'),
('13512020', 'S1', 'Gifari Kautsar', 'L', 'Jalan Pelesiran Gang Mama Rustama No. 61/56', '081313899535', 'A', '197109071997022001'),
('13512021', 'S1', 'Eric', 'L', 'Ciumbeluit', '085262312602', 'O', '197109071997022001'),
('13512022', 'S1', 'Andarias Silvanus', 'L', 'Gang saleh', '08997007714', 'B', '197109071997022001'),
('13512023', 'S1', 'Junita Sinambela', 'P', 'Jalan Cisitu Baru Dalam No.6', '085360174698', 'O', '197109071997022001'),
('13512024', 'S1', 'Riady Sastra Kusuma', 'L', 'Kebon bibit no32b', '08978670424', 'O', '197109071997022001'),
('13512025', 'S1', 'Stephen', 'L', 'Jln. Tubagus Ismail 5 no. 2A', '081513765370', 'A', '197109071997022001'),
('13512026', 'S1', 'Indam Muhammad', 'L', 'Jl. Guntur Sari Kulon no. 5 Buah Batu', '08562230777', 'A', '197109071997022001'),
('13512027', 'S1', 'Cilvia Sianora Putri', 'P', 'Tubagus Ismail', '08993831581', 'B', '197109071997022001'),
('13512028', 'S1', 'Andre Susanto', 'L', 'Cisitu Indah V', '08568906325', 'AB', '197109071997022001'),
('13512029', 'S1', 'Linda Sekarwati', 'P', 'Jl. Plesiran no. 40/58', '085759759176', 'O', '197109071997022001'),
('13512030', 'S1', 'Alvin Natawiguna', 'L', 'Jl. Ciumbuleuit No. 85', '081282222028', 'A', '197109071997022001'),
('13512031', 'S1', 'Jonathan', 'L', 'Gardujati', '08988881551', 'A', '197109071997022001'),
('13512032', 'S1', 'Timothy Pratama', 'L', 'Jalan Griya Raya I no 1 perumahan Griya Mas daerah Cibogo', '0811221832', 'A', '197109071997022001'),
('13512033', 'S1', 'Ahmad', 'L', 'Jl Cisitu Indah no 14', '087883403928', 'B', '197109071997022001'),
('13512034', 'S1', 'Vidia Anindhita', 'P', 'Jalan Kanayakan Baru No.31A Dago Coblong Bandung', '08567175005', 'B', '197701272008012011'),
('13512035', 'S1', 'Steve Immanuel Harnadi', 'L', 'Jl. Ancol Timur III No.41', '081222324170', 'O', '197701272008012011'),
('13512036', 'S1', 'Riva Syafri Rachmatullah', 'L', 'Jl. Cisitu Lama no.14/160C RT.08 RW.12', '08995602430', 'B', '197701272008012011'),
('13512037', 'S1', 'Yanfa Adi Putra', 'L', 'an', '085703047464', 'O', '197701272008012011'),
('13512038', 'S1', 'Viktor Trimulya Buntoro', 'L', 'Jalan Sangkuriang 27 Dago 40135', '081804168524', 'O', '197701272008012011'),
('13512039', 'S1', 'Felicia Christie', 'P', 'Tubagus Ismail', '08566369311', 'O', '197701272008012011'),
('13512040', 'S1', 'Yusuf Rahmatullah', 'L', 'Komplek Panghegar Permai Ujung Berung Bandung', '085721632951', 'A', '197701272008012011'),
('13512041', 'S1', 'Ivana Clairine Irsan', 'P', 'Tubagus Ismail', '0817385226', 'B', '197701272008012011'),
('13512042', 'S1', 'Muhammad Reza Irvanda', 'L', 'Plesiran', '085762378535', 'A', '197701272008012011'),
('13512043', 'S1', 'Aryya Dwisatya Widigdha', 'L', 'Kebon Kembang', '087757214299', 'B', '197701272008012011'),
('13512044', 'S1', 'Kevin Maulana', 'L', 'Komp. Biofarma No 12 Jalan Gunung Batu Bandung', '081361342705', 'B', '197701272008012011'),
('13512045', 'S1', 'Gilang Julian Suherik', 'L', 'Jl. Tamansari no 60/56 Rt.04 Rw.06 (Taman Hewan)', '083824455975', 'O', '197701272008012011'),
('13512046', 'S1', 'Michael Alexander Wangsa', 'L', 'Jl. Terusan Purabaya 80/66', '08986438008', 'O', '197701272008012011'),
('13512047', 'S1', 'Fahmi Dumadi', 'L', 'Jl. Gunung Batu Gg. H. Juariah 35', '085320107514', 'A', '197701272008012011'),
('13512048', 'S1', 'Muntaha Ilmi', 'L', 'Jl. Pelesiran no. 19 Kel. Tamansari Kec. Bandung Wetan', '081282345008', 'B', '197701272008012011'),
('13512049', 'S1', 'Diah Fauziah', 'P', 'Jalan Cisitu Lama Gang 2 No 89/154c', '085374566905', 'O', '197701272008012011'),
('13512050', 'S1', 'Teofebano', 'L', 'Cisitu lama 8 no 1a. kosan pontek', '08979465639', 'A', '197701272008012011'),
('13512051', 'S1', 'Yollanda Sekarrini', 'P', 'Cisitu Lama I', '085767438495', 'A', '197701272008012011'),
('13512052', 'S1', 'Try Ajitiono', 'L', 'Jln. Cisitu Lama 8 No 51', '08159778609', 'O', '197701272008012011'),
('13512053', 'S1', 'Rakhmatullah Yoga Sutrisna', 'L', 'pelesiran', '085721629079', 'O', '197701272008012011'),
('13512054', 'S1', 'Luqman Faizlani Kusnadi', 'L', 'Jalan Cisitu Lama 54 kec Coblong kel Dago.40135', '081214593043', 'B', '197701272008012011'),
('13512055', 'S1', 'Mario Filino', 'L', 'Jalan Ir. H. Juanda 454 gang Dago Elos III Bandung', '081384497255', 'A', '197701272008012011'),
('13512056', 'S1', 'Danang Afnan Hudaya', 'L', 'Jl.Bijaksana III no.9 Pasteur Sukajadi', '081326455055', 'O', '197701272008012011'),
('13512057', 'S1', 'Susanti Gojali', 'P', 'Jln. Tubagus Ismail 7 no. 15', '081930251469', 'AB', '197701272008012011'),
('13512058', 'S1', 'Andrey Simaputera', 'L', 'Tubagus Ismail', '085721248822', 'A', '197701272008012011'),
('13512059', 'S1', 'Jeffrey Lingga Binangkit', 'L', 'Jalan Tubagus Ismail XVII No. 55c', '085724715978', 'B', '197701272008012011'),
('13512060', 'S1', 'Adhika Sigit Ramanto', 'L', 'Jl. Cisitu Lama VIII No. 1A', '0811191255', 'B', '197701272008012011'),
('13512061', 'S2', 'Tegar Aji Pangestu', 'L', 'Pelesiran 57a/56', '085646172290', 'O', '197701272008012011'),
('13512062', 'S1', 'Riska', 'P', 'Pelesiran nomor 26', '085370294043', 'O', '197701272008012011'),
('13512063', 'S2', 'Ardi Wicaksono', 'L', 'Tubagus Ismail', '087880219119', 'B', '197701272008012011'),
('13512064', 'S2', 'Daniar Heri Kurniawan', 'L', 'plesiran', '089679558799', 'B', '197701272008012011'),
('13512065', 'S2', 'Willy', 'L', 'Ciumbuleuit', '0819884765', 'AB', '197701272008012011'),
('13512066', 'S2', 'Calvin Sadewa', 'L', 'dago timur', '082364996779', 'O', '197701272008012011'),
('13512067', 'S2', 'Muhammad Husain Jakfari', 'L', 'Sadang Seraang', '08973056008', 'B', '197902102009122001'),
('13512068', 'S2', 'Khaidzir Muhammad Shahih', 'L', 'Jl. Surapati No. 143/144 C Suci', '081322975540', 'O', '197902102009122001'),
('13512069', 'S2', 'Binanda Smarta Aji', 'L', 'Jalan Cisitu Baru 13', '085755190027', 'O', '197902102009122001'),
('13512070', 'S2', 'Willy', 'L', 'Ciumbuleuit', '081361714046', 'O', '197902102009122001'),
('13512071', 'S2', 'Winson Waisakurnia', 'L', 'Ciumbuleuit', '085262816093', 'A', '197902102009122001'),
('13512072', 'S1', 'Kanya Paramita', 'P', 'Jl. Tilil no 1 Daerah Gasibu Bandung 40133', '081214414146', 'O', '197902102009122001'),
('13512073', 'S2', 'Bagaskara Pramudita', 'L', 'Tubagus Ismail', '085784041155', 'A', '197902102009122001'),
('13512074', 'S1', 'Jacqueline Ibrahim', 'P', 'Tubagus', '085720286295', 'A', '197902102009122001'),
('13512075', 'S2', 'Rafi Ramadhan', 'L', 'Jalan Nilem V No 6 RT 02 RW 05 Buah Batu Kel. Cijagra Kec. Lengkong', '08172300229', 'AB', '197902102009122001'),
('13512076', 'S2', 'Ahmad Zaky', 'L', 'Jl. Cisitu Lama III no. 24a/154c 40135', '08170009891', 'O', '197902102009122001'),
('13512077', 'S1', 'Khoirunnisa Afifah', 'P', 'Cisitu Lama Gang 2 No 132/154C', '087835688435', 'O', '197902102009122001'),
('13512078', 'S2', 'Ramandika Pranamulia', 'L', 'Jl. Imam Bonjol No. 41', '085881315920', 'B', '197902102009122001'),
('13512079', 'S2', 'Dariel Valdano', 'L', 'Cisitu lama 9 no 32', '087788616270', 'B', '197902102009122001'),
('13512080', 'S1', 'Hayyu'' Luthfi Hanifah', 'P', 'Kebon Bibit Barat I No 50', '085229167235', 'O', '197902102009122001'),
('13512081', 'S3', 'Hendro Triokta Brianto', 'L', 'JL. Cikutra Gg. Sekepondok 1', '02291563991', 'O', '197902102009122001'),
('13512082', 'S3', 'Marcelinus Henry Menori', 'L', 'Cimahi', '081394410971', 'B', '197902102009122001'),
('13512083', 'S3', 'Ihsan Naufal Ardanto', 'L', 'Jl. Cisitu Lama IX no. 21', '081319279337', 'O', '197902102009122001'),
('13512084', 'S1', 'Choirunnisa Fatima', 'P', 'Jalan Dago Timur no 4', '085876817870', 'O', '197902102009122001'),
('13512085', 'S3', 'Melvin Fonda', 'L', 'jalan cisitu indah 2 no 3', '087887470382', 'A', '197902102009122001'),
('13512086', 'S3', 'Stanley Santoso', 'L', 'Tubagus Ismail', '087868919833', 'O', '197902102009122001'),
('13512087', 'S3', 'Mochamad Lutfi Fadlan', 'L', 'Jalan Babakan Ciamis no.73B', '085694357352', 'B', '197902102009122001'),
('13512088', 'S1', 'Annisaur Rosi Lutfiana', 'P', 'Taman Hewan', '085776877500', 'O', '197902102009122001'),
('13512089', 'S3', 'Rikysamuel', 'L', 'Jl kresna no 1', '085722064771', 'B', '197902102009122001'),
('13512090', 'S1', 'Nisa Dian Rachmadi', 'P', 'Margahayu', '085221464301', 'O', '197902102009122001'),
('13512091', 'S1', 'Windy Amelia', 'P', 'Jalan Kanayakan Baru No.31A Dago Coblong Bandung', '08568554061', 'AB', '197902102009122001'),
('13512092', 'S3', 'Reinaldo Michael Hasian', 'L', 'Lembah Tubagus Ismail no.99', '085795638723', 'A', '197902102009122001'),
('13512093', 'S3', 'Jonathan Sudibya', 'L', 'Tubagus Ismail', '087878180839', 'A', '197902102009122001'),
('13512094', 'S3', 'Aldyaka Mushofan', 'L', 'Jl cisitu lama gang 2 no 25/154c', '085728300606', 'O', '197902102009122001'),
('13512095', 'S1', 'Edmund Ophie', 'L', 'Jalan Cisitu Baru No 46', '081224268134', 'O', '197902102009122001'),
('13512096', 'S1', 'Kevin', 'L', 'Jl. Ciumbuleuit Gang Suhari 2 No. 46-48', '085760949600', 'B', '197902102009122001'),
('13512097', 'S1', 'Kevin', 'L', 'Jl. Bukit Tunggul no 4', '089625547803', 'A', '197902102009122001'),
('13512098', 'S1', 'William Stefan Hartono', 'L', 'Jalan Ciumbuleuit 47', '089671536604', 'O', '197902102009122001'),
('13512099', 'S1', 'Aurelia', 'P', 'Jalan Kanayakan Baru no. 49', '085691141448', 'O', '197902102009122001'),
('13512100', 'S1', 'Luthfi Hamid Masykuri', 'L', 'Jalan Cisitu Lama Gang 5 No 42B', '085292871348', 'B', '197902102009122001');

-- --------------------------------------------------------

--
-- Table structure for table `mengambil`
--

CREATE TABLE IF NOT EXISTS `mengambil` (
  `nim` varchar(20) NOT NULL,
  `id_kelas` int(11) NOT NULL,
  `nilai` float(5,2) NOT NULL,
  `indeks` varchar(5) NOT NULL,
  PRIMARY KEY (`nim`,`id_kelas`),
  KEY `kelas_id` (`id_kelas`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `mengambil`
--

INSERT INTO `mengambil` (`nim`, `id_kelas`, `nilai`, `indeks`) VALUES
('13512001', 135121001, 3.50, 'AB'),
('13512001', 135220001, 4.00, 'A'),
('13512001', 135221101, 3.50, 'AB'),
('13512001', 135224001, 1.00, 'D'),
('13512002', 135121002, 2.50, 'BC'),
('13512002', 135220002, 4.00, 'A'),
('13512002', 135221102, 2.00, 'C'),
('13512002', 135224002, 2.00, 'C'),
('13512003', 135121001, 2.50, 'C'),
('13512003', 135220001, 4.00, 'A'),
('13512003', 135224001, 2.00, 'C'),
('13512004', 135121002, 2.50, 'BC'),
('13512004', 135220002, 3.00, 'B'),
('13512004', 135221102, 4.00, 'A'),
('13512004', 135224002, 3.00, 'B'),
('13512005', 135220001, 2.00, 'C'),
('13512005', 135221101, 2.00, 'C'),
('13512005', 135224001, 0.00, 'E'),
('13512006', 135121002, 4.00, 'A'),
('13512006', 135220002, 2.50, 'BC'),
('13512007', 135121001, 4.00, ''),
('13512007', 135221101, 4.00, ''),
('13512008', 135121002, 1.00, ''),
('13512008', 135221102, 4.00, ''),
('13512009', 135220001, 4.00, ''),
('13512009', 135221101, 2.50, ''),
('13512009', 135224001, 4.00, ''),
('13512010', 135121002, 3.00, ''),
('13512010', 135220002, 3.00, ''),
('13512010', 135221102, 3.00, ''),
('13512011', 135121001, 2.00, ''),
('13512011', 135221101, 4.00, ''),
('13512011', 135224001, 3.50, ''),
('13512012', 135221102, 1.00, ''),
('13512012', 135224002, 4.00, ''),
('13512013', 135121001, 3.00, ''),
('13512013', 135220001, 1.00, ''),
('13512014', 135220002, 3.50, ''),
('13512014', 135224002, 4.00, ''),
('13512015', 135121001, 2.50, ''),
('13512015', 135221101, 4.00, ''),
('13512015', 135224001, 3.50, ''),
('13512016', 135121002, 4.00, ''),
('13512016', 135220002, 1.00, ''),
('13512016', 135221102, 1.00, ''),
('13512016', 135224002, 3.50, ''),
('13512017', 135220001, 1.00, ''),
('13512017', 135221101, 2.00, ''),
('13512018', 135121002, 2.50, ''),
('13512018', 135220002, 2.00, ''),
('13512018', 135224002, 1.00, ''),
('13512019', 135121001, 3.00, ''),
('13512019', 135221101, 3.00, ''),
('13512020', 135121002, 4.00, ''),
('13512020', 135221102, 3.50, ''),
('13512020', 135224002, 4.00, ''),
('13512021', 135121001, 2.50, ''),
('13512021', 135220001, 3.50, ''),
('13512021', 135221101, 3.50, ''),
('13512022', 135121002, 4.00, ''),
('13512022', 135221102, 2.00, ''),
('13512023', 135121001, 4.00, ''),
('13512023', 135220001, 2.50, ''),
('13512023', 135221101, 3.00, ''),
('13512023', 135224001, 2.50, ''),
('13512024', 135121002, 3.00, ''),
('13512025', 135121001, 3.50, ''),
('13512025', 135220001, 2.50, ''),
('13512025', 135221101, 3.00, ''),
('13512025', 135224001, 4.00, ''),
('13512026', 135224002, 3.50, ''),
('13512027', 135121001, 2.50, ''),
('13512027', 135220001, 2.50, ''),
('13512027', 135224001, 3.00, ''),
('13512028', 135121002, 3.50, ''),
('13512028', 135220002, 4.00, ''),
('13512028', 135221102, 3.00, ''),
('13512028', 135224002, 2.50, ''),
('13512029', 135121001, 1.00, ''),
('13512029', 135220001, 0.00, ''),
('13512029', 135224001, 1.00, ''),
('13512030', 135121002, 3.00, ''),
('13512030', 135220002, 2.50, ''),
('13512030', 135221102, 2.50, ''),
('13512030', 135224002, 2.00, ''),
('13512031', 135121001, 3.50, ''),
('13512031', 135220001, 2.50, ''),
('13512031', 135221101, 4.00, ''),
('13512031', 135224001, 2.00, ''),
('13512032', 135121002, 4.00, ''),
('13512032', 135221102, 4.00, ''),
('13512032', 135224002, 4.00, ''),
('13512033', 135121001, 4.00, ''),
('13512033', 135220001, 4.00, ''),
('13512033', 135224001, 4.00, ''),
('13512034', 135220002, 0.00, ''),
('13512034', 135221102, 4.00, ''),
('13512034', 135224002, 2.00, ''),
('13512035', 135220001, 1.00, ''),
('13512036', 135220002, 0.00, ''),
('13512036', 135221102, 4.00, ''),
('13512037', 135121001, 3.00, ''),
('13512037', 135221101, 2.00, ''),
('13512037', 135224001, 4.00, ''),
('13512038', 135224002, 2.00, ''),
('13512039', 135121001, 4.00, ''),
('13512039', 135220001, 3.00, ''),
('13512039', 135221101, 3.50, ''),
('13512040', 135220002, 3.00, ''),
('13512040', 135221102, 3.50, ''),
('13512040', 135224002, 3.50, ''),
('13512041', 135220001, 3.00, ''),
('13512041', 135221101, 3.00, ''),
('13512041', 135224001, 0.00, ''),
('13512042', 135121002, 3.00, ''),
('13512042', 135220002, 2.50, ''),
('13512043', 135121001, 1.00, ''),
('13512043', 135224001, 2.00, ''),
('13512044', 135121002, 2.00, ''),
('13512044', 135220002, 3.50, ''),
('13512044', 135221102, 2.50, ''),
('13512044', 135224002, 2.50, ''),
('13512045', 135221101, 4.00, ''),
('13512045', 135224001, 3.50, ''),
('13512046', 135221102, 3.50, ''),
('13512047', 135121001, 4.00, ''),
('13512047', 135221101, 2.00, ''),
('13512047', 135224001, 2.50, ''),
('13512048', 135220002, 2.50, ''),
('13512048', 135221102, 3.00, ''),
('13512048', 135224002, 2.50, ''),
('13512049', 135121001, 3.50, ''),
('13512049', 135220001, 3.50, ''),
('13512049', 135221101, 4.00, ''),
('13512050', 135121002, 2.00, ''),
('13512050', 135220002, 4.00, ''),
('13512050', 135221102, 3.00, ''),
('13512050', 135224002, 4.00, ''),
('13512051', 135220001, 3.50, ''),
('13512051', 135221101, 4.00, ''),
('13512051', 135224001, 2.00, ''),
('13512052', 135121002, 3.50, ''),
('13512052', 135221102, 2.50, ''),
('13512052', 135224002, 4.00, ''),
('13512053', 135121001, 4.00, ''),
('13512053', 135221101, 1.00, ''),
('13512054', 135220002, 4.00, ''),
('13512054', 135224002, 2.00, ''),
('13512055', 135121001, 3.00, ''),
('13512055', 135220001, 4.00, ''),
('13512056', 135220002, 4.00, ''),
('13512056', 135221102, 1.00, ''),
('13512056', 135224002, 3.00, ''),
('13512057', 135221101, 3.50, ''),
('13512057', 135224001, 2.50, ''),
('13512058', 135121002, 4.00, ''),
('13512058', 135220002, 2.00, ''),
('13512058', 135224002, 4.00, ''),
('13512059', 135121001, 2.50, ''),
('13512059', 135220001, 4.00, ''),
('13512059', 135221101, 2.00, ''),
('13512059', 135224001, 4.00, ''),
('13512060', 135121002, 2.50, ''),
('13512060', 135221102, 2.00, ''),
('13512060', 135224002, 4.00, ''),
('13512062', 135220002, 3.50, ''),
('13512063', 135121001, 4.00, ''),
('13512064', 135121002, 3.00, ''),
('13512064', 135221102, 1.00, ''),
('13512064', 135224002, 2.50, ''),
('13512065', 135220001, 4.00, ''),
('13512065', 135224001, 2.00, ''),
('13512066', 135121002, 1.00, ''),
('13512066', 135220002, 0.00, ''),
('13512066', 135221102, 4.00, ''),
('13512066', 135224002, 1.00, ''),
('13512067', 135121001, 3.50, ''),
('13512067', 135220001, 0.00, ''),
('13512067', 135221101, 2.00, ''),
('13512067', 135224001, 4.00, ''),
('13512068', 135121002, 3.00, ''),
('13512068', 135221102, 2.00, ''),
('13512069', 135121001, 3.50, ''),
('13512069', 135220001, 4.00, ''),
('13512069', 135221101, 3.50, ''),
('13512069', 135224001, 2.00, ''),
('13512070', 135224002, 3.00, ''),
('13512071', 135220001, 2.50, ''),
('13512071', 135221101, 0.00, ''),
('13512071', 135224001, 3.50, ''),
('13512072', 135220002, 1.00, ''),
('13512072', 135221102, 2.00, ''),
('13512073', 135121001, 2.50, ''),
('13512073', 135220001, 4.00, ''),
('13512074', 135121002, 3.50, ''),
('13512074', 135220002, 3.50, ''),
('13512075', 135121001, 1.00, ''),
('13512075', 135221101, 3.50, ''),
('13512076', 135220002, 2.00, ''),
('13512077', 135220001, 4.00, ''),
('13512077', 135221101, 3.00, ''),
('13512078', 135224002, 1.00, ''),
('13512079', 135121001, 3.00, ''),
('13512079', 135220001, 0.00, ''),
('13512079', 135224001, 2.50, ''),
('13512080', 135121002, 2.50, ''),
('13512080', 135220002, 2.50, ''),
('13512080', 135224002, 2.00, ''),
('13512081', 135121001, 3.00, ''),
('13512081', 135220001, 2.50, ''),
('13512081', 135221101, 3.50, ''),
('13512081', 135224001, 2.50, ''),
('13512082', 135221102, 4.00, ''),
('13512083', 135121001, 1.00, ''),
('13512083', 135221101, 1.00, ''),
('13512084', 135220002, 2.50, ''),
('13512084', 135221102, 4.00, ''),
('13512084', 135224002, 0.00, ''),
('13512085', 135121001, 1.00, ''),
('13512085', 135220001, 4.00, ''),
('13512085', 135224001, 2.00, ''),
('13512086', 135121002, 4.00, ''),
('13512086', 135220002, 2.50, ''),
('13512087', 135221101, 4.00, ''),
('13512087', 135224001, 3.00, ''),
('13512088', 135121002, 4.00, ''),
('13512088', 135221102, 1.00, ''),
('13512089', 135121001, 3.50, ''),
('13512089', 135220001, 4.00, ''),
('13512089', 135224001, 2.50, ''),
('13512090', 135121002, 1.00, ''),
('13512090', 135220002, 1.00, ''),
('13512090', 135221102, 1.00, ''),
('13512090', 135224002, 1.00, ''),
('13512091', 135121001, 0.00, ''),
('13512091', 135220001, 2.50, ''),
('13512091', 135224001, 1.00, ''),
('13512092', 135121002, 1.00, ''),
('13512092', 135220002, 3.50, ''),
('13512092', 135221102, 4.00, ''),
('13512092', 135224002, 4.00, ''),
('13512093', 135121001, 4.00, ''),
('13512093', 135220001, 2.00, ''),
('13512093', 135221101, 3.50, ''),
('13512093', 135224001, 1.00, ''),
('13512094', 135121002, 3.50, ''),
('13512094', 135220002, 4.00, ''),
('13512095', 135121001, 0.00, ''),
('13512095', 135221101, 1.00, ''),
('13512096', 135121002, 2.00, ''),
('13512096', 135220002, 4.00, ''),
('13512096', 135221102, 3.00, ''),
('13512096', 135224002, 4.00, ''),
('13512097', 135121001, 0.00, ''),
('13512097', 135221101, 2.50, ''),
('13512097', 135224001, 4.00, ''),
('13512098', 135121002, 1.50, ''),
('13512098', 135220002, 4.00, ''),
('13512098', 135221102, 2.50, ''),
('13512099', 135121001, 4.00, ''),
('13512099', 135220001, 2.00, ''),
('13512099', 135224001, 3.50, '');

-- --------------------------------------------------------

--
-- Table structure for table `rapor`
--

CREATE TABLE IF NOT EXISTS `rapor` (
  `nim` varchar(20) NOT NULL,
  `semester` int(5) NOT NULL,
  `tahun` int(5) NOT NULL,
  `NR` float(5,2) NOT NULL,
  `sks_total` int(5) NOT NULL,
  `sks_ambil` int(5) NOT NULL,
  `status` varchar(20) NOT NULL,
  PRIMARY KEY (`nim`,`semester`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `rapor`
--

INSERT INTO `rapor` (`nim`, `semester`, `tahun`, `NR`, `sks_total`, `sks_ambil`, `status`) VALUES
('13512002', 3, 2015, 3.25, 20, 20, 'lengkap'),
('13512002', 4, 2016, 2.95, 19, 19, 'lengkap'),
('13512005', 3, 2015, 3.15, 23, 23, 'lengkap'),
('13512015', 2, 2015, 3.30, 20, 20, 'lengkap'),
('13512015', 3, 2015, 3.45, 20, 22, 'tidak lengkap'),
('13512022', 2, 2015, 3.40, 22, 22, 'lengkap'),
('13512022', 3, 2015, 3.15, 20, 22, 'tidak lengkap'),
('13512075', 3, 2015, 3.35, 21, 21, 'lengkap'),
('13512075', 4, 2016, 3.00, 22, 24, 'belum lengkap'),
('13512075', 5, 2016, 3.33, 21, 23, 'Belum lengkap');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `daftar_ulang`
--
ALTER TABLE `daftar_ulang`
  ADD CONSTRAINT `daftar_ulang_ibfk_1` FOREIGN KEY (`nim`) REFERENCES `mahasiswa` (`nim`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `kelas`
--
ALTER TABLE `kelas`
  ADD CONSTRAINT `kelas_dosen` FOREIGN KEY (`nip_dosen`) REFERENCES `dosen` (`nip`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `kelas_kuliah` FOREIGN KEY (`kode_kuliah`) REFERENCES `kuliah` (`kode_kuliah`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `mahasiswa`
--
ALTER TABLE `mahasiswa`
  ADD CONSTRAINT `dosen_wali` FOREIGN KEY (`nip_dosen_wali`) REFERENCES `dosen` (`nip`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `mengambil`
--
ALTER TABLE `mengambil`
  ADD CONSTRAINT `kelas_id` FOREIGN KEY (`id_kelas`) REFERENCES `kelas` (`id_kelas`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `kelas_mahasiswa` FOREIGN KEY (`nim`) REFERENCES `mahasiswa` (`nim`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `rapor`
--
ALTER TABLE `rapor`
  ADD CONSTRAINT `rapor_ibfk_1` FOREIGN KEY (`nim`) REFERENCES `mahasiswa` (`nim`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
