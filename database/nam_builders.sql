-- NAM Builders and Supply Corp Database


-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 30, 2026 at 04:59 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `nam_builders`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_users`
--

CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin_users`
--

INSERT INTO `admin_users` (`id`, `username`, `password`, `email`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$10$Tbgwlzktw7tTH3MLaZqeqOVjdw.LqqTMzuSo0AiItTCw7Mtv0uuIy', 'admin@nambuilders.com', '2026-03-02 01:31:04', '2026-03-02 01:31:04');

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `id` int(11) NOT NULL,
  `client_name` varchar(150) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`id`, `client_name`, `image_path`, `description`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(9, 'AMCOR', 'clients/2_1773637851_4910.png', '', 1, 0, '2026-03-16 05:10:51', '2026-03-16 05:10:51'),
(10, 'AMKOR', 'clients/amkor_removebg_preview_1773637880_5417.png', '', 1, 0, '2026-03-16 05:11:20', '2026-03-16 05:11:20'),
(11, 'BMP', 'clients/BMP_removebg_preview_1773637894_4071.png', '', 1, 0, '2026-03-16 05:11:34', '2026-03-16 05:11:34'),
(12, 'ENKEI', 'clients/enkei_removebg_preview_1773637910_8321.png', '', 1, 0, '2026-03-16 05:11:50', '2026-03-16 05:11:50'),
(13, 'DAIHO', 'clients/daiho_removebg_preview_1773637928_8655.png', '', 1, 0, '2026-03-16 05:12:08', '2026-03-16 05:12:08'),
(14, 'GLADES', 'clients/glades_removebg_preview_1773637942_4225.png', '', 1, 0, '2026-03-16 05:12:22', '2026-03-16 05:12:22'),
(15, 'IV', 'clients/IV_removebg_preview_1773637953_1056.png', '', 1, 0, '2026-03-16 05:12:33', '2026-03-16 05:12:33'),
(16, 'KEDC', 'clients/kedc_removebg_preview_1773637968_6246.png', '', 1, 0, '2026-03-16 05:12:48', '2026-03-16 05:12:48'),
(17, 'KINPO', 'clients/kinpo_removebg_preview_1773637981_9379.png', '', 1, 0, '2026-03-16 05:13:01', '2026-03-16 05:13:01'),
(18, 'LITTLEFUSE', 'clients/littlefuse_removebg_preview_1773637995_6648.png', '', 1, 0, '2026-03-16 05:13:15', '2026-03-16 05:13:15'),
(19, 'MPST', 'clients/mpst_removebg_preview_1773638008_4865.png', '', 1, 0, '2026-03-16 05:13:28', '2026-03-16 05:13:28'),
(20, 'MYBRUSH', 'clients/mybrush_removebg_preview_1773638024_1244.png', '', 1, 0, '2026-03-16 05:13:44', '2026-03-16 05:13:44'),
(21, 'NODA', 'clients/noda_removebg_preview_1773638039_7793.png', '', 1, 0, '2026-03-16 05:13:59', '2026-03-16 05:13:59'),
(22, 'POSCO', 'clients/posco_removebg_preview_1773638050_8105.png', '', 1, 0, '2026-03-16 05:14:10', '2026-03-16 05:14:10'),
(23, 'SANKO', 'clients/sanko_removebg_preview_1773638068_9573.png', '', 1, 0, '2026-03-16 05:14:28', '2026-03-16 05:14:28'),
(24, 'SANSHU', 'clients/sansyu_removebg_preview_1773638090_9346.png', '', 1, 0, '2026-03-16 05:14:50', '2026-03-16 05:14:50'),
(25, 'SHINDEN', 'clients/shindegen_removebg_preview_1773638230_6056.png', '', 1, 0, '2026-03-16 05:15:10', '2026-03-16 05:17:10'),
(26, 'TOPIC', 'clients/topic_removebg_preview_1773638141_6430.png', '', 1, 0, '2026-03-16 05:15:41', '2026-03-16 05:15:41'),
(28, 'UNI', 'clients/unipresident_removebg_preview_1773797119_1616.png', '', 1, 0, '2026-03-18 01:25:19', '2026-03-18 01:25:19');

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `service_needed` varchar(150) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `is_replied` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `services`
--

CREATE TABLE `services` (
  `id` int(11) NOT NULL,
  `service_name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `icon_class` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `services`
--

INSERT INTO `services` (`id`, `service_name`, `description`, `image_path`, `icon_class`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(26, 'Gutter Repair', 'We provide reliable gutter repair services to fix leaks, clogs, and damaged sections, ensuring proper water flow and protecting your roof, walls, and foundation from water damage.', NULL, NULL, 1, 0, '2026-03-16 03:42:26', '2026-03-18 01:39:50'),
(27, 'Shuttle Door Installation', 'We install durable and secure shutter doors designed to protect your property while providing smooth operation and long-lasting performance for residential or commercial spaces.', NULL, NULL, 1, 0, '2026-03-16 03:47:04', '2026-03-16 03:47:04'),
(28, 'Electrical Lighting', 'We provide professional electrical lighting installation to ensure safe wiring, efficient illumination, and a well-lit space for homes, offices, and commercial areas.', NULL, NULL, 1, 0, '2026-03-16 03:51:14', '2026-03-16 03:51:14'),
(29, 'Wall Partition', 'We install durable wall partitions to efficiently divide spaces, creating functional areas while maintaining a clean and professional interior layout.', NULL, NULL, 1, 0, '2026-03-16 03:53:19', '2026-03-16 03:54:48'),
(30, 'Smoke Detector', 'We install reliable smoke detectors to help detect fire hazards early, improving safety and providing added protection for homes, offices, and commercial spaces.', NULL, NULL, 1, 0, '2026-03-16 03:57:55', '2026-03-16 03:57:55'),
(31, 'Aluminum Composite', 'We install high-quality aluminum composite panels that provide a modern look, durability, and weather resistance, ideal for exterior cladding and interior design applications.', NULL, NULL, 1, 0, '2026-03-16 04:01:37', '2026-03-16 04:01:37'),
(32, 'Fabrication', 'hello', NULL, NULL, 1, 0, '2026-03-16 04:21:33', '2026-03-16 04:21:33'),
(34, 'Grease Trap', 'We install reliable grease traps that effectively capture fats, oils, and grease from wastewater, helping maintain proper drainage and prevent plumbing blockages in kitchens and commercial spaces.', NULL, NULL, 1, 0, '2026-03-16 04:31:33', '2026-03-16 04:31:33'),
(35, 'Mirror Installation', 'We provide precise mirror installation for homes and commercial spaces, ensuring secure mounting, clean alignment, and a polished finish that enhances the look of any interior.', NULL, NULL, 1, 0, '2026-03-16 04:35:26', '2026-03-16 04:35:26'),
(36, 'Painting Works', 'We deliver professional painting services that enhance and protect your surfaces, providing a smooth finish, long-lasting color, and a refreshed look for residential and commercial spaces.', NULL, NULL, 1, 0, '2026-03-16 04:37:56', '2026-03-16 04:39:57'),
(37, 'Swing Door', 'We install durable and smoothly operating swing doors, offering both functionality and style for residential and commercial spaces while ensuring safety and ease of use.', NULL, NULL, 1, 0, '2026-03-16 04:39:51', '2026-03-16 04:47:33'),
(39, 'Carpet Tiles', 'We provide professional carpet tile installation, delivering a stylish, durable, and easy-to-maintain flooring solution perfect for offices, commercial spaces, and modern interiors.', NULL, NULL, 1, 0, '2026-03-16 04:49:35', '2026-03-16 04:49:35'),
(40, 'Mural Paintings', 'We create custom mural paintings that transform walls into vibrant works of art, adding personality, style, and a unique visual impact to any space.', NULL, NULL, 1, 0, '2026-03-16 04:52:10', '2026-03-16 04:52:10'),
(41, 'Dismantling Works', 'We provide safe and efficient dismantling services, carefully taking down structures, fixtures, or equipment while minimizing damage and preparing sites for renovation or disposal.', NULL, NULL, 1, 0, '2026-03-16 04:55:45', '2026-03-16 04:55:45'),
(42, 'Floor Tiling', 'We offer professional floor tiling services, delivering durable, precise, and visually appealing floors that enhance the look and functionality of any residential or commercial space.', NULL, NULL, 1, 0, '2026-03-16 04:57:21', '2026-03-16 04:57:21'),
(43, 'Roofing Replacement', 'We provide expert roofing replacement services, installing durable and weather-resistant materials to protect your property while enhancing its appearance and longevity.', NULL, NULL, 1, 0, '2026-03-16 05:00:25', '2026-03-16 05:00:25'),
(45, 'Roll up Repair', 'We offer fast and reliable roll-up door repair, fixing malfunctions, dents, and worn-out components to ensure smooth operation, security, and durability for your doors.', NULL, NULL, 1, 0, '2026-03-18 01:27:09', '2026-03-18 01:27:09');

-- --------------------------------------------------------

--
-- Table structure for table `service_images`
--

CREATE TABLE `service_images` (
  `id` int(11) NOT NULL,
  `service_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `service_images`
--

INSERT INTO `service_images` (`id`, `service_id`, `image_path`, `sort_order`, `created_at`) VALUES
(47, 26, 'services/received_568982399462664_1773632546.jpeg', 0, '2026-03-16 03:42:26'),
(48, 26, 'services/received_3000715666758887_1773632546.jpeg', 1, '2026-03-16 03:42:26'),
(49, 27, 'services/received_625756003474429_1773632824.jpeg', 0, '2026-03-16 03:47:04'),
(50, 27, 'services/received_884235340293440_1773632824.jpeg', 1, '2026-03-16 03:47:04'),
(51, 27, 'services/received_1318665189473310_1773632824.jpeg', 2, '2026-03-16 03:47:04'),
(52, 28, 'services/received_1520015055512756_1773633074.jpeg', 0, '2026-03-16 03:51:14'),
(53, 28, 'services/received_1712614709231694_1773633074.jpeg', 1, '2026-03-16 03:51:14'),
(56, 29, 'services/received_320404610808122_1773633288.jpeg', 0, '2026-03-16 03:54:48'),
(57, 29, 'services/received_325761280243454_1773633288.jpeg', 1, '2026-03-16 03:54:48'),
(58, 29, 'services/received_359229030095083_1773633288.jpeg', 2, '2026-03-16 03:54:48'),
(59, 29, 'services/received_369692442193520_1773633288.jpeg', 3, '2026-03-16 03:54:48'),
(60, 30, 'services/received_395620523195602_1773633475.jpeg', 0, '2026-03-16 03:57:55'),
(61, 30, 'services/received_512205174722130_1773633475.jpeg', 1, '2026-03-16 03:57:55'),
(62, 30, 'services/received_1221047359026542_1773633475.jpeg', 2, '2026-03-16 03:57:55'),
(63, 31, 'services/received_888287636133110_1773633697.jpeg', 0, '2026-03-16 04:01:37'),
(64, 31, 'services/received_1486484931897578_1773633697.jpeg', 1, '2026-03-16 04:01:37'),
(65, 31, 'services/received_2067941450216105_1773633697.jpeg', 2, '2026-03-16 04:01:37'),
(66, 32, 'services/Messenger_creation_4C413420_3FE0_4AB9_8849_E8872B39F612_1773634893_6600.jpeg', 0, '2026-03-16 04:21:33'),
(67, 32, 'services/IMG20250327172057_1773634893_4946.jpg', 1, '2026-03-16 04:21:33'),
(68, 32, 'services/IMG20250408093035_1773634893_2142.jpg', 2, '2026-03-16 04:21:33'),
(69, 32, 'services/IMG20250408093051_1773634893_1517.jpg', 3, '2026-03-16 04:21:33'),
(82, 34, 'services/IMG20250407095307_1773635493_7654.jpg', 0, '2026-03-16 04:31:33'),
(83, 34, 'services/IMG20250407095342_1773635493_4204.jpg', 1, '2026-03-16 04:31:33'),
(84, 34, 'services/IMG20250407154111_1773635493_9967.jpg', 2, '2026-03-16 04:31:33'),
(85, 34, 'services/IMG20250423091155_1773635493_5987.jpg', 3, '2026-03-16 04:31:33'),
(86, 34, 'services/IMG20250423091237_1773635493_8893.jpg', 4, '2026-03-16 04:31:33'),
(87, 35, 'services/received_505725208807987_1773635726_2087.jpeg', 0, '2026-03-16 04:35:26'),
(88, 35, 'services/received_1033662541602418_1773635726_7407.jpeg', 1, '2026-03-16 04:35:26'),
(89, 35, 'services/received_1173101200415855_1773635726_1406.jpeg', 2, '2026-03-16 04:35:26'),
(90, 36, 'services/received_285729110690804_1773635876_8255.jpeg', 0, '2026-03-16 04:37:56'),
(91, 36, 'services/received_645096654486811_1773635876_7921.jpeg', 1, '2026-03-16 04:37:56'),
(92, 36, 'services/received_757462513104701_1773635876_8726.jpeg', 2, '2026-03-16 04:37:56'),
(93, 36, 'services/received_938674194657140_1773635876_5781.jpeg', 3, '2026-03-16 04:37:56'),
(94, 36, 'services/received_964172621336639_1773635876_8914.jpeg', 4, '2026-03-16 04:37:56'),
(95, 36, 'services/received_1134602547423299_1773635876_6727.jpeg', 5, '2026-03-16 04:37:56'),
(96, 37, 'services/IMG20250423115229_1773635991_5698.jpg', 0, '2026-03-16 04:39:51'),
(97, 37, 'services/IMG20250423115548_1773635991_5005.jpg', 1, '2026-03-16 04:39:51'),
(98, 37, 'services/IMG20250423115735_1773635991_5737.jpg', 2, '2026-03-16 04:39:51'),
(99, 37, 'services/IMG20250423162045_1773635991_8429.jpg', 3, '2026-03-16 04:39:51'),
(105, 37, 'services/IMG20250726180608_1773636453_8996.jpg', 4, '2026-03-16 04:47:33'),
(106, 39, 'services/IMG20250725190911_1773636575_7932.jpg', 0, '2026-03-16 04:49:35'),
(107, 39, 'services/IMG20250725191237_1773636575_4895.jpg', 1, '2026-03-16 04:49:35'),
(108, 39, 'services/IMG20250725191649_1773636575_8705.jpg', 2, '2026-03-16 04:49:35'),
(109, 39, 'services/IMG20250726150547_1773636575_3242.jpg', 3, '2026-03-16 04:49:35'),
(110, 39, 'services/IMG20250726150558_1773636575_3645.jpg', 4, '2026-03-16 04:49:35'),
(111, 39, 'services/IMG20250726152314_1773636575_9538.jpg', 5, '2026-03-16 04:49:35'),
(112, 40, 'services/IMG20250708102700_1773636730_8151.jpg', 0, '2026-03-16 04:52:10'),
(113, 40, 'services/IMG20250708113344_1773636730_4737.jpg', 1, '2026-03-16 04:52:10'),
(114, 40, 'services/IMG20250709095351_1773636730_8504.jpg', 2, '2026-03-16 04:52:10'),
(115, 40, 'services/IMG20250709104207_1773636730_7672.jpg', 3, '2026-03-16 04:52:10'),
(116, 40, 'services/IMG20250709110751_1773636730_8034.jpg', 4, '2026-03-16 04:52:10'),
(117, 41, 'services/Messenger_creation_1D3A61E5_F903_4801_822B_F978B3B4994E_1773636945_1743.jpeg', 0, '2026-03-16 04:55:45'),
(118, 41, 'services/IMG_20250429_083032_1773636945_8129.jpg', 1, '2026-03-16 04:55:45'),
(119, 41, 'services/IMG20250110103030_1773636945_1523.jpg', 2, '2026-03-16 04:55:45'),
(120, 41, 'services/IMG20250410100257_1773636945_8192.jpg', 3, '2026-03-16 04:55:45'),
(121, 41, 'services/IMG20250414105558_1773636945_1973.jpg', 4, '2026-03-16 04:55:45'),
(122, 41, 'services/IMG20250414134810_1773636945_8537.jpg', 5, '2026-03-16 04:55:45'),
(123, 42, 'services/IMG20241119134420_1773637041_6495.jpg', 0, '2026-03-16 04:57:21'),
(124, 42, 'services/IMG20241119134455_1773637041_7976.jpg', 1, '2026-03-16 04:57:21'),
(125, 42, 'services/IMG20241119134503_1773637041_6839.jpg', 2, '2026-03-16 04:57:21'),
(126, 42, 'services/IMG20241207084051_1773637041_1627.jpg', 3, '2026-03-16 04:57:21'),
(127, 42, 'services/IMG20241207090440_1773637041_6874.jpg', 4, '2026-03-16 04:57:21'),
(128, 43, 'services/IMG20250106135644_1773637225_6661.jpg', 0, '2026-03-16 05:00:25'),
(129, 43, 'services/IMG20250111084713_1773637225_5086.jpg', 1, '2026-03-16 05:00:25'),
(130, 43, 'services/IMG20250111112144_1773637225_1633.jpg', 2, '2026-03-16 05:00:25'),
(131, 43, 'services/IMG20250111112245_1773637225_6872.jpg', 3, '2026-03-16 05:00:25'),
(132, 43, 'services/IMG20250111112409_1773637225_3817.jpg', 4, '2026-03-16 05:00:25'),
(139, 45, 'services/IMG20241016153129_1773797229_1545.jpg', 0, '2026-03-18 01:27:09'),
(140, 45, 'services/IMG20241016153640_1773797229_1048.jpg', 1, '2026-03-18 01:27:09'),
(141, 45, 'services/IMG20241123132155_1773797229_8446.jpg', 2, '2026-03-18 01:27:09'),
(142, 45, 'services/IMG20241123134033_1773797229_1548.jpg', 3, '2026-03-18 01:27:09'),
(143, 45, 'services/IMG20241123145224_1773797229_5413.jpg', 4, '2026-03-18 01:27:09');

-- --------------------------------------------------------

--
-- Table structure for table `site_stats`
--

CREATE TABLE `site_stats` (
  `id` int(11) NOT NULL,
  `stat_key` varchar(60) NOT NULL,
  `label` varchar(120) NOT NULL,
  `value` int(11) NOT NULL DEFAULT 0,
  `suffix` varchar(10) DEFAULT '',
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `site_stats`
--

INSERT INTO `site_stats` (`id`, `stat_key`, `label`, `value`, `suffix`, `sort_order`, `is_active`) VALUES
(1, 'projects_completed', 'Projects Completed', 0, '+', 1, 1),
(2, 'happy_clients', 'Happy Clients', 0, '+', 2, 1),
(3, 'years_experience', 'Years Experience', 0, '+', 3, 1),
(4, 'service_categories', 'Service Categories', 0, '+', 4, 1);

-- --------------------------------------------------------

--
-- Table structure for table `supplies`
--

CREATE TABLE `supplies` (
  `id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `supply_name` varchar(150) NOT NULL,
  `description` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `supplies`
--

INSERT INTO `supplies` (`id`, `category_id`, `supply_name`, `description`, `image_path`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(40, 15, 'Arch File', '', 'supplies/arch_file_1773886818_5292.jpg', 1, 0, '2026-03-19 02:20:18', '2026-03-19 02:20:18'),
(41, 15, 'Magic Tape', '', 'supplies/magic_tape_1773886891_7517.jpg', 1, 0, '2026-03-19 02:21:31', '2026-03-19 02:21:31'),
(42, 15, 'Ballpen', '', 'supplies/ballpen_1773888106_4002.jpg', 1, 0, '2026-03-19 02:41:46', '2026-03-19 02:41:46'),
(43, 15, 'Battery', '', 'supplies/battery_1773888135_8315.jpg', 1, 0, '2026-03-19 02:42:15', '2026-03-19 02:42:15'),
(44, 15, 'Glue Stick', '', 'supplies/glue_stick_1773888199_9140.jpg', 1, 0, '2026-03-19 02:43:19', '2026-03-19 02:43:19'),
(45, 15, 'Clearbook', '', 'supplies/clear_book_1773888253_4231.jpg', 1, 0, '2026-03-19 02:44:13', '2026-03-19 02:44:34'),
(46, 15, 'Cutter', '', 'supplies/cutter_1773888325_4378.jpg', 1, 0, '2026-03-19 02:45:25', '2026-03-19 02:45:25'),
(47, 15, 'Colored Paper', '', 'supplies/colored_paper_1773888681_5737.jpg', 1, 0, '2026-03-19 02:51:21', '2026-03-19 02:51:21'),
(48, 15, 'Calculator', '', 'supplies/calculator_1773888756_7090.jpg', 1, 0, '2026-03-19 02:52:36', '2026-03-19 02:52:36'),
(49, 15, 'Bond Paper', '', 'supplies/bond_paper_1773888866_8502.jpg', 1, 0, '2026-03-19 02:54:26', '2026-03-19 02:54:26'),
(50, 15, 'Binder Clip', '', 'supplies/binder_clip_1773891404_4721.jpg', 1, 0, '2026-03-19 03:36:44', '2026-03-19 03:36:44'),
(51, 15, 'Carbon Paper', '', 'supplies/carbon_paper_1773891582_1624.jpg', 1, 0, '2026-03-19 03:39:42', '2026-03-19 03:39:42'),
(52, 16, 'Angle Board', '', 'supplies/angle_board_1773892309_7476.jpg', 1, 0, '2026-03-19 03:51:49', '2026-03-19 03:51:49'),
(53, 16, 'Bubble Wrap', '', 'supplies/bubble_wra_1773892340_2282.webp', 1, 0, '2026-03-19 03:52:20', '2026-03-19 03:52:20'),
(54, 16, 'Cloth Duct Tape', '', 'supplies/duct_tape_1773892456_2769.jpg', 1, 0, '2026-03-19 03:54:16', '2026-03-19 03:54:16'),
(55, 16, 'Corrugated Board', '', 'supplies/corrugated_1773892550_5079.png', 1, 0, '2026-03-19 03:55:50', '2026-03-19 03:55:50'),
(56, 16, 'PP Strap', '', 'supplies/ppstarp_1773892575_5193.jpg', 1, 0, '2026-03-19 03:56:15', '2026-03-19 03:56:15'),
(57, 16, 'Kraft Paper', '', 'supplies/kraft_1773892596_1833.jpg', 1, 0, '2026-03-19 03:56:36', '2026-03-19 03:56:36'),
(58, 16, 'Magic Sponge', '', 'supplies/magic_1773892620_7055.webp', 1, 0, '2026-03-19 03:57:00', '2026-03-19 03:57:00'),
(59, 16, 'PE Foam', '', 'supplies/pefoam_1773892647_6350.jpg', 1, 0, '2026-03-19 03:57:27', '2026-03-19 03:57:27'),
(60, 16, 'Rags', '', 'supplies/rag_1773892671_6033.jpg', 1, 0, '2026-03-19 03:57:51', '2026-03-19 03:57:51'),
(61, 16, 'Chopstick', '', 'supplies/chap_1773892727_1692.webp', 1, 0, '2026-03-19 03:58:47', '2026-03-19 03:58:47'),
(62, 16, 'Jack Wrap and Stretch Film', '', 'supplies/jackwrap_1774832865_1097.jpg', 1, 0, '2026-03-30 01:07:45', '2026-03-30 01:07:45'),
(63, 16, 'Styro Foam', '', 'supplies/styro_1774832901_9760.jpg', 1, 0, '2026-03-30 01:08:21', '2026-03-30 01:08:21'),
(64, 17, 'ACU Drain Pump', '', 'supplies/acu_1774833024_6468.jpg', 1, 0, '2026-03-30 01:10:24', '2026-03-30 01:10:24'),
(65, 17, 'Adhesive Hooks', '', 'supplies/hooks_1774833078_7526.jpg', 1, 0, '2026-03-30 01:11:18', '2026-03-30 01:11:18'),
(66, 17, 'Billiard Balls', '', 'supplies/billiard_1774833110_5011.jpg', 1, 0, '2026-03-30 01:11:50', '2026-03-30 01:11:50'),
(67, 17, 'Cooling Motor', '', 'supplies/cooling_1774833175_7158.jpg', 1, 0, '2026-03-30 01:12:55', '2026-03-30 01:12:55'),
(68, 17, 'Cut of Wheel', '', 'supplies/cut_1774833200_5453.jpg', 1, 0, '2026-03-30 01:13:20', '2026-03-30 01:13:20'),
(69, 17, 'Cutting Disc', '', 'supplies/cutting_1774833229_1044.webp', 1, 0, '2026-03-30 01:13:49', '2026-03-30 01:13:49'),
(70, 17, 'Dumbbell', '', 'supplies/dumble_1774833258_6550.jpg', 1, 0, '2026-03-30 01:14:18', '2026-03-30 01:14:18'),
(71, 17, 'Glove Valve', '', 'supplies/globe_1774833383_4855.jpg', 1, 0, '2026-03-30 01:16:23', '2026-03-30 01:16:23'),
(72, 17, 'Grease Paste Heat', '', 'supplies/grease_1774833428_3175.webp', 1, 0, '2026-03-30 01:17:08', '2026-03-30 01:17:08'),
(73, 19, 'Apron with Embroidered Company Logo', '', 'supplies/apron_1774833654_8869.jpg', 1, 0, '2026-03-30 01:20:54', '2026-03-30 01:20:54'),
(74, 19, 'Laboratory Gown with Embroidered Company Logo', '', 'supplies/laboratory_gown_1774833723_9007.jpg', 1, 0, '2026-03-30 01:22:03', '2026-03-30 01:22:03'),
(75, 19, 'Long Sleeve Shirt', '', 'supplies/longsleeve_1774833749_3998.jpg', 1, 0, '2026-03-30 01:22:29', '2026-03-30 01:22:29'),
(76, 19, 'Polo Shirt', '', 'supplies/poloshirt_1774833784_1322.jpg', 1, 0, '2026-03-30 01:23:04', '2026-03-30 01:23:04'),
(77, 19, 'Reflectorized Long Sleeve', '', 'supplies/reflectorized_1774833848_9274.jpg', 1, 0, '2026-03-30 01:24:08', '2026-03-30 01:24:08'),
(78, 19, 'Round Neck Shirt', '', 'supplies/round_neck_shirt_1774833874_8002.jpg', 1, 0, '2026-03-30 01:24:34', '2026-03-30 01:24:34'),
(79, 20, 'Arm Band', '', 'supplies/Picture2_1774833931_6437.jpg', 1, 0, '2026-03-30 01:25:31', '2026-03-30 01:25:31'),
(80, 20, 'Auto Darkening Mask', '', 'supplies/auto_darkening_mask_1774833957_1039.webp', 1, 0, '2026-03-30 01:25:57', '2026-03-30 01:25:57'),
(81, 20, 'Earplug', '', 'supplies/ear_1774833991_4634.jpg', 1, 0, '2026-03-30 01:26:31', '2026-03-30 01:26:31'),
(82, 20, 'Gloves', '', 'supplies/gloves_1774834026_5274.webp', 1, 0, '2026-03-30 01:27:06', '2026-03-30 01:27:06'),
(83, 20, 'Hard Hat', '', 'supplies/hard_hat_1774834050_6864.jpg', 1, 0, '2026-03-30 01:27:30', '2026-03-30 01:27:30'),
(84, 20, 'Headlight', '', 'supplies/headlight_1774834071_2350.jpg', 1, 0, '2026-03-30 01:27:51', '2026-03-30 01:27:51'),
(85, 20, 'Respiratory Mask', '', 'supplies/respiratory_1774834135_7065.jpg', 1, 0, '2026-03-30 01:28:55', '2026-03-30 01:28:55'),
(86, 20, 'Reflectorized Tape', '', 'supplies/reflectorized_tape_1774834166_8260.jpg', 1, 0, '2026-03-30 01:29:26', '2026-03-30 01:29:26'),
(87, 20, 'Safety Ear Muff', '', 'supplies/safety_ear_1774834196_1742.jpg', 1, 0, '2026-03-30 01:29:56', '2026-03-30 01:29:56'),
(88, 20, 'Safety Shoes', '', 'supplies/safety_shoes_1774834224_5545.webp', 1, 0, '2026-03-30 01:30:24', '2026-03-30 01:30:24'),
(89, 20, 'Safety Vest', '', 'supplies/safety_vest_1774834244_1981.jpg', 1, 0, '2026-03-30 01:30:44', '2026-03-30 01:30:44'),
(90, 20, 'Sprinter Shoes', '', 'supplies/sprinter_shoes_1774834267_9308.jpg', 1, 0, '2026-03-30 01:31:07', '2026-03-30 01:31:07');

-- --------------------------------------------------------

--
-- Table structure for table `supply_categories`
--

CREATE TABLE `supply_categories` (
  `id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `supply_categories`
--

INSERT INTO `supply_categories` (`id`, `category_name`, `description`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(15, 'Office Supply', 'Reliable office supply solutions designed to keep your workspace efficient, organized, and fully equipped for daily operations. From essential tools to workplace necessities, we support smooth and productive business workflows.', 1, 0, '2026-03-19 02:19:14', '2026-03-19 02:19:14'),
(16, 'Consumables', 'High-quality consumables that ensure continuous operations and everyday efficiency in your workspace. Designed for reliability and consistent performance, they help maintain smooth and uninterrupted workflow.', 1, 0, '2026-03-19 03:43:05', '2026-03-19 03:43:05'),
(17, 'Tools/Engineering/Technical Items', '', 1, 0, '2026-03-30 01:09:23', '2026-03-30 01:09:23'),
(18, 'Medicine/Medical Items', '', 1, 0, '2026-03-30 01:18:32', '2026-03-30 01:18:32'),
(19, 'Company Uniform/Clothing', '', 1, 0, '2026-03-30 01:19:36', '2026-03-30 01:19:36'),
(20, 'Personal Protective Equipment', '', 1, 0, '2026-03-30 01:25:10', '2026-03-30 01:25:10');

-- --------------------------------------------------------

--
-- Table structure for table `updates`
--

CREATE TABLE `updates` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `update_images`
--

CREATE TABLE `update_images` (
  `id` int(11) NOT NULL,
  `update_id` int(11) NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `sort_order` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_users`
--
ALTER TABLE `admin_users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `services`
--
ALTER TABLE `services`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `service_images`
--
ALTER TABLE `service_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_service_images_service_id` (`service_id`);

--
-- Indexes for table `site_stats`
--
ALTER TABLE `site_stats`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `stat_key` (`stat_key`);

--
-- Indexes for table `supplies`
--
ALTER TABLE `supplies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_supplies_category` (`category_id`),
  ADD KEY `idx_supplies_active` (`is_active`);

--
-- Indexes for table `supply_categories`
--
ALTER TABLE `supply_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `updates`
--
ALTER TABLE `updates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_updates_active` (`is_active`,`sort_order`);

--
-- Indexes for table `update_images`
--
ALTER TABLE `update_images`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_update_images_post` (`update_id`,`sort_order`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE `admin_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `services`
--
ALTER TABLE `services`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `service_images`
--
ALTER TABLE `service_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=146;

--
-- AUTO_INCREMENT for table `site_stats`
--
ALTER TABLE `site_stats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `supplies`
--
ALTER TABLE `supplies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT for table `supply_categories`
--
ALTER TABLE `supply_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `updates`
--
ALTER TABLE `updates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `update_images`
--
ALTER TABLE `update_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `service_images`
--
ALTER TABLE `service_images`
  ADD CONSTRAINT `service_images_ibfk_1` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `supplies`
--
ALTER TABLE `supplies`
  ADD CONSTRAINT `supplies_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `supply_categories` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `update_images`
--
ALTER TABLE `update_images`
  ADD CONSTRAINT `update_images_ibfk_1` FOREIGN KEY (`update_id`) REFERENCES `updates` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

 

-- Admin Users Table
CREATE TABLE IF NOT EXISTS admin_users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Clients Table
CREATE TABLE IF NOT EXISTS clients (
    id INT PRIMARY KEY AUTO_INCREMENT,
    client_name VARCHAR(150) NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    description TEXT,
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Services Table
CREATE TABLE IF NOT EXISTS services (
    id INT PRIMARY KEY AUTO_INCREMENT,
    service_name VARCHAR(150) NOT NULL,
    description TEXT,
    image_path VARCHAR(255),
    icon_class VARCHAR(100),
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Contact Messages Table
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT PRIMARY KEY AUTO_INCREMENT,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    service_needed VARCHAR(150),
    message TEXT,
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin user (username: admin, password: admin123)
INSERT INTO admin_users (username, password, email) VALUES 
('admin', '$2y$10$Tbgwlzktw7tTH3MLaZqeqOVjdw.LqqTMzuSo0AiItTCw7Mtv0uuIy', 'admin@nambuilders.com');


-- Service Images Table (multiple images per service)
CREATE TABLE IF NOT EXISTS service_images (
    id INT PRIMARY KEY AUTO_INCREMENT,
    service_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
);

-- Index for fast lookups by service
CREATE INDEX idx_service_images_service_id ON service_images(service_id);

-- Supply Categories Table
CREATE TABLE IF NOT EXISTS supply_categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category_name VARCHAR(100) NOT NULL,
    description TEXT,
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Supplies Table
CREATE TABLE IF NOT EXISTS supplies (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category_id INT NOT NULL,
    supply_name VARCHAR(150) NOT NULL,
    description TEXT,
    image_path VARCHAR(255) DEFAULT NULL,
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES supply_categories(id) ON DELETE CASCADE
);


CREATE INDEX idx_supplies_category ON supplies(category_id);
CREATE INDEX idx_supplies_active   ON supplies(is_active);

-- NAM Builders: Updates / Posts feature
-- Run this to add the updates tables to the existing nam_builders database

 

-- Main updates table
CREATE TABLE IF NOT EXISTS updates (
    id          INT PRIMARY KEY AUTO_INCREMENT,
    title       VARCHAR(255)  NOT NULL,
    description TEXT          NOT NULL,
    image_path  VARCHAR(255)  DEFAULT NULL,   -- backward compat: first/cover image
    is_active   TINYINT(1)    DEFAULT 1,
    sort_order  INT           DEFAULT 0,
    created_at  TIMESTAMP     DEFAULT CURRENT_TIMESTAMP,
    updated_at  TIMESTAMP     DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Multiple images per post (supports the slideshow in the modal)
CREATE TABLE IF NOT EXISTS update_images (
    id          INT PRIMARY KEY AUTO_INCREMENT,
    update_id   INT           NOT NULL,
    image_path  VARCHAR(255)  NOT NULL,
    sort_order  INT           DEFAULT 0,
    FOREIGN KEY (update_id) REFERENCES updates(id) ON DELETE CASCADE
);

-- Indexes
CREATE INDEX IF NOT EXISTS idx_updates_active     ON updates(is_active, sort_order);
CREATE INDEX IF NOT EXISTS idx_update_images_post ON update_images(update_id, sort_order);

-- NAM Builders: Site Stats feature
-- Run this on your nam_builders database

 

CREATE TABLE IF NOT EXISTS site_stats (
    id          INT PRIMARY KEY AUTO_INCREMENT,
    stat_key    VARCHAR(60)  NOT NULL UNIQUE,   -- machine key e.g. "projects_completed"
    label       VARCHAR(120) NOT NULL,           -- display label e.g. "Projects Completed"
    value       INT          NOT NULL DEFAULT 0, -- numeric value e.g. 150
    suffix      VARCHAR(10)  DEFAULT '',         -- e.g. "+" or "" or "k+"
    sort_order  INT          DEFAULT 0,
    is_active   TINYINT(1)   DEFAULT 1
);

-- Seed with current hardcoded values
INSERT IGNORE INTO site_stats (stat_key, label, value, suffix, sort_order) VALUES
  ('projects_completed', 'Projects Completed', 150, '+', 1),
  ('happy_clients',      'Happy Clients',       50, '+', 2),
  ('years_experience',   'Years Experience',    15, '+', 3),
  ('service_categories', 'Service Categories',   6, '',  4);

  -- Run this in your database to add the replied status
 
ALTER TABLE contact_messages ADD COLUMN is_replied TINYINT(1) DEFAULT 0 AFTER is_read;