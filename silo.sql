-- phpMyAdmin SQL Dump
-- version 3.4.11.1deb2+deb7u1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 11, 2015 at 07:49 PM
-- Server version: 5.5.40
-- PHP Version: 5.4.36-0+deb7u1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `silo`
--

-- --------------------------------------------------------

--
-- Table structure for table `bentuksediaan`
--

CREATE TABLE IF NOT EXISTS `bentuksediaan` (
  `idbentuk_sediaan` smallint(6) NOT NULL AUTO_INCREMENT,
  `nama_bentuk` varchar(35) NOT NULL,
  `deskripsi` varchar(255) NOT NULL,
  `enabled` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`idbentuk_sediaan`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=14 ;

--
-- Dumping data for table `bentuksediaan`
--

INSERT INTO `bentuksediaan` (`idbentuk_sediaan`, `nama_bentuk`, `deskripsi`, `enabled`) VALUES
(1, 'PULVIS/PULVERES/SERBUK', 'Pulvis (serbuk) adalah campuran kering bahan obat atau zat kimia yang dihaluskan ditujukan untuk obat dalam atau obat luar. pulveres adalah serbuk yang dibagi-bagi dalam bobot yang diperkirakan sama, masing-masing dibungkus dengan pengemas yang cocok untu', 1),
(2, 'TABLET', 'tablet ialah sediaan padat mengandung bahan obat dengan atau tanpa bahan pengisi. zat tambahan pada tablet berfungsi sebagai pengisi, pengembang, pengikat, pelicin, dan pembasah atau fungsi lain yang cocok.', 1),
(3, 'KAPSUL', 'kapsul ialah sediaan padat yang terdiri dari obat dalam cangkang keras atau lunak yang dapat larut. cangkang kapsul terbuat dari gelatin, pati atau bahan lain yang cocok.\r\n', 1),
(4, 'SUPPOSITORIA', 'supositoria adalah sediaan padat dalam berbagai bobot dan bentuk yang diberikan melalui rektal, vagina atau urethal.', 1),
(5, 'KAPLET', 'kaplet adalah tablet berbentuk seperti kapsul yang pembuatannya melalui kempa.', 1),
(6, 'TABLET PELLET', 'sediaan tablet kecil, silindris, dan steril yang pemakaiannya ditanam (inflantasi) kedalam jaringan.', 1),
(7, 'TABLET LOZENGE', 'sediaan tablet yang rasanya manis dan baunya enak yang penggunaannya dihisap melalui mulut.', 1),
(8, 'GAS', 'biasanya berupa oksigen, obat anestesi atau zat yang digunakan untuk sterilisasi. ', 1),
(9, 'AEROSOL', ' sediaan yang mengandung 1 atau lebih zat berkhasiat dalam wadah yang diberi tekanan, digunakan untuk obat luar atau obat dalam. pemakaiannya disedot melalui hidung atau mulut atau disemprotkan dalam bentuk kabut ke saluran pernapasan.', 1),
(10, 'INFUSA', 'sediaan cair yang dibuat dengan menyari simplisia nabati dengan aair panas selama 15 menit.', 1),
(11, 'EMULSI', 'sediaan yang mengandung bahan obat cair, atau larutan obat, terdispersi dalam cairan pembawa dan distabilkan dengan emulgator yang sesuai.', 1),
(13, 'ELIXIR', 'Sediaan farmasi yang berbentuk cair yang mengandung air dan alkohol (hidroalkohol)', 1);

-- --------------------------------------------------------

--
-- Table structure for table `detail_lpo`
--

CREATE TABLE IF NOT EXISTS `detail_lpo` (
  `iddetail_lpo` int(11) NOT NULL AUTO_INCREMENT,
  `idlpo` int(11) NOT NULL,
  `idobat` int(11) NOT NULL,
  `idobat_puskesmas` int(11) NOT NULL,
  `kode_obat` varchar(10) COLLATE latin1_general_ci NOT NULL,
  `nama_obat` varchar(150) COLLATE latin1_general_ci NOT NULL,
  `harga` mediumint(9) NOT NULL,
  `idsatuan_obat` tinyint(4) NOT NULL,
  `idgolongan` tinyint(4) NOT NULL,
  `kemasan` varchar(100) COLLATE latin1_general_ci NOT NULL,
  `stock_awal` mediumint(9) NOT NULL,
  `penerimaan` mediumint(9) NOT NULL,
  `persediaan` mediumint(9) NOT NULL,
  `pemakaian` mediumint(9) NOT NULL,
  `stock_akhir` mediumint(9) NOT NULL,
  `permintaan` mediumint(9) NOT NULL,
  `date_added` datetime NOT NULL,
  `date_modified` datetime NOT NULL,
  PRIMARY KEY (`iddetail_lpo`),
  KEY `idlpo` (`idlpo`),
  KEY `idobat_puskesmas` (`idobat_puskesmas`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=2626 ;

-- --------------------------------------------------------

--
-- Table structure for table `detail_penghapusan_stock`
--

CREATE TABLE IF NOT EXISTS `detail_penghapusan_stock` (
  `iddetail_penghapusan_stock` int(11) NOT NULL AUTO_INCREMENT,
  `idpenghapusan_stock` int(11) NOT NULL,
  `iddetail_sbbm` int(11) NOT NULL,
  `idobat` int(11) NOT NULL,
  `kode_obat` varchar(10) COLLATE latin1_general_ci NOT NULL,
  `nama_obat` varchar(150) COLLATE latin1_general_ci NOT NULL,
  `harga` mediumint(9) NOT NULL,
  `idsatuan_obat` tinyint(4) NOT NULL,
  `idgolongan` tinyint(4) NOT NULL,
  `kemasan` varchar(100) COLLATE latin1_general_ci NOT NULL,
  `tanggal_expire` date NOT NULL,
  `qty` mediumint(9) NOT NULL,
  `pemakaian` mediumint(9) NOT NULL,
  `sisa` mediumint(9) NOT NULL,
  `date_added` datetime NOT NULL,
  `date_modified` datetime NOT NULL,
  PRIMARY KEY (`iddetail_penghapusan_stock`),
  KEY `idsbbk` (`idpenghapusan_stock`),
  KEY `idobat` (`idobat`),
  KEY `idpenghapusan_stock` (`idpenghapusan_stock`),
  KEY `iddetail_sbbm` (`iddetail_sbbm`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `detail_sbbk`
--

CREATE TABLE IF NOT EXISTS `detail_sbbk` (
  `iddetail_sbbk` int(11) NOT NULL AUTO_INCREMENT,
  `idsbbm` int(11) NOT NULL,
  `iddetail_sbbm` int(11) NOT NULL,
  `idsbbk` int(11) NOT NULL,
  `idobat` int(11) NOT NULL,
  `idobat_puskesmas` int(11) NOT NULL,
  `kode_obat` varchar(10) COLLATE latin1_general_ci NOT NULL,
  `nama_obat` varchar(150) COLLATE latin1_general_ci NOT NULL,
  `harga` mediumint(9) NOT NULL,
  `idsatuan_obat` tinyint(4) NOT NULL,
  `idgolongan` tinyint(4) NOT NULL,
  `kemasan` varchar(100) COLLATE latin1_general_ci NOT NULL,
  `no_batch` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `stock_awal` mediumint(9) NOT NULL,
  `penerimaan` mediumint(9) NOT NULL,
  `persediaan` mediumint(9) NOT NULL,
  `pemakaian` mediumint(9) NOT NULL,
  `stock_akhir` mediumint(9) NOT NULL,
  `permintaan` mediumint(9) NOT NULL,
  `pemberian` mediumint(9) NOT NULL,
  `islpo` tinyint(1) NOT NULL DEFAULT '1',
  `ischecked` tinyint(1) NOT NULL DEFAULT '0',
  `date_added` datetime NOT NULL,
  `date_modified` datetime NOT NULL,
  PRIMARY KEY (`iddetail_sbbk`),
  KEY `idsbbk` (`idsbbk`),
  KEY `idobat` (`idobat`),
  KEY `idobat_puskesmas` (`idobat_puskesmas`),
  KEY `iddetail_sbbm` (`iddetail_sbbm`),
  KEY `idsbbm` (`idsbbm`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=17 ;

-- --------------------------------------------------------

--
-- Table structure for table `detail_sbbk_puskesmas`
--

CREATE TABLE IF NOT EXISTS `detail_sbbk_puskesmas` (
  `iddetail_sbbk_puskesmas` int(11) NOT NULL AUTO_INCREMENT,
  `idsbbk_puskesmas` int(11) NOT NULL,
  `iddetail_sbbm_puskesmas` int(11) NOT NULL,
  `idobat` int(11) NOT NULL,
  `idobat_puskesmas` int(11) NOT NULL,
  `kode_obat` varchar(10) COLLATE latin1_general_ci NOT NULL,
  `nama_obat` varchar(150) COLLATE latin1_general_ci NOT NULL,
  `harga` mediumint(9) NOT NULL,
  `idsatuan_obat` tinyint(4) NOT NULL,
  `idgolongan` tinyint(4) NOT NULL,
  `kemasan` varchar(100) COLLATE latin1_general_ci NOT NULL,
  `no_batch` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `stock_awal_unit` mediumint(9) NOT NULL,
  `penerimaan_unit` mediumint(9) NOT NULL,
  `persediaan_unit` mediumint(9) NOT NULL,
  `pemakaian_unit` mediumint(9) NOT NULL,
  `stock_akhir_unit` mediumint(9) NOT NULL,
  `permintaan_unit` mediumint(9) NOT NULL,
  `pemberian_puskesmas` mediumint(9) NOT NULL,
  `islpo_unit` tinyint(1) NOT NULL DEFAULT '1',
  `ischecked_unit` tinyint(1) NOT NULL DEFAULT '0',
  `isuse_unit` tinyint(1) NOT NULL DEFAULT '0',
  `date_added` datetime NOT NULL,
  `date_modified` datetime NOT NULL,
  PRIMARY KEY (`iddetail_sbbk_puskesmas`),
  KEY `idsbbk` (`idsbbk_puskesmas`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `detail_sbbm`
--

CREATE TABLE IF NOT EXISTS `detail_sbbm` (
  `iddetail_sbbm` int(11) NOT NULL AUTO_INCREMENT,
  `idprogram` tinyint(4) NOT NULL,
  `idsbbm` int(11) NOT NULL,
  `idobat` int(11) NOT NULL,
  `kode_obat` varchar(10) NOT NULL,
  `no_reg` varchar(75) NOT NULL,
  `nama_obat` varchar(150) NOT NULL,
  `nama_merek` varchar(150) NOT NULL,
  `harga` mediumint(9) NOT NULL,
  `idsatuan_obat` tinyint(4) NOT NULL,
  `idgolongan` tinyint(4) NOT NULL,
  `idbentuk_sediaan` smallint(6) NOT NULL,
  `nama_bentuk` varchar(75) NOT NULL,
  `farmakologi` varchar(200) NOT NULL,
  `komposisi` varchar(100) NOT NULL,
  `kemasan` varchar(100) NOT NULL,
  `idprodusen` mediumint(9) NOT NULL,
  `nama_produsen` varchar(200) NOT NULL,
  `no_batch` varchar(50) NOT NULL,
  `qty` int(11) NOT NULL,
  `tanggal_expire` date NOT NULL,
  `status_obat` enum('unverified','verified') NOT NULL DEFAULT 'unverified',
  `barcode` varchar(9) NOT NULL,
  `isdestroyed` tinyint(1) NOT NULL DEFAULT '0',
  `date_added` datetime NOT NULL,
  `date_modified` datetime NOT NULL,
  PRIMARY KEY (`iddetail_sbbm`),
  KEY `idsbbm` (`idsbbm`),
  KEY `idprodusen` (`idprodusen`),
  KEY `idobat` (`idobat`),
  KEY `barcode` (`barcode`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=32 ;

-- --------------------------------------------------------

--
-- Table structure for table `detail_sbbm_puskesmas`
--

CREATE TABLE IF NOT EXISTS `detail_sbbm_puskesmas` (
  `iddetail_sbbm_puskesmas` int(11) NOT NULL AUTO_INCREMENT,
  `idsbbm_puskesmas` int(11) NOT NULL,
  `iddetail_sbbk_gudang` int(11) NOT NULL,
  `idprogram_gudang` tinyint(4) NOT NULL,
  `idsumber_dana_gudang` tinyint(4) NOT NULL,
  `idobat` int(11) NOT NULL,
  `idobat_puskesmas` int(11) NOT NULL,
  `kode_obat` varchar(10) NOT NULL,
  `nama_obat` varchar(150) NOT NULL,
  `harga` mediumint(9) NOT NULL,
  `idsatuan_obat` tinyint(4) NOT NULL,
  `idgolongan` tinyint(4) NOT NULL,
  `kemasan` varchar(100) NOT NULL,
  `no_batch` varchar(50) NOT NULL,
  `qty` int(11) NOT NULL,
  `tanggal_expire` date NOT NULL,
  `barcode` varchar(9) NOT NULL,
  `date_added` datetime NOT NULL,
  `date_modified` datetime NOT NULL,
  PRIMARY KEY (`iddetail_sbbm_puskesmas`),
  KEY `idsbbm` (`idsbbm_puskesmas`),
  KEY `idobat` (`idobat`),
  KEY `barcode` (`barcode`),
  KEY `idobat_puskesmas` (`idobat_puskesmas`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `farmakologi`
--

CREATE TABLE IF NOT EXISTS `farmakologi` (
  `idfarmakologi` smallint(6) NOT NULL AUTO_INCREMENT,
  `nama_farmakologi` varchar(35) NOT NULL,
  `deskripsi` varchar(255) NOT NULL,
  `enabled` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`idfarmakologi`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=41 ;

--
-- Dumping data for table `farmakologi`
--

INSERT INTO `farmakologi` (`idfarmakologi`, `nama_farmakologi`, `deskripsi`, `enabled`) VALUES
(1, 'ANTIBIOTIKA', 'zat-zat yang dihasilkan oleh mikroba terutama fungi dan bakteri tanah, yang dapat menghambat pertumbuhan atau membasmi mikroba jenis lain, sedang toksisitasnya terhadap manusia relative kecil.', 1),
(2, 'ANTIMALARIA', 'obat-obat yang digunakan untuk mencegah dan mengobati penyakit yang disebabkan oleh parasite bersel tunggal (protozoa) yang ditularkan melalui gigitan nyamuk anopheles betina yang menggigit pada malam hari dengan posisi menjungkit', 1),
(3, 'ANTIAMUBA', 'obat-obat yang digunakan untuk mengobati penyakit yang disebabkan oleh mikro organisme bersel tunggal (protozoa) yaitu Entamoeba histolytica yang dikenal dengan dysentri amuba', 1),
(4, 'ANTHELMETIKA', 'obat-obat anti cacing adalah obat-obat yang dapat memusnahkan cacing parasite yang ada dalam tubuh manusia dan hewan', 1),
(5, 'ANTIFUNGI', 'obat-obat yang digunakan untuk menghilangkan infeksi yang disebabkan oleh jamur', 1),
(6, 'ANTIVIRUS', '-', 1),
(7, 'ANTINEOPLASTIKA (ANTIKANKER)', '-', 1),
(8, 'ANTI TBC', 'obat-obat atau kombinasi yang diberikan dalam jangka waktu tertentu untuk mengobati penderita tuberculosis', 1),
(9, 'ANTILEPRA', '-', 1),
(10, 'ANTASIDA DAN ANTIULCER', 'obat-obat yang digunakan untuk menetralisir atau mengikat asam lambung atau mengurangi produksi asam lambung yang dapat menyebabkan timbulnya tukak lambung atau sakit maag.', 1),
(11, 'DIGESTIVA', 'obat-obat yang digunakan untuk membantu proses pencernaan lambung-usus terutama pada keadaan defisiensi zat pembantu pencernaan.', 1),
(12, 'ANTIDIARE', 'obat-obat yang digunakan untuk menanggulangi atau mengobati penyakit yang disebabkan oleh bakteri / kuman, virus, cacing atau keracunan makanan. Gejala diare adalah buang air besar berulang kali dengan banyak cairan, kadang disertai darah / lendir.', 1),
(13, 'PENCAHAR (LAXATIVA)', 'obat-obat atau zat-zat yang dapat mempercepat peristaltic usus sehingga mempermudah / melancarkan buang air besar. Penggunaan pencahar pada anak-anak harus dihindari kecuali diresepkan oleh dokter.', 1),
(14, 'ANTISPASMODIK', 'obat-obat atau zat-zat yang digunakan untuk melawan kejang-kejang otot yang sering mengakibatkan nyeri perut (saluran pencernaan). Meskipun dapat mengurangi spasme usus tapi penggunaannya dalam sindrom usus pencernaan sebagai obat tambahan saja', 1),
(15, 'KOLAGOGA', 'obat yang digunakan sebagai peluruh atau penghancur batu empedu. Batu empedu merupakan penyakit yang terjadi di saluran atau kandung empedu. Faktor pencetusnya meliputi hiperkolesterolemia, penyumbatan disaluran empedu dan radang saluran empedu.', 1),
(16, 'HEPATO PROTECTOR (PROTEKTOR HATI)', 'obat-obat yang digunakan sebagai vitamin tambahan untuk melindungi, meringankan atau menghilangkan gangguan fungsi hati karena adanya bahan kimia, penyakit kuning atau gangguan dalam penyaringan lemak oleh hati.', 1),
(17, 'ANALGETIKA', 'obat-obat yang dapat mengurangi atau menghilangkan rasa nyeri tanpa menghilangkan kesadaran', 1),
(18, 'ANTIEMETIKA ', 'obat-obat yang digunakan untuk mengurangi atau menghilangkan perasaan mual dan muntah. Karena muntah hanya suatu gejala, maka yang penting dalam pengobatan adalah mencari penyebabnya.', 1),
(19, 'ANTIEPILEPSI', '-', 1),
(20, 'PSIKOFARMAKA', 'obat-obat yang berkhasiat terhadap susunan saraf sentral dengan mempengaruhi fungsi psikis dan proses-proses mental', 1),
(21, 'HIPNOTIKA', 'obat yang diberikan malam hari dalam dosis terapi, dapat mempertinggi keinginan faal / tubuh normal untuk tidur, mempermudah dan atau menyebabkan tidur. ', 1),
(22, 'ANESTETIKA', 'obat yang digunakan untuk menghilangkan rasa sakit dalam bermacam-macam tindakan operasi.', 1),
(23, 'ANTIPARKINSON', 'obat yang digunakan untuk mengobati penyakit Parkinson yang ditandai dengan gejala termor, kaku otot, gangguan gaya berjalan, gangguan kognitif, persepsi dan daya ingat.', 1),
(24, 'NOOTROPIK / NEUROTROPIK', 'obat yang digunakan pada gangguan (insufisiensi) cerebral seperti mudah lupa, kurang konsentrasi dan vertigo', 1),
(25, 'KARDIAKA (OBAT JANTUNG)', 'obat yang bekerja pada jantung dan pembuluh darah baik arteri maupun vena secara langsung dapat memulihkan fungsi otot jantung yang terganggu menjadi normal kembali.', 1),
(26, 'DIURETIKA', 'zat-zat yang dapat memperbanyak pengeluaran air seni (diuresis) akibat khasiat langsung terhadap ginjal', 1),
(27, 'ANTI HIPERTENSI', 'obat yang digunakan untuk menurunkan peninggian tekanan sistolik dan diastolic diatas 140/90 mmHg', 1),
(28, 'HEMATINIKA', 'obat khusus yang digunakan untuk menstimulir atau memperbaiki proses pembentukan eritrosit / sel darah merah atau dikenal dengan obat pembentuk darah.', 1),
(29, 'HEMOSTATIKA', 'obat yang digunakan untuk menghentikan pendarahan.', 1),
(30, 'OKSITOSIKA', 'obat yang dapat merangsang kontraksi uterus / Rahim pada saat hamil', 1),
(31, 'ANTI TROMBOLITIKA', '-', 1),
(32, 'ENZYM', '-', 1),
(33, 'VITAMIN DAN MINERAL', '-', 1),
(34, 'HORMON', '-', 1),
(35, 'ANTITUSIVA', 'obat yang menghambat batuk (menekan reflex batuk) secara sentral, biasa digunakan pada batuk kering dan terus menerus.', 1),
(36, 'EKSPEKTORANSIA', 'obat yang melancarkan pengeluaran dahak (lendir) dari saluran napas, dahak jadi lebih encer dan mudah dikeluarkan. Mukolitika adalah obat yang mencairkan / melarutkan lendir /dahak.', 1),
(37, 'SEDATIVA', 'obat yang menimbulkan depresi ringan susunan saraf sentral tanpa menyebabkan tidur (obat pereda).', 1),
(38, 'DEKONGESTAN', 'obat yang digunakan untuk meringankan hidung tersumbat karena pilek, hay fever, alergi dan lain-lain.', 1),
(39, 'ANTIHISTAMINIKA', 'obat yang digunakan untuk melawan atau meredakan akibat yang ditimbulkan oleh adanya kelebihan histamine dalam tubuh dengan gejala alergi antara lain gatal-gatal, eksima, bersin dan lain-lain.', 1),
(40, 'OBAT ANTIASMA DAN BRONKODILATOR', 'obat yang digunakan untuk mengurangi / menghilangkan gangguan sesak napas yang disertai batuk dan dahak yang berlebihan.', 1);

-- --------------------------------------------------------

--
-- Table structure for table `kartu_stock`
--

CREATE TABLE IF NOT EXISTS `kartu_stock` (
  `idkartu_stock` bigint(20) NOT NULL AUTO_INCREMENT,
  `idobat` int(11) NOT NULL,
  `idsbbm` int(11) NOT NULL,
  `iddetail_sbbm` int(11) NOT NULL,
  `idsbbk` int(11) NOT NULL,
  `iddetail_sbbk` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `bulan` char(2) COLLATE latin1_general_ci NOT NULL,
  `tahun` year(4) NOT NULL,
  `tanggal_expire` date NOT NULL,
  `mode` enum('keluar','masuk') COLLATE latin1_general_ci NOT NULL,
  `isopname` tinyint(1) NOT NULL DEFAULT '0',
  `islocked` tinyint(1) NOT NULL DEFAULT '0',
  `isdestroyed` tinyint(1) NOT NULL DEFAULT '0',
  `date_added` datetime NOT NULL,
  `date_modified` datetime NOT NULL,
  PRIMARY KEY (`idkartu_stock`),
  KEY `idobat` (`idobat`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=18041 ;

-- --------------------------------------------------------

--
-- Table structure for table `kartu_stock_puskesmas`
--

CREATE TABLE IF NOT EXISTS `kartu_stock_puskesmas` (
  `idkartu_stock_puskesmas` bigint(20) NOT NULL AUTO_INCREMENT,
  `idobat` int(11) NOT NULL,
  `idobat_puskesmas` int(11) NOT NULL,
  `idpuskesmas` int(11) NOT NULL,
  `idsbbm_puskesmas` int(11) NOT NULL,
  `iddetail_sbbm_puskesmas` int(11) NOT NULL,
  `idsbbk_puskesmas` int(11) NOT NULL,
  `iddetail_sbbk_puskesmas` int(11) NOT NULL,
  `tanggal_puskesmas` date NOT NULL,
  `bulan_puskesmas` char(2) COLLATE latin1_general_ci NOT NULL,
  `tahun_puskesmas` year(4) NOT NULL,
  `tanggal_expire_puskesmas` date NOT NULL,
  `mode_puskesmas` enum('keluar','masuk') COLLATE latin1_general_ci NOT NULL,
  `isopname_puskesmas` tinyint(1) NOT NULL DEFAULT '0',
  `islocked_puskesmas` tinyint(1) NOT NULL DEFAULT '0',
  `isdestroyed` tinyint(1) NOT NULL DEFAULT '0',
  `date_added` datetime NOT NULL,
  `date_modified` datetime NOT NULL,
  PRIMARY KEY (`idkartu_stock_puskesmas`),
  KEY `idpuskesmas` (`idpuskesmas`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Table structure for table `kartu_stock_puskesmas_dinas`
--

CREATE TABLE IF NOT EXISTS `kartu_stock_puskesmas_dinas` (
  `idkartu_stock_puskesmas` bigint(20) NOT NULL AUTO_INCREMENT,
  `idobat` int(11) NOT NULL,
  `idobat_puskesmas` int(11) NOT NULL,
  `idpuskesmas` int(11) NOT NULL,
  `idsbbm_puskesmas` int(11) NOT NULL,
  `iddetail_sbbm_puskesmas` int(11) NOT NULL,
  `idsbbk_puskesmas` int(11) NOT NULL,
  `iddetail_sbbk_puskesmas` int(11) NOT NULL,
  `tanggal_puskesmas` date NOT NULL,
  `bulan_puskesmas` char(2) COLLATE latin1_general_ci NOT NULL,
  `tahun_puskesmas` year(4) NOT NULL,
  `tanggal_expire_puskesmas` date NOT NULL,
  `mode_puskesmas` enum('keluar','masuk') COLLATE latin1_general_ci NOT NULL,
  `isopname_puskesmas` tinyint(1) NOT NULL DEFAULT '0',
  `islocked_puskesmas` tinyint(1) NOT NULL DEFAULT '0',
  `isdestroyed` tinyint(1) NOT NULL DEFAULT '0',
  `date_added` datetime NOT NULL,
  `date_modified` datetime NOT NULL,
  PRIMARY KEY (`idkartu_stock_puskesmas`),
  KEY `idpuskesmas` (`idpuskesmas`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Table structure for table `kecamatan`
--

CREATE TABLE IF NOT EXISTS `kecamatan` (
  `idkecamatan` tinyint(4) NOT NULL AUTO_INCREMENT,
  `iddt2` mediumint(9) NOT NULL,
  `nama_kecamatan` varchar(60) NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idkecamatan`),
  UNIQUE KEY `nama_kecamatan` (`nama_kecamatan`),
  KEY `iddt2` (`iddt2`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=20 ;

--
-- Dumping data for table `kecamatan`
--

INSERT INTO `kecamatan` (`idkecamatan`, `iddt2`, `nama_kecamatan`, `enabled`) VALUES
(1, 8, 'BINTAN TIMUR', 1),
(2, 8, 'GUNUNG KIJANG', 1),
(3, 8, 'TELUK BINTAN', 1),
(4, 8, 'BINTAN UTARA', 1),
(5, 8, 'TELUK SEBONG', 1),
(6, 8, 'TAMBELAN', 1),
(7, 8, 'SERI KUALA LOBAM', 1),
(8, 8, 'TOAPAYA', 1),
(9, 8, 'BINTAN PESISIR', 1),
(10, 8, 'MANTANG', 1);

-- --------------------------------------------------------

--
-- Table structure for table `log_ks`
--

CREATE TABLE IF NOT EXISTS `log_ks` (
  `idlog` bigint(20) NOT NULL AUTO_INCREMENT,
  `idobat` int(11) NOT NULL,
  `idsbbm` int(11) NOT NULL,
  `iddetail_sbbm` int(11) NOT NULL,
  `idsbbk` int(11) NOT NULL,
  `iddetail_sbbk` int(11) NOT NULL,
  `tanggal` date NOT NULL,
  `bulan` char(2) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `tahun` year(4) NOT NULL,
  `qty` mediumint(9) NOT NULL,
  `sisa_stock` mediumint(9) NOT NULL,
  `keterangan` varchar(150) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `mode` enum('masuk','keluar') CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `userid` int(11) NOT NULL,
  `username` varchar(40) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`idlog`),
  KEY `idobat` (`idobat`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=15 ;

-- --------------------------------------------------------

--
-- Table structure for table `log_ks_puskesmas`
--

CREATE TABLE IF NOT EXISTS `log_ks_puskesmas` (
  `idlog_puskesmas` bigint(20) NOT NULL AUTO_INCREMENT,
  `idpuskesmas` int(11) NOT NULL,
  `idobat` int(11) NOT NULL,
  `idobat_puskesmas` int(11) NOT NULL,
  `idsbbm_puskesmas` int(11) NOT NULL,
  `iddetail_sbbm_puskesmas` int(11) NOT NULL,
  `idsbbk_puskesmas` int(11) NOT NULL,
  `iddetail_sbbk_puskesmas` int(11) NOT NULL,
  `tanggal_puskesmas` date NOT NULL,
  `bulan_puskesmas` char(2) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `tahun_puskesmas` year(4) NOT NULL,
  `qty_puskesmas` mediumint(9) NOT NULL,
  `sisa_stock_puskesmas` mediumint(9) NOT NULL,
  `keterangan_puskesmas` varchar(150) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `mode_puskesmas` enum('masuk','keluar') CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `userid_puskesmas` int(11) NOT NULL,
  `username_puskesmas` varchar(100) NOT NULL,
  `nama_user_puskesmas` varchar(100) NOT NULL,
  `date_added` datetime NOT NULL,
  PRIMARY KEY (`idlog_puskesmas`),
  KEY `idpuskesmas` (`idpuskesmas`),
  KEY `idobat` (`idobat`),
  KEY `idobat_puskesmas` (`idobat_puskesmas`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `master_lpo`
--

CREATE TABLE IF NOT EXISTS `master_lpo` (
  `idlpo` int(11) NOT NULL AUTO_INCREMENT,
  `no_lpo` varchar(35) COLLATE latin1_general_ci NOT NULL,
  `idpuskesmas` int(11) NOT NULL,
  `tanggal_lpo` date NOT NULL,
  `jumlah_kunjungan_gratis` mediumint(9) NOT NULL,
  `jumlah_kunjungan_bayar` mediumint(9) NOT NULL,
  `jumlah_kunjungan_bpjs` mediumint(9) NOT NULL,
  `nip_ka` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `nama_ka` varchar(150) COLLATE latin1_general_ci NOT NULL,
  `nip_pengelola_obat` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `nama_pengelola_obat` varchar(150) COLLATE latin1_general_ci NOT NULL,
  `nip_kadis` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `nama_kadis` varchar(150) COLLATE latin1_general_ci NOT NULL,
  `nip_ka_gudang` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `nama_ka_gudang` varchar(150) COLLATE latin1_general_ci NOT NULL,
  `status` enum('none','draft','complete') COLLATE latin1_general_ci NOT NULL DEFAULT 'none',
  `response_lpo` tinyint(4) NOT NULL DEFAULT '1',
  `tahun` year(4) NOT NULL,
  `date_added` datetime NOT NULL,
  `date_modified` datetime NOT NULL,
  PRIMARY KEY (`idlpo`),
  KEY `idpuskesmas` (`idpuskesmas`),
  KEY `no_lpo` (`no_lpo`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Table structure for table `master_obat`
--

CREATE TABLE IF NOT EXISTS `master_obat` (
  `idobat` int(11) NOT NULL AUTO_INCREMENT,
  `kode_obat` varchar(10) COLLATE latin1_general_ci NOT NULL,
  `no_reg` varchar(75) COLLATE latin1_general_ci NOT NULL,
  `nama_obat` varchar(150) COLLATE latin1_general_ci NOT NULL,
  `nama_merek` varchar(150) COLLATE latin1_general_ci NOT NULL,
  `harga` mediumint(9) NOT NULL,
  `idsatuan_obat` tinyint(4) NOT NULL,
  `idgolongan` tinyint(4) NOT NULL,
  `idbentuk_sediaan` smallint(6) NOT NULL,
  `nama_bentuk` varchar(75) COLLATE latin1_general_ci NOT NULL,
  `farmakologi` varchar(200) COLLATE latin1_general_ci NOT NULL,
  `komposisi` varchar(100) COLLATE latin1_general_ci NOT NULL,
  `kemasan` varchar(100) COLLATE latin1_general_ci NOT NULL,
  `idprodusen` mediumint(9) NOT NULL,
  `stock` int(11) NOT NULL,
  `min_stock` int(11) NOT NULL,
  `max_stock` int(11) NOT NULL,
  `status_obat` enum('unverified','verified') COLLATE latin1_general_ci NOT NULL DEFAULT 'unverified',
  `date_added` date NOT NULL,
  `date_modified` date NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idobat`),
  KEY `kode_obat` (`kode_obat`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=383 ;

--
-- Dumping data for table `master_obat`
--

INSERT INTO `master_obat` (`idobat`, `kode_obat`, `no_reg`, `nama_obat`, `nama_merek`, `harga`, `idsatuan_obat`, `idgolongan`, `idbentuk_sediaan`, `nama_bentuk`, `farmakologi`, `komposisi`, `kemasan`, `idprodusen`, `stock`, `min_stock`, `max_stock`, `status_obat`, `date_added`, `date_modified`, `enabled`) VALUES
(7, '189', '189', 'LARUTAN BENEDICT 100 ML.', '-', 68000, 1, 2, 13, '100nl', '{"22":"ANESTETIKA"}', 'benedict', 'botol', 4, 0, 0, 0, 'unverified', '2015-01-13', '2015-01-13', 1),
(8, '1', '1', 'ARTESUNAT INJEKSI', '-', 19000, 9, 2, 2, '-', '{"22":"ANESTETIKA"}', ' Artesunat Injeksi', 'Tablet', 3, 0, 0, 0, 'unverified', '2015-01-13', '2015-01-13', 1),
(9, '2', '2', 'ARTEMETER INJEKSI', '-', 12500, 9, 1, 2, '-', '{"17":"ANALGETIKA"}', 'Artemeter Injeksi', 'Tablet', 4, 0, 0, 0, 'unverified', '2015-01-13', '2015-01-13', 1),
(11, '4', '4', 'AIR RAKSA DENTAL USE  100 G.', '-', 220000, 1, 2, 11, '-', '{"27":"ANTI HIPERTENSI"}', 'Air Raksa Dental use 100 G.', 'Botol', 1, 0, 0, 0, 'unverified', '2015-01-13', '2015-01-13', 1),
(12, '5', '5', 'ALBENDAZOL TABLET 400 MG', '-', 427, 13, 1, 5, '-', '{"27":"ANTI HIPERTENSI"}', 'Albendazol tablet 400 mg', 'Tablet', 2, 0, 0, 0, 'unverified', '2015-01-13', '2015-01-13', 1),
(13, '3', '3', 'A C T ( ARTESUNAT 50 MG + AMODIAQUIN 200 MG ) TABLET', '-', 1200, 13, 1, 5, '-', '{"4":"ANTHELMETIKA"}', 'A C T ( Artesunat 50 mg + Amodiaquin 200 mg ) tablet', 'Tablet', 2, 0, 0, 0, 'unverified', '2015-01-13', '2015-03-16', 1),
(14, '6', '6', 'ALLYLESTRENOL TABLET 5 MG', '-', 1650, 13, 1, 11, '-', '{"4":"ANTHELMETIKA"}', 'Allylestrenol tablet 5 mg', 'Tablet', 2, 0, 0, 0, 'unverified', '2015-01-13', '2015-01-13', 1),
(15, '7', '7', 'ALOPURINOL TABLET 100 MG', '-', 139, 13, 1, 5, '-', '{"31":"ANTI TROMBOLITIKA"}', 'Alopurinol tablet 100 mg', 'Tablet', 1, 0, 0, 0, 'unverified', '2015-01-13', '2015-01-13', 1),
(16, '8', '8', 'AMBROKSOL SIRUP  15 MG/5 ML. @ 60 ML.', '-', 3465, 1, 1, 8, '-', '{"31":"ANTI TROMBOLITIKA"}', 'Ambroksol sirup  15 mg/5 ml. @ 60 ml.', 'Botol', 4, 0, 0, 0, 'unverified', '2015-01-13', '2015-01-13', 1),
(17, '9', '9', 'AMBROKSOL TABLET 30 MG', '-', 119, 13, 1, 5, '-', '{"27":"ANTI HIPERTENSI"}', 'Ambroksol tablet 30 mg', 'Tablet', 5, 0, 0, 0, 'unverified', '2015-01-13', '2015-01-13', 1),
(18, '10', '10', 'AMINOFILIN INJEKSI 24 MG / ML  @ 10 ML', '-', 2695, 14, 1, 8, '-', '{"4":"ANTHELMETIKA"}', 'Aminofilin injeksi 24 mg / ml  @ 10 ml', 'Ampul', 1, 0, 0, 0, 'unverified', '2015-01-13', '2015-01-13', 1),
(19, '11', '11', 'AMINOFILIN TABLET 200 MG', '-', 110, 13, 2, 13, '-', '{"31":"ANTI TROMBOLITIKA"}', 'Aminofilin tablet 200 mg', 'Tablet', 4, 0, 0, 0, 'unverified', '2015-01-13', '2015-01-13', 1),
(20, '12', '12', 'AMITRIPTILIN HCL TABLET SALUT 25 MG', '-', 92, 13, 1, 2, '-', '{"22":"ANESTETIKA"}', 'Amitriptilin HCl tablet salut 25 mg', 'Tablet', 5, 0, 0, 0, 'unverified', '2015-01-13', '2015-01-13', 1),
(21, '13', '13', 'AMLODIPIN TABLET 5 MG', '-', 1260, 13, 1, 5, '-', '{"1":"ANTIBIOTIKA"}', 'Amlodipin tablet 5 mg', 'Tablet', 5, 0, 0, 0, 'unverified', '2015-01-13', '2015-01-13', 1),
(22, '14', '14', 'AMOKSISILIN CAPSUL 250 MG', '-', 303, 13, 1, 5, '-', '{"3":"ANTIAMUBA"}', 'Amoksisilin capsul 250 mg', 'Tablet', 4, 0, 0, 0, 'unverified', '2015-01-13', '2015-01-13', 1),
(23, '15', '15', 'AMOKSISILIN INJEKSI 1000 MG', '-', 7875, 9, 1, 11, '-', '{"3":"ANTIAMUBA"}', 'Amoksisilin injeksi 1000 mg', 'Vial', 4, 0, 0, 0, 'unverified', '2015-01-13', '2015-01-13', 1),
(24, '16', '16', 'AMOKSISILIN KAPLET 500 MG', '-', 389, 15, 1, 5, '-', '{"3":"ANTIAMUBA"}', 'Amoksisilin kaplet 500 mg', 'Kaplet', 2, 0, 0, 0, 'unverified', '2015-01-13', '2015-01-13', 1),
(25, '17', '17', 'AMOKSISILIN SIRUP KERING 125 MG / 5 ML', '-', 3885, 1, 1, 5, '-', '{"1":"ANTIBIOTIKA"}', 'Amoksisilin sirup kering 125 mg / 5 ml', 'Botol', 1, 0, 0, 0, 'unverified', '2015-01-13', '2015-01-13', 1),
(26, '18', '18', 'AMOKSISILIN+ASAM KLAVULANAT 625 MG TABLET', '-', 5308, 13, 1, 9, '-', '{"1":"ANTIBIOTIKA"}', 'Amoksisilin+Asam Klavulanat 625 mg tablet', 'Tablet', 5, 0, 0, 0, 'unverified', '2015-01-13', '2015-01-13', 1),
(27, '19', '19', 'AMPISILIN CAPSUL 250 MG', '-', 282, 7, 1, 3, '-', '{"3":"ANTIAMUBA"}', 'Ampisilin capsul 250 mg', 'Kapsul', 4, 0, 0, 0, 'unverified', '2015-01-13', '2015-01-13', 1),
(28, '20', '20', 'AMPISILIN KAPLET 500 MG', '-', 900, 15, 1, 5, '-', '{"3":"ANTIAMUBA"}', 'Ampisilin kaplet 500 mg', 'Kaplet', 3, 0, 0, 0, 'unverified', '2015-01-13', '2015-02-08', 1),
(29, '21', '21', 'AMPISILIN SERBUK INJEKSI 1000 MG', '-', 2887, 9, 1, 5, '-', '{"3":"ANTIAMUBA"}', 'Ampisilin serbuk injeksi 1000 mg', 'Vial', 5, 0, 0, 0, 'unverified', '2015-01-13', '2015-01-13', 1),
(30, '22', '22', 'AMPISILIN SIRUP KERING 125 MG / 5 ML', '-', 4199, 1, 1, 9, '-', '{"1":"ANTIBIOTIKA"}', 'Ampisilin sirup kering 125 mg / 5 ml', 'Botol', 4, 0, 0, 0, 'unverified', '2015-01-13', '2015-01-13', 1),
(31, '23', '23', 'ANTASIDA DOEN  I TABLET  KOMB : AL(OH) 200 MG + MG (OH) 200 MG', '-', 170, 13, 1, 3, '-', '{"3":"ANTIAMUBA"}', 'Antasida DOEN  I tablet  komb : Al(OH) 200 mg + Mg (OH) 200 mg', 'Tablet', 4, 0, 0, 0, 'unverified', '2015-01-13', '2015-01-13', 1),
(32, '24', '24', 'ANTASIDA DOEN II SUSP. 60 ML.KOMB.:AL(OH) 200 MG/5 ML.+MG(OH) 200 MG/5 ML', '-', 4043, 1, 1, 3, '-', '{"3":"ANTIAMUBA"}', 'Antasida DOEN II susp. 60 ml.komb.:Al(OH) 200 mg/5 ml.+Mg(OH) 200 mg/5 ml', 'Botol', 3, 0, 0, 0, 'unverified', '2015-01-13', '2015-01-13', 1),
(33, '25', '25', 'ANTI BAKTERI DOEN SALAP KOMB : BASITRASIN 500 IU/G + POLIMIKSIN 1000 IU/G', '-', 2643, 12, 1, 10, '-', '{"31":"ANTI TROMBOLITIKA"}', 'Anti Bakteri DOEN salap komb : Basitrasin 500 IU/g + Polimiksin 1000 IU/g', 'Tube', 3, 0, 0, 0, 'unverified', '2015-01-13', '2015-01-13', 1),
(34, '26', '26', 'ANTI FUNGI DOEN KOMB : AS. BENZOAT 6 % + AS. SALISILAT 3 %', '-', 1155, 10, 2, 10, '-', '{"3":"ANTIAMUBA"}', 'Anti Fungi DOEN komb : As. Benzoat 6 % + As. Salisilat 3 %', 'Pot', 3, 0, 0, 0, 'unverified', '2015-01-13', '2015-01-13', 1),
(35, '27', '27', 'ANTI HEMOROID DOEN KOMBINASI ', '-', 2100, 11, 1, 8, '-', '{"3":"ANTIAMUBA"}', 'Anti Hemoroid DOEN kombinasi ', 'Supp', 3, 0, 0, 0, 'unverified', '2015-01-13', '2015-01-13', 1),
(36, '28', '28', 'ANTI MALARIA DOEN KOMBINASI : PIRIMETAMIN 25 MG + SULFADOKSIN 500 MG', '-', 473, 13, 1, 10, '-', '{"3":"ANTIAMUBA"}', 'Anti Malaria DOEN kombinasi : Pirimetamin 25 mg + Sulfadoksin 500 mg', 'Tablet', 3, 0, 0, 0, 'unverified', '2015-01-13', '2015-01-13', 1),
(37, '29', '29', 'ANTI MIGREN DOEN KOMBINASI : ERGOTAMIN TARTRAT 1 MG + KOFFEIN 50 MG', '-', 129, 13, 1, 5, '-', '{"3":"ANTIAMUBA"}', 'Anti Migren DOEN kombinasi : Ergotamin Tartrat 1 mg + Koffein 50 mg', 'Tablet', 2, 0, 0, 0, 'unverified', '2015-01-13', '2015-01-13', 1),
(38, '30', '30', 'ANTISKABIES KRIM 10 GR', '-', 8518, 12, 1, 10, '-', '{"3":"ANTIAMUBA"}', 'Antiskabies krim 10 gr', 'Tube', 2, 0, 0, 0, 'unverified', '2015-01-13', '2015-01-13', 1),
(39, '31', '31', 'AQUA DEST STERIL 500 ML.', '-', 3588, 1, 1, 3, '-', '{"17":"ANALGETIKA"}', 'Aqua Dest Steril 500 ml.', 'Botol', 1, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(40, '32', '32', 'AQUA PRO INJEKSI STERIL 25 ML., BEBAS PIROGEN', '-', 2420, 1, 1, 8, '-', '{"22":"ANESTETIKA"}', 'Aqua Pro Injeksi steril 25 ml., bebas pirogen', 'Botol', 2, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(41, '33', '33', 'ASAM ASETIL SALISILAT ( ASETOSAL )  TABLET 80 MG', '-', 374, 13, 2, 5, '-', '{"22":"ANESTETIKA"}', 'Asam asetil Salisilat ( Asetosal )  tablet 80 mg', 'Tablet', 5, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(42, '34', '34', 'ASAM ASKORBAT TABLET   50 MG ', '-', 29, 13, 1, 5, '-', '{"31":"ANTI TROMBOLITIKA"}', 'Asam Askorbat tablet   50 mg ', 'Tablet', 5, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(43, '35', '35', 'ASAM ASKORBAT TABLET 250 MG ', '-', 134, 13, 1, 10, '-', '{"22":"ANESTETIKA"}', 'Asam Askorbat tablet 250 mg ', 'Tablet', 1, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(44, '36', '36', 'ASAM FOLAT TABLET 1 MG', '-', 71, 13, 1, 5, '-', '{"3":"ANTIAMUBA"}', 'Asam Folat tablet 1 mg', 'Tablet', 3, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(45, '37', '37', 'ASAM MEFENAMAT CAPSUL 250 MG', '-', 116, 7, 1, 3, '-', '{"3":"ANTIAMUBA"}', 'Asam Mefenamat capsul 250 mg', 'Kapsul', 4, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(46, '38', '38', 'ASAM MEFENAMAT KAPLET 500 MG', '-', 163, 15, 1, 5, '-', '{"22":"ANESTETIKA"}', 'Asam Mefenamat kaplet 500 mg', 'Kaplet', 4, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(47, '191', '111', 'LARUTAN ETANOL ASAM 100 ML.', '-', 178200, 1, 2, 9, '100 ml', '{"17":"ANALGETIKA"}', 'cair', 'botol', 4, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(48, '190', '1111', 'LARUTAN EOSIN 2 % 100 ML.', '-', 86250, 1, 2, 9, 'BOTOL', '{"17":"ANALGETIKA"}', 'CAIR', 'BOTOL', 4, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(49, '192', '11111', 'LARUTAN GIEMSA STAIN 100 ML.', '-', 166750, 1, 2, 9, '100 ml', '{"22":"ANESTETIKA"}', 'CAIR', 'BOTOL', 4, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(50, '193', '111111', 'LARUTAN KARBOL FUCHSIN 100 ML.', '-', 74750, 1, 2, 9, '100 ml', '{"17":"ANALGETIKA"}', 'CAUR', 'BOTOL', 4, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(51, '194', '1111111', 'LARUTAN METANOL 100 ML.', '-', 44000, 14, 2, 9, '100 ml', '{"22":"ANESTETIKA"}', 'CAIR', 'BOTOL', 4, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(52, '195', '11111111', 'LARUTAN METILEN BIRU 100 ML.', '-', 69000, 1, 2, 9, '100 ml', '{"17":"ANALGETIKA"}', 'CAIR', 'BOTOL', 4, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(53, '196', '111111111', 'LARUTAN TURK 100 ML.', '-', 57500, 1, 2, 9, '100 ml', '{"17":"ANALGETIKA"}', 'CAIR', 'BOTOL', 4, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(54, '39', '39', 'ASAM TRANEKSAMAT INJEKSI 250 MG', '-', 3850, 14, 1, 8, '-', '{"31":"ANTI TROMBOLITIKA"}', 'Asam Traneksamat injeksi 250 mg', 'Ampul', 5, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(55, '40', '40', 'ASAM TRANEKSAMAT TABLET 500 MG', '-', 1045, 13, 1, 3, '-', '{"31":"ANTI TROMBOLITIKA"}', 'Asam Traneksamat tablet 500 mg', 'Tablet', 5, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(56, '41', '41', 'ASIATIKOSIDA KRIM 1 % @ 10 G', '-', 51150, 12, 1, 3, '-', '{"31":"ANTI TROMBOLITIKA"}', 'Asiatikosida krim 1 % @ 10 g', 'Tube', 2, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(57, '42', '42', 'ASIKLOVIR KRIM 5 % @ 5 GRAM', '-', 3150, 12, 1, 3, '-', '{"31":"ANTI TROMBOLITIKA"}', 'Asiklovir krim 5 % @ 5 gram', 'Tube', 2, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(58, '43', '43', 'ASIKLOVIR TABLET 200 MG', '-', 509, 13, 1, 10, '-', '{"31":"ANTI TROMBOLITIKA"}', 'Asiklovir tablet 200 mg', 'Tablet', 3, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(59, '44', '44', 'ASIKLOVIR TABLET 400 MG', '-', 600, 13, 2, 5, '-', '{"31":"ANTI TROMBOLITIKA"}', 'Asiklovir tablet 400 mg', 'Tablet', 1, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(60, '45', '45', 'ATROPIN SULFAT TETES MATA 0,5 % @ 5 ML.', '-', 117, 1, 1, 5, '-', '{"8":"ANTI TBC"}', 'Atropin Sulfat Tetes Mata 0,5 % @ 5 ml.', 'Botol', 2, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(61, '46', '46', 'ATTAPULGITE ( NODIAR) TABLET', '-', 275, 13, 1, 10, '-', '{"8":"ANTI TBC"}', 'Attapulgite ( Nodiar) tablet', 'Tablet', 4, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(62, '48', '48', 'BENZATIN BENZIL PENISILLIN INJEKSI 2,4 JUTA IU', '-', 5400, 9, 1, 5, '-', '{"27":"ANTI HIPERTENSI"}', 'Benzatin Benzil Penisillin injeksi 2,4 Juta IU', 'Vial', 3, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(63, '47', '47', 'BAHAN PENGISI SALURAN AKAR GIGI', '-', 579700, 16, 1, 5, '-', '{"3":"ANTIAMUBA"}', 'Bahan pengisi saluran akar gigi', 'Set', 3, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(64, '49', '49', 'BENZATIN BENZIL PENISILLIN INJEKSI 1,2 JUTA IU', '-', 3803, 9, 1, 10, '-', '{"8":"ANTI TBC"}', 'Benzatin Benzil Penisillin injeksi 1,2 Juta IU', 'Vial', 1, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(65, '50', '50', 'BESI SIRUP 150 ML.', '-', 1800, 1, 1, 11, '-', '{"4":"ANTHELMETIKA"}', 'Besi sirup 150 ml.', 'Botol', 1, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(66, '51', '51', 'BETAHISTIN MESILAT TABLET 6 MG', '-', 970, 13, 1, 5, '-', '{"8":"ANTI TBC"}', 'Betahistin Mesilat tablet 6 mg', 'Tablet', 2, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(67, '52', '52', 'BETAMETASON KREM 0,1 %  @ 5 G', '-', 1959, 12, 1, 3, '-', '{"8":"ANTI TBC"}', 'Betametason Krem 0,1 %  @ 5 g', 'Tube', 3, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(68, '53', '53', 'BISAKODIL ( DULCOLAX ) SUPPOSITORIA  10 MG DEWASA', '-', 14454, 11, 1, 5, '-', '{"31":"ANTI TROMBOLITIKA"}', 'Bisakodil ( Dulcolax ) suppositoria  10 mg dewasa', 'Supp', 1, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(69, '54', '54', 'BISAKODIL ( DULCOLAX ) SUPPOSITORIA 5 MG DEWASA', '-', 12267, 11, 1, 10, '-', '{"8":"ANTI TBC"}', 'Bisakodil ( Dulcolax ) suppositoria 5 mg dewasa', 'Supp', 2, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(70, '55', '55', 'BISAKODIL TABLET 10 MG', '-', 1039, 13, 1, 5, '-', '{"8":"ANTI TBC"}', 'Bisakodil tablet 10 mg', 'Tablet', 3, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(71, '56', '56', 'BISOPROLOL TABLET 5 MG', '-', 2438, 13, 1, 8, '-', '{"4":"ANTHELMETIKA"}', 'Bisoprolol tablet 5 mg', 'Tablet', 2, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(72, '57', '57', 'BORAX GLISERIN SOLUTIO 15 ML.', '-', 2090, 1, 1, 11, '-', '{"4":"ANTHELMETIKA"}', 'Borax Gliserin solutio 15 ml.', 'Botol', 3, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(73, '58', '58', 'BROMHEKSIN TABLET 8 MG', '-', 40, 13, 1, 5, '-', '{"31":"ANTI TROMBOLITIKA"}', 'Bromheksin tablet 8 mg', 'Tablet', 3, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(74, '59', '59', 'BROMHEKSIN SIRUP 100 ML', '-', 6050, 1, 1, 3, '-', '{"8":"ANTI TBC"}', 'Bromheksin sirup 100 ml', 'Botol', 2, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(75, '60', '60', 'CENDO LYTEERS EYE DROP 15 ML', '-', 20900, 1, 1, 11, '-', '{"8":"ANTI TBC"}', 'Cendo Lyteers eye drop 15 ml', 'Botol', 3, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(76, '61', '61', 'CENDO CENFRESH EYE DROP 5 ML', '-', 33000, 1, 1, 5, '-', '{"8":"ANTI TBC"}', 'Cendo Cenfresh eye drop 5 ml', 'Botol', 1, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(77, '62', '62', 'CENDO XITROL EYE DROP 5 ML', '-', 27500, 1, 1, 5, '-', '{"3":"ANTIAMUBA"}', 'Cendo Xitrol eye drop 5 ml', 'Botol', 1, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(78, '63', '63', 'CENDO XITROL EYE OINT 3,5 GR', '-', 29700, 12, 1, 10, '-', '{"8":"ANTI TBC"}', 'Cendo Xitrol eye oint 3,5 gr', 'Tube', 2, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(79, '64', '64', 'CENDO CATARLENT EYE DROP 5 ML', '-', 22000, 1, 1, 5, '-', '{"3":"ANTIAMUBA"}', 'Cendo Catarlent eye drop 5 ml', 'Botol', 3, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(80, '65', '65', 'CENDO POLYGRAN EYE DROP 5 ML', '-', 33000, 1, 1, 3, '-', '{"31":"ANTI TROMBOLITIKA"}', 'Cendo Polygran eye drop 5 ml', 'Botol', 1, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(81, '66', '66', 'CENDO POLYGRAN EYE OINT 3,5 GR', '-', 22000, 12, 1, 11, '-', '{"4":"ANTHELMETIKA"}', 'Cendo Polygran eye oint 3,5 gr', 'Tube', 1, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(82, '67', '67', 'CENDO POLYDEX EYE DROP 5 ML', '-', 45100, 1, 1, 8, '-', '{"27":"ANTI HIPERTENSI"}', 'Cendo Polydex eye drop 5 ml', 'Botol', 1, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(83, '68', '68', 'CENDO TIMOLOL 0,5% EYE DROP 5 ML', '-', 55000, 1, 1, 5, '-', '{"31":"ANTI TROMBOLITIKA"}', 'Cendo Timolol 0,5% eye drop 5 ml', 'Botol', 2, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(84, '198', '1981', 'LARUTAN H2O2 3%', '-', 50600, 1, 2, 9, 'TABUNG', '{"22":"ANESTETIKA"}', 'CAIR', 'BOTOL', 4, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(85, '69', '69', 'TOBROSON EYE DROP', '-', 41250, 1, 1, 8, '-', '{"31":"ANTI TROMBOLITIKA"}', 'Tobroson eye drop', 'Botol', 2, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(86, '71', '71', 'CEFADROKSIL 125 MG / 5 ML SIRUP KERING', '-', 8610, 1, 1, 5, '-', '{"31":"ANTI TROMBOLITIKA"}', 'Cefadroksil 125 mg / 5 ml sirup kering', 'Botol', 1, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(87, '197', '1971', 'LARUTAN NATRIUM CITRAS 3 % @100 ML', '-', 28750, 1, 2, 9, '100 ml', '{"22":"ANESTETIKA"}', 'CAIR', 'BOTOL', 4, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(88, '70', '70', 'CAIRAN IRIGASI NAOCL 5% DAN EDTA 5%', '-', 290000, 17, 1, 3, '-', '{"31":"ANTI TROMBOLITIKA"}', 'Cairan Irigasi NaOCl 5% dan EDTA 5%', 'Ktk', 1, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(89, '72', '72', 'CEFADROKSIL KAPSUL 500 MG', '-', 735, 7, 1, 5, '-', '{"4":"ANTHELMETIKA"}', 'Cefadroksil kapsul 500 mg', 'Kapsul', 2, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(90, '73', '73', 'CEFIXIME 100 MG', '-', 2514, 13, 1, 5, '-', '{"27":"ANTI HIPERTENSI"}', 'Cefixime 100 mg', 'Tablet', 4, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(91, '199', '1991', 'LARUTAN HCL 0,1N ', '-', 49500, 1, 2, 9, '100 ml', '{"22":"ANESTETIKA"}', 'CAIR', 'BOTOL', 4, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(92, '74', '74', 'CEFOTAKSIM INJEKSI 1 G', '-', 8085, 9, 2, 3, '-', '{"8":"ANTI TBC"}', 'Cefotaksim injeksi 1 g', 'Vial', 3, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(93, '75', '75', 'CEFTRIAKSON INJEKSI 1 G', '-', 10027, 9, 1, 5, '-', '{"18":"ANTIEMETIKA"}', 'Ceftriakson injeksi 1 g', 'Vial', 1, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(94, '200', '2001', 'LARUTAN RESS ECHER', '-', 74750, 1, 2, 9, '100 ml', '{"22":"ANESTETIKA"}', 'CAIR', 'BOTOL', 4, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(95, '76', '76', 'CETIRIZINA TABLET 10 MG', '-', 329, 13, 1, 5, '-', '{"4":"ANTHELMETIKA"}', 'Cetirizina tablet 10 mg', 'Tablet', 1, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(96, '77', '77', 'CETIRIZINA SIRUP 5 MG / 5 ML', '-', 11550, 1, 3, 8, '-', '{"4":"ANTHELMETIKA"}', 'Cetirizina sirup 5 mg / 5 ml', 'Ampul', 1, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(97, '201', '2011', 'LARUTAN MERSI OIL', '-', 575000, 1, 2, 9, '100 ml', '{"17":"ANALGETIKA"}', 'CAIR', 'BOTOL', 4, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(98, '78', '78', 'CIMETIDIN INJEKSI 200 MG', '-', 7480, 14, 1, 3, '-', '{"4":"ANTHELMETIKA"}', 'Cimetidin injeksi 200 mg', 'Ampun', 1, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(99, '202', '2021', 'LIDOCAIN INJEKSI 2 % @ 2 ML', '-', 971, 14, 2, 9, '2 ml', '{"22":"ANESTETIKA"}', 'CAIR', 'AMPUL', 4, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(100, '79', '79', 'CIMETIDIN TABLET 200 MG', '-', 121, 13, 1, 10, '-', '{"8":"ANTI TBC"}', 'Cimetidin tablet 200 mg', 'Tablet', 1, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(101, '80', '80', 'CIPROFLOKSASIN KAPLET 500 MG', '-', 273, 15, 1, 8, '-', '{"4":"ANTHELMETIKA"}', 'Ciprofloksasin kaplet 500 mg', 'Kaplet', 2, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(102, '81', '81', 'CLINDAMISIN CAPSUL 150 MG', '-', 536, 7, 1, 11, '-', '{"27":"ANTI HIPERTENSI"}', 'Clindamisin Capsul 150 mg', 'Kapsul', 1, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(103, '82', '82', 'DEKSAMETASON INJEKSI 5 MG / ML - 1 ML', '-', 867, 14, 1, 13, '-', '{"10":"ANTASIDA DAN ANTIULCER"}', 'Deksametason injeksi 5 mg / ml - 1 ml', 'Ampul', 2, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(104, '203', '2031', 'LINKOMISIN CAPSUL 500 MG', '-', 682, 7, 2, 3, '500 mg', '{"22":"ANESTETIKA"}', 'KAPSUL', 'BOTOL', 4, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(105, '204', '2041', 'LISOL SOLUTIO 1000 ML.', '-', 51108, 1, 2, 9, '1000 ml', '{"22":"ANESTETIKA"}', 'CAIR', 'BOTOL', 4, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(106, '205', '2051', 'LOPERAMID TABLET 2 MG', '-', 96, 13, 2, 2, '2 mg', '{"22":"ANESTETIKA"}', '2 mg', 'TABLET', 4, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(107, '83', '83', 'DEKSAMETASON TABLET 0,5 MG', '-', 91, 13, 1, 13, '-', '{"27":"ANTI HIPERTENSI"}', 'Deksametason tablet 0,5 mg', 'Tablet', 1, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(108, '84', '84', 'DEKSTRAN 70 - LARUTAN INFUS 6 % STERIL @ 500 ML.', '-', 35526, 1, 1, 8, '-', '{"27":"ANTI HIPERTENSI"}', 'Dekstran 70 - larutan infus 6 % steril @ 500 ml.', 'Botol', 2, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(109, '207', '2071', 'MAGNESIUM SULFAT INJEKSI (IV) 40 % - 25 ML', '-', 19479, 9, 2, 9, '25 ml', '{"22":"ANESTETIKA"}', '25 ml', 'DUS', 4, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(110, '85', '85', 'DEKSTROMETORFAN SYRUP 10 MG / 5 ML @ 60 ML.', '-', 3008, 1, 1, 8, '-', '{"27":"ANTI HIPERTENSI"}', 'Dekstrometorfan syrup 10 mg / 5 ml @ 60 ml.', 'Botol', 2, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(111, '86', '86', 'DEKSTROMETORFAN TABLET 15 MG', '-', 10, 13, 1, 13, '-', '{"4":"ANTHELMETIKA"}', 'Dekstrometorfan tablet 15 mg', 'Tablet', 2, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(112, '87', '87', 'DEVITALISASI PASTA ( NON ARSEN )', '-', 520300, 1, 1, 13, '-', '{"4":"ANTHELMETIKA"}', 'Devitalisasi Pasta ( Non Arsen )', 'botol', 2, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(113, '208', '2081', 'MEBENDAZOL TABLET 100 MG', '-', 141, 7, 2, 3, '100 mg', '{"22":"ANESTETIKA"}', '100 mg', 'DUS', 4, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(114, '88', '88', 'DIAZEPAM INJEKSI 5 MG / ML - 2 ML', '-', 873, 14, 1, 8, '-', '{"4":"ANTHELMETIKA"}', 'Diazepam injeksi 5 mg / ml - 2 ml', 'Ampul', 2, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(115, '89', '89', 'DIAZEPAM TABLET 2 MG', '-', 3, 13, 3, 8, '-', '{"27":"ANTI HIPERTENSI"}', 'Diazepam tablet 2 mg', 'Tablet', 1, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(116, '209', '2091', 'MELOKSIKAM CAPSUL 15 MG', '-', 1259, 7, 2, 3, '15 mg', '{"22":"ANESTETIKA"}', '15 mg', 'KAPSUL', 4, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(117, '90', '90', 'DIAZEPAM TABLET 5 MG', '-', 139, 13, 1, 11, '-', '{"4":"ANTHELMETIKA"}', 'Diazepam tablet 5 mg', 'Tablet', 2, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(118, '91', '91', 'DIAZEPAM RECTAL 5 MG', '-', 25520, 12, 1, 5, '-', '{"4":"ANTHELMETIKA"}', 'Diazepam Rectal 5 mg', 'Tube', 1, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(119, '92', '92', 'DIFENHIDRAMIN HCL INJEKSI 10 MG / ML - 1 ML', '-', 460, 14, 1, 11, '-', '{"4":"ANTHELMETIKA"}', 'Difenhidramin HCl injeksi 10 mg / ml - 1 ml', 'Ampul', 2, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(120, '210', '2101', 'METAMPIRON ( ANTALGIN )  INJEKSI 250 MG /1 ML  @  2 ML', '-', 1285, 14, 2, 9, '2 ml', '{"22":"ANESTETIKA"}', '250 mg', 'AMPUL', 4, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(121, '93', '93', 'DIGOKSIN TABLET 0,25 MG', '-', 83, 13, 1, 10, '-', '{"27":"ANTI HIPERTENSI"}', 'Digoksin tablet 0,25 mg', 'Tablet', 2, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(122, '94', '94', 'DILTIAZEM TABLET 30 MG', '-', 152, 13, 1, 8, '-', '{"27":"ANTI HIPERTENSI"}', 'Diltiazem tablet 30 mg', 'Tablet', 2, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(123, '95', '95', 'DIMENHIDRINAT TABLET 50 MG', '-', 89, 13, 1, 11, '-', '{"4":"ANTHELMETIKA"}', 'Dimenhidrinat tablet 50 mg', 'Tablet', 2, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(124, '211', '2111', 'METAMPIRON ( ANTALGIN )  TABLET 500 MG', '-', 67, 13, 2, 9, '500 mg', '{"22":"ANESTETIKA"}', '500 mg', 'TABLET', 4, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(125, '96', '96', 'DESINFEKSI TANGAN 500 ML', '-', 88550, 1, 1, 10, '-', '{"4":"ANTHELMETIKA"}', 'Desinfeksi tangan 500 ml', 'Botol', 2, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(126, '97', '97', 'DOKSISIKLIN  CAPSUL 100 MG', '-', 216, 7, 1, 3, '-', '{"27":"ANTI HIPERTENSI"}', 'Doksisiklin  capsul 100 mg', 'Kapsul', 2, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(127, '212', '2121', 'METFORMIN TABLET 500 MG', '-', 173, 13, 2, 9, '500 mg', '{"22":"ANESTETIKA"}', '500 mg', 'TABLET', 4, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(128, '98', '98', 'DOMPERIDON TABLET 10 MG', '-', 424, 13, 2, 2, '-', '{"27":"ANTI HIPERTENSI"}', 'Domperidon tablet 10 mg', 'Tablet', 2, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(129, '99', '99', 'EFEDRIN HCL TABLET 25 MG', '-', 54, 13, 1, 2, '-', '{"4":"ANTHELMETIKA"}', 'Efedrin HCl tablet 25 mg', 'Tablet', 3, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(130, '100', '100', 'EKSTRAK BELLADON TABLET 10 MG', '-', 50, 13, 1, 2, '-', '{"27":"ANTI HIPERTENSI"}', 'Ekstrak Belladon tablet 10 mg', 'Tablet', 3, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(131, '101', '101', 'EKSTRAK PLASENTA GEL @ 15 G', '-', 16500, 12, 1, 5, '-', '{"4":"ANTHELMETIKA"}', 'Ekstrak Plasenta Gel @ 15 g', 'Tube', 2, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(132, '102', '102', 'EPINEFRIN HCL  ( ADRENALIN ) INJ 0,1 % - 1 ML', '-', 346, 14, 1, 11, '-', '{"27":"ANTI HIPERTENSI"}', 'Epinefrin HCl  ( Adrenalin ) inj 0,1 % - 1 ml', 'Ampul', 3, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(133, '103', '103', 'ERITROMISIN  KAPLET 500 MG', '-', 977, 15, 1, 8, '-', '{"27":"ANTI HIPERTENSI"}', 'Eritromisin  kaplet 500 mg', 'Kaplet', 3, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(134, '104', '104', 'ERITROMISIN SIRUP 200 MG / 5 ML', '-', 8505, 1, 1, 5, '-', '{"8":"ANTI TBC"}', 'Eritromisin sirup 200 mg / 5 ml', 'Botol', 2, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(135, '105', '105', 'ETAKRIDIN ( RIVANOL ) LARUTAN 0,1 % @ 300 ML.', '-', 1890, 1, 1, 8, '-', '{"27":"ANTI HIPERTENSI"}', 'Etakridin ( Rivanol ) larutan 0,1 % @ 300 ml.', 'Botol', 2, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(136, '107', '107', 'ETANOL LARUTAN 70 % @ 1000 ML.', '-', 24200, 1, 1, 10, '-', '{"4":"ANTHELMETIKA"}', 'Etanol larutan 70 % @ 1000 ml.', 'Botol', 3, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(137, '106', '106', 'ETAMBUTOL TABLET 250 MG', '-', 178, 18, 1, 8, '-', '{"27":"ANTI HIPERTENSI"}', 'Etambutol tablet 250 mg', 'Kotak', 3, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(138, '108', '108', 'ETYL KLORIDA SEMPROT @100 ML', '-', 82500, 1, 1, 8, '-', '{"27":"ANTI HIPERTENSI"}', 'Etyl Klorida semprot @100 ml', 'Botol', 2, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(139, '109', '109', 'ETINILESTRADIOL TABLET 0,05 MG', '-', 1912, 1, 1, 5, '-', '{"27":"ANTI HIPERTENSI"}', 'Etinilestradiol tablet 0,05 mg', 'Botol', 1, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(140, '110', '110', 'EUGENOL CAIRAN 10 ML.', '-', 38130, 1, 1, 10, '-', '{"27":"ANTI HIPERTENSI"}', 'Eugenol cairan 10 ml.', 'Botol', 2, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(141, '112', '112', 'FENOBARBITAL INJEKSI 50 MG / ML - 2 ML', '-', 670, 18, 1, 8, '-', '{"27":"ANTI HIPERTENSI"}', 'Fenobarbital injeksi 50 mg / ml - 2 ml', 'Botol', 3, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(142, '111', '111.', 'FENILBUTAZON TABLET 200 MG', '-', 69, 1, 1, 11, '-', '{"4":"ANTHELMETIKA"}', 'Fenilbutazon tablet 200 mg', 'Botol', 1, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(143, '113', '113', 'FENOBARBITAL TABLET    30 MG', '-', 5, 1, 1, 8, '-', '{"4":"ANTHELMETIKA"}', 'Fenobarbital tablet    30 mg', 'Botol', 2, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(144, '114', '114', 'FENOBARBITAL TABLET 100 MG', '-', 46, 1, 1, 11, '-', '{"27":"ANTI HIPERTENSI"}', 'Fenobarbital tablet 100 mg', 'Botol', 2, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(145, '115', '115', 'FENOL GLISEROL TETES TELINGA 10 %  @  5 ML.', '-', 1059, 18, 1, 8, '-', '{"27":"ANTI HIPERTENSI"}', 'Fenol Gliserol tetes telinga 10 %  @  5 ml.', 'Kotak', 3, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(146, '116', '116', 'FERO SULFAT TABLET 300 MG (TABLET TAMBAH DARAH)', '-', 1523, 13, 1, 13, '-', '{"27":"ANTI HIPERTENSI"}', 'Fero Sulfat tablet 300 mg (tablet tambah darah)', 'Tablet', 1, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(147, '117', '117', 'FITOMENADION 2 MG/ML INJEKSI', '-', 8250, 14, 1, 8, '-', '{"4":"ANTHELMETIKA"}', 'Fitomenadion 2 mg/ml injeksi', 'Ampul', 2, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(148, '118', '118', 'FITOMENADION ( VITAMIN K 1 ) INJEKSI 10 MG / ML - 1 ML', '-', 933, 14, 1, 5, '-', '{"27":"ANTI HIPERTENSI"}', 'Fitomenadion ( Vitamin K 1 ) injeksi 10 mg / ml - 1 ml', 'Ampul', 2, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(149, '119', '119', 'FITOMENADION ( VITAMIN K 1 ) TABLET SALUT GULA 10 MG', '-', 715, 13, 1, 11, '-', '{"27":"ANTI HIPERTENSI"}', 'Fitomenadion ( Vitamin K 1 ) tablet salut gula 10 mg', 'Tablet', 2, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(150, '120', '120', 'FLAVOKSAT HCL ( UROXAL ) TAB. 200 MG', '-', 2200, 13, 1, 11, '-', '{"27":"ANTI HIPERTENSI"}', 'Flavoksat HCl ( Uroxal ) tab. 200 mg', 'Tablet', 2, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(151, '213', '2131', 'METILERGOMETRIN MALEAT INJEKSI 0,200 MG - 1 ML', '-', 1435, 14, 2, 9, '1 ml', '{"17":"ANALGETIKA"}', '250 mg', 'ampul', 4, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(152, '214', '2141', 'METILERGOMETRIN MALEAT TABLET SALUT 0,125 MG', '-', 115, 13, 2, 9, 'mg', '{"17":"ANALGETIKA"}', '0,125 mg', 'TABLET', 4, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(153, '215', '2151', 'METILPREDNISOLON TABLET 4 MG', '-', 514, 13, 2, 9, 'MG', '{"22":"ANESTETIKA"}', '4 MG', 'TABLET', 4, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(154, '216', '2161', 'METOKLOPRAMID DROP @ 10 ML', '-', 13976, 12, 2, 9, 'ML', '{"17":"ANALGETIKA"}', '10 ML', 'FLS', 4, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(155, '217', '2171', 'METOKLOPRAMID INJEKSI', '-', 4235, 14, 2, 9, 'ML', '{"17":"ANALGETIKA"}', 'ML', 'ampul', 4, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(156, '218', '2181', 'METOKLOPRAMID SYRUP @ 60 ML.', '-', 5319, 1, 2, 9, 'ML', '{"17":"ANALGETIKA"}', '60 ML', 'BOTOL', 4, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(157, '219', '2191', 'METOKLORPRAMID TABLET 10 MG', '-', 97, 13, 2, 2, 'MG', '{"17":"ANALGETIKA"}', '10 MG', 'TABLET', 4, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(158, '220', '2201', 'METRONIDAZOL TABLET 250 MG', '-', 137, 13, 2, 2, 'MG', '{"17":"ANALGETIKA"}', '250 MG', 'TABLET', 4, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(159, '221', '2211', 'METRONIDAZOL TABLET 500 MG', '-', 200, 13, 2, 2, 'MG', '{"17":"ANALGETIKA"}', '500 MG', 'TABLET', 4, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(160, '222', '2221', 'MIKONAZOL KRIM 2 % @ 10 G', '-', 3000, 12, 2, 9, 'GR', '{"22":"ANESTETIKA"}', '10 GR', 'TUBE', 4, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(161, '223', '2231', 'MULTIVITAMIN ANAK  SYRUP', '-', 8217, 1, 2, 9, 'ML', '{"22":"ANESTETIKA"}', 'ML', 'BOTOL', 4, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(162, '224', '2241', 'MULTIVITAMIN MINERAL DENGAN ZAT BESI TABLET DEWASA', '-', 242, 13, 2, 2, 'GR', '{"17":"ANALGETIKA"}', 'GR', 'TABLET', 4, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(163, '225', '2251', 'MUMMIFYING PASTA', '-', 137853, 1, 2, 6, 'GR', '{"22":"ANESTETIKA"}', 'GR', 'BOTOL', 4, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(164, '226', '2261', 'NACL  0,225 %+ DEXTROSE 5 % ( CAIRAN 1 : 4 ) INFUS @ 500 ML.', '-', 12100, 1, 2, 7, 'ML', '{"22":"ANESTETIKA"}', '500 ML', 'BOTOL', 4, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(165, '227', '2271', 'NATRIUM BIKARBONAT TABLET 500 MG', '-', 10, 13, 2, 2, 'MG', '{"17":"ANALGETIKA"}', '500 MG', 'TABLET', 4, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(166, '228', '2281', 'NATRIUM DIKLOFENAK EMULGEL', '-', 11000, 12, 2, 3, 'GR', '{"17":"ANALGETIKA"}', 'GR', 'TUBE', 4, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(167, '229', '2291', 'NATRIUM DIKLOFENAK TAB. 25 MG', '-', 176, 13, 2, 2, 'MG', '{"17":"ANALGETIKA"}', '25 MG', 'TABLET', 4, 0, 0, 0, 'unverified', '2015-01-15', '2015-01-15', 1),
(168, '230', '2301', 'NATRIUM DIKLOFENAK TAB. 50 MG', '-', 229, 13, 2, 2, 'MG', '{"22":"ANESTETIKA"}', '50 MG', 'TABLET', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(169, '231', '2311', 'NATRIUM HEPARINA OINT @ 15 G', '-', 15400, 12, 2, 3, 'GR', '{"17":"ANALGETIKA"}', '15 G', 'TUBE', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(170, '232', '2321', 'NATRIUM KLORIDA LARUTAN INFUS 0,9 % STERIL @ 500 ML.', '-', 5145, 1, 2, 3, 'ML', '{"22":"ANESTETIKA"}', '500 ML', 'BOTOL', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(171, '233', '2331', 'NATRIUM TIOSULFAT INJEKSI 25 % - 10 ML', '-', 14768, 14, 2, 10, 'ML', '{"17":"ANALGETIKA"}', '10 ML', 'ampul', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(172, '234', '2341', 'NARCOTEST 5 IN 1', '-', 101200, 16, 2, 5, 'ML', '{"17":"ANALGETIKA"}', ' ML', 'PCS', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(173, '235', '2351', 'NEUROTROPIK 5000 VITAMIN INJEKSI', '-', 5500, 14, 2, 10, 'ML', '{"17":"ANALGETIKA"}', 'ML', 'ampul', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(174, '236', '2361', 'NEUROTROPIK VITAMIN TABLET', '-', 443, 13, 2, 2, 'MG', '{"17":"ANALGETIKA"}', 'MG', 'TABLET', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(175, '237', '2371', 'NIFEDIPIN TABLET 10 MG', '-', 149, 13, 2, 2, 'MG', '{"17":"ANALGETIKA"}', 'MG', 'TABLET', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(176, '238', '2381', 'NISTATIN ORAL DROP', '-', 33000, 16, 2, 5, 'MG', '{"17":"ANALGETIKA"}', 'MG', 'FLS', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(177, '239', '2391', 'NISTATIN TABLET SALUT 500.000 IU', '-', 630, 13, 2, 5, 'MG', '{"17":"ANALGETIKA"}', 'MG', 'TABLET', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(178, '240', '2401', 'NISTATIN TABLET VAGINAL 100.000 IU / G', '-', 389, 13, 2, 2, 'G', '{"22":"ANESTETIKA"}', 'G', 'TABLET', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(179, '241', '2411', 'NORIT (CARBO ADSORBEN) TABLET', '-', 225750, 13, 2, 2, 'G', '{"17":"ANALGETIKA"}', 'G', 'TABLET', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(180, '242', '2421', 'OBAT ANTI TUBERKULOSIS KATEGORI ANAK ( FDC )', '-', 225750, 16, 2, 9, 'G', '{"17":"ANALGETIKA"}', 'G', 'PAKET', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(181, '243', '2431', 'OBAT ANTI TUBERKULOSIS KATEGORI I ( FDC )', '-', 377999, 16, 2, 9, 'G', '{"22":"ANESTETIKA"}', 'G', 'PAKET', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(182, '244', '2441', 'OBAT ANTI TUBERKULOSIS KATEGORI II ( FDC )', '-', 1260000, 16, 2, 9, 'G', '{"17":"ANALGETIKA"}', 'G', 'PAKET', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(183, '245', '2451', 'OBAT ANTI TUBERKULOSIS KATEGORI III', '-', 154000, 16, 2, 9, 'G', '{"22":"ANESTETIKA"}', 'G', 'PAKET', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(184, '246', '2461', 'OBAT ANTI TUBERKULOSIS SISIPAN', '-', 66000, 16, 2, 9, 'G', '{"22":"ANESTETIKA"}', 'G', 'PAKET', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(185, '247', '2471', 'OBAT BATUK HITAM ( O B H ) SIRUP 100 ML.', '-', 1502, 1, 2, 5, 'ML', '{"22":"ANESTETIKA"}', '100 ML', 'BOTOL', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(186, '248', '2481', 'OKSITETRASIKLIN HCL INJEKSI I.M 50 MG/ ML - 10 ML', '-', 3255, 9, 2, 2, 'ML', '{"22":"ANESTETIKA"}', '10 ML', 'VIAL', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(187, '249', '2491', 'OKSITETRASIKLIN HCL SALAP 3 % 5 G', '-', 1734, 12, 2, 8, 'G', '{"22":"ANESTETIKA"}', '5G', 'TUBE', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(188, '250', '2501', 'OKSITETRASIKLIN HCL SALAP MATA 1 % @ 3,5 G.', '-', 2016, 12, 2, 8, 'G', '{"17":"ANALGETIKA"}', '3.5G', 'TUBE', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(189, '251', '2511', 'OKSITOKSIN INJ 10 IU / ML-1 ML', '-', 1785, 14, 2, 9, 'ML', '{"17":"ANALGETIKA"}', '1 ML', 'AMPUL', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(190, '252', '2521', 'OMEPRAZOL CAPSUL 20 MG', '-', 423, 7, 2, 9, 'MG', '{"17":"ANALGETIKA"}', '20 MG', 'KAPSUL', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(191, '253', '2531', 'PANCREATIN KOMB.  TABLET', '-', 440, 13, 2, 2, 'G', '{"17":"ANALGETIKA"}', 'G', 'TABLET', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(192, '254', '2541', 'PAPAVERIN TABLET 40 MG', '-', 601, 13, 2, 2, 'MG', '{"22":"ANESTETIKA"}', '40 MG', 'TABLET', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(193, '255', '2551', 'PAPAVERIN INJ 40 MG / ML', '-', 364, 14, 2, 10, 'ML', '{"17":"ANALGETIKA"}', '40 MG', 'AMPUL', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(194, '256', '2561', 'PARASETAMOL DROP', '-', 5040, 1, 2, 9, 'ML', '{"17":"ANALGETIKA"}', 'ML', 'BOTOL', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(195, '257', '2571', 'PARASETAMOL TABLET 500 MG', '-', 110, 13, 2, 2, 'MG', '{"17":"ANALGETIKA"}', '500 MG', 'TABLET', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(196, '258', '2581', 'PARASETAMOL TABLET 100 MG', '-', 44, 13, 2, 2, 'MG', '{"22":"ANESTETIKA"}', '100 MG', 'TABLET', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(197, '259', '2591', 'PARASETAMOL SYR 120MG / 5 ML', '-', 2415, 1, 2, 10, 'ML', '{"22":"ANESTETIKA"}', '120 MG', 'BOTOL', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(198, '260', '2601', 'PIRANTEL TABLET 125 MG', '-', 336, 13, 2, 2, 'MG', '{"22":"ANESTETIKA"}', '125 MG', 'TABLET', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(199, '261', '2611', 'PIRASETAM TABLET 400 MG', '-', 527, 13, 2, 2, 'MG', '{"17":"ANALGETIKA"}', '400 MG', 'TABLET', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(200, '262', '2621', 'PIRASETAM INJ ', '-', 5775, 3, 2, 10, 'ML', '{"22":"ANESTETIKA"}', 'ML', 'AMPUL', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(201, '263', '2631', 'PIRATIAZIN TEOKLAT + PIRIDOKSIN TABLET', '-', 2200, 13, 2, 2, 'G', '{"22":"ANESTETIKA"}', 'G', 'TABLET', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(202, '264', '2641', 'PIRAZINAMID TABLET 500 MG', '-', 220, 13, 2, 2, 'MG', '{"22":"ANESTETIKA"}', '500', 'TABLET', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(203, '265', '2651', 'PIRIDOKSIN  TABLET 10 MG', '-', 18, 13, 2, 2, 'ML', '{"22":"ANESTETIKA"}', '10 MG', 'TABLET', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(204, '266', '2661', 'PIRIDOKSIN INJEKSI 1 ML.', '-', 13, 14, 2, 10, 'ML', '{"17":"ANALGETIKA"}', 'ML', '1 ML', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(205, '267', '2671', 'PIROKSIKAM  CAPSUL 10 MG', '-', 88, 7, 2, 3, 'MG', '{"22":"ANESTETIKA"}', '20 MG', 'KAPSUL', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(206, '268', '2681', 'PIROKSIKAM  TABLET  20 MG', '-', 110, 13, 2, 2, 'MG', '{"22":"ANESTETIKA"}', '20 MG', 'TABLET', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(207, '269', '2691', 'PETHIDIN HCL  50 MG/ML INJEKSI', '-', 11992, 14, 2, 10, 'ML', '{"17":"ANALGETIKA"}', '50 MG/ML', 'AMPUL', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(208, '270', '2701', 'POLIKRESULEN LARUTAN 360 MG/ML @ 10 ML.', '-', 33550, 1, 2, 9, 'ML', '{"22":"ANESTETIKA"}', '360 MG/ML', 'BOTOL', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(209, '271', '2711', 'POVIDON IODIDA 10 %  30 ML', '-', 2520, 1, 2, 9, 'ML', '{"17":"ANALGETIKA"}', '10 ML', 'BOTOL', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(210, '272', '2721', 'POVIDON IODIDA 10 %  300 ML', '-', 13841, 1, 2, 9, 'ML', '{"22":"ANESTETIKA"}', '300 ML', 'BOTOL', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(211, '273', '2731', 'PREDNISON TABLET 5 MG', '-', 66, 13, 2, 2, 'MG', '{"17":"ANALGETIKA"}', '5 MG', 'TABLET', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(212, '274', '2741', 'PRIMAKUIN TABLET 15 MG', '-', 30, 13, 2, 2, 'MG', '{"22":"ANESTETIKA"}', '15 MG', 'TABLET', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(213, '275', '2751', 'PROKAIN BENZIL PENISILLIN INJEKSI 3 JUTA IU ', '-', 375, 9, 2, 9, 'G', '{"22":"ANESTETIKA"}', 'G', 'VIAL', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(214, '276', '2761', 'PROPANOLOL HCL TABLET 40 MG', '-', 82, 13, 2, 2, 'MG', '{"17":"ANALGETIKA"}', '40 MG', 'TABLET', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(215, '277', '2771', 'PROPANOLOL TABLET 10 MG', '-', 58, 13, 2, 2, 'MG', '{"17":"ANALGETIKA"}', '10 MG', 'TABLET', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(216, '278', '2781', 'PROPILTIOURASIL TABLET 100 MG', '-', 315, 13, 2, 2, 'MG', '{"17":"ANALGETIKA"}', '100 MG', 'TABLET', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(217, '279', '2791', 'RANITIDINE TABLET 150 MG', '-', 231, 13, 2, 2, 'MG', '{"22":"ANESTETIKA"}', '150 MG', 'TABLET', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(218, '280', '2801', 'RANITIDINE INJ', '-', 2699, 14, 2, 9, 'ML', '{"17":"ANALGETIKA"}', 'ML', 'AMPUL', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(219, '281', '2811', 'REAGEN PEMERIKSA GOL DARAH ANTI A DAN ANTI B', '-', 84000, 16, 2, 9, 'SET', '{"22":"ANESTETIKA"}', 'MG', 'SET', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(220, '282', '2821', 'RESERPIN TABLET 0,10 MG', '-', 26, 13, 2, 2, 'MG', '{"17":"ANALGETIKA"}', '600 MG', 'TABLET', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(221, '283', '2831', 'RESERPIN TABLET 0,25 MG', '-', 45, 13, 2, 2, 'MG', '{"17":"ANALGETIKA"}', '0.25 MG', 'TABLET', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(222, '284', '2841', 'RIFAMPISIN CAPSUL 300 MG', '-', 334, 7, 2, 3, 'MG', '{"17":"ANALGETIKA"}', '300 MG', 'KAPSUL', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(223, '285', '2851', 'RIFAMPISIN KAPLET  450 MG', '-', 430, 15, 2, 5, 'MG', '{"17":"ANALGETIKA"}', '450 MG', 'KAPLET', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(224, '286', '2861', 'RIFAMPISIN KAPLET  600 MG', '-', 671, 15, 2, 5, 'MG', '{"17":"ANALGETIKA"}', '600 MG', 'KAPLET', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(225, '287', '2871', 'RINGER LAKTAT LARUTAN INFUS STERIL 500 ML', '-', 5697, 1, 2, 9, 'ML', '{"17":"ANALGETIKA"}', '500 ML', 'BOTOL', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(226, '288', '2881', 'SALAP 2 - 4 KOMBINASI : AS.SALISILAT 2 % + BELERANG ENDAP 4 % @ 30 G', '-', 825, 12, 2, 9, 'ML', '{"17":"ANALGETIKA"}', 'ML', 'TUBE', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(227, '289', '2891', 'SALBUTAMOL TABLET 2 MG', '-', 88, 13, 2, 2, 'MG', '{"22":"ANESTETIKA"}', '2 MG', 'TABLET', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(228, '290', '2901', 'SALBUTAMOL TABLET 4 MG', '-', 102, 13, 2, 2, 'MG', '{"17":"ANALGETIKA"}', '4 MG', 'TABLET', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(229, '291', '2911', 'SALISIL BEDAK 2 % @ 50 G', '-', 1502, 4, 2, 1, 'G', '{"17":"ANALGETIKA"}', '50 G', 'BUNGKUS', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(230, '292', '2921', 'SEMEN SENG FOSFAT SERBUK & CAIRAN', '-', 102300, 16, 2, 9, 'ML', '{"22":"ANESTETIKA"}', 'ML', 'SET', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(231, '293', '2931', 'SERUM ANTI BISA ULAR POLIVALEN INJEKSI 5 ML ( ABU I )', '-', 21000, 9, 2, 9, 'ML', '{"22":"ANESTETIKA"}', '5 ML', 'VIAL', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(232, '294', '2941', 'SERUM ANTI DIFTERI INJEKSI 20.000 IU / VIAL  (ADS)', '-', 732848, 9, 2, 9, 'ML', '{"17":"ANALGETIKA"}', 'ML', 'VIAL', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(233, '295', '2951', 'SERUM ANTI TETANUS INJEKSI 1.500 IU / AMPUL (ATS)', '-', 50000, 14, 2, 10, 'ML', '{"17":"ANALGETIKA"}', '1.500 IU', 'AMPUL', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(234, '296', '2961', 'SERUM ANTI TETANUS INJEKSI 20.000 IU / VIAL (ATS)', '-', 451000, 9, 2, 9, 'ML', '{"17":"ANALGETIKA"}', '20000 IU', 'VIAL', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(235, '297', '2971', 'SIANOKOBALAMIN  INJEKSI 500 MCG/ML - 1 ML', '-', 496, 14, 2, 9, 'ML', '{"17":"ANALGETIKA"}', '500 MCG/ML', 'AMPUL', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(236, '298', '2981', 'SIANOKOBALAMIN  TABLET 50 MCG', '-', 14, 13, 2, 2, 'MCG', '{"17":"ANALGETIKA"}', '50 MCG', 'TABLET', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(237, '299', '2991', 'SILVER AMALGAM SERBUK 65 - 75 % 1 OZ', '-', 619300, 1, 2, 9, '1 OZ', '{"22":"ANESTETIKA"}', '65-75 %', 'BOTOL', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(238, '300', '3001', 'SIMVASTATIN TABLET 10 MG', '-', 539, 13, 2, 2, 'MG', '{"22":"ANESTETIKA"}', 'MG', 'TABLET', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(239, '3001', '30011', 'SPONS GELATIN CUBICLES 1 X 1 X 1 CM @ 24 PCS', '-', 8525, 18, 2, 1, '24 PCS', '{"22":"ANESTETIKA"}', 'PCS', 'KOTAK', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(240, '302', '3021', 'STREPTOMISIN INJEKSI 1500 MG / ML @ 15 ML.', '-', 54840, 9, 2, 9, 'ML', '{"22":"ANESTETIKA"}', '1500 MG/ML', 'VIAL', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(241, '303', '3031', 'TEMPORARY STOPPING FLETCHER  SERBUK 100 G. & CAIRAN', '-', 35200, 16, 2, 9, '100 G', '{"22":"ANESTETIKA"}', 'CAIRAN', 'SET', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(242, '304', '3041', 'TEOFILIN TABLET 10 MG', '-', 57, 13, 2, 2, 'MG', '{"17":"ANALGETIKA"}', '10 MG', 'TABLET', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(243, '305', '3051', 'TERBUTALIN TABLET 2,5 MG', '-', 2035, 13, 2, 2, 'MG', '{"17":"ANALGETIKA"}', '2,5 MG', 'TABLET', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(244, '306', '3061', 'TETRASIKLIN HCL KAPSUL 500 MG', '-', 177, 7, 2, 3, 'MG', '{"17":"ANALGETIKA"}', '500 MG', 'KAPSUL', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(245, '307', '3071', 'TIAMFENIKOL CAPSUL 500 MG', '-', 409, 7, 2, 3, 'MG', '{"22":"ANESTETIKA"}', '500 MG', 'KAPSUL', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(246, '308', '3081', 'TIAMIN HCL  INJEKSI 100 MG / ML - 1 ML', '-', 735, 14, 2, 9, 'ML', '{"22":"ANESTETIKA"}', '100 MG', 'AMPUL', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(247, '309', '3091', 'TIAMIN HCL  TABLET 50 MG', '-', 40, 13, 2, 2, 'MG', '{"22":"ANESTETIKA"}', '50 MG', 'TABLET', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(248, '310', '3101', 'TRAMADOL CAPSUL 50 MG', '-', 347, 7, 2, 3, 'MG', '{"22":"ANESTETIKA"}', '50 MG', 'KAPSUL', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(249, '311', '3111', 'TRAMADOL INJEKSI', '-', 6403, 14, 2, 10, 'ML', '{"17":"ANALGETIKA"}', 'ML', 'AMPUL', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(250, '312', '3121', 'TRIHEKSIFENIDIL HIDROKLORIDA TAB 2 MG ', '-', 100, 13, 2, 2, 'MG', '{"17":"ANALGETIKA"}', '2 MG', 'TABLET', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(251, '313', '3131', 'TRIKRESOL FORMALIN (TKF)', '-', 88000, 1, 2, 9, 'ML', '{"17":"ANALGETIKA"}', 'ML', 'BOTOL', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(252, '314', '3141', 'TRIPOLIDIN + PSEUDOEFEDRIN TABLET', '-', 462, 13, 2, 2, 'G', '{"22":"ANESTETIKA"}', 'G', 'TABLET', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(253, '315', '3151', 'TUMPATAN COMPOSITE (LIGHT CURED)', '-', 1522400, 16, 2, 3, 'G', '{"17":"ANALGETIKA"}', 'G', 'SET', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(254, '316', '3161', 'TUMPATAN SEMENTARA', '-', 442200, 18, 2, 1, 'G', '{"22":"ANESTETIKA"}', 'G', 'KOTAK', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(255, '317', '3171', 'VITAMIN B-KOMPLEKS TABLET', '-', 23, 13, 2, 2, 'G', '{"17":"ANALGETIKA"}', 'G', 'TABLET', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(256, '318', '3181', 'ZINC ', '-', 499, 13, 2, 2, 'G', '{"22":"ANESTETIKA"}', 'G', 'TABLET', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(257, '319', '3191', 'I.V. CATHETER NOMOR 18', '-', 7700, 4, 2, 1, 'G', '{"17":"ANALGETIKA"}', 'G', 'PCS', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(258, '320', '3201', 'I.V. CATHETER NOMOR 20 ', '-', 12100, 4, 2, 1, 'G', '{"22":"ANESTETIKA"}', 'G', 'PCS', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(259, '321', '3211', 'I.V. CATHETER NOMOR 22', '-', 12100, 4, 2, 1, 'G', '{"17":"ANALGETIKA"}', 'G', 'PCS', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(260, '322', '3221', 'I.V. CATHETER NOMOR 24', '-', 12100, 4, 2, 1, 'G', '{"22":"ANESTETIKA"}', 'G', 'PCS', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(261, '323', '3231', 'ALAT SUNTIK SEKALI PAKAI 1 ML', '-', 3727, 3, 2, 10, 'ML', '{"22":"ANESTETIKA"}', '1 ML', 'PCS', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(262, '324', '3241', 'ALAT SUNTIK SEKALI PAKAI 2,5 ML', '-', 1606, 3, 2, 10, 'ML', '{"22":"ANESTETIKA"}', '2,5 ML', 'PCS', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(263, '325', '3251', 'ALAT SUNTIK SEKALI PAKAI 5 ML', '-', 1815, 3, 2, 10, 'ML', '{"22":"ANESTETIKA"}', '5 ML', 'PCS', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(264, '326', '3261', 'BISTURY / MATA PISAU BEDAH', '-', 398, 3, 2, 1, 'BUAH', '{"22":"ANESTETIKA"}', 'SET', 'PCS', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(265, '327', '3271', 'BLOOD LANCET', '-', 282, 1, 2, 10, 'ML', '{"17":"ANALGETIKA"}', 'ML', 'PCS', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(266, '328', '3281', 'OBJECT GLASS / SLIDE GLASS', '-', 31625, 18, 2, 9, 'SET', '{"22":"ANESTETIKA"}', 'SET', 'KOTAK', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(267, '329', '3291', 'CAT GUT / BENANG BEDAH NO. 2/0 DENGAN JARUM BEDAH', '-', 8708, 3, 2, 13, 'SET', '{"17":"ANALGETIKA"}', 'SET', 'PCS', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(268, '330', '3301', 'CAT GUT / BENANG BEDAH NO. 3/0 DENGAN JARUM BEDAH', '-', 8708, 3, 2, 11, 'SET', '{"17":"ANALGETIKA"}', 'SET', 'PCS', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1);
INSERT INTO `master_obat` (`idobat`, `kode_obat`, `no_reg`, `nama_obat`, `nama_merek`, `harga`, `idsatuan_obat`, `idgolongan`, `idbentuk_sediaan`, `nama_bentuk`, `farmakologi`, `komposisi`, `kemasan`, `idprodusen`, `stock`, `min_stock`, `max_stock`, `status_obat`, `date_added`, `date_modified`, `enabled`) VALUES
(269, '331', '3311', 'DECT GLASS / COVER GLASS', '-', 140, 3, 2, 11, 'SET', '{"22":"ANESTETIKA"}', 'SET', 'PCS', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(270, '332', '3321', 'ELASTIS VERBANB 4 INCI ( GENERAL CARE )', '-', 22500, 4, 2, 9, 'SET', '{"17":"ANALGETIKA"}', 'ROLL', 'ROLL', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(271, '333', '3331', 'FILTER PAPER ', '-', 1150, 3, 2, 11, 'SET', '{"17":"ANALGETIKA"}', 'PCS', 'PCS', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(272, '334', '3341', 'FOLLEY CATHETER 2. W 30 CC NO.14', '-', 6563, 3, 1, 9, 'SET', '{"22":"ANESTETIKA"}', 'SET', 'PCS', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(273, '335', '3351', 'FOLLEY CATHETER 2. W 30 CC NO.16', '-', 6563, 3, 2, 9, 'SET', '{"17":"ANALGETIKA"}', 'SET', 'PCS', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(274, '336', '3361', 'FOLLEY CATHETER 2. W 30 CC NO.18', '-', 6563, 3, 2, 9, 'SET', '{"22":"ANESTETIKA"}', 'SET', 'PCS', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(275, '337', '3371', 'HANDSCOON  NO.6,5', '-', 1540, 3, 2, 9, 'PASANG', '{"22":"ANESTETIKA"}', 'SET', 'PASANG', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(276, '338', '3381', 'HANDSCOON  NO.6,5 STERIL', '-', 1540, 3, 2, 9, 'SET', '{"22":"ANESTETIKA"}', 'SET', 'PASANG', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(277, '339', '3391', 'HANDSCOON  NO.7', '-', 1738, 3, 2, 9, 'SET', '{"22":"ANESTETIKA"}', 'SET', 'PASANG', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(278, '340', '3401', 'HANDSCOON  NO.7 STERIL', '-', 1540, 3, 2, 9, 'SET', '{"22":"ANESTETIKA"}', 'SET', 'PASANG', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(279, '341', '3411', 'HANDSCOON  NO.7.5 ( SENSI GLOVES )', '-', 1738, 3, 2, 9, 'SET', '{"17":"ANALGETIKA"}', 'SET', 'PASANG', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(280, '342', '3421', 'HANDSCOON  NO.7.5 STERIL', '-', 7900, 3, 2, 9, 'SET', '{"22":"ANESTETIKA"}', 'SET', 'PASANG', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(281, '343', '3431', 'INFUS SET ANAK', '-', 17600, 14, 2, 9, 'SET', '{"17":"ANALGETIKA"}', 'SET', 'SET', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(282, '344', '3441', 'INFUS SET DEWASA', '-', 17600, 3, 2, 9, 'SET', '{"22":"ANESTETIKA"}', 'SET', 'SET', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(283, '345', '3451', 'KAPAS BERLEMAK 500 GRAM', '-', 5250, 3, 2, 1, 'ROL', '{"17":"ANALGETIKA"}', 'ROL', 'ROL', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(284, '346', '3461', 'KAPAS PEMBALUT / ABSORBEN 250 GRAM', '-', 33000, 3, 2, 9, 'ROLL', '{"17":"ANALGETIKA"}', 'ROLL', 'ROLL', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(285, '347', '3471', 'KASA KOMPRES 40 / 40 STERIL', '-', 2750, 4, 2, 9, 'BUAH', '{"17":"ANALGETIKA"}', 'BUNGKUD', 'BUNGKUS', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(286, '348', '3481', 'KASA HIDROFIL STERIL 18 X 22 CM (ISI 12 LBR)', '-', 4988, 3, 2, 9, 'BUAH', '{"17":"ANALGETIKA"}', 'BUNGKUS', 'BUNGKUS', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(287, '349', '3491', 'KASA PEMBALUT 2 M X 80 CM', '-', 11000, 3, 2, 9, 'BUAH', '{"22":"ANESTETIKA"}', 'BUNGKUS', 'BUNGKUS', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(288, '350', '3501', 'KASA PEMBALUT 16 X 16 STERIL', '-', 11000, 3, 2, 9, 'KOTAK', '{"17":"ANALGETIKA"}', 'KOTAK', 'KOTAK', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(289, '351', '3511', 'KASA PEMBALUT HIDROFIL 4 M X 10 CM', '-', 3520, 3, 2, 9, 'ROLL', '{"17":"ANALGETIKA"}', 'ROLL', 'ROLL', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(290, '352', '3521', 'KASA PEMBALUT HIDROFIL 4 M X 5 CM', '-', 1760, 3, 2, 9, 'ROLL', '{"17":"ANALGETIKA"}', 'ROLL', 'ROLL', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(291, '353', '3531', 'KASA PEMBALUT HIDROFIL 4 M X 15 CM', '-', 4950, 3, 2, 9, 'ROLL', '{"17":"ANALGETIKA"}', 'ROLL', 'ROLL', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(292, '354', '3541', 'KASA PEMBALUT HIDROFIL 4 M X 3 CM', '-', 1650, 3, 2, 9, 'ROLL', '{"17":"ANALGETIKA"}', 'ROLL', 'ROLL', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(293, '355', '3551', 'KATETER NELATON NO. 14', '-', 15000, 3, 2, 1, 'PCS', '{"22":"ANESTETIKA"}', 'PCS', 'PCS', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(294, '356', '3561', 'KATETER NELATON NO. 16', '-', 15000, 3, 2, 9, 'PCS', '{"17":"ANALGETIKA"}', 'PCS', 'PCS', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(295, '357', '3571', 'KATETER NELATON NO. 18', '-', 15000, 3, 2, 9, 'PCS', '{"17":"ANALGETIKA"}', 'PCS', 'PCS', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(296, '358', '3581', 'LAMPU SPIRITUS', '-', 57500, 3, 2, 11, 'PCS', '{"17":"ANALGETIKA"}', 'PCS', 'PCS', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(297, '359', '3591', 'MASKER DISPOSIBLE 3 PLY ( DIAPRO )', '-', 341, 3, 2, 11, 'PCS', '{"17":"ANALGETIKA"}', 'PCS', 'PCS', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(298, '360', '3601', 'MASKER N95 ', '-', 28750, 3, 2, 9, 'PCS', '{"17":"ANALGETIKA"}', 'PCS', 'PCS', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(299, '361', '3611', 'NEEDLE TERUMO 23 G', '-', 825, 3, 2, 9, 'PCS', '{"22":"ANESTETIKA"}', 'PCS', 'PCS', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(300, '362', '3621', 'NEEDLE TERUMO 24 G', '-', 825, 3, 2, 9, 'PCS', '{"17":"ANALGETIKA"}', 'PCS', 'PCS', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(301, '363', '3631', 'NEEDLE TERUMO 25 G', '-', 825, 3, 2, 9, 'PCS', '{"17":"ANALGETIKA"}', 'PCS', 'PCS', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(302, '364', '3641', 'PEMBALUT GIPS', '-', 5600, 3, 2, 9, 'ROLL', '{"17":"ANALGETIKA"}', 'ROLL', 'ROLL', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(303, '365', '3651', 'PLESTER 5 YARDS X 2 INCI', '-', 12100, 3, 2, 9, 'ROLL', '{"17":"ANALGETIKA"}', 'ROLL', 'ROL', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(304, '366', '3661', 'PLESTER TAHAN AIR ( 25 CM X 10 CM )', '-', 22000, 3, 2, 9, 'BUAH', '{"17":"ANALGETIKA"}', 'BUNGKUS', 'BUNGKUS', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(305, '367', '3671', 'POT SPUTUM', '-', 3795, 3, 2, 9, 'BUAH', '{"17":"ANALGETIKA"}', 'BUAH', 'BUAH', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(306, '368', '3681', 'RAPID TEST FOR MALARIA', '-', 47410, 3, 2, 9, 'BUAH', '{"17":"ANALGETIKA"}', 'BUAH', 'BUAH', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(307, '369', '3691', 'RAPID TEST FOR DENGUE IGG/IGM CASSETTE', '-', 159280, 3, 2, 9, 'BUAH', '{"17":"ANALGETIKA"}', 'BUAH', 'BUAH', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(308, '370', '3701', 'S I L K  ( BENANG BEDAH SUTERA ) NO. 3.0  75 CM DENGAN JARUM BEDAH', '-', 5500, 3, 2, 9, 'BUAH', '{"22":"ANESTETIKA"}', 'BUAH', 'BUAH', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(309, '371', '3711', 'TABUNG HEMATOKRIT ( ASSISTANT )', '-', 1725, 3, 2, 9, 'BUAH', '{"17":"ANALGETIKA"}', 'BUAH', 'BUAH', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(310, '372', '3721', 'TABUNG WESTERGREEN', '-', 5895, 3, 2, 9, 'BUAH', '{"17":"ANALGETIKA"}', 'BUAH', 'BUAH', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(311, '373', '3731', 'TABUNG REAKSI PURPLE CAP, EDTA VOL 3 ML', '-', 2277, 3, 1, 9, 'BUAH', '{"22":"ANESTETIKA"}', 'BUAH', 'BUAH', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(312, '374', '3741', 'TES KEHAMILAN', '-', 9460, 3, 2, 9, 'BUAH', '{"17":"ANALGETIKA"}', 'BUAH', 'BUAH', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(313, '375', '3751', 'TEST STRIP URINALYSA 10 PARAMETER', '-', 313500, 3, 2, 9, 'B', '{"17":"ANALGETIKA"}', 'BUAH', 'BUAH', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(314, '376', '3761', 'URINE BAG', '-', 3410, 3, 2, 9, 'BUAH', '{"22":"ANESTETIKA"}', 'BUAH', 'BUAH', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(315, '121', '121', 'FRAMISETIN SULFAT GAUZE DRESSING 10 CM X 10 CM', '-', 8800, 8, 4, 11, '-', '{"27":"ANTI HIPERTENSI"}', 'Framisetin sulfat gauze dressing 10 cm x 10 cm', 'Lembat', 1, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(316, '122', '122', 'FUROSEMID INJEKSI 10 MG/ML @ 2 ML.', '-', 1932, 14, 1, 11, '-', '{"27":"ANTI HIPERTENSI"}', 'Furosemid injeksi 10 mg/ml @ 2 ml.', 'Ampul', 3, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(317, '123', '123', 'FUROSEMID TABLET 40 MG', '-', 107, 13, 4, 8, '-', '{"22":"ANESTETIKA"}', 'Furosemid tablet 40 mg', 'Tablet', 2, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(318, '124', '124', 'GAMEKSAN EMULSI 1 % ', '-', 4400, 1, 1, 10, '-', '{"27":"ANTI HIPERTENSI"}', 'Gameksan Emulsi 1 % ', 'Botol', 5, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(319, '125', '125', 'GARAM ORALIT  UNTUK 200 ML AIR', '-', 399, 19, 1, 5, '-', '{"27":"ANTI HIPERTENSI"}', 'alit  untuk 200 ml air', 'Sachet', 3, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(320, '126', '126', 'GEMFIBROZIL CAPSUL 300 MG', '-', 366, 7, 3, 8, '-', '{"8":"ANTI TBC"}', 'Gemfibrozil capsul 300 mg', 'kapsul', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(321, '127', '127', 'GEMFIBROZIL CAPSUL 600 MG', '-', 546, 7, 3, 3, '-', '{"27":"ANTI HIPERTENSI"}', 'Gemfibrozil capsul 600 mg', 'Kapsul', 3, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(322, '128', '128', 'GENTAMISIN INJEKSI 80 MG', '-', 3528, 9, 2, 5, '-', '{"8":"ANTI TBC"}', 'Gentamisin injeksi 80 mg', 'Vial', 2, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(323, '129', '129', 'GENTAMISIN SALEP KULIT 5 G', '-', 1929, 12, 1, 8, '-', '{"10":"ANTASIDA DAN ANTIULCER"}', 'Gentamisin salep kulit 5 g', 'Tube', 3, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(324, '130', '130', 'GENTAMISIN SULFAT TETES MATA 0.3%', '-', 3465, 1, 4, 10, '-', '{"27":"ANTI HIPERTENSI"}', 'Gentamisin Sulfat tetes mata 0.3%', 'Botol', 3, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(325, '131', '131', 'GENTIAN VIOLET LARUTAN 1 % 10 ML', '-', 473, 1, 1, 10, '-', '{"10":"ANTASIDA DAN ANTIULCER"}', 'Gentian Violet larutan 1 % 10 ml', 'Botol', 3, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(326, '132', '132', 'GLASS IONOMER SEMEN (SERBUK + LARUTAN)', '-', 4140000, 16, 2, 5, '-', '{"8":"ANTI TBC"}', 'Glass Ionomer semen (serbuk + larutan)', 'Set', 3, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(327, '133', '133', 'GLIBENKLAMID TABLET 5 MG', '-', 76, 13, 2, 8, '-', '{"4":"ANTHELMETIKA"}', 'Glibenklamid tablet 5 mg', 'Tablet', 3, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(328, '134', '134', 'GLIMEPIRIDE TABLET 1 MG', '-', 914, 13, 1, 8, '-', '{"27":"ANTI HIPERTENSI"}', 'Glimepiride tablet 1 mg', 'Tablet', 2, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(329, '135', '135', 'GLISERIL GUAYAKOLAT TABLET 100 MG', '-', 29, 13, 2, 8, '-', '{"27":"ANTI HIPERTENSI"}', 'Gliseril Guayakolat tablet 100 mg', 'Tablet', 3, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(330, '136', '136', 'GLISERIN CAIRAN 100 ML', '-', 3990, 1, 1, 8, '-', '{"27":"ANTI HIPERTENSI"}', 'Gliserin cairan 100 ml', 'Botol', 3, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(331, '137', '137', 'GLUKOSA LARUTAN INFUS    5 % STERIL  500 ML', '-', 5313, 1, 1, 10, '-', '{"27":"ANTI HIPERTENSI"}', 'Glukosa Larutan infus    5 % steril  500 ml', 'Botol', 2, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(332, '138', '138', 'GLUKOSA LARUTAN INFUS  40 % STERIL   ', '-', 1525, 1, 2, 8, '-', '{"4":"ANTHELMETIKA"}', 'Glukosa Larutan infus  40 % steril   ', 'Botol', 3, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(333, '139', '139', 'GLUKOSA LARUTAN INFUS 10 %  STERIL  500 ML.', '-', 4100, 1, 2, 11, '-', '{"27":"ANTI HIPERTENSI"}', 'Glukosa Larutan infus 10 %  steril  500 ml.', 'Botol', 3, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(334, '140', '140', 'GRISEOFULVIN TABLET 125 MG, MICRONIZED', '-', 252, 1, 4, 8, '-', '{"27":"ANTI HIPERTENSI"}', 'Griseofulvin tablet 125 mg, micronized', 'Botol', 3, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(335, '141', '141', 'HALOPERIDOL TABLET 1,5 MG', '-', 83, 1, 2, 8, '-', '{"27":"ANTI HIPERTENSI"}', 'Haloperidol tablet 1,5 mg', 'Botol', 2, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(336, '142', '142', 'HIDROKLORTIAZIDA  TABLET 25 MG', '-', 17, 1, 3, 8, '-', '{"27":"ANTI HIPERTENSI"}', 'Hidroklortiazida  tablet 25 mg', 'Botol', 3, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(337, '143', '143', 'HIDROKORTISON KRIM 2,5 % @ 5G.', '-', 3128, 12, 2, 8, '-', '{"4":"ANTHELMETIKA"}', 'Hidrokortison krim 2,5 % @ 5g.', 'Tube', 3, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(338, '144', '144', 'HYOSCINE-N-BUTILBROMIDE TABLET 10 MG', '-', 315, 13, 3, 8, '-', '{"27":"ANTI HIPERTENSI"}', 'Hyoscine-N-Butilbromide tablet 10 mg', 'Tablet', 2, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(339, '145', '145', 'IBUPROFEN TABLET 200 MG', '-', 110, 13, 1, 8, '-', '{"27":"ANTI HIPERTENSI"}', 'Ibuprofen tablet 200 mg', 'Tablet', 3, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(340, '146', '146', 'IBUPROFEN TABLET 400 MG', '-', 189, 13, 2, 8, '-', '{"10":"ANTASIDA DAN ANTIULCER"}', 'Ibuprofen tablet 400 mg', 'Tablet', 3, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(341, '147', '147', 'IBUPROFEN SUSPENSI 100 MG / 5 ML', '-', 3675, 1, 2, 11, '-', '{"4":"ANTHELMETIKA"}', 'Ibuprofen Suspensi 100 mg / 5 ml', 'Botol', 2, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(342, '148', '148', 'ICHTYOL ( SALAP HITAM )', '-', 4802, 10, 1, 11, '-', '{"27":"ANTI HIPERTENSI"}', 'Ichtyol ( Salap Hitam )', 'Pot', 1, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(343, '149', '149', 'ISOKSUPRINA HCL TAB. 20 MG', '-', 4070, 13, 1, 8, '-', '{"8":"ANTI TBC"}', 'Isoksuprina HCl tab. 20 mg', 'Tablet', 3, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(344, '150', '150', 'ISONIAZID TABLET 300 MG', '-', 66, 13, 1, 5, '-', '{"4":"ANTHELMETIKA"}', 'Isoniazid tablet 300 mg', 'Tablet', 3, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(345, '151', '151', 'ISOSORBID DINITRAT TABLET SUBLINGUAL 5 MG', '-', 85, 13, 1, 10, '-', '{"10":"ANTASIDA DAN ANTIULCER"}', 'Isosorbid Dinitrat tablet sublingual 5 mg', 'Tablet', 1, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(346, '152', '152', 'KALIUM DIKLOFENAK 25 MG TABLET', '-', 85, 13, 2, 11, '-', '{"4":"ANTHELMETIKA"}', 'Kalium diklofenak 25 mg tablet', 'Tablet', 3, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(347, '153', '153', 'KALIUM DIKLOFENAK 50 MG TABLET', '-', 85, 13, 1, 3, '-', '{"27":"ANTI HIPERTENSI"}', 'Kalium diklofenak 50 mg tablet', 'Tablet', 2, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(348, '154', '154', 'KALIUM PERMANGANAT SERBUK 5 G.', '-', 3000, 10, 2, 10, '-', '{"27":"ANTI HIPERTENSI"}', 'Kalium Permanganat serbuk 5 g.', 'Pot', 3, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(349, '155', '155', 'KALSIUM HIDROKSIDA PASTA @ 2 TUBE', '-', 330000, 16, 4, 8, '-', '{"27":"ANTI HIPERTENSI"}', 'Kalsium Hidroksida pasta @ 2 tube', 'Set', 2, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(350, '156', '156', 'KALSIUM LAKTAT TABLET 500 MG', '-', 55, 13, 2, 8, '-', '{"27":"ANTI HIPERTENSI"}', 'Kalsium Laktat tablet 500 mg', 'Tablet', 3, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(351, '157', '157', 'KANAMYCIN INJEKSI 1 GRAM', '-', 11000, 9, 2, 11, '-', '{"27":"ANTI HIPERTENSI"}', 'Kanamycin injeksi 1 gram', 'Vial', 2, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(352, '158', '158', 'KAOLIN PEKTIN SIRUP', '-', 4730, 1, 2, 8, '-', '{"27":"ANTI HIPERTENSI"}', 'Kaolin Pektin sirup', 'Botol', 2, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(353, '159', '159', 'KAPTOPRIL TABLET 12,5 MG', '-', 95, 13, 1, 11, '-', '{"27":"ANTI HIPERTENSI"}', 'Kaptopril tablet 12,5 mg', 'Tablet', 3, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(354, '160', '160', 'KAPTOPRIL TABLET 25 MG', '-', 145, 13, 1, 11, '-', '{"27":"ANTI HIPERTENSI"}', 'Kaptopril tablet 25 mg', 'Tablet', 3, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(355, '161', '161', 'KARBAMAZEPIN TABLET 200 MG', '-', 246, 13, 2, 11, '-', '{"27":"ANTI HIPERTENSI"}', 'Karbamazepin tablet 200 mg', 'Tablet', 3, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(356, '162', '162', 'KETOKONAZOL KRIM 2 % @ 10 G.', '-', 7349, 18, 1, 5, '-', '{"27":"ANTI HIPERTENSI"}', 'm 2 % @ 10 g.', 'Kotal', 3, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(357, '163', '163', 'KETOKONAZOL TABLET 200 MG', '-', 431, 13, 1, 3, '-', '{"10":"ANTASIDA DAN ANTIULCER"}', 'Ketokonazol tablet 200 mg', 'Tablet', 2, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(358, '164', '164', 'KETOPROFEN TABLET 100 MG', '-', 1417, 13, 1, 11, '-', '{"27":"ANTI HIPERTENSI"}', 'Ketoprofen tablet 100 mg', 'Tablet', 3, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(359, '165', '165', 'KETOPROFEN INJEKSI 100 MG @ 1 ML', '-', 5933, 14, 1, 11, '-', '{"8":"ANTI TBC"}', 'Ketoprofen injeksi 100 mg @ 1 ml', 'Ampul', 1, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(360, '166', '166', 'KETOROLAC 30 MG/ML INJEKSI', '-', 14033, 14, 1, 8, '-', '{"4":"ANTHELMETIKA"}', 'Ketorolac 30 mg/ml injeksi', 'Ampul', 2, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(361, '167', '167', 'KLORAMFENIKOL CAPSUL 250 MG ', '-', 146, 7, 1, 10, '-', '{"27":"ANTI HIPERTENSI"}', 'Kloramfenikol capsul 250 mg', 'Kapsul', 4, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(362, '168', '168', 'KLORAMFENIKOL SALAP MATA 1 %  @ 3,5 G.', '-', 1640, 12, 1, 11, '-', '{"27":"ANTI HIPERTENSI"}', 'Kloramfenikol salap mata 1 %  @ 3,5 g.', 'Tube', 3, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(363, '169', '169', 'KLORAMFENIKOL SUSPENSI 125 MG / 5 ML. @ 60 ML.', '-', 4725, 1, 1, 11, '-', '{"27":"ANTI HIPERTENSI"}', 'Kloramfenikol suspensi 125 mg / 5 ml. @ 60 ml.', 'Botol', 2, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(364, '170', '170', 'KLORAMFENIKOL TETES MATA  0,5 % ', '-', 5155, 1, 2, 13, '-', '{"4":"ANTHELMETIKA"}', 'Kloramfenikol tetes mata  0,5 % ', 'Botol', 3, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(365, '171', '171', 'KLORAMFENIKOL TETES TELINGA 3 %  @ 5 ML', '-', 1680, 1, 1, 11, '-', '{"4":"ANTHELMETIKA"}', 'Kloramfenikol tetes telinga 3 %  @ 5 ml', 'Botol', 2, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(366, '172', '172', 'KLORFENIRAMIN MALEAT ( CTM ) TABLET 4 MG', '-', 25, 13, 1, 11, '-', '{"17":"ANALGETIKA"}', 'Klorfeniramin Maleat ( CTM ) tablet 4 mg', 'Tablet', 2, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(367, '173', '173', 'KLORFENOL KAMFER MENTOL ( CHKM ) CAIRAN 10 ML.', '-', 55000, 1, 2, 11, '-', '{"27":"ANTI HIPERTENSI"}', 'Klorfenol Kamfer Mentol ( CHKM ) cairan 10 ml.', 'Botol', 3, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(368, '174', '174', 'KLOROKUIN TABLET 150 MG', '-', 58, 13, 1, 8, '-', '{"4":"ANTHELMETIKA"}', 'Klorokuin tablet 150 mg', 'Tablet', 2, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(369, '175', '175', 'KLORPROMAZIN HCL INJEKSI 25 MG / ML - 1 ML', '-', 418, 14, 2, 11, '-', '{"4":"ANTHELMETIKA"}', 'Klorpromazin HCl injeksi 25 mg / ml - 1 ml', 'Ampul', 2, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(370, '176', '176', 'KLORPROMAZIN HCL TABLET SALUT   25 MG', '-', 24, 13, 1, 11, '-', '{"4":"ANTHELMETIKA"}', 'Klorpromazin HCl tablet salut   25 mg', 'Tablet', 2, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(371, '177', '177', 'KLORPROMAZIN HCL TABLET SALUT 100 MG', '-', 79, 13, 1, 11, '-', '{"4":"ANTHELMETIKA"}', 'Klorpromazin HCl tablet salut 100 mg', 'Tablet', 2, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(372, '178', '178', 'KLORPROPAMID TABLET 100 MG', '-', 50, 13, 1, 11, '-', '{"4":"ANTHELMETIKA"}', 'Klorpropamid tablet 100 mg', 'Tablet', 2, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(373, '179', '179', 'KODEIN FOSFAT TABLET 10 MG', '-', 388, 13, 1, 11, '-', '{"27":"ANTI HIPERTENSI"}', 'Kodein Fosfat tablet 10 mg', 'Tablet', 3, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(374, '180', '180', 'KOTRIMOKSAZOL SUSP. KOMB. : SULFAMETOKSAZOL 200 MG. + TRIMETOPRIM 40 MG/5ML', '-', 4158, 1, 2, 8, '-', '{"27":"ANTI HIPERTENSI"}', 'Kotrimoksazol susp. komb. : Sulfametoksazol 200 mg. + Trimetoprim 40 mg/5ml', 'Botol', 3, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(375, '181', '181', 'KOTRIMOKSAZOL TAB DEWASA KOMB : SULFAMETOKSAZOL 400 MG+TRIMETOPRIM 80 MG', '-', 173, 1, 1, 11, '-', '{"27":"ANTI HIPERTENSI"}', 'Kotrimoksazol tab dewasa komb : Sulfametoksazol 400 mg+Trimetoprim 80 mg', 'Botol', 2, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(376, '182', '182', 'KOTRIMOKSAZOL TAB PEDIATRIK KOMB : SULFAMETOKSAZOL 100 MG+TRIMETOPRIM 20 MG', '-', 50, 1, 1, 11, '-', '{"4":"ANTHELMETIKA"}', 'Kotrimoksazol tab pediatrik komb : Sulfametoksazol 100 mg+Trimetoprim 20 mg', 'Botol', 2, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(377, '183', '183', 'KUININ ( KINA ) TABLET 200 MG', '-', 250, 13, 1, 11, '-', '{"27":"ANTI HIPERTENSI"}', 'Kuinin ( Kina ) tablet 200 mg', 'Tablet', 2, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(378, '184', '184', 'KUININ INJEKSI IV 25 % SEBAGAI 2HCL - 2 ML', '-', 3350, 14, 3, 11, '-', '{"4":"ANTHELMETIKA"}', 'Kuinin Injeksi IV 25 % sebagai 2HCl - 2 ml', 'Ampul', 3, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(379, '185', '185', 'LACTOBACILLUS', '-', 3788, 19, 1, 11, '-', '{"27":"ANTI HIPERTENSI"}', 'Lactobacillus', 'Sachet', 2, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(380, '186', '186', 'LANZOPRAZOL KAPSUL 30 MG', '-', 1577, 15, 2, 11, '-', '{"27":"ANTI HIPERTENSI"}', 'Lanzoprazol Kapsul 30 mg', 'Kaplet', 3, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(381, '187', '187', 'LARUTAN ANISOL', '-', 230000, 1, 1, 11, '-', '{"27":"ANTI HIPERTENSI"}', 'Larutan Anisol', 'Botol', 3, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1),
(382, '188', '188', 'LARUTAN ASAM SULFOSALISILAT 20 % 100 ML.', '-', 657800, 1, 1, 13, '-', '{"4":"ANTHELMETIKA"}', 'Larutan Asam sulfosalisilat 20 % 100 ml.', 'Larutan Asam sulfosalisilat 20 % 100 ml.', 1, 0, 0, 0, 'unverified', '2015-01-16', '2015-01-16', 1);

-- --------------------------------------------------------

--
-- Table structure for table `master_obat_puskesmas`
--

CREATE TABLE IF NOT EXISTS `master_obat_puskesmas` (
  `idobat_puskesmas` int(11) NOT NULL AUTO_INCREMENT,
  `idobat` int(11) NOT NULL,
  `idpuskesmas` int(11) NOT NULL,
  `kode_obat` varchar(10) COLLATE latin1_general_ci NOT NULL,
  `no_reg` varchar(75) COLLATE latin1_general_ci NOT NULL,
  `nama_obat` varchar(150) COLLATE latin1_general_ci NOT NULL,
  `nama_merek` varchar(150) COLLATE latin1_general_ci NOT NULL,
  `harga` mediumint(9) NOT NULL,
  `idsatuan_obat` tinyint(4) NOT NULL,
  `idgolongan` tinyint(4) NOT NULL,
  `idbentuk_sediaan` smallint(6) NOT NULL,
  `nama_bentuk` varchar(75) COLLATE latin1_general_ci NOT NULL,
  `farmakologi` varchar(200) COLLATE latin1_general_ci NOT NULL,
  `komposisi` varchar(100) COLLATE latin1_general_ci NOT NULL,
  `kemasan` varchar(100) COLLATE latin1_general_ci NOT NULL,
  `idprodusen` mediumint(9) NOT NULL,
  `stock` int(11) NOT NULL,
  `stock_dinas` int(11) NOT NULL,
  `min_stock` int(11) NOT NULL,
  `max_stock` int(11) NOT NULL,
  `status_obat` enum('unverified','verified') COLLATE latin1_general_ci NOT NULL DEFAULT 'unverified',
  PRIMARY KEY (`idobat_puskesmas`),
  KEY `idpuskesmas` (`idpuskesmas`),
  KEY `idobat` (`idobat`),
  KEY `kode_obat` (`kode_obat`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=376 ;

--
-- Dumping data for table `master_obat_puskesmas`
--

INSERT INTO `master_obat_puskesmas` (`idobat_puskesmas`, `idobat`, `idpuskesmas`, `kode_obat`, `no_reg`, `nama_obat`, `nama_merek`, `harga`, `idsatuan_obat`, `idgolongan`, `idbentuk_sediaan`, `nama_bentuk`, `farmakologi`, `komposisi`, `kemasan`, `idprodusen`, `stock`, `stock_dinas`, `min_stock`, `max_stock`, `status_obat`) VALUES
(1, 7, 2102040201, '189', '189', 'LARUTAN BENEDICT 100 ML.', '-', 68000, 1, 2, 13, '100nl', '{"22":"ANESTETIKA"}', 'benedict', 'botol', 4, 0, 0, 0, 0, 'unverified'),
(2, 8, 2102040201, '1', '1', 'ARTESUNAT INJEKSI', '-', 19000, 9, 2, 2, '-', '{"22":"ANESTETIKA"}', ' Artesunat Injeksi', 'Tablet', 3, 0, 0, 0, 0, 'unverified'),
(3, 9, 2102040201, '2', '2', 'ARTEMETER INJEKSI', '-', 12500, 9, 1, 2, '-', '{"17":"ANALGETIKA"}', 'Artemeter Injeksi', 'Tablet', 4, 0, 0, 0, 0, 'unverified'),
(4, 11, 2102040201, '4', '4', 'AIR RAKSA DENTAL USE  100 G.', '-', 220000, 1, 2, 11, '-', '{"27":"ANTI HIPERTENSI"}', 'Air Raksa Dental use 100 G.', 'Botol', 1, 0, 0, 0, 0, 'unverified'),
(5, 12, 2102040201, '5', '5', 'ALBENDAZOL TABLET 400 MG', '-', 427, 13, 1, 5, '-', '{"27":"ANTI HIPERTENSI"}', 'Albendazol tablet 400 mg', 'Tablet', 2, 0, 0, 0, 0, 'unverified'),
(6, 13, 2102040201, '3', '3', 'A C T ( ARTESUNAT 50 MG + AMODIAQUIN 200 MG ) TABLET', '-', 1200, 13, 1, 5, '-', '{"4":"ANTHELMETIKA"}', 'A C T ( Artesunat 50 mg + Amodiaquin 200 mg ) tablet', 'Tablet', 2, 0, 45, 0, 0, 'unverified'),
(7, 14, 2102040201, '6', '6', 'ALLYLESTRENOL TABLET 5 MG', '-', 1650, 13, 1, 11, '-', '{"4":"ANTHELMETIKA"}', 'Allylestrenol tablet 5 mg', 'Tablet', 2, 0, 12, 0, 0, 'unverified'),
(8, 15, 2102040201, '7', '7', 'ALOPURINOL TABLET 100 MG', '-', 139, 13, 1, 5, '-', '{"31":"ANTI TROMBOLITIKA"}', 'Alopurinol tablet 100 mg', 'Tablet', 1, 0, 0, 0, 0, 'unverified'),
(9, 16, 2102040201, '8', '8', 'AMBROKSOL SIRUP  15 MG/5 ML. @ 60 ML.', '-', 3465, 1, 1, 8, '-', '{"31":"ANTI TROMBOLITIKA"}', 'Ambroksol sirup  15 mg/5 ml. @ 60 ml.', 'Botol', 4, 0, 0, 0, 0, 'unverified'),
(10, 17, 2102040201, '9', '9', 'AMBROKSOL TABLET 30 MG', '-', 119, 13, 1, 5, '-', '{"27":"ANTI HIPERTENSI"}', 'Ambroksol tablet 30 mg', 'Tablet', 5, 0, 0, 0, 0, 'unverified'),
(11, 18, 2102040201, '10', '10', 'AMINOFILIN INJEKSI 24 MG / ML  @ 10 ML', '-', 2695, 14, 1, 8, '-', '{"4":"ANTHELMETIKA"}', 'Aminofilin injeksi 24 mg / ml  @ 10 ml', 'Ampul', 1, 0, 0, 0, 0, 'unverified'),
(12, 19, 2102040201, '11', '11', 'AMINOFILIN TABLET 200 MG', '-', 110, 13, 2, 13, '-', '{"31":"ANTI TROMBOLITIKA"}', 'Aminofilin tablet 200 mg', 'Tablet', 4, 0, 0, 0, 0, 'unverified'),
(13, 20, 2102040201, '12', '12', 'AMITRIPTILIN HCL TABLET SALUT 25 MG', '-', 92, 13, 1, 2, '-', '{"22":"ANESTETIKA"}', 'Amitriptilin HCl tablet salut 25 mg', 'Tablet', 5, 0, 0, 0, 0, 'unverified'),
(14, 21, 2102040201, '13', '13', 'AMLODIPIN TABLET 5 MG', '-', 1260, 13, 1, 5, '-', '{"1":"ANTIBIOTIKA"}', 'Amlodipin tablet 5 mg', 'Tablet', 5, 0, 0, 0, 0, 'unverified'),
(15, 22, 2102040201, '14', '14', 'AMOKSISILIN CAPSUL 250 MG', '-', 303, 13, 1, 5, '-', '{"3":"ANTIAMUBA"}', 'Amoksisilin capsul 250 mg', 'Tablet', 4, 0, 0, 0, 0, 'unverified'),
(16, 23, 2102040201, '15', '15', 'AMOKSISILIN INJEKSI 1000 MG', '-', 7875, 9, 1, 11, '-', '{"3":"ANTIAMUBA"}', 'Amoksisilin injeksi 1000 mg', 'Vial', 4, 0, 0, 0, 0, 'unverified'),
(17, 24, 2102040201, '16', '16', 'AMOKSISILIN KAPLET 500 MG', '-', 389, 15, 1, 5, '-', '{"3":"ANTIAMUBA"}', 'Amoksisilin kaplet 500 mg', 'Kaplet', 2, 0, 0, 0, 0, 'unverified'),
(18, 25, 2102040201, '17', '17', 'AMOKSISILIN SIRUP KERING 125 MG / 5 ML', '-', 3885, 1, 1, 5, '-', '{"1":"ANTIBIOTIKA"}', 'Amoksisilin sirup kering 125 mg / 5 ml', 'Botol', 1, 0, 0, 0, 0, 'unverified'),
(19, 26, 2102040201, '18', '18', 'AMOKSISILIN+ASAM KLAVULANAT 625 MG TABLET', '-', 5308, 13, 1, 9, '-', '{"1":"ANTIBIOTIKA"}', 'Amoksisilin+Asam Klavulanat 625 mg tablet', 'Tablet', 5, 0, 0, 0, 0, 'unverified'),
(20, 27, 2102040201, '19', '19', 'AMPISILIN CAPSUL 250 MG', '-', 282, 7, 1, 3, '-', '{"3":"ANTIAMUBA"}', 'Ampisilin capsul 250 mg', 'Kapsul', 4, 0, 0, 0, 0, 'unverified'),
(21, 28, 2102040201, '20', '20', 'AMPISILIN KAPLET 500 MG', '-', 900, 15, 1, 5, '-', '{"3":"ANTIAMUBA"}', 'Ampisilin kaplet 500 mg', 'Kaplet', 3, 0, 0, 0, 0, 'unverified'),
(22, 29, 2102040201, '21', '21', 'AMPISILIN SERBUK INJEKSI 1000 MG', '-', 2887, 9, 1, 5, '-', '{"3":"ANTIAMUBA"}', 'Ampisilin serbuk injeksi 1000 mg', 'Vial', 5, 0, 0, 0, 0, 'unverified'),
(23, 30, 2102040201, '22', '22', 'AMPISILIN SIRUP KERING 125 MG / 5 ML', '-', 4199, 1, 1, 9, '-', '{"1":"ANTIBIOTIKA"}', 'Ampisilin sirup kering 125 mg / 5 ml', 'Botol', 4, 0, 0, 0, 0, 'unverified'),
(24, 31, 2102040201, '23', '23', 'ANTASIDA DOEN  I TABLET  KOMB : AL(OH) 200 MG + MG (OH) 200 MG', '-', 170, 13, 1, 3, '-', '{"3":"ANTIAMUBA"}', 'Antasida DOEN  I tablet  komb : Al(OH) 200 mg + Mg (OH) 200 mg', 'Tablet', 4, 0, 0, 0, 0, 'unverified'),
(25, 32, 2102040201, '24', '24', 'ANTASIDA DOEN II SUSP. 60 ML.KOMB.:AL(OH) 200 MG/5 ML.+MG(OH) 200 MG/5 ML', '-', 4043, 1, 1, 3, '-', '{"3":"ANTIAMUBA"}', 'Antasida DOEN II susp. 60 ml.komb.:Al(OH) 200 mg/5 ml.+Mg(OH) 200 mg/5 ml', 'Botol', 3, 0, 0, 0, 0, 'unverified'),
(26, 33, 2102040201, '25', '25', 'ANTI BAKTERI DOEN SALAP KOMB : BASITRASIN 500 IU/G + POLIMIKSIN 1000 IU/G', '-', 2643, 12, 1, 10, '-', '{"31":"ANTI TROMBOLITIKA"}', 'Anti Bakteri DOEN salap komb : Basitrasin 500 IU/g + Polimiksin 1000 IU/g', 'Tube', 3, 0, 0, 0, 0, 'unverified'),
(27, 34, 2102040201, '26', '26', 'ANTI FUNGI DOEN KOMB : AS. BENZOAT 6 % + AS. SALISILAT 3 %', '-', 1155, 10, 2, 10, '-', '{"3":"ANTIAMUBA"}', 'Anti Fungi DOEN komb : As. Benzoat 6 % + As. Salisilat 3 %', 'Pot', 3, 0, 0, 0, 0, 'unverified'),
(28, 35, 2102040201, '27', '27', 'ANTI HEMOROID DOEN KOMBINASI ', '-', 2100, 11, 1, 8, '-', '{"3":"ANTIAMUBA"}', 'Anti Hemoroid DOEN kombinasi ', 'Supp', 3, 0, 0, 0, 0, 'unverified'),
(29, 36, 2102040201, '28', '28', 'ANTI MALARIA DOEN KOMBINASI : PIRIMETAMIN 25 MG + SULFADOKSIN 500 MG', '-', 473, 13, 1, 10, '-', '{"3":"ANTIAMUBA"}', 'Anti Malaria DOEN kombinasi : Pirimetamin 25 mg + Sulfadoksin 500 mg', 'Tablet', 3, 0, 0, 0, 0, 'unverified'),
(30, 37, 2102040201, '29', '29', 'ANTI MIGREN DOEN KOMBINASI : ERGOTAMIN TARTRAT 1 MG + KOFFEIN 50 MG', '-', 129, 13, 1, 5, '-', '{"3":"ANTIAMUBA"}', 'Anti Migren DOEN kombinasi : Ergotamin Tartrat 1 mg + Koffein 50 mg', 'Tablet', 2, 0, 0, 0, 0, 'unverified'),
(31, 38, 2102040201, '30', '30', 'ANTISKABIES KRIM 10 GR', '-', 8518, 12, 1, 10, '-', '{"3":"ANTIAMUBA"}', 'Antiskabies krim 10 gr', 'Tube', 2, 0, 0, 0, 0, 'unverified'),
(32, 39, 2102040201, '31', '31', 'AQUA DEST STERIL 500 ML.', '-', 3588, 1, 1, 3, '-', '{"17":"ANALGETIKA"}', 'Aqua Dest Steril 500 ml.', 'Botol', 1, 0, 0, 0, 0, 'unverified'),
(33, 40, 2102040201, '32', '32', 'AQUA PRO INJEKSI STERIL 25 ML., BEBAS PIROGEN', '-', 2420, 1, 1, 8, '-', '{"22":"ANESTETIKA"}', 'Aqua Pro Injeksi steril 25 ml., bebas pirogen', 'Botol', 2, 0, 0, 0, 0, 'unverified'),
(34, 41, 2102040201, '33', '33', 'ASAM ASETIL SALISILAT ( ASETOSAL )  TABLET 80 MG', '-', 374, 13, 2, 5, '-', '{"22":"ANESTETIKA"}', 'Asam asetil Salisilat ( Asetosal )  tablet 80 mg', 'Tablet', 5, 0, 0, 0, 0, 'unverified'),
(35, 42, 2102040201, '34', '34', 'ASAM ASKORBAT TABLET   50 MG ', '-', 29, 13, 1, 5, '-', '{"31":"ANTI TROMBOLITIKA"}', 'Asam Askorbat tablet   50 mg ', 'Tablet', 5, 0, 0, 0, 0, 'unverified'),
(36, 43, 2102040201, '35', '35', 'ASAM ASKORBAT TABLET 250 MG ', '-', 134, 13, 1, 10, '-', '{"22":"ANESTETIKA"}', 'Asam Askorbat tablet 250 mg ', 'Tablet', 1, 0, 0, 0, 0, 'unverified'),
(37, 44, 2102040201, '36', '36', 'ASAM FOLAT TABLET 1 MG', '-', 71, 13, 1, 5, '-', '{"3":"ANTIAMUBA"}', 'Asam Folat tablet 1 mg', 'Tablet', 3, 0, 0, 0, 0, 'unverified'),
(38, 45, 2102040201, '37', '37', 'ASAM MEFENAMAT CAPSUL 250 MG', '-', 116, 7, 1, 3, '-', '{"3":"ANTIAMUBA"}', 'Asam Mefenamat capsul 250 mg', 'Kapsul', 4, 0, 0, 0, 0, 'unverified'),
(39, 46, 2102040201, '38', '38', 'ASAM MEFENAMAT KAPLET 500 MG', '-', 163, 15, 1, 5, '-', '{"22":"ANESTETIKA"}', 'Asam Mefenamat kaplet 500 mg', 'Kaplet', 4, 0, 0, 0, 0, 'unverified'),
(40, 47, 2102040201, '191', '111', 'LARUTAN ETANOL ASAM 100 ML.', '-', 178200, 1, 2, 9, '100 ml', '{"17":"ANALGETIKA"}', 'cair', 'botol', 4, 0, 0, 0, 0, 'unverified'),
(41, 48, 2102040201, '190', '1111', 'LARUTAN EOSIN 2 % 100 ML.', '-', 86250, 1, 2, 9, 'BOTOL', '{"17":"ANALGETIKA"}', 'CAIR', 'BOTOL', 4, 0, 0, 0, 0, 'unverified'),
(42, 49, 2102040201, '192', '11111', 'LARUTAN GIEMSA STAIN 100 ML.', '-', 166750, 1, 2, 9, '100 ml', '{"22":"ANESTETIKA"}', 'CAIR', 'BOTOL', 4, 0, 0, 0, 0, 'unverified'),
(43, 50, 2102040201, '193', '111111', 'LARUTAN KARBOL FUCHSIN 100 ML.', '-', 74750, 1, 2, 9, '100 ml', '{"17":"ANALGETIKA"}', 'CAUR', 'BOTOL', 4, 0, 0, 0, 0, 'unverified'),
(44, 51, 2102040201, '194', '1111111', 'LARUTAN METANOL 100 ML.', '-', 44000, 14, 2, 9, '100 ml', '{"22":"ANESTETIKA"}', 'CAIR', 'BOTOL', 4, 0, 0, 0, 0, 'unverified'),
(45, 52, 2102040201, '195', '11111111', 'LARUTAN METILEN BIRU 100 ML.', '-', 69000, 1, 2, 9, '100 ml', '{"17":"ANALGETIKA"}', 'CAIR', 'BOTOL', 4, 0, 0, 0, 0, 'unverified'),
(46, 53, 2102040201, '196', '111111111', 'LARUTAN TURK 100 ML.', '-', 57500, 1, 2, 9, '100 ml', '{"17":"ANALGETIKA"}', 'CAIR', 'BOTOL', 4, 0, 0, 0, 0, 'unverified'),
(47, 54, 2102040201, '39', '39', 'ASAM TRANEKSAMAT INJEKSI 250 MG', '-', 3850, 14, 1, 8, '-', '{"31":"ANTI TROMBOLITIKA"}', 'Asam Traneksamat injeksi 250 mg', 'Ampul', 5, 0, 0, 0, 0, 'unverified'),
(48, 55, 2102040201, '40', '40', 'ASAM TRANEKSAMAT TABLET 500 MG', '-', 1045, 13, 1, 3, '-', '{"31":"ANTI TROMBOLITIKA"}', 'Asam Traneksamat tablet 500 mg', 'Tablet', 5, 0, 0, 0, 0, 'unverified'),
(49, 56, 2102040201, '41', '41', 'ASIATIKOSIDA KRIM 1 % @ 10 G', '-', 51150, 12, 1, 3, '-', '{"31":"ANTI TROMBOLITIKA"}', 'Asiatikosida krim 1 % @ 10 g', 'Tube', 2, 0, 0, 0, 0, 'unverified'),
(50, 57, 2102040201, '42', '42', 'ASIKLOVIR KRIM 5 % @ 5 GRAM', '-', 3150, 12, 1, 3, '-', '{"31":"ANTI TROMBOLITIKA"}', 'Asiklovir krim 5 % @ 5 gram', 'Tube', 2, 0, 0, 0, 0, 'unverified'),
(51, 58, 2102040201, '43', '43', 'ASIKLOVIR TABLET 200 MG', '-', 509, 13, 1, 10, '-', '{"31":"ANTI TROMBOLITIKA"}', 'Asiklovir tablet 200 mg', 'Tablet', 3, 0, 0, 0, 0, 'unverified'),
(52, 59, 2102040201, '44', '44', 'ASIKLOVIR TABLET 400 MG', '-', 600, 13, 2, 5, '-', '{"31":"ANTI TROMBOLITIKA"}', 'Asiklovir tablet 400 mg', 'Tablet', 1, 0, 0, 0, 0, 'unverified'),
(53, 60, 2102040201, '45', '45', 'ATROPIN SULFAT TETES MATA 0,5 % @ 5 ML.', '-', 117, 1, 1, 5, '-', '{"8":"ANTI TBC"}', 'Atropin Sulfat Tetes Mata 0,5 % @ 5 ml.', 'Botol', 2, 0, 0, 0, 0, 'unverified'),
(54, 61, 2102040201, '46', '46', 'ATTAPULGITE ( NODIAR) TABLET', '-', 275, 13, 1, 10, '-', '{"8":"ANTI TBC"}', 'Attapulgite ( Nodiar) tablet', 'Tablet', 4, 0, 0, 0, 0, 'unverified'),
(55, 62, 2102040201, '48', '48', 'BENZATIN BENZIL PENISILLIN INJEKSI 2,4 JUTA IU', '-', 5400, 9, 1, 5, '-', '{"27":"ANTI HIPERTENSI"}', 'Benzatin Benzil Penisillin injeksi 2,4 Juta IU', 'Vial', 3, 0, 0, 0, 0, 'unverified'),
(56, 63, 2102040201, '47', '47', 'BAHAN PENGISI SALURAN AKAR GIGI', '-', 579700, 16, 1, 5, '-', '{"3":"ANTIAMUBA"}', 'Bahan pengisi saluran akar gigi', 'Set', 3, 0, 0, 0, 0, 'unverified'),
(57, 64, 2102040201, '49', '49', 'BENZATIN BENZIL PENISILLIN INJEKSI 1,2 JUTA IU', '-', 3803, 9, 1, 10, '-', '{"8":"ANTI TBC"}', 'Benzatin Benzil Penisillin injeksi 1,2 Juta IU', 'Vial', 1, 0, 0, 0, 0, 'unverified'),
(58, 65, 2102040201, '50', '50', 'BESI SIRUP 150 ML.', '-', 1800, 1, 1, 11, '-', '{"4":"ANTHELMETIKA"}', 'Besi sirup 150 ml.', 'Botol', 1, 0, 0, 0, 0, 'unverified'),
(59, 66, 2102040201, '51', '51', 'BETAHISTIN MESILAT TABLET 6 MG', '-', 970, 13, 1, 5, '-', '{"8":"ANTI TBC"}', 'Betahistin Mesilat tablet 6 mg', 'Tablet', 2, 0, 0, 0, 0, 'unverified'),
(60, 67, 2102040201, '52', '52', 'BETAMETASON KREM 0,1 %  @ 5 G', '-', 1959, 12, 1, 3, '-', '{"8":"ANTI TBC"}', 'Betametason Krem 0,1 %  @ 5 g', 'Tube', 3, 0, 0, 0, 0, 'unverified'),
(61, 68, 2102040201, '53', '53', 'BISAKODIL ( DULCOLAX ) SUPPOSITORIA  10 MG DEWASA', '-', 14454, 11, 1, 5, '-', '{"31":"ANTI TROMBOLITIKA"}', 'Bisakodil ( Dulcolax ) suppositoria  10 mg dewasa', 'Supp', 1, 0, 0, 0, 0, 'unverified'),
(62, 69, 2102040201, '54', '54', 'BISAKODIL ( DULCOLAX ) SUPPOSITORIA 5 MG DEWASA', '-', 12267, 11, 1, 10, '-', '{"8":"ANTI TBC"}', 'Bisakodil ( Dulcolax ) suppositoria 5 mg dewasa', 'Supp', 2, 0, 0, 0, 0, 'unverified'),
(63, 70, 2102040201, '55', '55', 'BISAKODIL TABLET 10 MG', '-', 1039, 13, 1, 5, '-', '{"8":"ANTI TBC"}', 'Bisakodil tablet 10 mg', 'Tablet', 3, 0, 0, 0, 0, 'unverified'),
(64, 71, 2102040201, '56', '56', 'BISOPROLOL TABLET 5 MG', '-', 2438, 13, 1, 8, '-', '{"4":"ANTHELMETIKA"}', 'Bisoprolol tablet 5 mg', 'Tablet', 2, 0, 0, 0, 0, 'unverified'),
(65, 72, 2102040201, '57', '57', 'BORAX GLISERIN SOLUTIO 15 ML.', '-', 2090, 1, 1, 11, '-', '{"4":"ANTHELMETIKA"}', 'Borax Gliserin solutio 15 ml.', 'Botol', 3, 0, 0, 0, 0, 'unverified'),
(66, 73, 2102040201, '58', '58', 'BROMHEKSIN TABLET 8 MG', '-', 40, 13, 1, 5, '-', '{"31":"ANTI TROMBOLITIKA"}', 'Bromheksin tablet 8 mg', 'Tablet', 3, 0, 0, 0, 0, 'unverified'),
(67, 74, 2102040201, '59', '59', 'BROMHEKSIN SIRUP 100 ML', '-', 6050, 1, 1, 3, '-', '{"8":"ANTI TBC"}', 'Bromheksin sirup 100 ml', 'Botol', 2, 0, 0, 0, 0, 'unverified'),
(68, 75, 2102040201, '60', '60', 'CENDO LYTEERS EYE DROP 15 ML', '-', 20900, 1, 1, 11, '-', '{"8":"ANTI TBC"}', 'Cendo Lyteers eye drop 15 ml', 'Botol', 3, 0, 0, 0, 0, 'unverified'),
(69, 76, 2102040201, '61', '61', 'CENDO CENFRESH EYE DROP 5 ML', '-', 33000, 1, 1, 5, '-', '{"8":"ANTI TBC"}', 'Cendo Cenfresh eye drop 5 ml', 'Botol', 1, 0, 0, 0, 0, 'unverified'),
(70, 77, 2102040201, '62', '62', 'CENDO XITROL EYE DROP 5 ML', '-', 27500, 1, 1, 5, '-', '{"3":"ANTIAMUBA"}', 'Cendo Xitrol eye drop 5 ml', 'Botol', 1, 0, 0, 0, 0, 'unverified'),
(71, 78, 2102040201, '63', '63', 'CENDO XITROL EYE OINT 3,5 GR', '-', 29700, 12, 1, 10, '-', '{"8":"ANTI TBC"}', 'Cendo Xitrol eye oint 3,5 gr', 'Tube', 2, 0, 0, 0, 0, 'unverified'),
(72, 79, 2102040201, '64', '64', 'CENDO CATARLENT EYE DROP 5 ML', '-', 22000, 1, 1, 5, '-', '{"3":"ANTIAMUBA"}', 'Cendo Catarlent eye drop 5 ml', 'Botol', 3, 0, 0, 0, 0, 'unverified'),
(73, 80, 2102040201, '65', '65', 'CENDO POLYGRAN EYE DROP 5 ML', '-', 33000, 1, 1, 3, '-', '{"31":"ANTI TROMBOLITIKA"}', 'Cendo Polygran eye drop 5 ml', 'Botol', 1, 0, 0, 0, 0, 'unverified'),
(74, 81, 2102040201, '66', '66', 'CENDO POLYGRAN EYE OINT 3,5 GR', '-', 22000, 12, 1, 11, '-', '{"4":"ANTHELMETIKA"}', 'Cendo Polygran eye oint 3,5 gr', 'Tube', 1, 0, 0, 0, 0, 'unverified'),
(75, 82, 2102040201, '67', '67', 'CENDO POLYDEX EYE DROP 5 ML', '-', 45100, 1, 1, 8, '-', '{"27":"ANTI HIPERTENSI"}', 'Cendo Polydex eye drop 5 ml', 'Botol', 1, 0, 0, 0, 0, 'unverified'),
(76, 83, 2102040201, '68', '68', 'CENDO TIMOLOL 0,5% EYE DROP 5 ML', '-', 55000, 1, 1, 5, '-', '{"31":"ANTI TROMBOLITIKA"}', 'Cendo Timolol 0,5% eye drop 5 ml', 'Botol', 2, 0, 0, 0, 0, 'unverified'),
(77, 84, 2102040201, '198', '1981', 'LARUTAN H2O2 3%', '-', 50600, 1, 2, 9, 'TABUNG', '{"22":"ANESTETIKA"}', 'CAIR', 'BOTOL', 4, 0, 0, 0, 0, 'unverified'),
(78, 85, 2102040201, '69', '69', 'TOBROSON EYE DROP', '-', 41250, 1, 1, 8, '-', '{"31":"ANTI TROMBOLITIKA"}', 'Tobroson eye drop', 'Botol', 2, 0, 0, 0, 0, 'unverified'),
(79, 86, 2102040201, '71', '71', 'CEFADROKSIL 125 MG / 5 ML SIRUP KERING', '-', 8610, 1, 1, 5, '-', '{"31":"ANTI TROMBOLITIKA"}', 'Cefadroksil 125 mg / 5 ml sirup kering', 'Botol', 1, 0, 0, 0, 0, 'unverified'),
(80, 87, 2102040201, '197', '1971', 'LARUTAN NATRIUM CITRAS 3 % @100 ML', '-', 28750, 1, 2, 9, '100 ml', '{"22":"ANESTETIKA"}', 'CAIR', 'BOTOL', 4, 0, 0, 0, 0, 'unverified'),
(81, 88, 2102040201, '70', '70', 'CAIRAN IRIGASI NAOCL 5% DAN EDTA 5%', '-', 290000, 17, 1, 3, '-', '{"31":"ANTI TROMBOLITIKA"}', 'Cairan Irigasi NaOCl 5% dan EDTA 5%', 'Ktk', 1, 0, 0, 0, 0, 'unverified'),
(82, 89, 2102040201, '72', '72', 'CEFADROKSIL KAPSUL 500 MG', '-', 735, 7, 1, 5, '-', '{"4":"ANTHELMETIKA"}', 'Cefadroksil kapsul 500 mg', 'Kapsul', 2, 0, 0, 0, 0, 'unverified'),
(83, 90, 2102040201, '73', '73', 'CEFIXIME 100 MG', '-', 2514, 13, 1, 5, '-', '{"27":"ANTI HIPERTENSI"}', 'Cefixime 100 mg', 'Tablet', 4, 0, 0, 0, 0, 'unverified'),
(84, 91, 2102040201, '199', '1991', 'LARUTAN HCL 0,1N ', '-', 49500, 1, 2, 9, '100 ml', '{"22":"ANESTETIKA"}', 'CAIR', 'BOTOL', 4, 0, 0, 0, 0, 'unverified'),
(85, 92, 2102040201, '74', '74', 'CEFOTAKSIM INJEKSI 1 G', '-', 8085, 9, 2, 3, '-', '{"8":"ANTI TBC"}', 'Cefotaksim injeksi 1 g', 'Vial', 3, 0, 0, 0, 0, 'unverified'),
(86, 93, 2102040201, '75', '75', 'CEFTRIAKSON INJEKSI 1 G', '-', 10027, 9, 1, 5, '-', '{"18":"ANTIEMETIKA"}', 'Ceftriakson injeksi 1 g', 'Vial', 1, 0, 0, 0, 0, 'unverified'),
(87, 94, 2102040201, '200', '2001', 'LARUTAN RESS ECHER', '-', 74750, 1, 2, 9, '100 ml', '{"22":"ANESTETIKA"}', 'CAIR', 'BOTOL', 4, 0, 0, 0, 0, 'unverified'),
(88, 95, 2102040201, '76', '76', 'CETIRIZINA TABLET 10 MG', '-', 329, 13, 1, 5, '-', '{"4":"ANTHELMETIKA"}', 'Cetirizina tablet 10 mg', 'Tablet', 1, 0, 0, 0, 0, 'unverified'),
(89, 96, 2102040201, '77', '77', 'CETIRIZINA SIRUP 5 MG / 5 ML', '-', 11550, 1, 3, 8, '-', '{"4":"ANTHELMETIKA"}', 'Cetirizina sirup 5 mg / 5 ml', 'Ampul', 1, 0, 0, 0, 0, 'unverified'),
(90, 97, 2102040201, '201', '2011', 'LARUTAN MERSI OIL', '-', 575000, 1, 2, 9, '100 ml', '{"17":"ANALGETIKA"}', 'CAIR', 'BOTOL', 4, 0, 0, 0, 0, 'unverified'),
(91, 98, 2102040201, '78', '78', 'CIMETIDIN INJEKSI 200 MG', '-', 7480, 14, 1, 3, '-', '{"4":"ANTHELMETIKA"}', 'Cimetidin injeksi 200 mg', 'Ampun', 1, 0, 0, 0, 0, 'unverified'),
(92, 99, 2102040201, '202', '2021', 'LIDOCAIN INJEKSI 2 % @ 2 ML', '-', 971, 14, 2, 9, '2 ml', '{"22":"ANESTETIKA"}', 'CAIR', 'AMPUL', 4, 0, 0, 0, 0, 'unverified'),
(93, 100, 2102040201, '79', '79', 'CIMETIDIN TABLET 200 MG', '-', 121, 13, 1, 10, '-', '{"8":"ANTI TBC"}', 'Cimetidin tablet 200 mg', 'Tablet', 1, 0, 0, 0, 0, 'unverified'),
(94, 101, 2102040201, '80', '80', 'CIPROFLOKSASIN KAPLET 500 MG', '-', 273, 15, 1, 8, '-', '{"4":"ANTHELMETIKA"}', 'Ciprofloksasin kaplet 500 mg', 'Kaplet', 2, 0, 0, 0, 0, 'unverified'),
(95, 102, 2102040201, '81', '81', 'CLINDAMISIN CAPSUL 150 MG', '-', 536, 7, 1, 11, '-', '{"27":"ANTI HIPERTENSI"}', 'Clindamisin Capsul 150 mg', 'Kapsul', 1, 0, 0, 0, 0, 'unverified'),
(96, 103, 2102040201, '82', '82', 'DEKSAMETASON INJEKSI 5 MG / ML - 1 ML', '-', 867, 14, 1, 13, '-', '{"10":"ANTASIDA DAN ANTIULCER"}', 'Deksametason injeksi 5 mg / ml - 1 ml', 'Ampul', 2, 0, 0, 0, 0, 'unverified'),
(97, 104, 2102040201, '203', '2031', 'LINKOMISIN CAPSUL 500 MG', '-', 682, 7, 2, 3, '500 mg', '{"22":"ANESTETIKA"}', 'KAPSUL', 'BOTOL', 4, 0, 0, 0, 0, 'unverified'),
(98, 105, 2102040201, '204', '2041', 'LISOL SOLUTIO 1000 ML.', '-', 51108, 1, 2, 9, '1000 ml', '{"22":"ANESTETIKA"}', 'CAIR', 'BOTOL', 4, 0, 0, 0, 0, 'unverified'),
(99, 106, 2102040201, '205', '2051', 'LOPERAMID TABLET 2 MG', '-', 96, 13, 2, 2, '2 mg', '{"22":"ANESTETIKA"}', '2 mg', 'TABLET', 4, 0, 0, 0, 0, 'unverified'),
(100, 107, 2102040201, '83', '83', 'DEKSAMETASON TABLET 0,5 MG', '-', 91, 13, 1, 13, '-', '{"27":"ANTI HIPERTENSI"}', 'Deksametason tablet 0,5 mg', 'Tablet', 1, 0, 0, 0, 0, 'unverified'),
(101, 108, 2102040201, '84', '84', 'DEKSTRAN 70 - LARUTAN INFUS 6 % STERIL @ 500 ML.', '-', 35526, 1, 1, 8, '-', '{"27":"ANTI HIPERTENSI"}', 'Dekstran 70 - larutan infus 6 % steril @ 500 ml.', 'Botol', 2, 0, 0, 0, 0, 'unverified'),
(102, 109, 2102040201, '207', '2071', 'MAGNESIUM SULFAT INJEKSI (IV) 40 % - 25 ML', '-', 19479, 9, 2, 9, '25 ml', '{"22":"ANESTETIKA"}', '25 ml', 'DUS', 4, 0, 0, 0, 0, 'unverified'),
(103, 110, 2102040201, '85', '85', 'DEKSTROMETORFAN SYRUP 10 MG / 5 ML @ 60 ML.', '-', 3008, 1, 1, 8, '-', '{"27":"ANTI HIPERTENSI"}', 'Dekstrometorfan syrup 10 mg / 5 ml @ 60 ml.', 'Botol', 2, 0, 0, 0, 0, 'unverified'),
(104, 111, 2102040201, '86', '86', 'DEKSTROMETORFAN TABLET 15 MG', '-', 10, 13, 1, 13, '-', '{"4":"ANTHELMETIKA"}', 'Dekstrometorfan tablet 15 mg', 'Tablet', 2, 0, 0, 0, 0, 'unverified'),
(105, 112, 2102040201, '87', '87', 'DEVITALISASI PASTA ( NON ARSEN )', '-', 520300, 1, 1, 13, '-', '{"4":"ANTHELMETIKA"}', 'Devitalisasi Pasta ( Non Arsen )', 'botol', 2, 0, 0, 0, 0, 'unverified'),
(106, 113, 2102040201, '208', '2081', 'MEBENDAZOL TABLET 100 MG', '-', 141, 7, 2, 3, '100 mg', '{"22":"ANESTETIKA"}', '100 mg', 'DUS', 4, 0, 0, 0, 0, 'unverified'),
(107, 114, 2102040201, '88', '88', 'DIAZEPAM INJEKSI 5 MG / ML - 2 ML', '-', 873, 14, 1, 8, '-', '{"4":"ANTHELMETIKA"}', 'Diazepam injeksi 5 mg / ml - 2 ml', 'Ampul', 2, 0, 0, 0, 0, 'unverified'),
(108, 115, 2102040201, '89', '89', 'DIAZEPAM TABLET 2 MG', '-', 3, 13, 3, 8, '-', '{"27":"ANTI HIPERTENSI"}', 'Diazepam tablet 2 mg', 'Tablet', 1, 0, 0, 0, 0, 'unverified'),
(109, 116, 2102040201, '209', '2091', 'MELOKSIKAM CAPSUL 15 MG', '-', 1259, 7, 2, 3, '15 mg', '{"22":"ANESTETIKA"}', '15 mg', 'KAPSUL', 4, 0, 0, 0, 0, 'unverified'),
(110, 117, 2102040201, '90', '90', 'DIAZEPAM TABLET 5 MG', '-', 139, 13, 1, 11, '-', '{"4":"ANTHELMETIKA"}', 'Diazepam tablet 5 mg', 'Tablet', 2, 0, 0, 0, 0, 'unverified'),
(111, 118, 2102040201, '91', '91', 'DIAZEPAM RECTAL 5 MG', '-', 25520, 12, 1, 5, '-', '{"4":"ANTHELMETIKA"}', 'Diazepam Rectal 5 mg', 'Tube', 1, 0, 0, 0, 0, 'unverified'),
(112, 119, 2102040201, '92', '92', 'DIFENHIDRAMIN HCL INJEKSI 10 MG / ML - 1 ML', '-', 460, 14, 1, 11, '-', '{"4":"ANTHELMETIKA"}', 'Difenhidramin HCl injeksi 10 mg / ml - 1 ml', 'Ampul', 2, 0, 0, 0, 0, 'unverified'),
(113, 120, 2102040201, '210', '2101', 'METAMPIRON ( ANTALGIN )  INJEKSI 250 MG /1 ML  @  2 ML', '-', 1285, 14, 2, 9, '2 ml', '{"22":"ANESTETIKA"}', '250 mg', 'AMPUL', 4, 0, 0, 0, 0, 'unverified'),
(114, 121, 2102040201, '93', '93', 'DIGOKSIN TABLET 0,25 MG', '-', 83, 13, 1, 10, '-', '{"27":"ANTI HIPERTENSI"}', 'Digoksin tablet 0,25 mg', 'Tablet', 2, 0, 0, 0, 0, 'unverified'),
(115, 122, 2102040201, '94', '94', 'DILTIAZEM TABLET 30 MG', '-', 152, 13, 1, 8, '-', '{"27":"ANTI HIPERTENSI"}', 'Diltiazem tablet 30 mg', 'Tablet', 2, 0, 0, 0, 0, 'unverified'),
(116, 123, 2102040201, '95', '95', 'DIMENHIDRINAT TABLET 50 MG', '-', 89, 13, 1, 11, '-', '{"4":"ANTHELMETIKA"}', 'Dimenhidrinat tablet 50 mg', 'Tablet', 2, 0, 0, 0, 0, 'unverified'),
(117, 124, 2102040201, '211', '2111', 'METAMPIRON ( ANTALGIN )  TABLET 500 MG', '-', 67, 13, 2, 9, '500 mg', '{"22":"ANESTETIKA"}', '500 mg', 'TABLET', 4, 0, 0, 0, 0, 'unverified'),
(118, 125, 2102040201, '96', '96', 'DESINFEKSI TANGAN 500 ML', '-', 88550, 1, 1, 10, '-', '{"4":"ANTHELMETIKA"}', 'Desinfeksi tangan 500 ml', 'Botol', 2, 0, 0, 0, 0, 'unverified'),
(119, 126, 2102040201, '97', '97', 'DOKSISIKLIN  CAPSUL 100 MG', '-', 216, 7, 1, 3, '-', '{"27":"ANTI HIPERTENSI"}', 'Doksisiklin  capsul 100 mg', 'Kapsul', 2, 0, 0, 0, 0, 'unverified'),
(120, 127, 2102040201, '212', '2121', 'METFORMIN TABLET 500 MG', '-', 173, 13, 2, 9, '500 mg', '{"22":"ANESTETIKA"}', '500 mg', 'TABLET', 4, 0, 0, 0, 0, 'unverified'),
(121, 128, 2102040201, '98', '98', 'DOMPERIDON TABLET 10 MG', '-', 424, 13, 2, 2, '-', '{"27":"ANTI HIPERTENSI"}', 'Domperidon tablet 10 mg', 'Tablet', 2, 0, 0, 0, 0, 'unverified'),
(122, 129, 2102040201, '99', '99', 'EFEDRIN HCL TABLET 25 MG', '-', 54, 13, 1, 2, '-', '{"4":"ANTHELMETIKA"}', 'Efedrin HCl tablet 25 mg', 'Tablet', 3, 0, 0, 0, 0, 'unverified'),
(123, 130, 2102040201, '100', '100', 'EKSTRAK BELLADON TABLET 10 MG', '-', 50, 13, 1, 2, '-', '{"27":"ANTI HIPERTENSI"}', 'Ekstrak Belladon tablet 10 mg', 'Tablet', 3, 0, 0, 0, 0, 'unverified'),
(124, 131, 2102040201, '101', '101', 'EKSTRAK PLASENTA GEL @ 15 G', '-', 16500, 12, 1, 5, '-', '{"4":"ANTHELMETIKA"}', 'Ekstrak Plasenta Gel @ 15 g', 'Tube', 2, 0, 0, 0, 0, 'unverified'),
(125, 132, 2102040201, '102', '102', 'EPINEFRIN HCL  ( ADRENALIN ) INJ 0,1 % - 1 ML', '-', 346, 14, 1, 11, '-', '{"27":"ANTI HIPERTENSI"}', 'Epinefrin HCl  ( Adrenalin ) inj 0,1 % - 1 ml', 'Ampul', 3, 0, 0, 0, 0, 'unverified'),
(126, 133, 2102040201, '103', '103', 'ERITROMISIN  KAPLET 500 MG', '-', 977, 15, 1, 8, '-', '{"27":"ANTI HIPERTENSI"}', 'Eritromisin  kaplet 500 mg', 'Kaplet', 3, 0, 0, 0, 0, 'unverified'),
(127, 134, 2102040201, '104', '104', 'ERITROMISIN SIRUP 200 MG / 5 ML', '-', 8505, 1, 1, 5, '-', '{"8":"ANTI TBC"}', 'Eritromisin sirup 200 mg / 5 ml', 'Botol', 2, 0, 0, 0, 0, 'unverified'),
(128, 135, 2102040201, '105', '105', 'ETAKRIDIN ( RIVANOL ) LARUTAN 0,1 % @ 300 ML.', '-', 1890, 1, 1, 8, '-', '{"27":"ANTI HIPERTENSI"}', 'Etakridin ( Rivanol ) larutan 0,1 % @ 300 ml.', 'Botol', 2, 0, 0, 0, 0, 'unverified'),
(129, 136, 2102040201, '107', '107', 'ETANOL LARUTAN 70 % @ 1000 ML.', '-', 24200, 1, 1, 10, '-', '{"4":"ANTHELMETIKA"}', 'Etanol larutan 70 % @ 1000 ml.', 'Botol', 3, 0, 0, 0, 0, 'unverified'),
(130, 137, 2102040201, '106', '106', 'ETAMBUTOL TABLET 250 MG', '-', 178, 18, 1, 8, '-', '{"27":"ANTI HIPERTENSI"}', 'Etambutol tablet 250 mg', 'Kotak', 3, 0, 0, 0, 0, 'unverified'),
(131, 138, 2102040201, '108', '108', 'ETYL KLORIDA SEMPROT @100 ML', '-', 82500, 1, 1, 8, '-', '{"27":"ANTI HIPERTENSI"}', 'Etyl Klorida semprot @100 ml', 'Botol', 2, 0, 0, 0, 0, 'unverified'),
(132, 139, 2102040201, '109', '109', 'ETINILESTRADIOL TABLET 0,05 MG', '-', 1912, 1, 1, 5, '-', '{"27":"ANTI HIPERTENSI"}', 'Etinilestradiol tablet 0,05 mg', 'Botol', 1, 0, 0, 0, 0, 'unverified'),
(133, 140, 2102040201, '110', '110', 'EUGENOL CAIRAN 10 ML.', '-', 38130, 1, 1, 10, '-', '{"27":"ANTI HIPERTENSI"}', 'Eugenol cairan 10 ml.', 'Botol', 2, 0, 0, 0, 0, 'unverified'),
(134, 141, 2102040201, '112', '112', 'FENOBARBITAL INJEKSI 50 MG / ML - 2 ML', '-', 670, 18, 1, 8, '-', '{"27":"ANTI HIPERTENSI"}', 'Fenobarbital injeksi 50 mg / ml - 2 ml', 'Botol', 3, 0, 0, 0, 0, 'unverified'),
(135, 142, 2102040201, '111', '111.', 'FENILBUTAZON TABLET 200 MG', '-', 69, 1, 1, 11, '-', '{"4":"ANTHELMETIKA"}', 'Fenilbutazon tablet 200 mg', 'Botol', 1, 0, 0, 0, 0, 'unverified'),
(136, 143, 2102040201, '113', '113', 'FENOBARBITAL TABLET    30 MG', '-', 5, 1, 1, 8, '-', '{"4":"ANTHELMETIKA"}', 'Fenobarbital tablet    30 mg', 'Botol', 2, 0, 0, 0, 0, 'unverified'),
(137, 144, 2102040201, '114', '114', 'FENOBARBITAL TABLET 100 MG', '-', 46, 1, 1, 11, '-', '{"27":"ANTI HIPERTENSI"}', 'Fenobarbital tablet 100 mg', 'Botol', 2, 0, 0, 0, 0, 'unverified'),
(138, 145, 2102040201, '115', '115', 'FENOL GLISEROL TETES TELINGA 10 %  @  5 ML.', '-', 1059, 18, 1, 8, '-', '{"27":"ANTI HIPERTENSI"}', 'Fenol Gliserol tetes telinga 10 %  @  5 ml.', 'Kotak', 3, 0, 0, 0, 0, 'unverified'),
(139, 146, 2102040201, '116', '116', 'FERO SULFAT TABLET 300 MG (TABLET TAMBAH DARAH)', '-', 1523, 13, 1, 13, '-', '{"27":"ANTI HIPERTENSI"}', 'Fero Sulfat tablet 300 mg (tablet tambah darah)', 'Tablet', 1, 0, 0, 0, 0, 'unverified'),
(140, 147, 2102040201, '117', '117', 'FITOMENADION 2 MG/ML INJEKSI', '-', 8250, 14, 1, 8, '-', '{"4":"ANTHELMETIKA"}', 'Fitomenadion 2 mg/ml injeksi', 'Ampul', 2, 0, 0, 0, 0, 'unverified'),
(141, 148, 2102040201, '118', '118', 'FITOMENADION ( VITAMIN K 1 ) INJEKSI 10 MG / ML - 1 ML', '-', 933, 14, 1, 5, '-', '{"27":"ANTI HIPERTENSI"}', 'Fitomenadion ( Vitamin K 1 ) injeksi 10 mg / ml - 1 ml', 'Ampul', 2, 0, 0, 0, 0, 'unverified'),
(142, 149, 2102040201, '119', '119', 'FITOMENADION ( VITAMIN K 1 ) TABLET SALUT GULA 10 MG', '-', 715, 13, 1, 11, '-', '{"27":"ANTI HIPERTENSI"}', 'Fitomenadion ( Vitamin K 1 ) tablet salut gula 10 mg', 'Tablet', 2, 0, 0, 0, 0, 'unverified'),
(143, 150, 2102040201, '120', '120', 'FLAVOKSAT HCL ( UROXAL ) TAB. 200 MG', '-', 2200, 13, 1, 11, '-', '{"27":"ANTI HIPERTENSI"}', 'Flavoksat HCl ( Uroxal ) tab. 200 mg', 'Tablet', 2, 0, 0, 0, 0, 'unverified'),
(144, 151, 2102040201, '213', '2131', 'METILERGOMETRIN MALEAT INJEKSI 0,200 MG - 1 ML', '-', 1435, 14, 2, 9, '1 ml', '{"17":"ANALGETIKA"}', '250 mg', 'ampul', 4, 0, 0, 0, 0, 'unverified'),
(145, 152, 2102040201, '214', '2141', 'METILERGOMETRIN MALEAT TABLET SALUT 0,125 MG', '-', 115, 13, 2, 9, 'mg', '{"17":"ANALGETIKA"}', '0,125 mg', 'TABLET', 4, 0, 0, 0, 0, 'unverified'),
(146, 153, 2102040201, '215', '2151', 'METILPREDNISOLON TABLET 4 MG', '-', 514, 13, 2, 9, 'MG', '{"22":"ANESTETIKA"}', '4 MG', 'TABLET', 4, 0, 0, 0, 0, 'unverified'),
(147, 154, 2102040201, '216', '2161', 'METOKLOPRAMID DROP @ 10 ML', '-', 13976, 12, 2, 9, 'ML', '{"17":"ANALGETIKA"}', '10 ML', 'FLS', 4, 0, 0, 0, 0, 'unverified'),
(148, 155, 2102040201, '217', '2171', 'METOKLOPRAMID INJEKSI', '-', 4235, 14, 2, 9, 'ML', '{"17":"ANALGETIKA"}', 'ML', 'ampul', 4, 0, 0, 0, 0, 'unverified'),
(149, 156, 2102040201, '218', '2181', 'METOKLOPRAMID SYRUP @ 60 ML.', '-', 5319, 1, 2, 9, 'ML', '{"17":"ANALGETIKA"}', '60 ML', 'BOTOL', 4, 0, 0, 0, 0, 'unverified'),
(150, 157, 2102040201, '219', '2191', 'METOKLORPRAMID TABLET 10 MG', '-', 97, 13, 2, 2, 'MG', '{"17":"ANALGETIKA"}', '10 MG', 'TABLET', 4, 0, 0, 0, 0, 'unverified'),
(151, 158, 2102040201, '220', '2201', 'METRONIDAZOL TABLET 250 MG', '-', 137, 13, 2, 2, 'MG', '{"17":"ANALGETIKA"}', '250 MG', 'TABLET', 4, 0, 0, 0, 0, 'unverified'),
(152, 159, 2102040201, '221', '2211', 'METRONIDAZOL TABLET 500 MG', '-', 200, 13, 2, 2, 'MG', '{"17":"ANALGETIKA"}', '500 MG', 'TABLET', 4, 0, 0, 0, 0, 'unverified'),
(153, 160, 2102040201, '222', '2221', 'MIKONAZOL KRIM 2 % @ 10 G', '-', 3000, 12, 2, 9, 'GR', '{"22":"ANESTETIKA"}', '10 GR', 'TUBE', 4, 0, 0, 0, 0, 'unverified'),
(154, 161, 2102040201, '223', '2231', 'MULTIVITAMIN ANAK  SYRUP', '-', 8217, 1, 2, 9, 'ML', '{"22":"ANESTETIKA"}', 'ML', 'BOTOL', 4, 0, 0, 0, 0, 'unverified'),
(155, 162, 2102040201, '224', '2241', 'MULTIVITAMIN MINERAL DENGAN ZAT BESI TABLET DEWASA', '-', 242, 13, 2, 2, 'GR', '{"17":"ANALGETIKA"}', 'GR', 'TABLET', 4, 0, 0, 0, 0, 'unverified'),
(156, 163, 2102040201, '225', '2251', 'MUMMIFYING PASTA', '-', 137853, 1, 2, 6, 'GR', '{"22":"ANESTETIKA"}', 'GR', 'BOTOL', 4, 0, 0, 0, 0, 'unverified'),
(157, 164, 2102040201, '226', '2261', 'NACL  0,225 %+ DEXTROSE 5 % ( CAIRAN 1 : 4 ) INFUS @ 500 ML.', '-', 12100, 1, 2, 7, 'ML', '{"22":"ANESTETIKA"}', '500 ML', 'BOTOL', 4, 0, 0, 0, 0, 'unverified'),
(158, 165, 2102040201, '227', '2271', 'NATRIUM BIKARBONAT TABLET 500 MG', '-', 10, 13, 2, 2, 'MG', '{"17":"ANALGETIKA"}', '500 MG', 'TABLET', 4, 0, 0, 0, 0, 'unverified'),
(159, 166, 2102040201, '228', '2281', 'NATRIUM DIKLOFENAK EMULGEL', '-', 11000, 12, 2, 3, 'GR', '{"17":"ANALGETIKA"}', 'GR', 'TUBE', 4, 0, 0, 0, 0, 'unverified'),
(160, 167, 2102040201, '229', '2291', 'NATRIUM DIKLOFENAK TAB. 25 MG', '-', 176, 13, 2, 2, 'MG', '{"17":"ANALGETIKA"}', '25 MG', 'TABLET', 4, 0, 0, 0, 0, 'unverified'),
(161, 168, 2102040201, '230', '2301', 'NATRIUM DIKLOFENAK TAB. 50 MG', '-', 229, 13, 2, 2, 'MG', '{"22":"ANESTETIKA"}', '50 MG', 'TABLET', 4, 0, 0, 0, 0, 'unverified'),
(162, 169, 2102040201, '231', '2311', 'NATRIUM HEPARINA OINT @ 15 G', '-', 15400, 12, 2, 3, 'GR', '{"17":"ANALGETIKA"}', '15 G', 'TUBE', 4, 0, 0, 0, 0, 'unverified'),
(163, 170, 2102040201, '232', '2321', 'NATRIUM KLORIDA LARUTAN INFUS 0,9 % STERIL @ 500 ML.', '-', 5145, 1, 2, 3, 'ML', '{"22":"ANESTETIKA"}', '500 ML', 'BOTOL', 4, 0, 0, 0, 0, 'unverified'),
(164, 171, 2102040201, '233', '2331', 'NATRIUM TIOSULFAT INJEKSI 25 % - 10 ML', '-', 14768, 14, 2, 10, 'ML', '{"17":"ANALGETIKA"}', '10 ML', 'ampul', 4, 0, 0, 0, 0, 'unverified'),
(165, 172, 2102040201, '234', '2341', 'NARCOTEST 5 IN 1', '-', 101200, 16, 2, 5, 'ML', '{"17":"ANALGETIKA"}', ' ML', 'PCS', 4, 0, 0, 0, 0, 'unverified'),
(166, 173, 2102040201, '235', '2351', 'NEUROTROPIK 5000 VITAMIN INJEKSI', '-', 5500, 14, 2, 10, 'ML', '{"17":"ANALGETIKA"}', 'ML', 'ampul', 4, 0, 0, 0, 0, 'unverified'),
(167, 174, 2102040201, '236', '2361', 'NEUROTROPIK VITAMIN TABLET', '-', 443, 13, 2, 2, 'MG', '{"17":"ANALGETIKA"}', 'MG', 'TABLET', 4, 0, 0, 0, 0, 'unverified'),
(168, 175, 2102040201, '237', '2371', 'NIFEDIPIN TABLET 10 MG', '-', 149, 13, 2, 2, 'MG', '{"17":"ANALGETIKA"}', 'MG', 'TABLET', 4, 0, 0, 0, 0, 'unverified'),
(169, 176, 2102040201, '238', '2381', 'NISTATIN ORAL DROP', '-', 33000, 16, 2, 5, 'MG', '{"17":"ANALGETIKA"}', 'MG', 'FLS', 4, 0, 0, 0, 0, 'unverified'),
(170, 177, 2102040201, '239', '2391', 'NISTATIN TABLET SALUT 500.000 IU', '-', 630, 13, 2, 5, 'MG', '{"17":"ANALGETIKA"}', 'MG', 'TABLET', 4, 0, 0, 0, 0, 'unverified'),
(171, 178, 2102040201, '240', '2401', 'NISTATIN TABLET VAGINAL 100.000 IU / G', '-', 389, 13, 2, 2, 'G', '{"22":"ANESTETIKA"}', 'G', 'TABLET', 4, 0, 0, 0, 0, 'unverified'),
(172, 179, 2102040201, '241', '2411', 'NORIT (CARBO ADSORBEN) TABLET', '-', 225750, 13, 2, 2, 'G', '{"17":"ANALGETIKA"}', 'G', 'TABLET', 4, 0, 0, 0, 0, 'unverified'),
(173, 180, 2102040201, '242', '2421', 'OBAT ANTI TUBERKULOSIS KATEGORI ANAK ( FDC )', '-', 225750, 16, 2, 9, 'G', '{"17":"ANALGETIKA"}', 'G', 'PAKET', 4, 0, 0, 0, 0, 'unverified'),
(174, 181, 2102040201, '243', '2431', 'OBAT ANTI TUBERKULOSIS KATEGORI I ( FDC )', '-', 377999, 16, 2, 9, 'G', '{"22":"ANESTETIKA"}', 'G', 'PAKET', 4, 0, 0, 0, 0, 'unverified'),
(175, 182, 2102040201, '244', '2441', 'OBAT ANTI TUBERKULOSIS KATEGORI II ( FDC )', '-', 1260000, 16, 2, 9, 'G', '{"17":"ANALGETIKA"}', 'G', 'PAKET', 4, 0, 0, 0, 0, 'unverified'),
(176, 183, 2102040201, '245', '2451', 'OBAT ANTI TUBERKULOSIS KATEGORI III', '-', 154000, 16, 2, 9, 'G', '{"22":"ANESTETIKA"}', 'G', 'PAKET', 4, 0, 0, 0, 0, 'unverified'),
(177, 184, 2102040201, '246', '2461', 'OBAT ANTI TUBERKULOSIS SISIPAN', '-', 66000, 16, 2, 9, 'G', '{"22":"ANESTETIKA"}', 'G', 'PAKET', 4, 0, 0, 0, 0, 'unverified'),
(178, 185, 2102040201, '247', '2471', 'OBAT BATUK HITAM ( O B H ) SIRUP 100 ML.', '-', 1502, 1, 2, 5, 'ML', '{"22":"ANESTETIKA"}', '100 ML', 'BOTOL', 4, 0, 0, 0, 0, 'unverified'),
(179, 186, 2102040201, '248', '2481', 'OKSITETRASIKLIN HCL INJEKSI I.M 50 MG/ ML - 10 ML', '-', 3255, 9, 2, 2, 'ML', '{"22":"ANESTETIKA"}', '10 ML', 'VIAL', 4, 0, 0, 0, 0, 'unverified'),
(180, 187, 2102040201, '249', '2491', 'OKSITETRASIKLIN HCL SALAP 3 % 5 G', '-', 1734, 12, 2, 8, 'G', '{"22":"ANESTETIKA"}', '5G', 'TUBE', 4, 0, 0, 0, 0, 'unverified'),
(181, 188, 2102040201, '250', '2501', 'OKSITETRASIKLIN HCL SALAP MATA 1 % @ 3,5 G.', '-', 2016, 12, 2, 8, 'G', '{"17":"ANALGETIKA"}', '3.5G', 'TUBE', 4, 0, 0, 0, 0, 'unverified'),
(182, 189, 2102040201, '251', '2511', 'OKSITOKSIN INJ 10 IU / ML-1 ML', '-', 1785, 14, 2, 9, 'ML', '{"17":"ANALGETIKA"}', '1 ML', 'AMPUL', 4, 0, 0, 0, 0, 'unverified'),
(183, 190, 2102040201, '252', '2521', 'OMEPRAZOL CAPSUL 20 MG', '-', 423, 7, 2, 9, 'MG', '{"17":"ANALGETIKA"}', '20 MG', 'KAPSUL', 4, 0, 0, 0, 0, 'unverified'),
(184, 191, 2102040201, '253', '2531', 'PANCREATIN KOMB.  TABLET', '-', 440, 13, 2, 2, 'G', '{"17":"ANALGETIKA"}', 'G', 'TABLET', 4, 0, 0, 0, 0, 'unverified'),
(185, 192, 2102040201, '254', '2541', 'PAPAVERIN TABLET 40 MG', '-', 601, 13, 2, 2, 'MG', '{"22":"ANESTETIKA"}', '40 MG', 'TABLET', 4, 0, 0, 0, 0, 'unverified'),
(186, 193, 2102040201, '255', '2551', 'PAPAVERIN INJ 40 MG / ML', '-', 364, 14, 2, 10, 'ML', '{"17":"ANALGETIKA"}', '40 MG', 'AMPUL', 4, 0, 0, 0, 0, 'unverified'),
(187, 194, 2102040201, '256', '2561', 'PARASETAMOL DROP', '-', 5040, 1, 2, 9, 'ML', '{"17":"ANALGETIKA"}', 'ML', 'BOTOL', 4, 0, 0, 0, 0, 'unverified'),
(188, 195, 2102040201, '257', '2571', 'PARASETAMOL TABLET 500 MG', '-', 110, 13, 2, 2, 'MG', '{"17":"ANALGETIKA"}', '500 MG', 'TABLET', 4, 0, 0, 0, 0, 'unverified'),
(189, 196, 2102040201, '258', '2581', 'PARASETAMOL TABLET 100 MG', '-', 44, 13, 2, 2, 'MG', '{"22":"ANESTETIKA"}', '100 MG', 'TABLET', 4, 0, 0, 0, 0, 'unverified'),
(190, 197, 2102040201, '259', '2591', 'PARASETAMOL SYR 120MG / 5 ML', '-', 2415, 1, 2, 10, 'ML', '{"22":"ANESTETIKA"}', '120 MG', 'BOTOL', 4, 0, 0, 0, 0, 'unverified'),
(191, 198, 2102040201, '260', '2601', 'PIRANTEL TABLET 125 MG', '-', 336, 13, 2, 2, 'MG', '{"22":"ANESTETIKA"}', '125 MG', 'TABLET', 4, 0, 0, 0, 0, 'unverified'),
(192, 199, 2102040201, '261', '2611', 'PIRASETAM TABLET 400 MG', '-', 527, 13, 2, 2, 'MG', '{"17":"ANALGETIKA"}', '400 MG', 'TABLET', 4, 0, 0, 0, 0, 'unverified'),
(193, 200, 2102040201, '262', '2621', 'PIRASETAM INJ ', '-', 5775, 3, 2, 10, 'ML', '{"22":"ANESTETIKA"}', 'ML', 'AMPUL', 4, 0, 0, 0, 0, 'unverified'),
(194, 201, 2102040201, '263', '2631', 'PIRATIAZIN TEOKLAT + PIRIDOKSIN TABLET', '-', 2200, 13, 2, 2, 'G', '{"22":"ANESTETIKA"}', 'G', 'TABLET', 4, 0, 0, 0, 0, 'unverified'),
(195, 202, 2102040201, '264', '2641', 'PIRAZINAMID TABLET 500 MG', '-', 220, 13, 2, 2, 'MG', '{"22":"ANESTETIKA"}', '500', 'TABLET', 4, 0, 0, 0, 0, 'unverified'),
(196, 203, 2102040201, '265', '2651', 'PIRIDOKSIN  TABLET 10 MG', '-', 18, 13, 2, 2, 'ML', '{"22":"ANESTETIKA"}', '10 MG', 'TABLET', 4, 0, 0, 0, 0, 'unverified'),
(197, 204, 2102040201, '266', '2661', 'PIRIDOKSIN INJEKSI 1 ML.', '-', 13, 14, 2, 10, 'ML', '{"17":"ANALGETIKA"}', 'ML', '1 ML', 4, 0, 0, 0, 0, 'unverified'),
(198, 205, 2102040201, '267', '2671', 'PIROKSIKAM  CAPSUL 10 MG', '-', 88, 7, 2, 3, 'MG', '{"22":"ANESTETIKA"}', '20 MG', 'KAPSUL', 4, 0, 0, 0, 0, 'unverified'),
(199, 206, 2102040201, '268', '2681', 'PIROKSIKAM  TABLET  20 MG', '-', 110, 13, 2, 2, 'MG', '{"22":"ANESTETIKA"}', '20 MG', 'TABLET', 4, 0, 0, 0, 0, 'unverified'),
(200, 207, 2102040201, '269', '2691', 'PETHIDIN HCL  50 MG/ML INJEKSI', '-', 11992, 14, 2, 10, 'ML', '{"17":"ANALGETIKA"}', '50 MG/ML', 'AMPUL', 4, 0, 0, 0, 0, 'unverified'),
(201, 208, 2102040201, '270', '2701', 'POLIKRESULEN LARUTAN 360 MG/ML @ 10 ML.', '-', 33550, 1, 2, 9, 'ML', '{"22":"ANESTETIKA"}', '360 MG/ML', 'BOTOL', 4, 0, 0, 0, 0, 'unverified'),
(202, 209, 2102040201, '271', '2711', 'POVIDON IODIDA 10 %  30 ML', '-', 2520, 1, 2, 9, 'ML', '{"17":"ANALGETIKA"}', '10 ML', 'BOTOL', 4, 0, 0, 0, 0, 'unverified'),
(203, 210, 2102040201, '272', '2721', 'POVIDON IODIDA 10 %  300 ML', '-', 13841, 1, 2, 9, 'ML', '{"22":"ANESTETIKA"}', '300 ML', 'BOTOL', 4, 0, 0, 0, 0, 'unverified'),
(204, 211, 2102040201, '273', '2731', 'PREDNISON TABLET 5 MG', '-', 66, 13, 2, 2, 'MG', '{"17":"ANALGETIKA"}', '5 MG', 'TABLET', 4, 0, 0, 0, 0, 'unverified'),
(205, 212, 2102040201, '274', '2741', 'PRIMAKUIN TABLET 15 MG', '-', 30, 13, 2, 2, 'MG', '{"22":"ANESTETIKA"}', '15 MG', 'TABLET', 4, 0, 0, 0, 0, 'unverified'),
(206, 213, 2102040201, '275', '2751', 'PROKAIN BENZIL PENISILLIN INJEKSI 3 JUTA IU ', '-', 375, 9, 2, 9, 'G', '{"22":"ANESTETIKA"}', 'G', 'VIAL', 4, 0, 0, 0, 0, 'unverified'),
(207, 214, 2102040201, '276', '2761', 'PROPANOLOL HCL TABLET 40 MG', '-', 82, 13, 2, 2, 'MG', '{"17":"ANALGETIKA"}', '40 MG', 'TABLET', 4, 0, 0, 0, 0, 'unverified'),
(208, 215, 2102040201, '277', '2771', 'PROPANOLOL TABLET 10 MG', '-', 58, 13, 2, 2, 'MG', '{"17":"ANALGETIKA"}', '10 MG', 'TABLET', 4, 0, 0, 0, 0, 'unverified'),
(209, 216, 2102040201, '278', '2781', 'PROPILTIOURASIL TABLET 100 MG', '-', 315, 13, 2, 2, 'MG', '{"17":"ANALGETIKA"}', '100 MG', 'TABLET', 4, 0, 0, 0, 0, 'unverified'),
(210, 217, 2102040201, '279', '2791', 'RANITIDINE TABLET 150 MG', '-', 231, 13, 2, 2, 'MG', '{"22":"ANESTETIKA"}', '150 MG', 'TABLET', 4, 0, 0, 0, 0, 'unverified'),
(211, 218, 2102040201, '280', '2801', 'RANITIDINE INJ', '-', 2699, 14, 2, 9, 'ML', '{"17":"ANALGETIKA"}', 'ML', 'AMPUL', 4, 0, 0, 0, 0, 'unverified'),
(212, 219, 2102040201, '281', '2811', 'REAGEN PEMERIKSA GOL DARAH ANTI A DAN ANTI B', '-', 84000, 16, 2, 9, 'SET', '{"22":"ANESTETIKA"}', 'MG', 'SET', 4, 0, 0, 0, 0, 'unverified'),
(213, 220, 2102040201, '282', '2821', 'RESERPIN TABLET 0,10 MG', '-', 26, 13, 2, 2, 'MG', '{"17":"ANALGETIKA"}', '600 MG', 'TABLET', 4, 0, 0, 0, 0, 'unverified'),
(214, 221, 2102040201, '283', '2831', 'RESERPIN TABLET 0,25 MG', '-', 45, 13, 2, 2, 'MG', '{"17":"ANALGETIKA"}', '0.25 MG', 'TABLET', 4, 0, 0, 0, 0, 'unverified'),
(215, 222, 2102040201, '284', '2841', 'RIFAMPISIN CAPSUL 300 MG', '-', 334, 7, 2, 3, 'MG', '{"17":"ANALGETIKA"}', '300 MG', 'KAPSUL', 4, 0, 0, 0, 0, 'unverified'),
(216, 223, 2102040201, '285', '2851', 'RIFAMPISIN KAPLET  450 MG', '-', 430, 15, 2, 5, 'MG', '{"17":"ANALGETIKA"}', '450 MG', 'KAPLET', 4, 0, 0, 0, 0, 'unverified'),
(217, 224, 2102040201, '286', '2861', 'RIFAMPISIN KAPLET  600 MG', '-', 671, 15, 2, 5, 'MG', '{"17":"ANALGETIKA"}', '600 MG', 'KAPLET', 4, 0, 0, 0, 0, 'unverified'),
(218, 225, 2102040201, '287', '2871', 'RINGER LAKTAT LARUTAN INFUS STERIL 500 ML', '-', 5697, 1, 2, 9, 'ML', '{"17":"ANALGETIKA"}', '500 ML', 'BOTOL', 4, 0, 0, 0, 0, 'unverified'),
(219, 226, 2102040201, '288', '2881', 'SALAP 2 - 4 KOMBINASI : AS.SALISILAT 2 % + BELERANG ENDAP 4 % @ 30 G', '-', 825, 12, 2, 9, 'ML', '{"17":"ANALGETIKA"}', 'ML', 'TUBE', 4, 0, 0, 0, 0, 'unverified'),
(220, 227, 2102040201, '289', '2891', 'SALBUTAMOL TABLET 2 MG', '-', 88, 13, 2, 2, 'MG', '{"22":"ANESTETIKA"}', '2 MG', 'TABLET', 4, 0, 0, 0, 0, 'unverified'),
(221, 228, 2102040201, '290', '2901', 'SALBUTAMOL TABLET 4 MG', '-', 102, 13, 2, 2, 'MG', '{"17":"ANALGETIKA"}', '4 MG', 'TABLET', 4, 0, 0, 0, 0, 'unverified'),
(222, 229, 2102040201, '291', '2911', 'SALISIL BEDAK 2 % @ 50 G', '-', 1502, 4, 2, 1, 'G', '{"17":"ANALGETIKA"}', '50 G', 'BUNGKUS', 4, 0, 0, 0, 0, 'unverified'),
(223, 230, 2102040201, '292', '2921', 'SEMEN SENG FOSFAT SERBUK & CAIRAN', '-', 102300, 16, 2, 9, 'ML', '{"22":"ANESTETIKA"}', 'ML', 'SET', 4, 0, 0, 0, 0, 'unverified'),
(224, 231, 2102040201, '293', '2931', 'SERUM ANTI BISA ULAR POLIVALEN INJEKSI 5 ML ( ABU I )', '-', 21000, 9, 2, 9, 'ML', '{"22":"ANESTETIKA"}', '5 ML', 'VIAL', 4, 0, 0, 0, 0, 'unverified'),
(225, 232, 2102040201, '294', '2941', 'SERUM ANTI DIFTERI INJEKSI 20.000 IU / VIAL  (ADS)', '-', 732848, 9, 2, 9, 'ML', '{"17":"ANALGETIKA"}', 'ML', 'VIAL', 4, 0, 0, 0, 0, 'unverified'),
(226, 233, 2102040201, '295', '2951', 'SERUM ANTI TETANUS INJEKSI 1.500 IU / AMPUL (ATS)', '-', 50000, 14, 2, 10, 'ML', '{"17":"ANALGETIKA"}', '1.500 IU', 'AMPUL', 4, 0, 0, 0, 0, 'unverified'),
(227, 234, 2102040201, '296', '2961', 'SERUM ANTI TETANUS INJEKSI 20.000 IU / VIAL (ATS)', '-', 451000, 9, 2, 9, 'ML', '{"17":"ANALGETIKA"}', '20000 IU', 'VIAL', 4, 0, 0, 0, 0, 'unverified'),
(228, 235, 2102040201, '297', '2971', 'SIANOKOBALAMIN  INJEKSI 500 MCG/ML - 1 ML', '-', 496, 14, 2, 9, 'ML', '{"17":"ANALGETIKA"}', '500 MCG/ML', 'AMPUL', 4, 0, 0, 0, 0, 'unverified'),
(229, 236, 2102040201, '298', '2981', 'SIANOKOBALAMIN  TABLET 50 MCG', '-', 14, 13, 2, 2, 'MCG', '{"17":"ANALGETIKA"}', '50 MCG', 'TABLET', 4, 0, 0, 0, 0, 'unverified'),
(230, 237, 2102040201, '299', '2991', 'SILVER AMALGAM SERBUK 65 - 75 % 1 OZ', '-', 619300, 1, 2, 9, '1 OZ', '{"22":"ANESTETIKA"}', '65-75 %', 'BOTOL', 4, 0, 0, 0, 0, 'unverified'),
(231, 238, 2102040201, '300', '3001', 'SIMVASTATIN TABLET 10 MG', '-', 539, 13, 2, 2, 'MG', '{"22":"ANESTETIKA"}', 'MG', 'TABLET', 4, 0, 0, 0, 0, 'unverified'),
(232, 239, 2102040201, '3001', '30011', 'SPONS GELATIN CUBICLES 1 X 1 X 1 CM @ 24 PCS', '-', 8525, 18, 2, 1, '24 PCS', '{"22":"ANESTETIKA"}', 'PCS', 'KOTAK', 4, 0, 0, 0, 0, 'unverified'),
(233, 240, 2102040201, '302', '3021', 'STREPTOMISIN INJEKSI 1500 MG / ML @ 15 ML.', '-', 54840, 9, 2, 9, 'ML', '{"22":"ANESTETIKA"}', '1500 MG/ML', 'VIAL', 4, 0, 0, 0, 0, 'unverified'),
(234, 241, 2102040201, '303', '3031', 'TEMPORARY STOPPING FLETCHER  SERBUK 100 G. & CAIRAN', '-', 35200, 16, 2, 9, '100 G', '{"22":"ANESTETIKA"}', 'CAIRAN', 'SET', 4, 0, 0, 0, 0, 'unverified'),
(235, 242, 2102040201, '304', '3041', 'TEOFILIN TABLET 10 MG', '-', 57, 13, 2, 2, 'MG', '{"17":"ANALGETIKA"}', '10 MG', 'TABLET', 4, 0, 0, 0, 0, 'unverified'),
(236, 243, 2102040201, '305', '3051', 'TERBUTALIN TABLET 2,5 MG', '-', 2035, 13, 2, 2, 'MG', '{"17":"ANALGETIKA"}', '2,5 MG', 'TABLET', 4, 0, 0, 0, 0, 'unverified'),
(237, 244, 2102040201, '306', '3061', 'TETRASIKLIN HCL KAPSUL 500 MG', '-', 177, 7, 2, 3, 'MG', '{"17":"ANALGETIKA"}', '500 MG', 'KAPSUL', 4, 0, 0, 0, 0, 'unverified'),
(238, 245, 2102040201, '307', '3071', 'TIAMFENIKOL CAPSUL 500 MG', '-', 409, 7, 2, 3, 'MG', '{"22":"ANESTETIKA"}', '500 MG', 'KAPSUL', 4, 0, 0, 0, 0, 'unverified'),
(239, 246, 2102040201, '308', '3081', 'TIAMIN HCL  INJEKSI 100 MG / ML - 1 ML', '-', 735, 14, 2, 9, 'ML', '{"22":"ANESTETIKA"}', '100 MG', 'AMPUL', 4, 0, 0, 0, 0, 'unverified'),
(240, 247, 2102040201, '309', '3091', 'TIAMIN HCL  TABLET 50 MG', '-', 40, 13, 2, 2, 'MG', '{"22":"ANESTETIKA"}', '50 MG', 'TABLET', 4, 0, 0, 0, 0, 'unverified'),
(241, 248, 2102040201, '310', '3101', 'TRAMADOL CAPSUL 50 MG', '-', 347, 7, 2, 3, 'MG', '{"22":"ANESTETIKA"}', '50 MG', 'KAPSUL', 4, 0, 0, 0, 0, 'unverified'),
(242, 249, 2102040201, '311', '3111', 'TRAMADOL INJEKSI', '-', 6403, 14, 2, 10, 'ML', '{"17":"ANALGETIKA"}', 'ML', 'AMPUL', 4, 0, 0, 0, 0, 'unverified'),
(243, 250, 2102040201, '312', '3121', 'TRIHEKSIFENIDIL HIDROKLORIDA TAB 2 MG ', '-', 100, 13, 2, 2, 'MG', '{"17":"ANALGETIKA"}', '2 MG', 'TABLET', 4, 0, 0, 0, 0, 'unverified'),
(244, 251, 2102040201, '313', '3131', 'TRIKRESOL FORMALIN (TKF)', '-', 88000, 1, 2, 9, 'ML', '{"17":"ANALGETIKA"}', 'ML', 'BOTOL', 4, 0, 0, 0, 0, 'unverified'),
(245, 252, 2102040201, '314', '3141', 'TRIPOLIDIN + PSEUDOEFEDRIN TABLET', '-', 462, 13, 2, 2, 'G', '{"22":"ANESTETIKA"}', 'G', 'TABLET', 4, 0, 0, 0, 0, 'unverified'),
(246, 253, 2102040201, '315', '3151', 'TUMPATAN COMPOSITE (LIGHT CURED)', '-', 1522400, 16, 2, 3, 'G', '{"17":"ANALGETIKA"}', 'G', 'SET', 4, 0, 0, 0, 0, 'unverified'),
(247, 254, 2102040201, '316', '3161', 'TUMPATAN SEMENTARA', '-', 442200, 18, 2, 1, 'G', '{"22":"ANESTETIKA"}', 'G', 'KOTAK', 4, 0, 0, 0, 0, 'unverified'),
(248, 255, 2102040201, '317', '3171', 'VITAMIN B-KOMPLEKS TABLET', '-', 23, 13, 2, 2, 'G', '{"17":"ANALGETIKA"}', 'G', 'TABLET', 4, 0, 0, 0, 0, 'unverified'),
(249, 256, 2102040201, '318', '3181', 'ZINC ', '-', 499, 13, 2, 2, 'G', '{"22":"ANESTETIKA"}', 'G', 'TABLET', 4, 0, 0, 0, 0, 'unverified'),
(250, 257, 2102040201, '319', '3191', 'I.V. CATHETER NOMOR 18', '-', 7700, 4, 2, 1, 'G', '{"17":"ANALGETIKA"}', 'G', 'PCS', 4, 0, 0, 0, 0, 'unverified'),
(251, 258, 2102040201, '320', '3201', 'I.V. CATHETER NOMOR 20 ', '-', 12100, 4, 2, 1, 'G', '{"22":"ANESTETIKA"}', 'G', 'PCS', 4, 0, 0, 0, 0, 'unverified'),
(252, 259, 2102040201, '321', '3211', 'I.V. CATHETER NOMOR 22', '-', 12100, 4, 2, 1, 'G', '{"17":"ANALGETIKA"}', 'G', 'PCS', 4, 0, 0, 0, 0, 'unverified'),
(253, 260, 2102040201, '322', '3221', 'I.V. CATHETER NOMOR 24', '-', 12100, 4, 2, 1, 'G', '{"22":"ANESTETIKA"}', 'G', 'PCS', 4, 0, 0, 0, 0, 'unverified'),
(254, 261, 2102040201, '323', '3231', 'ALAT SUNTIK SEKALI PAKAI 1 ML', '-', 3727, 3, 2, 10, 'ML', '{"22":"ANESTETIKA"}', '1 ML', 'PCS', 4, 0, 0, 0, 0, 'unverified'),
(255, 262, 2102040201, '324', '3241', 'ALAT SUNTIK SEKALI PAKAI 2,5 ML', '-', 1606, 3, 2, 10, 'ML', '{"22":"ANESTETIKA"}', '2,5 ML', 'PCS', 4, 0, 0, 0, 0, 'unverified'),
(256, 263, 2102040201, '325', '3251', 'ALAT SUNTIK SEKALI PAKAI 5 ML', '-', 1815, 3, 2, 10, 'ML', '{"22":"ANESTETIKA"}', '5 ML', 'PCS', 4, 0, 0, 0, 0, 'unverified'),
(257, 264, 2102040201, '326', '3261', 'BISTURY / MATA PISAU BEDAH', '-', 398, 3, 2, 1, 'BUAH', '{"22":"ANESTETIKA"}', 'SET', 'PCS', 4, 0, 0, 0, 0, 'unverified'),
(258, 265, 2102040201, '327', '3271', 'BLOOD LANCET', '-', 282, 1, 2, 10, 'ML', '{"17":"ANALGETIKA"}', 'ML', 'PCS', 4, 0, 0, 0, 0, 'unverified'),
(259, 266, 2102040201, '328', '3281', 'OBJECT GLASS / SLIDE GLASS', '-', 31625, 18, 2, 9, 'SET', '{"22":"ANESTETIKA"}', 'SET', 'KOTAK', 4, 0, 0, 0, 0, 'unverified'),
(260, 267, 2102040201, '329', '3291', 'CAT GUT / BENANG BEDAH NO. 2/0 DENGAN JARUM BEDAH', '-', 8708, 3, 2, 13, 'SET', '{"17":"ANALGETIKA"}', 'SET', 'PCS', 4, 0, 0, 0, 0, 'unverified'),
(261, 268, 2102040201, '330', '3301', 'CAT GUT / BENANG BEDAH NO. 3/0 DENGAN JARUM BEDAH', '-', 8708, 3, 2, 11, 'SET', '{"17":"ANALGETIKA"}', 'SET', 'PCS', 4, 0, 0, 0, 0, 'unverified'),
(262, 269, 2102040201, '331', '3311', 'DECT GLASS / COVER GLASS', '-', 140, 3, 2, 11, 'SET', '{"22":"ANESTETIKA"}', 'SET', 'PCS', 4, 0, 0, 0, 0, 'unverified'),
(263, 270, 2102040201, '332', '3321', 'ELASTIS VERBANB 4 INCI ( GENERAL CARE )', '-', 22500, 4, 2, 9, 'SET', '{"17":"ANALGETIKA"}', 'ROLL', 'ROLL', 4, 0, 0, 0, 0, 'unverified'),
(264, 271, 2102040201, '333', '3331', 'FILTER PAPER ', '-', 1150, 3, 2, 11, 'SET', '{"17":"ANALGETIKA"}', 'PCS', 'PCS', 4, 0, 0, 0, 0, 'unverified'),
(265, 272, 2102040201, '334', '3341', 'FOLLEY CATHETER 2. W 30 CC NO.14', '-', 6563, 3, 1, 9, 'SET', '{"22":"ANESTETIKA"}', 'SET', 'PCS', 4, 0, 0, 0, 0, 'unverified'),
(266, 273, 2102040201, '335', '3351', 'FOLLEY CATHETER 2. W 30 CC NO.16', '-', 6563, 3, 2, 9, 'SET', '{"17":"ANALGETIKA"}', 'SET', 'PCS', 4, 0, 0, 0, 0, 'unverified'),
(267, 274, 2102040201, '336', '3361', 'FOLLEY CATHETER 2. W 30 CC NO.18', '-', 6563, 3, 2, 9, 'SET', '{"22":"ANESTETIKA"}', 'SET', 'PCS', 4, 0, 0, 0, 0, 'unverified'),
(268, 275, 2102040201, '337', '3371', 'HANDSCOON  NO.6,5', '-', 1540, 3, 2, 9, 'PASANG', '{"22":"ANESTETIKA"}', 'SET', 'PASANG', 4, 0, 0, 0, 0, 'unverified'),
(269, 276, 2102040201, '338', '3381', 'HANDSCOON  NO.6,5 STERIL', '-', 1540, 3, 2, 9, 'SET', '{"22":"ANESTETIKA"}', 'SET', 'PASANG', 4, 0, 0, 0, 0, 'unverified'),
(270, 277, 2102040201, '339', '3391', 'HANDSCOON  NO.7', '-', 1738, 3, 2, 9, 'SET', '{"22":"ANESTETIKA"}', 'SET', 'PASANG', 4, 0, 0, 0, 0, 'unverified'),
(271, 278, 2102040201, '340', '3401', 'HANDSCOON  NO.7 STERIL', '-', 1540, 3, 2, 9, 'SET', '{"22":"ANESTETIKA"}', 'SET', 'PASANG', 4, 0, 0, 0, 0, 'unverified'),
(272, 279, 2102040201, '341', '3411', 'HANDSCOON  NO.7.5 ( SENSI GLOVES )', '-', 1738, 3, 2, 9, 'SET', '{"17":"ANALGETIKA"}', 'SET', 'PASANG', 4, 0, 0, 0, 0, 'unverified'),
(273, 280, 2102040201, '342', '3421', 'HANDSCOON  NO.7.5 STERIL', '-', 7900, 3, 2, 9, 'SET', '{"22":"ANESTETIKA"}', 'SET', 'PASANG', 4, 0, 0, 0, 0, 'unverified'),
(274, 281, 2102040201, '343', '3431', 'INFUS SET ANAK', '-', 17600, 14, 2, 9, 'SET', '{"17":"ANALGETIKA"}', 'SET', 'SET', 4, 0, 0, 0, 0, 'unverified'),
(275, 282, 2102040201, '344', '3441', 'INFUS SET DEWASA', '-', 17600, 3, 2, 9, 'SET', '{"22":"ANESTETIKA"}', 'SET', 'SET', 4, 0, 0, 0, 0, 'unverified'),
(276, 283, 2102040201, '345', '3451', 'KAPAS BERLEMAK 500 GRAM', '-', 5250, 3, 2, 1, 'ROL', '{"17":"ANALGETIKA"}', 'ROL', 'ROL', 4, 0, 0, 0, 0, 'unverified'),
(277, 284, 2102040201, '346', '3461', 'KAPAS PEMBALUT / ABSORBEN 250 GRAM', '-', 33000, 3, 2, 9, 'ROLL', '{"17":"ANALGETIKA"}', 'ROLL', 'ROLL', 4, 0, 0, 0, 0, 'unverified'),
(278, 285, 2102040201, '347', '3471', 'KASA KOMPRES 40 / 40 STERIL', '-', 2750, 4, 2, 9, 'BUAH', '{"17":"ANALGETIKA"}', 'BUNGKUD', 'BUNGKUS', 4, 0, 0, 0, 0, 'unverified'),
(279, 286, 2102040201, '348', '3481', 'KASA HIDROFIL STERIL 18 X 22 CM (ISI 12 LBR)', '-', 4988, 3, 2, 9, 'BUAH', '{"17":"ANALGETIKA"}', 'BUNGKUS', 'BUNGKUS', 4, 0, 0, 0, 0, 'unverified');
INSERT INTO `master_obat_puskesmas` (`idobat_puskesmas`, `idobat`, `idpuskesmas`, `kode_obat`, `no_reg`, `nama_obat`, `nama_merek`, `harga`, `idsatuan_obat`, `idgolongan`, `idbentuk_sediaan`, `nama_bentuk`, `farmakologi`, `komposisi`, `kemasan`, `idprodusen`, `stock`, `stock_dinas`, `min_stock`, `max_stock`, `status_obat`) VALUES
(280, 287, 2102040201, '349', '3491', 'KASA PEMBALUT 2 M X 80 CM', '-', 11000, 3, 2, 9, 'BUAH', '{"22":"ANESTETIKA"}', 'BUNGKUS', 'BUNGKUS', 4, 0, 0, 0, 0, 'unverified'),
(281, 288, 2102040201, '350', '3501', 'KASA PEMBALUT 16 X 16 STERIL', '-', 11000, 3, 2, 9, 'KOTAK', '{"17":"ANALGETIKA"}', 'KOTAK', 'KOTAK', 4, 0, 0, 0, 0, 'unverified'),
(282, 289, 2102040201, '351', '3511', 'KASA PEMBALUT HIDROFIL 4 M X 10 CM', '-', 3520, 3, 2, 9, 'ROLL', '{"17":"ANALGETIKA"}', 'ROLL', 'ROLL', 4, 0, 0, 0, 0, 'unverified'),
(283, 290, 2102040201, '352', '3521', 'KASA PEMBALUT HIDROFIL 4 M X 5 CM', '-', 1760, 3, 2, 9, 'ROLL', '{"17":"ANALGETIKA"}', 'ROLL', 'ROLL', 4, 0, 0, 0, 0, 'unverified'),
(284, 291, 2102040201, '353', '3531', 'KASA PEMBALUT HIDROFIL 4 M X 15 CM', '-', 4950, 3, 2, 9, 'ROLL', '{"17":"ANALGETIKA"}', 'ROLL', 'ROLL', 4, 0, 0, 0, 0, 'unverified'),
(285, 292, 2102040201, '354', '3541', 'KASA PEMBALUT HIDROFIL 4 M X 3 CM', '-', 1650, 3, 2, 9, 'ROLL', '{"17":"ANALGETIKA"}', 'ROLL', 'ROLL', 4, 0, 0, 0, 0, 'unverified'),
(286, 293, 2102040201, '355', '3551', 'KATETER NELATON NO. 14', '-', 15000, 3, 2, 1, 'PCS', '{"22":"ANESTETIKA"}', 'PCS', 'PCS', 4, 0, 0, 0, 0, 'unverified'),
(287, 294, 2102040201, '356', '3561', 'KATETER NELATON NO. 16', '-', 15000, 3, 2, 9, 'PCS', '{"17":"ANALGETIKA"}', 'PCS', 'PCS', 4, 0, 0, 0, 0, 'unverified'),
(288, 295, 2102040201, '357', '3571', 'KATETER NELATON NO. 18', '-', 15000, 3, 2, 9, 'PCS', '{"17":"ANALGETIKA"}', 'PCS', 'PCS', 4, 0, 0, 0, 0, 'unverified'),
(289, 296, 2102040201, '358', '3581', 'LAMPU SPIRITUS', '-', 57500, 3, 2, 11, 'PCS', '{"17":"ANALGETIKA"}', 'PCS', 'PCS', 4, 0, 0, 0, 0, 'unverified'),
(290, 297, 2102040201, '359', '3591', 'MASKER DISPOSIBLE 3 PLY ( DIAPRO )', '-', 341, 3, 2, 11, 'PCS', '{"17":"ANALGETIKA"}', 'PCS', 'PCS', 4, 0, 0, 0, 0, 'unverified'),
(291, 298, 2102040201, '360', '3601', 'MASKER N95 ', '-', 28750, 3, 2, 9, 'PCS', '{"17":"ANALGETIKA"}', 'PCS', 'PCS', 4, 0, 0, 0, 0, 'unverified'),
(292, 299, 2102040201, '361', '3611', 'NEEDLE TERUMO 23 G', '-', 825, 3, 2, 9, 'PCS', '{"22":"ANESTETIKA"}', 'PCS', 'PCS', 4, 0, 0, 0, 0, 'unverified'),
(293, 300, 2102040201, '362', '3621', 'NEEDLE TERUMO 24 G', '-', 825, 3, 2, 9, 'PCS', '{"17":"ANALGETIKA"}', 'PCS', 'PCS', 4, 0, 0, 0, 0, 'unverified'),
(294, 301, 2102040201, '363', '3631', 'NEEDLE TERUMO 25 G', '-', 825, 3, 2, 9, 'PCS', '{"17":"ANALGETIKA"}', 'PCS', 'PCS', 4, 0, 0, 0, 0, 'unverified'),
(295, 302, 2102040201, '364', '3641', 'PEMBALUT GIPS', '-', 5600, 3, 2, 9, 'ROLL', '{"17":"ANALGETIKA"}', 'ROLL', 'ROLL', 4, 0, 0, 0, 0, 'unverified'),
(296, 303, 2102040201, '365', '3651', 'PLESTER 5 YARDS X 2 INCI', '-', 12100, 3, 2, 9, 'ROLL', '{"17":"ANALGETIKA"}', 'ROLL', 'ROL', 4, 0, 0, 0, 0, 'unverified'),
(297, 304, 2102040201, '366', '3661', 'PLESTER TAHAN AIR ( 25 CM X 10 CM )', '-', 22000, 3, 2, 9, 'BUAH', '{"17":"ANALGETIKA"}', 'BUNGKUS', 'BUNGKUS', 4, 0, 0, 0, 0, 'unverified'),
(298, 305, 2102040201, '367', '3671', 'POT SPUTUM', '-', 3795, 3, 2, 9, 'BUAH', '{"17":"ANALGETIKA"}', 'BUAH', 'BUAH', 4, 0, 0, 0, 0, 'unverified'),
(299, 306, 2102040201, '368', '3681', 'RAPID TEST FOR MALARIA', '-', 47410, 3, 2, 9, 'BUAH', '{"17":"ANALGETIKA"}', 'BUAH', 'BUAH', 4, 0, 0, 0, 0, 'unverified'),
(300, 307, 2102040201, '369', '3691', 'RAPID TEST FOR DENGUE IGG/IGM CASSETTE', '-', 159280, 3, 2, 9, 'BUAH', '{"17":"ANALGETIKA"}', 'BUAH', 'BUAH', 4, 0, 0, 0, 0, 'unverified'),
(301, 308, 2102040201, '370', '3701', 'S I L K  ( BENANG BEDAH SUTERA ) NO. 3.0  75 CM DENGAN JARUM BEDAH', '-', 5500, 3, 2, 9, 'BUAH', '{"22":"ANESTETIKA"}', 'BUAH', 'BUAH', 4, 0, 0, 0, 0, 'unverified'),
(302, 309, 2102040201, '371', '3711', 'TABUNG HEMATOKRIT ( ASSISTANT )', '-', 1725, 3, 2, 9, 'BUAH', '{"17":"ANALGETIKA"}', 'BUAH', 'BUAH', 4, 0, 0, 0, 0, 'unverified'),
(303, 310, 2102040201, '372', '3721', 'TABUNG WESTERGREEN', '-', 5895, 3, 2, 9, 'BUAH', '{"17":"ANALGETIKA"}', 'BUAH', 'BUAH', 4, 0, 0, 0, 0, 'unverified'),
(304, 311, 2102040201, '373', '3731', 'TABUNG REAKSI PURPLE CAP, EDTA VOL 3 ML', '-', 2277, 3, 1, 9, 'BUAH', '{"22":"ANESTETIKA"}', 'BUAH', 'BUAH', 4, 0, 0, 0, 0, 'unverified'),
(305, 312, 2102040201, '374', '3741', 'TES KEHAMILAN', '-', 9460, 3, 2, 9, 'BUAH', '{"17":"ANALGETIKA"}', 'BUAH', 'BUAH', 4, 0, 0, 0, 0, 'unverified'),
(306, 313, 2102040201, '375', '3751', 'TEST STRIP URINALYSA 10 PARAMETER', '-', 313500, 3, 2, 9, 'B', '{"17":"ANALGETIKA"}', 'BUAH', 'BUAH', 4, 0, 0, 0, 0, 'unverified'),
(307, 314, 2102040201, '376', '3761', 'URINE BAG', '-', 3410, 3, 2, 9, 'BUAH', '{"22":"ANESTETIKA"}', 'BUAH', 'BUAH', 4, 0, 0, 0, 0, 'unverified'),
(308, 315, 2102040201, '121', '121', 'FRAMISETIN SULFAT GAUZE DRESSING 10 CM X 10 CM', '-', 8800, 8, 4, 11, '-', '{"27":"ANTI HIPERTENSI"}', 'Framisetin sulfat gauze dressing 10 cm x 10 cm', 'Lembat', 1, 0, 0, 0, 0, 'unverified'),
(309, 316, 2102040201, '122', '122', 'FUROSEMID INJEKSI 10 MG/ML @ 2 ML.', '-', 1932, 14, 1, 11, '-', '{"27":"ANTI HIPERTENSI"}', 'Furosemid injeksi 10 mg/ml @ 2 ml.', 'Ampul', 3, 0, 0, 0, 0, 'unverified'),
(310, 317, 2102040201, '123', '123', 'FUROSEMID TABLET 40 MG', '-', 107, 13, 4, 8, '-', '{"22":"ANESTETIKA"}', 'Furosemid tablet 40 mg', 'Tablet', 2, 0, 0, 0, 0, 'unverified'),
(311, 318, 2102040201, '124', '124', 'GAMEKSAN EMULSI 1 % ', '-', 4400, 1, 1, 10, '-', '{"27":"ANTI HIPERTENSI"}', 'Gameksan Emulsi 1 % ', 'Botol', 5, 0, 0, 0, 0, 'unverified'),
(312, 319, 2102040201, '125', '125', 'GARAM ORALIT  UNTUK 200 ML AIR', '-', 399, 19, 1, 5, '-', '{"27":"ANTI HIPERTENSI"}', 'alit  untuk 200 ml air', 'Sachet', 3, 0, 0, 0, 0, 'unverified'),
(313, 320, 2102040201, '126', '126', 'GEMFIBROZIL CAPSUL 300 MG', '-', 366, 7, 3, 8, '-', '{"8":"ANTI TBC"}', 'Gemfibrozil capsul 300 mg', 'kapsul', 4, 0, 0, 0, 0, 'unverified'),
(314, 321, 2102040201, '127', '127', 'GEMFIBROZIL CAPSUL 600 MG', '-', 546, 7, 3, 3, '-', '{"27":"ANTI HIPERTENSI"}', 'Gemfibrozil capsul 600 mg', 'Kapsul', 3, 0, 0, 0, 0, 'unverified'),
(315, 322, 2102040201, '128', '128', 'GENTAMISIN INJEKSI 80 MG', '-', 3528, 9, 2, 5, '-', '{"8":"ANTI TBC"}', 'Gentamisin injeksi 80 mg', 'Vial', 2, 0, 0, 0, 0, 'unverified'),
(316, 323, 2102040201, '129', '129', 'GENTAMISIN SALEP KULIT 5 G', '-', 1929, 12, 1, 8, '-', '{"10":"ANTASIDA DAN ANTIULCER"}', 'Gentamisin salep kulit 5 g', 'Tube', 3, 0, 0, 0, 0, 'unverified'),
(317, 324, 2102040201, '130', '130', 'GENTAMISIN SULFAT TETES MATA 0.3%', '-', 3465, 1, 4, 10, '-', '{"27":"ANTI HIPERTENSI"}', 'Gentamisin Sulfat tetes mata 0.3%', 'Botol', 3, 0, 0, 0, 0, 'unverified'),
(318, 325, 2102040201, '131', '131', 'GENTIAN VIOLET LARUTAN 1 % 10 ML', '-', 473, 1, 1, 10, '-', '{"10":"ANTASIDA DAN ANTIULCER"}', 'Gentian Violet larutan 1 % 10 ml', 'Botol', 3, 0, 0, 0, 0, 'unverified'),
(319, 326, 2102040201, '132', '132', 'GLASS IONOMER SEMEN (SERBUK + LARUTAN)', '-', 4140000, 16, 2, 5, '-', '{"8":"ANTI TBC"}', 'Glass Ionomer semen (serbuk + larutan)', 'Set', 3, 0, 0, 0, 0, 'unverified'),
(320, 327, 2102040201, '133', '133', 'GLIBENKLAMID TABLET 5 MG', '-', 76, 13, 2, 8, '-', '{"4":"ANTHELMETIKA"}', 'Glibenklamid tablet 5 mg', 'Tablet', 3, 0, 0, 0, 0, 'unverified'),
(321, 328, 2102040201, '134', '134', 'GLIMEPIRIDE TABLET 1 MG', '-', 914, 13, 1, 8, '-', '{"27":"ANTI HIPERTENSI"}', 'Glimepiride tablet 1 mg', 'Tablet', 2, 0, 0, 0, 0, 'unverified'),
(322, 329, 2102040201, '135', '135', 'GLISERIL GUAYAKOLAT TABLET 100 MG', '-', 29, 13, 2, 8, '-', '{"27":"ANTI HIPERTENSI"}', 'Gliseril Guayakolat tablet 100 mg', 'Tablet', 3, 0, 0, 0, 0, 'unverified'),
(323, 330, 2102040201, '136', '136', 'GLISERIN CAIRAN 100 ML', '-', 3990, 1, 1, 8, '-', '{"27":"ANTI HIPERTENSI"}', 'Gliserin cairan 100 ml', 'Botol', 3, 0, 0, 0, 0, 'unverified'),
(324, 331, 2102040201, '137', '137', 'GLUKOSA LARUTAN INFUS    5 % STERIL  500 ML', '-', 5313, 1, 1, 10, '-', '{"27":"ANTI HIPERTENSI"}', 'Glukosa Larutan infus    5 % steril  500 ml', 'Botol', 2, 0, 0, 0, 0, 'unverified'),
(325, 332, 2102040201, '138', '138', 'GLUKOSA LARUTAN INFUS  40 % STERIL   ', '-', 1525, 1, 2, 8, '-', '{"4":"ANTHELMETIKA"}', 'Glukosa Larutan infus  40 % steril   ', 'Botol', 3, 0, 0, 0, 0, 'unverified'),
(326, 333, 2102040201, '139', '139', 'GLUKOSA LARUTAN INFUS 10 %  STERIL  500 ML.', '-', 4100, 1, 2, 11, '-', '{"27":"ANTI HIPERTENSI"}', 'Glukosa Larutan infus 10 %  steril  500 ml.', 'Botol', 3, 0, 0, 0, 0, 'unverified'),
(327, 334, 2102040201, '140', '140', 'GRISEOFULVIN TABLET 125 MG, MICRONIZED', '-', 252, 1, 4, 8, '-', '{"27":"ANTI HIPERTENSI"}', 'Griseofulvin tablet 125 mg, micronized', 'Botol', 3, 0, 0, 0, 0, 'unverified'),
(328, 335, 2102040201, '141', '141', 'HALOPERIDOL TABLET 1,5 MG', '-', 83, 1, 2, 8, '-', '{"27":"ANTI HIPERTENSI"}', 'Haloperidol tablet 1,5 mg', 'Botol', 2, 0, 0, 0, 0, 'unverified'),
(329, 336, 2102040201, '142', '142', 'HIDROKLORTIAZIDA  TABLET 25 MG', '-', 17, 1, 3, 8, '-', '{"27":"ANTI HIPERTENSI"}', 'Hidroklortiazida  tablet 25 mg', 'Botol', 3, 0, 0, 0, 0, 'unverified'),
(330, 337, 2102040201, '143', '143', 'HIDROKORTISON KRIM 2,5 % @ 5G.', '-', 3128, 12, 2, 8, '-', '{"4":"ANTHELMETIKA"}', 'Hidrokortison krim 2,5 % @ 5g.', 'Tube', 3, 0, 0, 0, 0, 'unverified'),
(331, 338, 2102040201, '144', '144', 'HYOSCINE-N-BUTILBROMIDE TABLET 10 MG', '-', 315, 13, 3, 8, '-', '{"27":"ANTI HIPERTENSI"}', 'Hyoscine-N-Butilbromide tablet 10 mg', 'Tablet', 2, 0, 0, 0, 0, 'unverified'),
(332, 339, 2102040201, '145', '145', 'IBUPROFEN TABLET 200 MG', '-', 110, 13, 1, 8, '-', '{"27":"ANTI HIPERTENSI"}', 'Ibuprofen tablet 200 mg', 'Tablet', 3, 0, 0, 0, 0, 'unverified'),
(333, 340, 2102040201, '146', '146', 'IBUPROFEN TABLET 400 MG', '-', 189, 13, 2, 8, '-', '{"10":"ANTASIDA DAN ANTIULCER"}', 'Ibuprofen tablet 400 mg', 'Tablet', 3, 0, 0, 0, 0, 'unverified'),
(334, 341, 2102040201, '147', '147', 'IBUPROFEN SUSPENSI 100 MG / 5 ML', '-', 3675, 1, 2, 11, '-', '{"4":"ANTHELMETIKA"}', 'Ibuprofen Suspensi 100 mg / 5 ml', 'Botol', 2, 0, 0, 0, 0, 'unverified'),
(335, 342, 2102040201, '148', '148', 'ICHTYOL ( SALAP HITAM )', '-', 4802, 10, 1, 11, '-', '{"27":"ANTI HIPERTENSI"}', 'Ichtyol ( Salap Hitam )', 'Pot', 1, 0, 0, 0, 0, 'unverified'),
(336, 343, 2102040201, '149', '149', 'ISOKSUPRINA HCL TAB. 20 MG', '-', 4070, 13, 1, 8, '-', '{"8":"ANTI TBC"}', 'Isoksuprina HCl tab. 20 mg', 'Tablet', 3, 0, 0, 0, 0, 'unverified'),
(337, 344, 2102040201, '150', '150', 'ISONIAZID TABLET 300 MG', '-', 66, 13, 1, 5, '-', '{"4":"ANTHELMETIKA"}', 'Isoniazid tablet 300 mg', 'Tablet', 3, 0, 0, 0, 0, 'unverified'),
(338, 345, 2102040201, '151', '151', 'ISOSORBID DINITRAT TABLET SUBLINGUAL 5 MG', '-', 85, 13, 1, 10, '-', '{"10":"ANTASIDA DAN ANTIULCER"}', 'Isosorbid Dinitrat tablet sublingual 5 mg', 'Tablet', 1, 0, 0, 0, 0, 'unverified'),
(339, 346, 2102040201, '152', '152', 'KALIUM DIKLOFENAK 25 MG TABLET', '-', 85, 13, 2, 11, '-', '{"4":"ANTHELMETIKA"}', 'Kalium diklofenak 25 mg tablet', 'Tablet', 3, 0, 0, 0, 0, 'unverified'),
(340, 347, 2102040201, '153', '153', 'KALIUM DIKLOFENAK 50 MG TABLET', '-', 85, 13, 1, 3, '-', '{"27":"ANTI HIPERTENSI"}', 'Kalium diklofenak 50 mg tablet', 'Tablet', 2, 0, 0, 0, 0, 'unverified'),
(341, 348, 2102040201, '154', '154', 'KALIUM PERMANGANAT SERBUK 5 G.', '-', 3000, 10, 2, 10, '-', '{"27":"ANTI HIPERTENSI"}', 'Kalium Permanganat serbuk 5 g.', 'Pot', 3, 0, 0, 0, 0, 'unverified'),
(342, 349, 2102040201, '155', '155', 'KALSIUM HIDROKSIDA PASTA @ 2 TUBE', '-', 330000, 16, 4, 8, '-', '{"27":"ANTI HIPERTENSI"}', 'Kalsium Hidroksida pasta @ 2 tube', 'Set', 2, 0, 0, 0, 0, 'unverified'),
(343, 350, 2102040201, '156', '156', 'KALSIUM LAKTAT TABLET 500 MG', '-', 55, 13, 2, 8, '-', '{"27":"ANTI HIPERTENSI"}', 'Kalsium Laktat tablet 500 mg', 'Tablet', 3, 0, 0, 0, 0, 'unverified'),
(344, 351, 2102040201, '157', '157', 'KANAMYCIN INJEKSI 1 GRAM', '-', 11000, 9, 2, 11, '-', '{"27":"ANTI HIPERTENSI"}', 'Kanamycin injeksi 1 gram', 'Vial', 2, 0, 0, 0, 0, 'unverified'),
(345, 352, 2102040201, '158', '158', 'KAOLIN PEKTIN SIRUP', '-', 4730, 1, 2, 8, '-', '{"27":"ANTI HIPERTENSI"}', 'Kaolin Pektin sirup', 'Botol', 2, 0, 0, 0, 0, 'unverified'),
(346, 353, 2102040201, '159', '159', 'KAPTOPRIL TABLET 12,5 MG', '-', 95, 13, 1, 11, '-', '{"27":"ANTI HIPERTENSI"}', 'Kaptopril tablet 12,5 mg', 'Tablet', 3, 0, 0, 0, 0, 'unverified'),
(347, 354, 2102040201, '160', '160', 'KAPTOPRIL TABLET 25 MG', '-', 145, 13, 1, 11, '-', '{"27":"ANTI HIPERTENSI"}', 'Kaptopril tablet 25 mg', 'Tablet', 3, 0, 0, 0, 0, 'unverified'),
(348, 355, 2102040201, '161', '161', 'KARBAMAZEPIN TABLET 200 MG', '-', 246, 13, 2, 11, '-', '{"27":"ANTI HIPERTENSI"}', 'Karbamazepin tablet 200 mg', 'Tablet', 3, 0, 0, 0, 0, 'unverified'),
(349, 356, 2102040201, '162', '162', 'KETOKONAZOL KRIM 2 % @ 10 G.', '-', 7349, 18, 1, 5, '-', '{"27":"ANTI HIPERTENSI"}', 'm 2 % @ 10 g.', 'Kotal', 3, 0, 0, 0, 0, 'unverified'),
(350, 357, 2102040201, '163', '163', 'KETOKONAZOL TABLET 200 MG', '-', 431, 13, 1, 3, '-', '{"10":"ANTASIDA DAN ANTIULCER"}', 'Ketokonazol tablet 200 mg', 'Tablet', 2, 0, 0, 0, 0, 'unverified'),
(351, 358, 2102040201, '164', '164', 'KETOPROFEN TABLET 100 MG', '-', 1417, 13, 1, 11, '-', '{"27":"ANTI HIPERTENSI"}', 'Ketoprofen tablet 100 mg', 'Tablet', 3, 0, 0, 0, 0, 'unverified'),
(352, 359, 2102040201, '165', '165', 'KETOPROFEN INJEKSI 100 MG @ 1 ML', '-', 5933, 14, 1, 11, '-', '{"8":"ANTI TBC"}', 'Ketoprofen injeksi 100 mg @ 1 ml', 'Ampul', 1, 0, 0, 0, 0, 'unverified'),
(353, 360, 2102040201, '166', '166', 'KETOROLAC 30 MG/ML INJEKSI', '-', 14033, 14, 1, 8, '-', '{"4":"ANTHELMETIKA"}', 'Ketorolac 30 mg/ml injeksi', 'Ampul', 2, 0, 0, 0, 0, 'unverified'),
(354, 361, 2102040201, '167', '167', 'KLORAMFENIKOL CAPSUL 250 MG ', '-', 146, 7, 1, 10, '-', '{"27":"ANTI HIPERTENSI"}', 'Kloramfenikol capsul 250 mg', 'Kapsul', 4, 0, 0, 0, 0, 'unverified'),
(355, 362, 2102040201, '168', '168', 'KLORAMFENIKOL SALAP MATA 1 %  @ 3,5 G.', '-', 1640, 12, 1, 11, '-', '{"27":"ANTI HIPERTENSI"}', 'Kloramfenikol salap mata 1 %  @ 3,5 g.', 'Tube', 3, 0, 0, 0, 0, 'unverified'),
(356, 363, 2102040201, '169', '169', 'KLORAMFENIKOL SUSPENSI 125 MG / 5 ML. @ 60 ML.', '-', 4725, 1, 1, 11, '-', '{"27":"ANTI HIPERTENSI"}', 'Kloramfenikol suspensi 125 mg / 5 ml. @ 60 ml.', 'Botol', 2, 0, 0, 0, 0, 'unverified'),
(357, 364, 2102040201, '170', '170', 'KLORAMFENIKOL TETES MATA  0,5 % ', '-', 5155, 1, 2, 13, '-', '{"4":"ANTHELMETIKA"}', 'Kloramfenikol tetes mata  0,5 % ', 'Botol', 3, 0, 0, 0, 0, 'unverified'),
(358, 365, 2102040201, '171', '171', 'KLORAMFENIKOL TETES TELINGA 3 %  @ 5 ML', '-', 1680, 1, 1, 11, '-', '{"4":"ANTHELMETIKA"}', 'Kloramfenikol tetes telinga 3 %  @ 5 ml', 'Botol', 2, 0, 0, 0, 0, 'unverified'),
(359, 366, 2102040201, '172', '172', 'KLORFENIRAMIN MALEAT ( CTM ) TABLET 4 MG', '-', 25, 13, 1, 11, '-', '{"17":"ANALGETIKA"}', 'Klorfeniramin Maleat ( CTM ) tablet 4 mg', 'Tablet', 2, 0, 0, 0, 0, 'unverified'),
(360, 367, 2102040201, '173', '173', 'KLORFENOL KAMFER MENTOL ( CHKM ) CAIRAN 10 ML.', '-', 55000, 1, 2, 11, '-', '{"27":"ANTI HIPERTENSI"}', 'Klorfenol Kamfer Mentol ( CHKM ) cairan 10 ml.', 'Botol', 3, 0, 0, 0, 0, 'unverified'),
(361, 368, 2102040201, '174', '174', 'KLOROKUIN TABLET 150 MG', '-', 58, 13, 1, 8, '-', '{"4":"ANTHELMETIKA"}', 'Klorokuin tablet 150 mg', 'Tablet', 2, 0, 0, 0, 0, 'unverified'),
(362, 369, 2102040201, '175', '175', 'KLORPROMAZIN HCL INJEKSI 25 MG / ML - 1 ML', '-', 418, 14, 2, 11, '-', '{"4":"ANTHELMETIKA"}', 'Klorpromazin HCl injeksi 25 mg / ml - 1 ml', 'Ampul', 2, 0, 0, 0, 0, 'unverified'),
(363, 370, 2102040201, '176', '176', 'KLORPROMAZIN HCL TABLET SALUT   25 MG', '-', 24, 13, 1, 11, '-', '{"4":"ANTHELMETIKA"}', 'Klorpromazin HCl tablet salut   25 mg', 'Tablet', 2, 0, 0, 0, 0, 'unverified'),
(364, 371, 2102040201, '177', '177', 'KLORPROMAZIN HCL TABLET SALUT 100 MG', '-', 79, 13, 1, 11, '-', '{"4":"ANTHELMETIKA"}', 'Klorpromazin HCl tablet salut 100 mg', 'Tablet', 2, 0, 0, 0, 0, 'unverified'),
(365, 372, 2102040201, '178', '178', 'KLORPROPAMID TABLET 100 MG', '-', 50, 13, 1, 11, '-', '{"4":"ANTHELMETIKA"}', 'Klorpropamid tablet 100 mg', 'Tablet', 2, 0, 0, 0, 0, 'unverified'),
(366, 373, 2102040201, '179', '179', 'KODEIN FOSFAT TABLET 10 MG', '-', 388, 13, 1, 11, '-', '{"27":"ANTI HIPERTENSI"}', 'Kodein Fosfat tablet 10 mg', 'Tablet', 3, 0, 0, 0, 0, 'unverified'),
(367, 374, 2102040201, '180', '180', 'KOTRIMOKSAZOL SUSP. KOMB. : SULFAMETOKSAZOL 200 MG. + TRIMETOPRIM 40 MG/5ML', '-', 4158, 1, 2, 8, '-', '{"27":"ANTI HIPERTENSI"}', 'Kotrimoksazol susp. komb. : Sulfametoksazol 200 mg. + Trimetoprim 40 mg/5ml', 'Botol', 3, 0, 0, 0, 0, 'unverified'),
(368, 375, 2102040201, '181', '181', 'KOTRIMOKSAZOL TAB DEWASA KOMB : SULFAMETOKSAZOL 400 MG+TRIMETOPRIM 80 MG', '-', 173, 1, 1, 11, '-', '{"27":"ANTI HIPERTENSI"}', 'Kotrimoksazol tab dewasa komb : Sulfametoksazol 400 mg+Trimetoprim 80 mg', 'Botol', 2, 0, 0, 0, 0, 'unverified'),
(369, 376, 2102040201, '182', '182', 'KOTRIMOKSAZOL TAB PEDIATRIK KOMB : SULFAMETOKSAZOL 100 MG+TRIMETOPRIM 20 MG', '-', 50, 1, 1, 11, '-', '{"4":"ANTHELMETIKA"}', 'Kotrimoksazol tab pediatrik komb : Sulfametoksazol 100 mg+Trimetoprim 20 mg', 'Botol', 2, 0, 0, 0, 0, 'unverified'),
(370, 377, 2102040201, '183', '183', 'KUININ ( KINA ) TABLET 200 MG', '-', 250, 13, 1, 11, '-', '{"27":"ANTI HIPERTENSI"}', 'Kuinin ( Kina ) tablet 200 mg', 'Tablet', 2, 0, 0, 0, 0, 'unverified'),
(371, 378, 2102040201, '184', '184', 'KUININ INJEKSI IV 25 % SEBAGAI 2HCL - 2 ML', '-', 3350, 14, 3, 11, '-', '{"4":"ANTHELMETIKA"}', 'Kuinin Injeksi IV 25 % sebagai 2HCl - 2 ml', 'Ampul', 3, 0, 0, 0, 0, 'unverified'),
(372, 379, 2102040201, '185', '185', 'LACTOBACILLUS', '-', 3788, 19, 1, 11, '-', '{"27":"ANTI HIPERTENSI"}', 'Lactobacillus', 'Sachet', 2, 0, 0, 0, 0, 'unverified'),
(373, 380, 2102040201, '186', '186', 'LANZOPRAZOL KAPSUL 30 MG', '-', 1577, 15, 2, 11, '-', '{"27":"ANTI HIPERTENSI"}', 'Lanzoprazol Kapsul 30 mg', 'Kaplet', 3, 0, 0, 0, 0, 'unverified'),
(374, 381, 2102040201, '187', '187', 'LARUTAN ANISOL', '-', 230000, 1, 1, 11, '-', '{"27":"ANTI HIPERTENSI"}', 'Larutan Anisol', 'Botol', 3, 0, 0, 0, 0, 'unverified'),
(375, 382, 2102040201, '188', '188', 'LARUTAN ASAM SULFOSALISILAT 20 % 100 ML.', '-', 657800, 1, 1, 13, '-', '{"4":"ANTHELMETIKA"}', 'Larutan Asam sulfosalisilat 20 % 100 ml.', 'Larutan Asam sulfosalisilat 20 % 100 ml.', 1, 0, 0, 0, 0, 'unverified');

-- --------------------------------------------------------

--
-- Table structure for table `master_sbbk`
--

CREATE TABLE IF NOT EXISTS `master_sbbk` (
  `idsbbk` int(11) NOT NULL AUTO_INCREMENT,
  `no_sbbk` varchar(35) COLLATE latin1_general_ci NOT NULL,
  `tanggal_sbbk` date NOT NULL,
  `idpuskesmas` int(11) NOT NULL,
  `permintaan_dari` varchar(200) COLLATE latin1_general_ci NOT NULL,
  `idlpo` int(11) NOT NULL,
  `no_lpo` varchar(25) COLLATE latin1_general_ci NOT NULL,
  `tanggal_lpo` date NOT NULL,
  `keperluan` varchar(150) COLLATE latin1_general_ci NOT NULL,
  `nip_penerima` varchar(20) COLLATE latin1_general_ci DEFAULT NULL,
  `nama_penerima` varchar(150) COLLATE latin1_general_ci NOT NULL,
  `tanggal_diterima` date NOT NULL,
  `nip_kadis` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `nama_kadis` varchar(150) COLLATE latin1_general_ci NOT NULL,
  `nip_ka_gudang` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `nama_ka_gudang` varchar(150) COLLATE latin1_general_ci NOT NULL,
  `nip_pengemas` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `nama_pengemas` varchar(150) COLLATE latin1_general_ci NOT NULL,
  `no_spmb` varchar(25) COLLATE latin1_general_ci NOT NULL,
  `status` enum('none','draft','complete') COLLATE latin1_general_ci NOT NULL DEFAULT 'none',
  `response_sbbk` tinyint(4) NOT NULL,
  `tahun` year(4) NOT NULL,
  `date_added` datetime NOT NULL,
  `date_modified` datetime NOT NULL,
  PRIMARY KEY (`idsbbk`),
  KEY `no_sbbk` (`no_sbbk`),
  KEY `no_lpo` (`no_lpo`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

--
-- Table structure for table `master_sbbk_puskesmas`
--

CREATE TABLE IF NOT EXISTS `master_sbbk_puskesmas` (
  `idsbbk_puskesmas` int(11) NOT NULL AUTO_INCREMENT,
  `no_sbbk_puskesmas` varchar(35) COLLATE latin1_general_ci NOT NULL,
  `tanggal_sbbk_puskesmas` date NOT NULL,
  `idpuskesmas` int(11) NOT NULL,
  `idunitpuskesmas` int(11) NOT NULL,
  `nama_unit` varchar(200) COLLATE latin1_general_ci NOT NULL,
  `idlpo_unit` int(11) NOT NULL,
  `no_lpo_unit` varchar(25) COLLATE latin1_general_ci NOT NULL,
  `tanggal_lpo_unit` date NOT NULL,
  `keperluan_unit` varchar(150) COLLATE latin1_general_ci NOT NULL,
  `nip_penerima_unit` varchar(20) COLLATE latin1_general_ci DEFAULT NULL,
  `nama_penerima_unit` varchar(150) COLLATE latin1_general_ci NOT NULL,
  `tanggal_diterima_unit` date NOT NULL,
  `nip_kadis` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `nama_kadis` varchar(150) COLLATE latin1_general_ci NOT NULL,
  `nip_ka_gudang_puskesmas` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `nama_ka_gudang_puskesmas` varchar(150) COLLATE latin1_general_ci NOT NULL,
  `nip_pengemas_puskesmas` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `nama_pengemas_puskesmas` varchar(150) COLLATE latin1_general_ci NOT NULL,
  `no_spmb_puskesmas` varchar(25) COLLATE latin1_general_ci NOT NULL,
  `status_puskesmas` enum('none','draft','complete') COLLATE latin1_general_ci NOT NULL DEFAULT 'none',
  `response_sbbk_puskesmas` tinyint(4) NOT NULL,
  `tahun_puskesmas` year(4) NOT NULL,
  `date_added` datetime NOT NULL,
  `date_modified` datetime NOT NULL,
  PRIMARY KEY (`idsbbk_puskesmas`),
  KEY `no_sbbk` (`no_sbbk_puskesmas`),
  KEY `no_lpo` (`no_lpo_unit`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `master_sbbm`
--

CREATE TABLE IF NOT EXISTS `master_sbbm` (
  `idsbbm` int(11) NOT NULL AUTO_INCREMENT,
  `no_sbbm` varchar(35) COLLATE latin1_general_ci NOT NULL,
  `tanggal_sbbm` date NOT NULL,
  `idsumber_dana` tinyint(4) NOT NULL,
  `sumber_dana` varchar(25) COLLATE latin1_general_ci NOT NULL,
  `idprogram` tinyint(4) NOT NULL,
  `nama_program` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `idpenyalur` mediumint(9) NOT NULL,
  `nama_penyalur` varchar(200) COLLATE latin1_general_ci NOT NULL,
  `no_faktur` varchar(25) COLLATE latin1_general_ci NOT NULL,
  `tanggal_faktur` date NOT NULL,
  `penerima` varchar(110) COLLATE latin1_general_ci NOT NULL,
  `status` enum('none','draft','complete') COLLATE latin1_general_ci NOT NULL DEFAULT 'none',
  `tahun` year(4) NOT NULL,
  `date_added` datetime NOT NULL,
  `date_modified` datetime NOT NULL,
  PRIMARY KEY (`idsbbm`),
  KEY `no_sbbm` (`no_sbbm`),
  KEY `no_faktur` (`no_faktur`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=17 ;

-- --------------------------------------------------------

--
-- Table structure for table `master_sbbm_puskesmas`
--

CREATE TABLE IF NOT EXISTS `master_sbbm_puskesmas` (
  `idsbbm_puskesmas` int(11) NOT NULL AUTO_INCREMENT,
  `idpuskesmas` int(11) NOT NULL,
  `idsbbk_gudang` int(11) NOT NULL,
  `tanggal_sbbk_gudang` date NOT NULL,
  `no_sbbk_gudang` varchar(35) COLLATE latin1_general_ci NOT NULL,
  `no_spmb_gudang` varchar(25) COLLATE latin1_general_ci NOT NULL,
  `tanggal_sbbm_puskesmas` date NOT NULL,
  `nip_penerima_puskesmas` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `nama_penerima_puskesmas` varchar(150) COLLATE latin1_general_ci NOT NULL,
  `status_puskesmas` enum('none','draft','complete') COLLATE latin1_general_ci NOT NULL DEFAULT 'none',
  `tahun_puskesmas` year(4) NOT NULL,
  `date_added` datetime NOT NULL,
  `date_modified` datetime NOT NULL,
  PRIMARY KEY (`idsbbm_puskesmas`),
  KEY `idpuskesmas` (`idpuskesmas`),
  KEY `idsbbk` (`idsbbk_gudang`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `penghapusan_stock`
--

CREATE TABLE IF NOT EXISTS `penghapusan_stock` (
  `idpenghapusan_stock` int(11) NOT NULL AUTO_INCREMENT,
  `no_berita_acara` int(11) NOT NULL,
  `tanggal_penghapusan` date NOT NULL,
  `tahun` year(4) NOT NULL,
  `status` enum('draft','complete') NOT NULL DEFAULT 'draft',
  `date_added` datetime NOT NULL,
  `date_modified` datetime NOT NULL,
  PRIMARY KEY (`idpenghapusan_stock`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `penyalur`
--

CREATE TABLE IF NOT EXISTS `penyalur` (
  `idpenyalur` mediumint(9) NOT NULL AUTO_INCREMENT,
  `nama_penyalur` varchar(200) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `alamat` varchar(150) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `kota` varchar(60) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `notelp` varchar(20) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `nohp` varchar(20) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `contactperson` varchar(75) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `email` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `web` varchar(50) CHARACTER SET latin1 COLLATE latin1_general_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idpenyalur`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `penyalur`
--

INSERT INTO `penyalur` (`idpenyalur`, `nama_penyalur`, `alamat`, `kota`, `notelp`, `nohp`, `contactperson`, `email`, `web`, `enabled`) VALUES
(1, 'PT. RAJAWALI NUSINDO', 'Jl. Jend. A. Yani Komp Ruko Taman Niaga Blok B No. 7 Kel. Sukajadi Kec. Kota Batam', 'Batam', '-', '-', 'Herma Refita', 'herma@gmail.com', '-', 1),
(2, 'PT. BINA SAN PRIMA', 'Komp. Graha Rejeki Mas Blok G-1 / No. 06 Sei Panas - Batam', 'Batam', '-', '-', 'Herdi Ida Sijabat. S.Farm. Apt', '', '-', 1),
(3, 'PT. ENSEVAL PUTERA MEGATRADING. TBK.', 'Komp. Citra Buana Blok CC No. 01 Kampung Seraya. Batu Ampar. Batam', 'Batam', '-', '-', 'Marlina. S.Farm. Apt', '', '-', 1),
(4, 'PT. INDOFARMA GLOBAL MEDIKA', 'Komp. Crown Hill Estate Blok E / 9 Batam Centre Batam 29432', 'Batam', '-', '-', 'Supriyadi', '', '-', 1),
(5, 'PT. KIMIA FARMA. TD', 'Jl. Jend. Sudirman. Batam - Centre', 'Batam', '-', '-', 'Dani Ramdani. S.Farm. Apt', '', '-', 1),
(6, 'PT. MENSA BINA SUKSES', 'Jl. Pelita VI Komp. Limousine No.1 RT 04 RW IX Batam', 'Batam', '-', '-', 'Pamuji', '', '-', 1),
(7, 'PT. MERAPI UTAMA FARMA', 'Jl. Engku Putri Kawasan Executive Industrial Park C2/12 B Belian', 'Batam', '0778-7482290, 748229', '-', 'Agus Wahyono', '', '-', 1);

-- --------------------------------------------------------

--
-- Table structure for table `penyalur_sbbm`
--

CREATE TABLE IF NOT EXISTS `penyalur_sbbm` (
  `idpenyalur_sbbm` mediumint(9) NOT NULL AUTO_INCREMENT,
  `idsbbm` int(11) NOT NULL,
  `idpenyalur` mediumint(9) NOT NULL,
  `nama_penyalur` varchar(200) COLLATE latin1_general_ci NOT NULL,
  `alamat` varchar(150) COLLATE latin1_general_ci NOT NULL,
  `kota` varchar(60) COLLATE latin1_general_ci NOT NULL,
  `notelp` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `nohp` varchar(20) COLLATE latin1_general_ci NOT NULL,
  `contactperson` varchar(75) COLLATE latin1_general_ci NOT NULL,
  `email` varchar(50) COLLATE latin1_general_ci NOT NULL,
  `web` varchar(50) COLLATE latin1_general_ci NOT NULL,
  PRIMARY KEY (`idpenyalur_sbbm`),
  KEY `idsbbm` (`idsbbm`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=23 ;

-- --------------------------------------------------------

--
-- Table structure for table `produsen`
--

CREATE TABLE IF NOT EXISTS `produsen` (
  `idprodusen` mediumint(11) NOT NULL AUTO_INCREMENT,
  `nama_produsen` varchar(200) NOT NULL,
  `alamat` varchar(150) NOT NULL,
  `kota` varchar(60) NOT NULL,
  `notelp` varchar(20) NOT NULL,
  `email` varchar(50) NOT NULL,
  `web` varchar(50) NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idprodusen`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `produsen`
--

INSERT INTO `produsen` (`idprodusen`, `nama_produsen`, `alamat`, `kota`, `notelp`, `email`, `web`, `enabled`) VALUES
(1, 'LAWSIM ZECHA & CO, PT.', 'Jl. Kayu Putih Raya No. 17,Jakarta Timur 13210,Indonesia', 'DKI Jakarta', '0214892659', '', 'http://www.lawsim.com', 1),
(2, 'MEDIFARMA LABORATORIES INC., PT.', 'Jakarta Industrial Estate Pulogadung Block L Kav. 11-13,Jl. Rawa Gelam V, Pulogadung,Jakarta Timur 13930,Indonesia', 'DKI Jakarta', '0214604026', '', '', 1),
(3, 'PT. HEXAPHARMA JAYA LABORATORIES', 'Gd. Ziebart Lt.1, Jl. Let. Jend. Suprapto Kav. 400', 'Jakarta Pusat', '021-4203030', '', 'http://www.hexpharmjaya.com', 1),
(4, 'BERNOFARM', 'Jl. Darmokali no. 76, Surabaya 60241-Indonesia', 'Surabaya', '031-5660025', '', 'www.bernofarm.com', 1),
(5, 'PT. PROMEDRAHARDJO FARMASI INDUSTRI', 'Jl. H. Ten No. 20, Rawamangun Jakarta 13220', 'Jakarta Barat', '0214710846', 'info@promed.co.id', '-', 1);

-- --------------------------------------------------------

--
-- Table structure for table `program`
--

CREATE TABLE IF NOT EXISTS `program` (
  `idprogram` tinyint(6) NOT NULL AUTO_INCREMENT,
  `nama_program` varchar(35) NOT NULL,
  `tahun` year(4) NOT NULL,
  `enabled` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`idprogram`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `program`
--

INSERT INTO `program` (`idprogram`, `nama_program`, `tahun`, `enabled`) VALUES
(1, 'REGULER', 0000, 1);

-- --------------------------------------------------------

--
-- Table structure for table `puskesmas`
--

CREATE TABLE IF NOT EXISTS `puskesmas` (
  `idpuskesmas` int(11) NOT NULL,
  `nama_puskesmas` varchar(200) NOT NULL,
  `idkecamatan` tinyint(4) NOT NULL,
  `alamat` varchar(150) NOT NULL,
  `notelpfax` varchar(70) NOT NULL,
  `kodepos` char(5) NOT NULL,
  `nip_ka` varchar(20) NOT NULL,
  `nama_ka` varchar(150) NOT NULL,
  `nip_pengelola_obat` varchar(20) NOT NULL,
  `nama_pengelola_obat` varchar(150) NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idpuskesmas`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `puskesmas`
--

INSERT INTO `puskesmas` (`idpuskesmas`, `nama_puskesmas`, `idkecamatan`, `alamat`, `notelpfax`, `kodepos`, `nip_ka`, `nama_ka`, `nip_pengelola_obat`, `nama_pengelola_obat`, `enabled`) VALUES
(2102040201, 'TELUK BINTAN', 12, 'Jl. Tok Sadek No. 5 Ds.Tembeling, Kec. Teluk Bintan', '-', '0', '197709072006042027', 'dr. YOSEI SUSANTI', '198312022009032007', 'Uli Dahlia Sihombing', 1),
(2102050101, 'TANJUNG UBAN', 0, ' 	 Jl. Imam Bonjol Tanjung Uban, Kec. Bintan Utara', '-', '0', '', '', '', '', 1),
(2102051201, 'TELUK SEBONG', 0, 'Jl. Raya Tanjung Uban Km.80, Kec. Teluk Sebong', '-', '0', '', '', '', '', 1),
(2102051202, 'SRI BINTAN', 0, 'Ds. Sri Bintan ,Jl.Raya Tanjung Uban KM 51, Kec. Teluk Sebong', '-', '0', '', '', '', '', 1),
(2102051203, 'BERAKIT', 13, 'Ds. Berakit, Jl. Bathin M.Ali, Kec. Teluk Sebong', '-', '29111', '0', '-', '0', '-', 1),
(2102052201, 'TELUK SASAH', 0, 'Jl. Kp. Harapan, Kec. Seri Kuala Lobam', '-', '0', '', '', '', '', 1),
(2102052202, 'KUALA SEMPANG', 0, 'Jl. Lintas Barat Desa Kuala Sempang Kec. Seri Kuala Lobam', '-', '0', '', '', '', '', 1),
(2102060101, 'KIJANG', 0, ' Jl. Barek Motor No.2, Kec. Bintan Timur', '-', '0', '', '', '', '', 1),
(2102060202, 'SEI LEKOP', 0, 'Jl. Nusantara KM 18, Kp. Sidomulyo, Kel. Sei Lekop Kec. Bintan Timur', '-', '0', '', '', '', '', 1),
(2102061101, 'KAWAL', 0, 'Jl. Wisata Bahari Km 9, Kec. Gunung Kijang', '-', '0', '', '', '', '', 1),
(2102062101, 'MANTANG', 0, 'Jl. Kayu Arang Rt 2 Rw 1 Mantang Lama, Kec. Mantang', '-', '0', '', '', '', '', 1),
(2102063201, 'KELONG', 9, 'Jl. Kesehatan Ds. Kelong, Kec. Bintan Pesisir', '-', '0', '0', 'dr. Kurniawan', '0', 'Denny', 1),
(2102064201, 'TOAPAYA', 17, 'Jl. Raya Tanjung Uban Km.26, Kec. Toapaya', '-', '0', '0', '0', '', '', 1),
(2102070101, 'TAMBELAN', 0, 'Jl. Bakti Husada, Kec. Tambelan', '-', '0', '', '', '', '', 1);

-- --------------------------------------------------------

--
-- Table structure for table `satuanobat`
--

CREATE TABLE IF NOT EXISTS `satuanobat` (
  `idsatuan_obat` smallint(6) NOT NULL AUTO_INCREMENT,
  `nama_satuan` varchar(35) NOT NULL,
  `deskripsi` varchar(255) NOT NULL,
  `enabled` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`idsatuan_obat`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=20 ;

--
-- Dumping data for table `satuanobat`
--

INSERT INTO `satuanobat` (`idsatuan_obat`, `nama_satuan`, `deskripsi`, `enabled`) VALUES
(1, 'BOTOL', '-', 1),
(3, 'BUAH', '-', 1),
(4, 'DUS', '-', 1),
(5, 'GELAS', '-', 1),
(6, 'KALENG', '-', 1),
(7, 'KAPSUL', '-', 1),
(8, 'LEMBAR', '-', 1),
(9, 'VIAL', '-', 1),
(10, 'POT', '-', 1),
(11, 'SUPP', '-', 1),
(12, 'TUBE', '-', 1),
(13, 'TABLET', '-', 1),
(14, 'AMPUL', '-', 1),
(15, 'KAPLET', '-', 1),
(16, 'SET', '-', 1),
(17, 'KTK', '-', 1),
(18, 'KOTAK', '-', 1),
(19, 'SACHET', '-', 1);

-- --------------------------------------------------------

--
-- Table structure for table `setting`
--

CREATE TABLE IF NOT EXISTS `setting` (
  `setting_id` smallint(6) NOT NULL,
  `group` varchar(50) NOT NULL,
  `key` varchar(150) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`setting_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `setting`
--

INSERT INTO `setting` (`setting_id`, `group`, `key`, `value`) VALUES
(1, 'path', 'dir_files', 'resources/files'),
(2, 'path', 'dir_userimages', 'resources/userimages'),
(3, 'path', 'dir_temp', 'resources/temp'),
(4, 'path', 'config_logo', 'resources/logobintansmall.png'),
(5, 'general', 'nip_kadis', '196108181984031009'),
(6, 'general', 'nama_kadis', 'ERWANZORI, SKM'),
(7, 'general', 'nip_ka_gudang', '197805042000032002'),
(8, 'general', 'nama_ka_gudang', 'TRIA HANDAYANI, S. Farm, Apt.'),
(9, 'general', 'nip_pengemas', '-'),
(10, 'general', 'nama_pengemas', 'Denny'),
(11, 'general', 'awal_tahun_sistem', '2015');

-- --------------------------------------------------------

--
-- Table structure for table `sumber_dana`
--

CREATE TABLE IF NOT EXISTS `sumber_dana` (
  `idsumber_dana` tinyint(4) NOT NULL AUTO_INCREMENT,
  `nama_sumber` varchar(25) COLLATE latin1_general_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idsumber_dana`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci AUTO_INCREMENT=5 ;

--
-- Dumping data for table `sumber_dana`
--

INSERT INTO `sumber_dana` (`idsumber_dana`, `nama_sumber`, `enabled`) VALUES
(1, 'APBD I', 1),
(2, 'APBD II', 1),
(3, 'APBN', 1),
(4, 'HIBAH', 1);

-- --------------------------------------------------------

--
-- Table structure for table `unitpuskesmas`
--

CREATE TABLE IF NOT EXISTS `unitpuskesmas` (
  `idunitpuskesmas` int(11) NOT NULL,
  `idpuskesmas` int(11) NOT NULL,
  `nama_unit` varchar(200) NOT NULL,
  `alamat` varchar(150) NOT NULL,
  `notelpfax` varchar(70) NOT NULL,
  `kodepos` char(5) NOT NULL,
  `nip_ka_unit` varchar(20) NOT NULL,
  `nama_ka_unit` varchar(150) NOT NULL,
  `nip_pengelola_obat` varchar(20) NOT NULL,
  `nama_pengelola_obat` varchar(150) NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`idunitpuskesmas`),
  KEY `idpuskesmas` (`idpuskesmas`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `unitpuskesmas`
--

INSERT INTO `unitpuskesmas` (`idunitpuskesmas`, `idpuskesmas`, `nama_unit`, `alamat`, `notelpfax`, `kodepos`, `nip_ka_unit`, `nama_ka_unit`, `nip_pengelola_obat`, `nama_pengelola_obat`, `enabled`) VALUES
(123, 2102040201, 'POLI GIGI', 'Jl. Brigjend Katamso No. 92', '0817023434', '29122', '6', '7', '8', '9', 1);

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `userid` int(11) NOT NULL AUTO_INCREMENT,
  `idpuskesmas` int(11) NOT NULL,
  `username` varchar(40) NOT NULL,
  `userpassword` varchar(150) NOT NULL,
  `salt` varchar(7) NOT NULL,
  `page` enum('sa','ad','sp','d') NOT NULL DEFAULT 'sa',
  `nip` bigint(20) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `mobile_phone` varchar(25) NOT NULL,
  `email` varchar(150) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `isdeleted` tinyint(1) NOT NULL DEFAULT '1',
  `foto` varchar(70) NOT NULL,
  `logintime` datetime NOT NULL,
  PRIMARY KEY (`userid`),
  KEY `idpuskesmas` (`idpuskesmas`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=10 ;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`userid`, `idpuskesmas`, `username`, `userpassword`, `salt`, `page`, `nip`, `nama`, `mobile_phone`, `email`, `active`, `isdeleted`, `foto`, `logintime`) VALUES
(1, 0, 'admin', '8ed512a957957a66ec528573f93ed6d6e2f64b146247567883e534f02ffb6430', 'eae3db', 'sa', 676767676767676767, 'MOCHAMMAD RIZKI ROMDONI', '081214553388', 'm_rizki_r@yacanet.com', 1, 0, 'resources/userimages/6dcb6a83-logoyacanet_.jpg', '2015-07-11 12:13:39'),
(2, 0, 'deni', 'e5dc72c1c3faee8b36521bbb1c94ed8ad67ad9d781d07da5d9aaae169497b2bc', '54fb42', 'sa', 0, '', '', 'deni@gmail.com', 1, 1, 'resources/userimages/no_photo.png', '2015-07-11 03:06:34');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `detail_lpo`
--
ALTER TABLE `detail_lpo`
  ADD CONSTRAINT `detail_lpo_ibfk_1` FOREIGN KEY (`idlpo`) REFERENCES `master_lpo` (`idlpo`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `detail_penghapusan_stock`
--
ALTER TABLE `detail_penghapusan_stock`
  ADD CONSTRAINT `detail_penghapusan_stock_ibfk_1` FOREIGN KEY (`idpenghapusan_stock`) REFERENCES `penghapusan_stock` (`idpenghapusan_stock`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detail_penghapusan_stock_ibfk_2` FOREIGN KEY (`iddetail_sbbm`) REFERENCES `detail_sbbm` (`iddetail_sbbm`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `detail_sbbk`
--
ALTER TABLE `detail_sbbk`
  ADD CONSTRAINT `detail_sbbk_ibfk_1` FOREIGN KEY (`idsbbk`) REFERENCES `master_sbbk` (`idsbbk`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `detail_sbbk_puskesmas`
--
ALTER TABLE `detail_sbbk_puskesmas`
  ADD CONSTRAINT `detail_sbbk_puskesmas_ibfk_1` FOREIGN KEY (`idsbbk_puskesmas`) REFERENCES `master_sbbk_puskesmas` (`idsbbk_puskesmas`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `detail_sbbm`
--
ALTER TABLE `detail_sbbm`
  ADD CONSTRAINT `detail_sbbm_ibfk_1` FOREIGN KEY (`idsbbm`) REFERENCES `master_sbbm` (`idsbbm`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `detail_sbbm_puskesmas`
--
ALTER TABLE `detail_sbbm_puskesmas`
  ADD CONSTRAINT `detail_sbbm_puskesmas_ibfk_1` FOREIGN KEY (`idsbbm_puskesmas`) REFERENCES `master_sbbm_puskesmas` (`idsbbm_puskesmas`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `kartu_stock_puskesmas`
--
ALTER TABLE `kartu_stock_puskesmas`
  ADD CONSTRAINT `kartu_stock_puskesmas_ibfk_1` FOREIGN KEY (`idpuskesmas`) REFERENCES `puskesmas` (`idpuskesmas`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `master_lpo`
--
ALTER TABLE `master_lpo`
  ADD CONSTRAINT `master_lpo_ibfk_1` FOREIGN KEY (`idpuskesmas`) REFERENCES `puskesmas` (`idpuskesmas`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `master_obat_puskesmas`
--
ALTER TABLE `master_obat_puskesmas`
  ADD CONSTRAINT `master_obat_puskesmas_ibfk_1` FOREIGN KEY (`idobat`) REFERENCES `master_obat` (`idobat`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `penyalur_sbbm`
--
ALTER TABLE `penyalur_sbbm`
  ADD CONSTRAINT `penyalur_sbbm_ibfk_1` FOREIGN KEY (`idsbbm`) REFERENCES `master_sbbm` (`idsbbm`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `unitpuskesmas`
--
ALTER TABLE `unitpuskesmas`
  ADD CONSTRAINT `unitpuskesmas_ibfk_1` FOREIGN KEY (`idpuskesmas`) REFERENCES `puskesmas` (`idpuskesmas`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
