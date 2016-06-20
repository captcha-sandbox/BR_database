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
-- Database: `br_deductive`
--

-- --------------------------------------------------------

--
-- Table structure for table `argumen_body`
--

CREATE TABLE IF NOT EXISTS `argumen_body` (
  `id_aturan` int(11) NOT NULL,
  `urutan_body` int(11) NOT NULL,
  `urutan_argumen` int(5) NOT NULL,
  `isi_argumen` varchar(100) NOT NULL,
  PRIMARY KEY (`id_aturan`,`urutan_body`,`urutan_argumen`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `argumen_body`
--

INSERT INTO `argumen_body` (`id_aturan`, `urutan_body`, `urutan_argumen`, `isi_argumen`) VALUES
(4, 1, 1, 'X'),
(4, 2, 1, 'X'),
(4, 2, 2, 'Y'),
(4, 2, 3, 'Z'),
(4, 3, 1, 'Z'),
(4, 3, 2, '3.25'),
(5, 1, 1, 'X'),
(5, 1, 2, 'Y'),
(5, 1, 3, 'Z'),
(5, 2, 1, 'X'),
(5, 2, 2, 'Y'),
(5, 2, 3, 'P'),
(5, 3, 1, 'X'),
(5, 3, 2, 'Y'),
(5, 3, 3, 'Q'),
(5, 4, 1, 'P'),
(5, 4, 2, 'Q'),
(6, 1, 1, 'X'),
(6, 1, 2, 'Y'),
(6, 1, 3, 'Z'),
(6, 2, 1, 'X'),
(6, 2, 2, 'Y'),
(6, 2, 3, 'N'),
(6, 3, 1, 'Y'),
(6, 3, 2, '1'),
(7, 1, 1, 'X'),
(7, 1, 2, 'Y'),
(7, 1, 3, 'Z'),
(7, 2, 1, 'Z'),
(7, 2, 2, '''S1'''),
(8, 1, 1, 'X'),
(8, 2, 1, 'X'),
(8, 2, 2, 'Y'),
(8, 2, 3, 'Z'),
(8, 3, 1, 'Z'),
(8, 3, 2, '2.75'),
(9, 1, 1, 'X'),
(9, 2, 1, 'X'),
(9, 2, 2, 'Y'),
(9, 2, 3, 'Z'),
(9, 3, 1, 'Z'),
(9, 3, 2, '3.00'),
(11, 1, 1, 'X'),
(11, 1, 2, 'Y'),
(11, 1, 3, 'Z'),
(11, 2, 1, 'Z'),
(11, 2, 2, '''S2'''),
(12, 1, 1, 'X'),
(12, 1, 2, 'Y'),
(12, 1, 3, 'Z'),
(12, 2, 1, 'Z'),
(12, 2, 2, '''S3'''),
(13, 1, 1, 'X'),
(13, 1, 2, 'Y'),
(13, 1, 3, 'Z'),
(13, 1, 4, 'N'),
(13, 2, 1, 'Y'),
(13, 2, 2, '1'),
(13, 3, 1, 'Z'),
(13, 3, 2, '1'),
(14, 1, 1, 'X'),
(14, 1, 2, 'Y'),
(14, 1, 3, 'Z'),
(14, 1, 4, 'N'),
(14, 2, 1, 'Y'),
(14, 2, 2, '2'),
(14, 3, 1, 'Y'),
(14, 3, 2, '1'),
(15, 1, 1, 'X'),
(15, 1, 2, 'Y'),
(15, 1, 3, 'Z'),
(15, 2, 1, 'X'),
(15, 2, 2, 'Y'),
(15, 2, 3, 'P'),
(15, 3, 1, 'X'),
(15, 3, 2, 'Y'),
(15, 3, 3, 'Q'),
(15, 4, 1, 'P'),
(15, 4, 2, 'Q'),
(16, 1, 1, 'X'),
(16, 2, 1, 'X'),
(16, 2, 2, 'Y'),
(16, 2, 3, 'Z'),
(16, 3, 1, 'Z'),
(16, 3, 2, '22'),
(16, 4, 1, 'Z'),
(16, 4, 2, '24'),
(17, 1, 1, 'X'),
(17, 2, 1, 'X'),
(17, 2, 2, 'Y'),
(17, 2, 3, 'Z'),
(17, 3, 1, 'Z'),
(17, 3, 2, '20'),
(17, 4, 1, 'Z'),
(17, 4, 2, '22'),
(18, 1, 1, 'X'),
(18, 2, 1, 'X'),
(18, 2, 2, 'Y'),
(18, 2, 3, 'Z'),
(18, 3, 1, 'Z'),
(18, 3, 2, '12'),
(18, 4, 1, 'Z'),
(18, 4, 2, '16'),
(19, 1, 1, 'X'),
(19, 1, 2, 'Y'),
(19, 1, 3, 'P'),
(19, 2, 1, 'X'),
(19, 2, 2, 'Y'),
(19, 2, 3, 'Q'),
(19, 3, 1, 'P'),
(19, 3, 2, 'Q'),
(19, 4, 1, 'Y'),
(19, 4, 2, '1'),
(20, 1, 1, 'X'),
(20, 1, 2, 'Y'),
(20, 1, 3, 'Z'),
(20, 2, 1, 'X'),
(20, 2, 2, 'Y'),
(20, 3, 1, 'Y'),
(20, 3, 2, '1'),
(21, 1, 1, 'X'),
(21, 1, 2, 'Y'),
(21, 1, 3, 'Z'),
(21, 2, 1, 'X'),
(21, 2, 2, 'Y'),
(21, 3, 1, 'Y'),
(21, 3, 2, '2');

-- --------------------------------------------------------

--
-- Table structure for table `argumen_head`
--

CREATE TABLE IF NOT EXISTS `argumen_head` (
  `id_rule` int(11) NOT NULL,
  `urutan` int(5) NOT NULL,
  `isi_argumen` varchar(100) NOT NULL,
  PRIMARY KEY (`id_rule`,`urutan`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `argumen_head`
--

INSERT INTO `argumen_head` (`id_rule`, `urutan`, `isi_argumen`) VALUES
(4, 1, 'X'),
(4, 2, 'Y'),
(5, 1, 'X'),
(5, 2, 'Y'),
(5, 3, 'Z'),
(6, 1, 'X'),
(6, 2, 'Y'),
(6, 3, 'Z'),
(7, 1, 'X'),
(8, 1, 'X'),
(8, 2, 'Y'),
(9, 1, 'X'),
(9, 2, 'Y'),
(11, 1, 'X'),
(12, 1, 'X'),
(13, 1, 'X'),
(13, 2, 'Y'),
(13, 3, 'Z'),
(13, 4, 'N'),
(14, 1, 'X'),
(14, 2, 'Y'),
(14, 3, 'Z'),
(14, 4, 'N'),
(15, 1, 'X'),
(15, 2, 'Y'),
(15, 3, 'Z'),
(16, 1, 'X'),
(16, 2, 'Y'),
(17, 1, 'X'),
(17, 2, 'Y'),
(18, 1, 'X'),
(18, 2, 'Y'),
(19, 1, 'X'),
(19, 2, 'Y'),
(20, 1, 'X'),
(20, 2, 'Y'),
(20, 3, 'Z'),
(21, 1, 'X'),
(21, 2, 'Y'),
(21, 3, 'Z');

-- --------------------------------------------------------

--
-- Table structure for table `body_idb`
--

CREATE TABLE IF NOT EXISTS `body_idb` (
  `id_aturan` int(11) NOT NULL,
  `urutan_body` int(11) NOT NULL,
  `predikat` int(11) NOT NULL,
  `is_negasi` varchar(10) NOT NULL,
  PRIMARY KEY (`id_aturan`,`urutan_body`),
  KEY `predikat_edb` (`predikat`),
  KEY `predikat` (`predikat`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `body_idb`
--

INSERT INTO `body_idb` (`id_aturan`, `urutan_body`, `predikat`, `is_negasi`) VALUES
(4, 1, 14, 'FALSE'),
(4, 2, 29, 'FALSE'),
(4, 3, 15, 'FALSE'),
(5, 1, 7, 'FALSE'),
(5, 2, 9, 'FALSE'),
(5, 3, 10, 'FALSE'),
(5, 4, 1, 'FALSE'),
(6, 1, 7, 'FALSE'),
(6, 2, 23, 'FALSE'),
(6, 3, 5, 'FALSE'),
(7, 1, 13, 'FALSE'),
(7, 2, 1, 'FALSE'),
(8, 1, 14, 'FALSE'),
(8, 2, 12, 'FALSE'),
(8, 3, 15, 'FALSE'),
(9, 1, 19, 'FALSE'),
(9, 2, 12, 'FALSE'),
(9, 3, 15, 'FALSE'),
(11, 1, 13, 'FALSE'),
(11, 2, 1, 'FALSE'),
(12, 1, 13, 'FALSE'),
(12, 2, 1, 'FALSE'),
(13, 1, 21, 'FALSE'),
(13, 2, 1, 'FALSE'),
(13, 3, 5, 'FALSE'),
(14, 1, 21, 'FALSE'),
(14, 2, 1, 'FALSE'),
(14, 3, 5, 'FALSE'),
(15, 1, 7, 'FALSE'),
(15, 2, 9, 'FALSE'),
(15, 3, 10, 'FALSE'),
(15, 4, 2, 'FALSE'),
(16, 1, 14, 'FALSE'),
(16, 2, 24, 'FALSE'),
(16, 3, 3, 'FALSE'),
(16, 4, 18, 'FALSE'),
(17, 1, 14, 'FALSE'),
(17, 2, 24, 'FALSE'),
(17, 3, 3, 'FALSE'),
(17, 4, 18, 'FALSE'),
(18, 1, 19, 'FALSE'),
(18, 2, 24, 'FALSE'),
(18, 3, 3, 'FALSE'),
(18, 4, 18, 'FALSE'),
(19, 1, 9, 'FALSE'),
(19, 2, 10, 'FALSE'),
(19, 3, 1, 'FALSE'),
(19, 4, 5, 'FALSE'),
(20, 1, 7, 'FALSE'),
(20, 2, 28, 'FALSE'),
(20, 3, 5, 'FALSE'),
(21, 1, 7, 'FALSE'),
(21, 2, 28, 'TRUE'),
(21, 3, 5, 'FALSE');

-- --------------------------------------------------------

--
-- Table structure for table `br_statement`
--

CREATE TABLE IF NOT EXISTS `br_statement` (
  `id_statement` varchar(10) NOT NULL,
  `id_policy` varchar(10) NOT NULL,
  `definition` varchar(100) NOT NULL,
  `predikat` int(11) NOT NULL,
  `target` int(5) NOT NULL,
  PRIMARY KEY (`id_statement`),
  KEY `id_predikat` (`id_policy`),
  KEY `id_predikat_2` (`id_policy`),
  KEY `predikat` (`predikat`),
  KEY `target` (`target`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `br_statement`
--

INSERT INTO `br_statement` (`id_statement`, `id_policy`, `definition`, `predikat`, `target`) VALUES
('BS2A', 'PA0404', 'Mahasiswa S1 yang boleh mengambil maksimal 24 sks', 8, 25),
('BS2B', 'PA0404', 'Mahasiswa S1 yang boleh mengambil maksimal 22 sks', 16, 26),
('BS3', 'PA0404', 'Mahasiswa S2 yang boleh mengambil maksimal 16 sks', 17, 27);

-- --------------------------------------------------------

--
-- Stand-in structure for view `check_bs2a`
--
CREATE TABLE IF NOT EXISTS `check_bs2a` (
`X` varchar(20)
,`Y` int(5)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `contoh`
--
CREATE TABLE IF NOT EXISTS `contoh` (
`nip` varchar(20)
,`nama` varchar(255)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `daftar`
--
CREATE TABLE IF NOT EXISTS `daftar` (
`nim` varchar(20)
,`semester` int(5)
,`sks` int(5)
);
-- --------------------------------------------------------

--
-- Table structure for table `edb`
--

CREATE TABLE IF NOT EXISTS `edb` (
  `id_predikat` int(11) NOT NULL,
  `reference` varchar(50) NOT NULL,
  PRIMARY KEY (`id_predikat`),
  KEY `reference` (`reference`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `edb`
--

INSERT INTO `edb` (`id_predikat`, `reference`) VALUES
(30, 'contoh'),
(24, 'daftar'),
(6, 'mahasiswa'),
(7, 'nr'),
(21, 'nr2'),
(10, 'sks_ambil'),
(9, 'sks_total'),
(13, 'strata_mhs');

-- --------------------------------------------------------

--
-- Table structure for table `idb`
--

CREATE TABLE IF NOT EXISTS `idb` (
  `id_aturan` int(11) NOT NULL AUTO_INCREMENT,
  `id_predikat` int(11) NOT NULL,
  PRIMARY KEY (`id_aturan`),
  KEY `id_predikat` (`id_predikat`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=22 ;

--
-- Dumping data for table `idb`
--

INSERT INTO `idb` (`id_aturan`, `id_predikat`) VALUES
(4, 8),
(5, 12),
(6, 12),
(7, 14),
(8, 16),
(9, 17),
(11, 19),
(12, 20),
(13, 22),
(14, 22),
(15, 23),
(16, 25),
(17, 26),
(18, 27),
(19, 28),
(20, 29),
(21, 29);

-- --------------------------------------------------------

--
-- Stand-in structure for view `last_nr2`
--
CREATE TABLE IF NOT EXISTS `last_nr2` (
`X` varchar(20)
,`Y` int(11)
,`Z` float(5,2)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `mahasiswa`
--
CREATE TABLE IF NOT EXISTS `mahasiswa` (
`nim` varchar(20)
);
-- --------------------------------------------------------

--
-- Table structure for table `max24_mhs`
--

CREATE TABLE IF NOT EXISTS `max24_mhs` (
  `nim` varchar(15) NOT NULL,
  `semester` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `max24_mhs`
--

INSERT INTO `max24_mhs` (`nim`, `semester`) VALUES
('13512002', 3),
('13512075', 3),
('13512075', 3),
('13512005', 3),
('13512071', 3);

-- --------------------------------------------------------

--
-- Stand-in structure for view `max24_sks`
--
CREATE TABLE IF NOT EXISTS `max24_sks` (
`X` varchar(20)
,`Y` int(11)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `mhs_s1`
--
CREATE TABLE IF NOT EXISTS `mhs_s1` (
`X` varchar(20)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `nr`
--
CREATE TABLE IF NOT EXISTS `nr` (
`nim` varchar(20)
,`semester` int(5)
,`NR` float(5,2)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `nr_lengkap`
--
CREATE TABLE IF NOT EXISTS `nr_lengkap` (
`X` varchar(20)
,`Y` int(5)
);
-- --------------------------------------------------------

--
-- Table structure for table `policy`
--

CREATE TABLE IF NOT EXISTS `policy` (
  `id_policy` varchar(10) NOT NULL,
  `deskripsi` varchar(100) NOT NULL,
  PRIMARY KEY (`id_policy`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `policy`
--

INSERT INTO `policy` (`id_policy`, `deskripsi`) VALUES
('PA0404', 'Beban lebih untuk percepatan studi');

-- --------------------------------------------------------

--
-- Table structure for table `predikat`
--

CREATE TABLE IF NOT EXISTS `predikat` (
  `id_predikat` int(11) NOT NULL AUTO_INCREMENT,
  `nama_predikat` varchar(50) NOT NULL,
  `jumlah_argumen` int(11) NOT NULL,
  `kelompok_predikat` varchar(10) NOT NULL,
  `deskripsi` varchar(100) NOT NULL,
  PRIMARY KEY (`id_predikat`),
  KEY `nama_predikat` (`nama_predikat`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=31 ;

--
-- Dumping data for table `predikat`
--

INSERT INTO `predikat` (`id_predikat`, `nama_predikat`, `jumlah_argumen`, `kelompok_predikat`, `deskripsi`) VALUES
(1, '=', 2, 'Operator', 'Operator sama dengan'),
(2, '<>', 2, 'Operator', 'Operator tidak sama dengan'),
(3, '>', 2, 'Operator', 'Operator lebih dari'),
(4, '<', 2, 'Operator', 'Operator kurang dari'),
(5, 'previous', 2, 'Operator', 'Operator decrement'),
(6, 'mahasiswa', 1, 'EDB', 'Entitas mahasiswa'),
(7, 'nr', 3, 'EDB', 'Nilai rata-rata mahasiswa'),
(8, 'max24_sks', 2, 'IDB', 'Aturan maksimal 24 sks'),
(9, 'sks_total', 3, 'EDB', 'Jumlah sks tanpa nilai T'),
(10, 'sks_ambil', 3, 'EDB', 'Jumlah sks yang diambil dalam 1 semester'),
(12, 'last_nr', 3, 'IDB', 'NR terakhir mahasiswa '),
(13, 'strata_mhs', 3, 'EDB', 'Keterangan strata mahasiswa'),
(14, 'mhs_s1', 1, 'IDB', 'Mahasiswa dengan strata S1'),
(15, '>=', 2, 'Operator', 'Lebih dari sama dengan'),
(16, 'max22_sks', 2, 'IDB', 'Aturan maksimal 22 sks'),
(17, 'max16_sks', 2, 'IDB', 'Aturan maksimal 16 sks'),
(18, '<=', 2, 'Operator', 'Kurang dari sama dengan'),
(19, 'mhs_s2', 1, 'IDB', 'Mahasiswa dengan strata S2'),
(20, 'mhs_s3', 1, 'IDB', 'Mahasiswa dengan strata S3'),
(21, 'nr2', 4, 'EDB', 'NR mahasiswa di semester dan tahun tertentu'),
(22, 'prev_nr', 4, 'IDB', 'NR mahasiswa di semester sebelumnya'),
(23, 'prev_semester', 3, 'IDB', 'Mahasiswa yang dilihat NR semester sebelumnya'),
(24, 'daftar', 3, 'EDB', 'Mahasiswa yang melakukan daftar ulang'),
(25, 'check_BS2A', 2, 'IDB', 'Snapshot data untuk aturan BS2A'),
(26, 'check_BS2B', 2, 'IDB', 'Snapshot data untuk aturan BS2B'),
(27, 'check_BS3', 2, 'IDB', 'Snapshot data untuk aturan BS3 '),
(28, 'nr_lengkap', 2, 'IDB', 'Jumlah SKS yang dihitung sesuai dengan jumlah sKS yang diambil'),
(29, 'last_nr2', 3, 'IDB', 'NR terakhir mahasiswa versi 2'),
(30, 'contoh', 3, 'EDB', 'Contoh EDB');

-- --------------------------------------------------------

--
-- Table structure for table `reference`
--

CREATE TABLE IF NOT EXISTS `reference` (
  `id_ref` varchar(50) NOT NULL,
  `predikat` int(11) NOT NULL,
  `table_name` varchar(50) NOT NULL,
  `db_name` varchar(30) NOT NULL,
  PRIMARY KEY (`id_ref`),
  KEY `predikat` (`predikat`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `reference`
--

INSERT INTO `reference` (`id_ref`, `predikat`, `table_name`, `db_name`) VALUES
('contoh', 30, 'dosen', 'praktikum'),
('daftar', 24, 'daftar_ulang', 'praktikum'),
('mahasiswa', 6, 'mahasiswa', 'praktikum'),
('nr', 7, 'rapor', 'praktikum'),
('nr2', 21, 'rapor', 'praktikum'),
('sks_ambil', 10, 'rapor', 'praktikum'),
('sks_total', 9, 'rapor', 'praktikum'),
('strata_mhs', 13, 'mahasiswa', 'praktikum');

-- --------------------------------------------------------

--
-- Table structure for table `ref_attribute`
--

CREATE TABLE IF NOT EXISTS `ref_attribute` (
  `id_ref` varchar(10) NOT NULL,
  `order` int(5) NOT NULL,
  `attr_name` varchar(50) NOT NULL,
  PRIMARY KEY (`id_ref`,`order`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `ref_attribute`
--

INSERT INTO `ref_attribute` (`id_ref`, `order`, `attr_name`) VALUES
('contoh', 1, 'nip'),
('contoh', 2, 'nama'),
('daftar', 1, 'nim'),
('daftar', 2, 'semester'),
('daftar', 3, 'sks'),
('mahasiswa', 1, 'nim'),
('nr', 1, 'nim'),
('nr', 2, 'semester'),
('nr', 3, 'NR'),
('nr2', 1, 'nim'),
('nr2', 2, 'semester'),
('nr2', 3, 'tahun'),
('nr2', 4, 'NR'),
('sks_ambil', 1, 'nim'),
('sks_ambil', 2, 'semester'),
('sks_ambil', 3, 'sks_ambil'),
('sks_total', 1, 'nim'),
('sks_total', 2, 'semester'),
('sks_total', 3, 'sks_total'),
('strata_mhs', 1, 'nim'),
('strata_mhs', 2, 'nama'),
('strata_mhs', 3, 'strata');

-- --------------------------------------------------------

--
-- Table structure for table `report`
--

CREATE TABLE IF NOT EXISTS `report` (
  `id_ref` varchar(10) NOT NULL,
  `id_rule` int(5) NOT NULL,
  `inst_name` varchar(50) NOT NULL,
  `status` varchar(20) NOT NULL,
  `check_time` datetime NOT NULL,
  PRIMARY KEY (`id_ref`,`id_rule`,`inst_name`),
  KEY `id_rule` (`id_rule`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `rule_attribute`
--

CREATE TABLE IF NOT EXISTS `rule_attribute` (
  `id_ref` varchar(50) NOT NULL,
  `urutan` int(5) NOT NULL,
  `attr_name` varchar(50) NOT NULL,
  PRIMARY KEY (`id_ref`,`urutan`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `rule_attribute`
--

INSERT INTO `rule_attribute` (`id_ref`, `urutan`, `attr_name`) VALUES
('max24_mhs', 1, 'nim'),
('max24_mhs', 2, 'semester');

-- --------------------------------------------------------

--
-- Table structure for table `rule_ref`
--

CREATE TABLE IF NOT EXISTS `rule_ref` (
  `reference` varchar(50) NOT NULL,
  `db_name` varchar(30) NOT NULL,
  `table_name` varchar(30) NOT NULL,
  `id_rule` int(11) NOT NULL,
  PRIMARY KEY (`reference`),
  KEY `id_rule` (`id_rule`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `rule_ref`
--

INSERT INTO `rule_ref` (`reference`, `db_name`, `table_name`, `id_rule`) VALUES
('max24_mhs', 'praktikum', 'daftar_ulang', 4);

-- --------------------------------------------------------

--
-- Stand-in structure for view `sks_ambil`
--
CREATE TABLE IF NOT EXISTS `sks_ambil` (
`nim` varchar(20)
,`semester` int(5)
,`sks_ambil` int(5)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `sks_total`
--
CREATE TABLE IF NOT EXISTS `sks_total` (
`nim` varchar(20)
,`semester` int(5)
,`sks_total` int(5)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `strata_mhs`
--
CREATE TABLE IF NOT EXISTS `strata_mhs` (
`nim` varchar(20)
,`nama` varchar(255)
,`strata` varchar(10)
);
-- --------------------------------------------------------

--
-- Structure for view `check_bs2a`
--
DROP TABLE IF EXISTS `check_bs2a`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `check_bs2a` AS select `mhs_s1`.`X` AS `X`,`daftar`.`semester` AS `Y` from (`mhs_s1` join `daftar`) where ((`mhs_s1`.`X` = `daftar`.`nim`) and (`daftar`.`sks` > 22) and (`daftar`.`sks` <= 24));

-- --------------------------------------------------------

--
-- Structure for view `contoh`
--
DROP TABLE IF EXISTS `contoh`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `contoh` AS select `praktikum`.`dosen`.`nip` AS `nip`,`praktikum`.`dosen`.`nama` AS `nama` from `praktikum`.`dosen`;

-- --------------------------------------------------------

--
-- Structure for view `daftar`
--
DROP TABLE IF EXISTS `daftar`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `daftar` AS select `praktikum`.`daftar_ulang`.`nim` AS `nim`,`praktikum`.`daftar_ulang`.`semester` AS `semester`,`praktikum`.`daftar_ulang`.`sks` AS `sks` from `praktikum`.`daftar_ulang`;

-- --------------------------------------------------------

--
-- Structure for view `last_nr2`
--
DROP TABLE IF EXISTS `last_nr2`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `last_nr2` AS select `nr`.`nim` AS `X`,`nr`.`semester` AS `Y`,`nr`.`NR` AS `Z` from (`nr` join `nr_lengkap`) where ((`nr`.`nim` = `nr_lengkap`.`X`) and (`nr`.`semester` = `nr_lengkap`.`Y`) and (`nr`.`nim` = 13512015) and (`nr`.`semester` = (4 - 1))) union select `nr`.`nim` AS `X`,`nr`.`semester` AS `Y`,`nr`.`NR` AS `Z` from `nr` where ((not(exists(select 1 from `nr_lengkap` where ((`nr`.`nim` = `nr_lengkap`.`X`) and (`nr`.`semester` = `nr_lengkap`.`Y`))))) and (`nr`.`nim` = 13512015) and (`nr`.`semester` = (4 - 2)));

-- --------------------------------------------------------

--
-- Structure for view `mahasiswa`
--
DROP TABLE IF EXISTS `mahasiswa`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `mahasiswa` AS select `praktikum`.`mahasiswa`.`nim` AS `nim` from `praktikum`.`mahasiswa`;

-- --------------------------------------------------------

--
-- Structure for view `max24_sks`
--
DROP TABLE IF EXISTS `max24_sks`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `max24_sks` AS select `mhs_s1`.`X` AS `X`,`last_nr2`.`Y` AS `Y` from (`mhs_s1` join `last_nr2`) where ((`mhs_s1`.`X` = `last_nr2`.`X`) and (`last_nr2`.`Z` >= 3.25));

-- --------------------------------------------------------

--
-- Structure for view `mhs_s1`
--
DROP TABLE IF EXISTS `mhs_s1`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `mhs_s1` AS select `strata_mhs`.`nim` AS `X` from `strata_mhs` where ((`strata_mhs`.`strata` = 'S1') and (`strata_mhs`.`nim` = 13512015));

-- --------------------------------------------------------

--
-- Structure for view `nr`
--
DROP TABLE IF EXISTS `nr`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `nr` AS select `praktikum`.`rapor`.`nim` AS `nim`,`praktikum`.`rapor`.`semester` AS `semester`,`praktikum`.`rapor`.`NR` AS `NR` from `praktikum`.`rapor`;

-- --------------------------------------------------------

--
-- Structure for view `nr_lengkap`
--
DROP TABLE IF EXISTS `nr_lengkap`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `nr_lengkap` AS select `sks_total`.`nim` AS `X`,`sks_total`.`semester` AS `Y` from (`sks_total` join `sks_ambil`) where ((`sks_total`.`nim` = `sks_ambil`.`nim`) and (`sks_total`.`semester` = `sks_ambil`.`semester`) and (`sks_total`.`sks_total` = `sks_ambil`.`sks_ambil`) and (`sks_total`.`nim` = 13512015) and (`sks_total`.`semester` = (4 - 1)));

-- --------------------------------------------------------

--
-- Structure for view `sks_ambil`
--
DROP TABLE IF EXISTS `sks_ambil`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `sks_ambil` AS select `praktikum`.`rapor`.`nim` AS `nim`,`praktikum`.`rapor`.`semester` AS `semester`,`praktikum`.`rapor`.`sks_ambil` AS `sks_ambil` from `praktikum`.`rapor`;

-- --------------------------------------------------------

--
-- Structure for view `sks_total`
--
DROP TABLE IF EXISTS `sks_total`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `sks_total` AS select `praktikum`.`rapor`.`nim` AS `nim`,`praktikum`.`rapor`.`semester` AS `semester`,`praktikum`.`rapor`.`sks_total` AS `sks_total` from `praktikum`.`rapor`;

-- --------------------------------------------------------

--
-- Structure for view `strata_mhs`
--
DROP TABLE IF EXISTS `strata_mhs`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `strata_mhs` AS select `praktikum`.`mahasiswa`.`nim` AS `nim`,`praktikum`.`mahasiswa`.`nama` AS `nama`,`praktikum`.`mahasiswa`.`strata` AS `strata` from `praktikum`.`mahasiswa`;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `argumen_body`
--
ALTER TABLE `argumen_body`
  ADD CONSTRAINT `argumen_body_ibfk_1` FOREIGN KEY (`id_aturan`) REFERENCES `body_idb` (`id_aturan`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `argumen_head`
--
ALTER TABLE `argumen_head`
  ADD CONSTRAINT `argumen_head_ibfk_1` FOREIGN KEY (`id_rule`) REFERENCES `idb` (`id_aturan`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `body_idb`
--
ALTER TABLE `body_idb`
  ADD CONSTRAINT `body_idb_ibfk_1` FOREIGN KEY (`id_aturan`) REFERENCES `idb` (`id_aturan`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `body_idb_ibfk_2` FOREIGN KEY (`predikat`) REFERENCES `predikat` (`id_predikat`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `br_statement`
--
ALTER TABLE `br_statement`
  ADD CONSTRAINT `br_statement_ibfk_1` FOREIGN KEY (`id_policy`) REFERENCES `policy` (`id_policy`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `br_statement_ibfk_2` FOREIGN KEY (`predikat`) REFERENCES `predikat` (`id_predikat`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `br_statement_ibfk_3` FOREIGN KEY (`target`) REFERENCES `predikat` (`id_predikat`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `edb`
--
ALTER TABLE `edb`
  ADD CONSTRAINT `edb_ibfk_1` FOREIGN KEY (`id_predikat`) REFERENCES `predikat` (`id_predikat`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `idb`
--
ALTER TABLE `idb`
  ADD CONSTRAINT `idb_ibfk_1` FOREIGN KEY (`id_predikat`) REFERENCES `predikat` (`id_predikat`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `reference`
--
ALTER TABLE `reference`
  ADD CONSTRAINT `reference_ibfk_1` FOREIGN KEY (`predikat`) REFERENCES `edb` (`id_predikat`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ref_attribute`
--
ALTER TABLE `ref_attribute`
  ADD CONSTRAINT `ref_attribute_ibfk_1` FOREIGN KEY (`id_ref`) REFERENCES `reference` (`id_ref`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `report`
--
ALTER TABLE `report`
  ADD CONSTRAINT `report_ibfk_1` FOREIGN KEY (`id_ref`) REFERENCES `reference` (`id_ref`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `report_ibfk_2` FOREIGN KEY (`id_rule`) REFERENCES `idb` (`id_aturan`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `rule_attribute`
--
ALTER TABLE `rule_attribute`
  ADD CONSTRAINT `rule_attribute_ibfk_1` FOREIGN KEY (`id_ref`) REFERENCES `rule_ref` (`reference`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `rule_ref`
--
ALTER TABLE `rule_ref`
  ADD CONSTRAINT `rule_ref_ibfk_1` FOREIGN KEY (`id_rule`) REFERENCES `idb` (`id_aturan`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
