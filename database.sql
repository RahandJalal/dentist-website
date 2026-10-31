-- --------------------------------------------------------
-- Host:                         127.0.0.1
-- Server version:               8.4.3 - MySQL Community Server - GPL
-- Server OS:                    Win64
-- HeidiSQL Version:             12.8.0.6908
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping data for table contact_db.admins: ~0 rows (approximately)
INSERT INTO `admins` (`id`, `name`, `email`, `password`) VALUES
	('qAyKVlR6qwJqv2gYePvz', 'admin', 'admin@gmail.com', '$2y$10$vSEP/Ki2l5oZlUIL8moMb.qT7wWBK2jIG8R/KSno4zbYbSrjqHVpe');

-- Dumping data for table contact_db.appointments: ~2 rows (approximately)
INSERT INTO `appointments` (`id`, `name`, `email`, `number`, `doctor`, `date`, `time`) VALUES
	(1, 'barwar', 'rahand.jalal.10@gmail.com', '1234567890', 'Dr. Amer Hamdani', '2027-03-03', '08:50'),
	(2, 'barwar', 'rahand.jalal.445@gmail.com', '1234567890', 'Dr. Marcus Vance', '2026-11-05', '11:30 AM');

-- Dumping data for table contact_db.doctors: ~5 rows (approximately)
INSERT INTO `doctors` (`id`, `name`, `specialty`, `experience`, `image`, `bio`) VALUES
	(1, 'Dr. Sarah Jenkins', 'Orthodontist', '8 Years', 'docf1.png', 'Specializes in clear aligners and corrective braces.'),
	(2, 'Dr. Marcus Vance', 'Oral Surgeon', '12 Years', 'ww.png', 'Expert in complex extractions and dental implants.'),
	(3, 'Dr. Emily Chen', 'Pediatric Dentist', '6 Years', 'docf2.png', 'Dedicated to gentle and pain-free pediatric dentistry.'),
	(4, 'Dr. Ahmed Al-Mansoor', 'Periodontist', '10 Years', 'fata.png', 'Focuses on gum care and reconstructive therapy.'),
	(5, 'Dr. Olivia Brooks', 'Cosmetic Dentist', '9 Years', 'doc3.png', 'Specializes in veneers, whitening, and smile design.');

-- Dumping data for table contact_db.messages: ~0 rows (approximately)

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
