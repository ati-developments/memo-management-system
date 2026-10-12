-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Oct 08, 2026 at 06:23 AM
-- Server version: 8.2.0
-- PHP Version: 8.2.13

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `memo_management_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `approval_steps`
--

DROP TABLE IF EXISTS `approval_steps`;
CREATE TABLE IF NOT EXISTS `approval_steps` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `workflow_id` bigint UNSIGNED NOT NULL,
  `approver_id` bigint UNSIGNED NOT NULL,
  `step_order` int UNSIGNED NOT NULL DEFAULT '1',
  `is_required` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `approval_steps_workflow_id_foreign` (`workflow_id`),
  KEY `approval_steps_approver_id_foreign` (`approver_id`)
) ENGINE=MyISAM AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `approval_steps`
--

INSERT INTO `approval_steps` (`id`, `workflow_id`, `approver_id`, `step_order`, `is_required`, `created_at`, `updated_at`) VALUES
(30, 5, 4, 1, 1, '2026-10-05 03:41:54', '2026-10-05 03:41:54'),
(15, 3, 3, 1, 1, '2026-09-24 07:24:33', '2026-09-24 07:24:33'),
(14, 2, 4, 3, 1, '2026-09-17 03:31:19', '2026-09-17 03:31:19'),
(13, 2, 3, 2, 1, '2026-09-17 03:31:19', '2026-09-17 03:31:19'),
(31, 5, 3, 2, 1, '2026-10-05 03:41:54', '2026-10-05 03:41:54'),
(16, 3, 4, 2, 1, '2026-09-24 07:24:33', '2026-09-24 07:24:33'),
(17, 4, 3, 1, 1, '2026-09-24 07:27:13', '2026-09-24 07:27:13'),
(18, 4, 4, 2, 1, '2026-09-24 07:27:13', '2026-09-24 07:27:13'),
(29, 1, 4, 2, 1, '2026-10-02 10:45:44', '2026-10-02 10:45:44'),
(28, 1, 3, 1, 1, '2026-10-02 10:45:44', '2026-10-02 10:45:44'),
(32, 6, 3, 1, 1, '2026-10-05 03:52:24', '2026-10-05 03:52:24'),
(33, 6, 4, 2, 1, '2026-10-05 03:52:24', '2026-10-05 03:52:24'),
(34, 7, 3, 1, 1, '2026-10-05 03:57:28', '2026-10-05 03:57:28'),
(35, 7, 4, 2, 1, '2026-10-05 03:57:28', '2026-10-05 03:57:28'),
(36, 8, 3, 1, 1, '2026-10-06 06:34:31', '2026-10-06 06:34:31'),
(37, 8, 4, 2, 1, '2026-10-06 06:34:31', '2026-10-06 06:34:31'),
(38, 9, 4, 1, 1, '2026-10-06 10:54:09', '2026-10-06 10:54:09'),
(39, 9, 1, 2, 1, '2026-10-06 10:54:09', '2026-10-06 10:54:09'),
(40, 9, 3, 3, 1, '2026-10-06 10:54:09', '2026-10-06 10:54:09'),
(41, 9, 2, 4, 1, '2026-10-06 10:54:09', '2026-10-06 10:54:09'),
(42, 10, 3, 1, 1, '2026-10-08 06:07:40', '2026-10-08 06:07:40'),
(43, 10, 4, 2, 1, '2026-10-08 06:07:40', '2026-10-08 06:07:40');

-- --------------------------------------------------------

--
-- Table structure for table `approval_workflows`
--

DROP TABLE IF EXISTS `approval_workflows`;
CREATE TABLE IF NOT EXISTS `approval_workflows` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `template_id` bigint UNSIGNED DEFAULT NULL,
  `workflow_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `approval_type` enum('sequential','open') COLLATE utf8mb4_unicode_ci NOT NULL,
  `approval_rule` enum('all','any','minimum') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'all',
  `minimum_approvals` int UNSIGNED DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `approval_workflows_template_id_foreign` (`template_id`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `approval_workflows`
--

INSERT INTO `approval_workflows` (`id`, `template_id`, `workflow_name`, `approval_type`, `approval_rule`, `minimum_approvals`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'Awissawella Dialog Approval', 'sequential', 'all', NULL, 1, '2026-09-08 04:09:27', '2026-10-02 10:45:44'),
(2, 3, 'Branch VPN General Approval', 'sequential', 'all', NULL, 1, '2026-09-11 02:50:50', '2026-09-11 02:50:50'),
(3, 4, 'Branch VPN Life Approval', 'sequential', 'all', NULL, 1, '2026-09-24 07:24:33', '2026-09-24 07:24:33'),
(4, 5, 'Brightway  Purchasing IT Assessories Approval', 'sequential', 'all', NULL, 1, '2026-09-24 07:27:13', '2026-09-24 07:27:13'),
(5, 3, 'Workflow for MEMO/2026/10/05-001', 'sequential', 'all', NULL, 0, '2026-10-05 03:41:54', '2026-10-05 03:41:54'),
(6, 1, 'Workflow for MEMO/IT/26/10/05', 'sequential', 'all', NULL, 0, '2026-10-05 03:52:24', '2026-10-05 03:52:24'),
(7, 1, 'Workflow for MEMO/IT/2026/10/05-002', 'sequential', 'all', NULL, 0, '2026-10-05 03:57:28', '2026-10-05 03:57:28'),
(8, 1, 'Workflow for MEMO/IT/2026/10/06-001', 'sequential', 'all', NULL, 0, '2026-10-06 06:34:31', '2026-10-06 06:34:31'),
(9, 33, 'Workflow for MEMO/IT/2026/10/06-002', 'open', 'all', NULL, 0, '2026-10-06 10:54:09', '2026-10-06 10:54:09'),
(10, 1, 'Workflow for MEMO/IT/2026/10/08-002', 'sequential', 'all', NULL, 0, '2026-10-08 06:07:40', '2026-10-08 06:07:40');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

DROP TABLE IF EXISTS `departments`;
CREATE TABLE IF NOT EXISTS `departments` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `department_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `department_code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `departments_department_code_unique` (`department_code`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `department_name`, `department_code`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Information Technology', 'IT', 'Information Technology Department', 1, '2026-08-31 23:28:00', '2026-08-31 23:28:00'),
(2, 'Human Resources', 'HR', 'Human Resources Department', 1, '2026-09-14 05:29:34', '2026-09-14 05:29:34'),
(3, 'Finance', 'FIN', 'Finance Department', 1, '2026-09-14 05:29:34', '2026-09-14 05:29:34');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
CREATE TABLE IF NOT EXISTS `jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`id`, `queue`, `payload`, `attempts`, `reserved_at`, `available_at`, `created_at`) VALUES
(1, 'default', '{\"uuid\":\"c42cbcf9-db39-4bc4-9a61-65752b2c6658\",\"displayName\":\"App\\\\Notifications\\\\MemoApprovalRequested\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":3,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":5:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:39:\\\"App\\\\Notifications\\\\MemoApprovalRequested\\\":3:{s:8:\\\"approval\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:23:\\\"App\\\\Models\\\\MemoApproval\\\";s:2:\\\"id\\\";i:63;s:9:\\\"relations\\\";a:3:{i:0;s:8:\\\"approver\\\";i:1;s:12:\\\"approvalStep\\\";i:2;s:8:\\\"workflow\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"id\\\";s:36:\\\"6955ed0d-fe48-4a67-bb09-dde484c7b0e1\\\";s:11:\\\"afterCommit\\\";b:1;}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}s:5:\\\"tries\\\";i:3;s:11:\\\"afterCommit\\\";b:1;}\",\"batchId\":null},\"createdAt\":1791268471,\"delay\":null}', 0, NULL, 1791268471, 1791268471),
(2, 'default', '{\"uuid\":\"4d827f98-f22d-44a5-94c8-e2d667fb3869\",\"displayName\":\"App\\\\Notifications\\\\MemoApprovalRequested\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":3,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":5:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:4;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:39:\\\"App\\\\Notifications\\\\MemoApprovalRequested\\\":3:{s:8:\\\"approval\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:23:\\\"App\\\\Models\\\\MemoApproval\\\";s:2:\\\"id\\\";i:66;s:9:\\\"relations\\\";a:3:{i:0;s:8:\\\"approver\\\";i:1;s:12:\\\"approvalStep\\\";i:2;s:8:\\\"workflow\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"id\\\";s:36:\\\"b99cb3ad-6351-4063-a51c-e36477775c5f\\\";s:11:\\\"afterCommit\\\";b:1;}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}s:5:\\\"tries\\\";i:3;s:11:\\\"afterCommit\\\";b:1;}\",\"batchId\":null},\"createdAt\":1791284049,\"delay\":null}', 0, NULL, 1791284049, 1791284049),
(3, 'default', '{\"uuid\":\"69dd3390-58b6-4f9b-81c5-88f020260792\",\"displayName\":\"App\\\\Notifications\\\\MemoApprovalRequested\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":3,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":5:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:1;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:39:\\\"App\\\\Notifications\\\\MemoApprovalRequested\\\":3:{s:8:\\\"approval\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:23:\\\"App\\\\Models\\\\MemoApproval\\\";s:2:\\\"id\\\";i:67;s:9:\\\"relations\\\";a:3:{i:0;s:8:\\\"approver\\\";i:1;s:12:\\\"approvalStep\\\";i:2;s:8:\\\"workflow\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"id\\\";s:36:\\\"962371e5-1e2b-4c49-a4b8-69a5867ebd53\\\";s:11:\\\"afterCommit\\\";b:1;}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}s:5:\\\"tries\\\";i:3;s:11:\\\"afterCommit\\\";b:1;}\",\"batchId\":null},\"createdAt\":1791284049,\"delay\":null}', 0, NULL, 1791284049, 1791284049),
(4, 'default', '{\"uuid\":\"de11eb0d-0503-45a2-a8ee-2c2dfc65dd3f\",\"displayName\":\"App\\\\Notifications\\\\MemoApprovalRequested\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":3,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":5:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:39:\\\"App\\\\Notifications\\\\MemoApprovalRequested\\\":3:{s:8:\\\"approval\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:23:\\\"App\\\\Models\\\\MemoApproval\\\";s:2:\\\"id\\\";i:68;s:9:\\\"relations\\\";a:3:{i:0;s:8:\\\"approver\\\";i:1;s:12:\\\"approvalStep\\\";i:2;s:8:\\\"workflow\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"id\\\";s:36:\\\"2eb37916-4e1a-4a96-a26c-b7b4d720d440\\\";s:11:\\\"afterCommit\\\";b:1;}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}s:5:\\\"tries\\\";i:3;s:11:\\\"afterCommit\\\";b:1;}\",\"batchId\":null},\"createdAt\":1791284049,\"delay\":null}', 0, NULL, 1791284049, 1791284049),
(5, 'default', '{\"uuid\":\"935c766f-a670-458b-83aa-d1398e819a6e\",\"displayName\":\"App\\\\Notifications\\\\MemoApprovalRequested\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":3,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":5:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:2;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:39:\\\"App\\\\Notifications\\\\MemoApprovalRequested\\\":3:{s:8:\\\"approval\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:23:\\\"App\\\\Models\\\\MemoApproval\\\";s:2:\\\"id\\\";i:69;s:9:\\\"relations\\\";a:3:{i:0;s:8:\\\"approver\\\";i:1;s:12:\\\"approvalStep\\\";i:2;s:8:\\\"workflow\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"id\\\";s:36:\\\"c7be1a77-df44-4877-944b-19e4f414278d\\\";s:11:\\\"afterCommit\\\";b:1;}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}s:5:\\\"tries\\\";i:3;s:11:\\\"afterCommit\\\";b:1;}\",\"batchId\":null},\"createdAt\":1791284049,\"delay\":null}', 0, NULL, 1791284049, 1791284049),
(6, 'default', '{\"uuid\":\"dd9d9be5-bac8-4632-b2e3-def81ff35a83\",\"displayName\":\"App\\\\Notifications\\\\MemoApprovalRequested\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":3,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":5:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:3;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:39:\\\"App\\\\Notifications\\\\MemoApprovalRequested\\\":3:{s:8:\\\"approval\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:23:\\\"App\\\\Models\\\\MemoApproval\\\";s:2:\\\"id\\\";i:71;s:9:\\\"relations\\\";a:3:{i:0;s:8:\\\"approver\\\";i:1;s:12:\\\"approvalStep\\\";i:2;s:8:\\\"workflow\\\";}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:2:\\\"id\\\";s:36:\\\"47e03237-48a8-43f2-bd7e-3822b41a8716\\\";s:11:\\\"afterCommit\\\";b:1;}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}s:5:\\\"tries\\\";i:3;s:11:\\\"afterCommit\\\";b:1;}\",\"batchId\":null},\"createdAt\":1791439660,\"delay\":null}', 0, NULL, 1791439660, 1791439660);

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE IF NOT EXISTS `job_batches` (
  `id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `memos`
--

DROP TABLE IF EXISTS `memos`;
CREATE TABLE IF NOT EXISTS `memos` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `memo_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `template_id` bigint UNSIGNED DEFAULT NULL,
  `department_id` bigint UNSIGNED NOT NULL,
  `created_by` bigint UNSIGNED NOT NULL,
  `subject` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `creation_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'template',
  `content` text COLLATE utf8mb4_unicode_ci,
  `submitted_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `text_blocks` json DEFAULT NULL,
  `inserted_items` json DEFAULT NULL,
  `table_formats` json DEFAULT NULL,
  `workflow_config` json DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `memos_memo_number_unique` (`memo_number`),
  KEY `memos_template_id_foreign` (`template_id`),
  KEY `memos_department_id_foreign` (`department_id`),
  KEY `memos_created_by_foreign` (`created_by`)
) ENGINE=MyISAM AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `memo_approvals`
--

DROP TABLE IF EXISTS `memo_approvals`;
CREATE TABLE IF NOT EXISTS `memo_approvals` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `memo_id` bigint UNSIGNED NOT NULL,
  `workflow_id` bigint UNSIGNED DEFAULT NULL,
  `approver_id` bigint UNSIGNED NOT NULL,
  `approval_step_id` bigint UNSIGNED DEFAULT NULL,
  `approval_role` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'approval',
  `action` enum('pending','approved','returned','rejected') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `comment` text COLLATE utf8mb4_unicode_ci,
  `signature_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `approval_ip` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `memo_approvals_memo_id_foreign` (`memo_id`),
  KEY `memo_approvals_workflow_id_foreign` (`workflow_id`),
  KEY `memo_approvals_approver_id_foreign` (`approver_id`),
  KEY `memo_approvals_approval_step_id_foreign` (`approval_step_id`)
) ENGINE=MyISAM AUTO_INCREMENT=73 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `memo_approvals`
--

INSERT INTO `memo_approvals` (`id`, `memo_id`, `workflow_id`, `approver_id`, `approval_step_id`, `approval_role`, `action`, `comment`, `signature_path`, `approved_at`, `approval_ip`, `created_at`, `updated_at`) VALUES
(47, 30, 1, 1, NULL, 'prepared', 'approved', NULL, 'signatures/0TPaBC6D9NCbqQFiLrWqtSmXyS37ZRsfKclCClNJ.png', '2026-10-02 10:41:08', '::1', '2026-10-02 10:40:47', '2026-10-02 10:41:08');

-- --------------------------------------------------------

--
-- Table structure for table `memo_attachments`
--

DROP TABLE IF EXISTS `memo_attachments`;
CREATE TABLE IF NOT EXISTS `memo_attachments` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `memo_id` bigint UNSIGNED NOT NULL,
  `original_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `storage_path` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mime_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `memo_attachments_memo_id_foreign` (`memo_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `memo_field_values`
--

DROP TABLE IF EXISTS `memo_field_values`;
CREATE TABLE IF NOT EXISTS `memo_field_values` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `memo_id` bigint UNSIGNED NOT NULL,
  `template_field_id` bigint UNSIGNED DEFAULT NULL,
  `field_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `field_value` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `memo_field_values_memo_id_foreign` (`memo_id`),
  KEY `memo_field_values_template_field_id_foreign` (`template_field_id`)
) ENGINE=MyISAM AUTO_INCREMENT=199 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `memo_field_values`
--

INSERT INTO `memo_field_values` (`id`, `memo_id`, `template_field_id`, `field_name`, `field_value`, `created_at`, `updated_at`) VALUES
(1, 1, 38, 'Payment_description', 'bebete', '2026-09-08 02:42:00', '2026-09-08 02:42:00'),
(2, 1, 39, 'vendor', 'getget', '2026-09-08 02:42:00', '2026-09-08 02:42:00'),
(3, 1, 40, 'bill_period', 'tegege', '2026-09-08 02:42:00', '2026-09-08 02:42:00'),
(4, 2, 38, 'Payment_description', 'bebete', '2026-09-08 03:02:25', '2026-09-08 03:02:25'),
(5, 2, 39, 'vendor', 'getget', '2026-09-08 03:02:25', '2026-09-08 03:02:25'),
(6, 2, 40, 'bill_period', 'tegege', '2026-09-08 03:02:25', '2026-09-08 03:02:25'),
(7, 3, 38, 'Payment_description', 'bebete', '2026-09-08 03:36:20', '2026-09-08 03:36:20'),
(8, 3, 39, 'vendor', 'getget', '2026-09-08 03:36:20', '2026-09-08 03:36:20'),
(9, 3, 40, 'bill_period', 'tegege', '2026-09-08 03:36:20', '2026-09-08 03:36:20'),
(10, 4, 38, 'Payment_description', 'pay for 1 year', '2026-09-08 04:11:52', '2026-09-08 04:11:52'),
(11, 4, 39, 'vendor', 'dialog', '2026-09-08 04:11:52', '2026-09-08 04:11:52'),
(12, 4, 40, 'bill_period', '1 year', '2026-09-08 04:11:52', '2026-09-08 04:11:52'),
(13, 5, 38, 'Payment_description', 'pay for 1 year', '2026-09-08 04:14:04', '2026-09-08 04:14:04'),
(14, 5, 39, 'vendor', 'dialog', '2026-09-08 04:14:04', '2026-09-08 04:14:04'),
(15, 5, 40, 'bill_period', '1 year', '2026-09-08 04:14:04', '2026-09-08 04:14:04'),
(16, 6, 38, 'Payment_description', 'pay for 1 year', '2026-09-08 04:14:57', '2026-09-08 04:14:57'),
(17, 6, 39, 'vendor', 'dialog', '2026-09-08 04:14:57', '2026-09-08 04:14:57'),
(18, 6, 40, 'bill_period', '1 year', '2026-09-08 04:14:57', '2026-09-08 04:14:57'),
(19, 7, 38, 'Payment_description', 'pay for 1 year', '2026-09-08 04:41:43', '2026-09-08 04:41:43'),
(20, 7, 39, 'vendor', 'dialog', '2026-09-08 04:41:43', '2026-09-08 04:41:43'),
(21, 7, 40, 'bill_period', '1 year', '2026-09-08 04:41:43', '2026-09-08 04:41:43'),
(22, 8, 38, 'Payment_description', 'pay for 1 year', '2026-09-08 04:44:06', '2026-09-08 04:44:06'),
(23, 8, 39, 'vendor', 'dialog', '2026-09-08 04:44:06', '2026-09-08 04:44:06'),
(24, 8, 40, 'bill_period', '1 year', '2026-09-08 04:44:06', '2026-09-08 04:44:06'),
(25, 9, 38, 'Payment_description', 'pay for 1 year', '2026-09-08 04:47:26', '2026-09-08 04:47:26'),
(26, 9, 39, 'vendor', 'dialog', '2026-09-08 04:47:26', '2026-09-08 04:47:26'),
(27, 9, 40, 'bill_period', '1 year', '2026-09-08 04:47:26', '2026-09-08 04:47:26'),
(28, 10, 38, 'Payment_description', 'pay for 1 year', '2026-09-08 04:48:18', '2026-09-08 04:48:18'),
(29, 10, 39, 'vendor', 'dialog', '2026-09-08 04:48:18', '2026-09-08 04:48:18'),
(30, 10, 40, 'bill_period', '1 year', '2026-09-08 04:48:18', '2026-09-08 04:48:18'),
(31, 11, 38, 'Payment_description', 'pay for 1 year', '2026-09-08 05:02:28', '2026-09-08 05:02:28'),
(32, 11, 39, 'vendor', 'dialog', '2026-09-08 05:02:28', '2026-09-08 05:02:28'),
(33, 11, 40, 'bill_period', '1 year', '2026-09-08 05:02:28', '2026-09-08 05:02:28'),
(34, 12, 38, 'Payment_description', 'fgndndsn dgnd', '2026-09-08 05:02:59', '2026-09-08 05:02:59'),
(35, 12, 39, 'vendor', 'dialog', '2026-09-08 05:02:59', '2026-09-08 05:02:59'),
(36, 12, 40, 'bill_period', '1 year', '2026-09-08 05:02:59', '2026-09-08 05:02:59'),
(37, 13, 38, 'Payment_description', 'hetdhbd', '2026-09-08 05:03:57', '2026-09-08 05:03:57'),
(38, 13, 39, 'vendor', 'dialog', '2026-09-08 05:03:57', '2026-09-08 05:03:57'),
(39, 13, 40, 'bill_period', 'bdfbdf', '2026-09-08 05:03:57', '2026-09-08 05:03:57'),
(40, 14, 41, 'Payment_description', 'Need to pay ASAP', '2026-09-11 02:56:29', '2026-09-11 02:56:29'),
(41, 14, 42, 'vendor', 'HUTCH', '2026-09-11 02:56:29', '2026-09-11 02:56:29'),
(42, 14, 43, 'bill_period', 'September 2026', '2026-09-11 02:56:29', '2026-09-11 02:56:29'),
(43, 15, 41, 'Payment_description', 'Need to Pay ASAP', '2026-09-11 03:07:45', '2026-09-11 03:07:45'),
(44, 15, 42, 'vendor', 'HUTCH', '2026-09-11 03:07:45', '2026-09-11 03:07:45'),
(45, 15, 43, 'bill_period', 'September 2026', '2026-09-11 03:07:45', '2026-09-11 03:07:45'),
(46, 16, 41, 'Payment_description', 'Payment complete', '2026-09-11 04:02:40', '2026-09-11 04:02:40'),
(47, 16, 42, 'vendor', 'Dialog', '2026-09-11 04:02:40', '2026-09-11 04:02:40'),
(48, 16, 43, 'bill_period', 'May 2026', '2026-09-11 04:02:40', '2026-09-11 04:02:40'),
(49, 17, 38, 'Payment_description', 'nyhnymy', '2026-09-11 04:22:46', '2026-09-11 04:22:46'),
(50, 17, 39, 'vendor', 'mymy', '2026-09-11 04:22:46', '2026-09-11 04:22:46'),
(51, 17, 40, 'bill_period', 'mymy', '2026-09-11 04:22:46', '2026-09-11 04:22:46'),
(52, 18, 38, 'Payment_description', 'MEEEEEEEEEEEEEEE', '2026-09-11 04:43:23', '2026-09-11 04:43:23'),
(53, 18, 39, 'vendor', 'MEEEE', '2026-09-11 04:43:23', '2026-09-11 04:43:23'),
(54, 18, 40, 'bill_period', 'May 2026', '2026-09-11 04:43:23', '2026-09-11 04:43:23'),
(55, 19, NULL, 'to', 'Finance', '2026-09-16 04:55:24', '2026-09-16 04:55:24'),
(56, 19, NULL, 'from', 'imanthi', '2026-09-16 04:55:24', '2026-09-16 04:55:24'),
(57, 19, NULL, 'through', 'danushaka', '2026-09-16 04:55:24', '2026-09-16 04:55:24'),
(58, 19, NULL, 'date', '2026-09-16', '2026-09-16 04:55:24', '2026-09-16 04:55:24'),
(59, 19, 38, 'Payment_description', 'Handle the Payment before next month', '2026-09-16 04:55:24', '2026-09-16 04:55:24'),
(60, 19, 39, 'vendor', 'dialog', '2026-09-16 04:55:24', '2026-09-16 04:55:24'),
(61, 19, 40, 'bill_period', 'January 2027', '2026-09-16 04:55:24', '2026-09-16 04:55:24'),
(62, 20, NULL, 'to', 'HR', '2026-09-17 03:29:55', '2026-09-17 03:29:55'),
(63, 20, NULL, 'from', 'Nimuthu Silva', '2026-09-17 03:29:55', '2026-09-17 03:29:55'),
(64, 20, NULL, 'through', 'Danushka', '2026-09-17 03:29:55', '2026-09-17 03:29:55'),
(65, 20, NULL, 'date', '2026-09-17', '2026-09-17 03:29:55', '2026-09-17 03:29:55'),
(66, 20, 41, 'Payment_description', 'THE PAYMENT IS DUE on 2027/12/22', '2026-09-17 03:29:55', '2026-09-17 03:29:55'),
(67, 20, 42, 'vendor', 'Hutch', '2026-09-17 03:29:55', '2026-09-17 03:29:55'),
(68, 20, 43, 'bill_period', 'January 2027', '2026-09-17 03:29:55', '2026-09-17 03:29:55'),
(69, 21, NULL, 'to', 'bhebhe', '2026-09-17 03:31:45', '2026-09-17 03:31:45'),
(70, 21, NULL, 'from', 'Nimuthu Silva', '2026-09-17 03:31:45', '2026-09-17 03:31:45'),
(71, 21, NULL, 'through', 'grege', '2026-09-17 03:31:45', '2026-09-17 03:31:45'),
(72, 21, NULL, 'date', '2026-09-17', '2026-09-17 03:31:45', '2026-09-17 03:31:45'),
(73, 21, 41, 'Payment_description', 'geger', '2026-09-17 03:31:45', '2026-09-17 03:31:45'),
(74, 21, 42, 'vendor', 'gregreg', '2026-09-17 03:31:45', '2026-09-17 03:31:45'),
(75, 21, 43, 'bill_period', 'gergre', '2026-09-17 03:31:45', '2026-09-17 03:31:45'),
(76, 22, NULL, 'to', 'HR', '2026-09-17 04:31:12', '2026-09-17 04:31:12'),
(77, 22, NULL, 'from', 'imanthi', '2026-09-17 04:31:12', '2026-09-17 04:31:12'),
(78, 22, NULL, 'through', 'danushaka', '2026-09-17 04:31:12', '2026-09-17 04:31:12'),
(79, 22, NULL, 'date', '2026-09-17', '2026-09-17 04:31:12', '2026-09-17 04:31:12'),
(80, 22, 41, 'Payment_description', 'bdbdht', '2026-09-17 04:31:12', '2026-09-17 04:31:12'),
(81, 22, 42, 'vendor', 'dialog', '2026-09-17 04:31:12', '2026-09-17 04:31:12'),
(82, 22, 43, 'bill_period', 'dnbgnd', '2026-09-17 04:31:12', '2026-09-17 04:31:12'),
(83, 23, NULL, 'to', 'Finance', '2026-09-28 03:11:47', '2026-09-28 03:11:47'),
(84, 23, NULL, 'from', 'Imanthi Abeyratne', '2026-09-28 03:11:47', '2026-09-28 03:11:47'),
(85, 23, NULL, 'through', 'Danushka', '2026-09-28 03:11:47', '2026-09-28 03:11:47'),
(86, 23, NULL, 'date', '2026-09-28', '2026-09-28 03:11:47', '2026-09-28 03:11:47'),
(87, 23, 38, 'Payment_description', 'hdb sh hsbvnj dvbhjsbsjbcjsbdh sn sbckbsbcs c chjbkns nm sncbschs cks bdch sdcn cchkbckn cmsdcksbcdjsdbc sd cbjkbck', '2026-09-28 03:11:47', '2026-09-28 03:11:47'),
(88, 23, 39, 'vendor', 'Dialog', '2026-09-28 03:11:47', '2026-09-28 03:11:47'),
(89, 23, 40, 'bill_period', 'September 2026', '2026-09-28 03:11:47', '2026-09-28 03:11:47'),
(90, 24, NULL, 'to', 'Finance', '2026-09-30 10:18:06', '2026-09-30 10:18:06'),
(91, 24, NULL, 'from', 'Imanthi Abeyratne', '2026-09-30 10:18:06', '2026-09-30 10:18:06'),
(92, 24, NULL, 'through', 'Danushka', '2026-09-30 10:18:06', '2026-09-30 10:18:06'),
(93, 24, NULL, 'date', '2026-09-30', '2026-09-30 10:18:06', '2026-09-30 10:18:06'),
(94, 24, 38, 'Payment_description', 'g4g4b4b4b4', '2026-09-30 10:18:06', '2026-09-30 10:18:06'),
(95, 24, 39, 'vendor', 'dialog', '2026-09-30 10:18:06', '2026-09-30 10:18:06'),
(96, 24, 40, 'bill_period', 'September 2026', '2026-09-30 10:18:06', '2026-09-30 10:18:06'),
(97, 25, NULL, 'to', 'Finance', '2026-09-30 10:20:32', '2026-09-30 10:20:32'),
(98, 25, NULL, 'from', 'Imanthi Abeyratne', '2026-09-30 10:20:32', '2026-09-30 10:20:32'),
(99, 25, NULL, 'through', 'Danushka', '2026-09-30 10:20:32', '2026-09-30 10:20:32'),
(100, 25, NULL, 'date', '2026-09-30', '2026-09-30 10:20:32', '2026-09-30 10:20:32'),
(101, 25, 38, 'Payment_description', 'g4g4b4b4b4', '2026-09-30 10:20:32', '2026-09-30 10:20:32'),
(102, 25, 39, 'vendor', 'dialog', '2026-09-30 10:20:32', '2026-09-30 10:20:32'),
(103, 25, 40, 'bill_period', 'September 2026', '2026-09-30 10:20:32', '2026-09-30 10:20:32'),
(104, 26, NULL, 'to', 'Finance', '2026-09-30 10:41:44', '2026-09-30 10:41:44'),
(105, 26, NULL, 'from', 'Imanthi Abeyratne', '2026-09-30 10:41:44', '2026-09-30 10:41:44'),
(106, 26, NULL, 'through', 'danushaka', '2026-09-30 10:41:44', '2026-09-30 10:41:44'),
(107, 26, NULL, 'date', '2026-09-30', '2026-09-30 10:41:44', '2026-09-30 10:41:44'),
(108, 26, 41, 'Payment_description', 'tntnhng', '2026-09-30 10:41:44', '2026-09-30 10:41:44'),
(109, 26, 42, 'vendor', 'gngg', '2026-09-30 10:41:44', '2026-09-30 10:41:44'),
(110, 26, 43, 'bill_period', 'ngngng', '2026-09-30 10:41:44', '2026-09-30 10:41:44'),
(111, 27, NULL, 'to', 'Finance', '2026-09-30 10:43:31', '2026-09-30 10:43:31'),
(112, 27, NULL, 'from', 'Imanthi Abeyratne', '2026-09-30 10:43:31', '2026-09-30 10:43:31'),
(113, 27, NULL, 'through', 'Danushka', '2026-09-30 10:43:31', '2026-09-30 10:43:31'),
(114, 27, NULL, 'date', '2026-09-30', '2026-09-30 10:43:31', '2026-09-30 10:43:31'),
(115, 27, 38, 'Payment_description', 'fwrgbfdbdbdgb', '2026-09-30 10:43:31', '2026-09-30 10:43:31'),
(116, 27, 39, 'vendor', 'dialog', '2026-09-30 10:43:31', '2026-09-30 10:43:31'),
(117, 27, 40, 'bill_period', 'September 2026', '2026-09-30 10:43:31', '2026-09-30 10:43:31'),
(118, 28, NULL, 'to', 'Finance', '2026-09-30 11:02:02', '2026-09-30 11:02:02'),
(119, 28, NULL, 'from', 'Imanthi Abeyratne', '2026-09-30 11:02:02', '2026-09-30 11:02:02'),
(120, 28, NULL, 'through', 'danushaka', '2026-09-30 11:02:02', '2026-09-30 11:02:02'),
(121, 28, NULL, 'date', '2026-09-30', '2026-09-30 11:02:02', '2026-09-30 11:02:02'),
(122, 28, 38, 'Payment_description', 'yh v b c', '2026-09-30 11:02:02', '2026-09-30 11:02:02'),
(123, 28, 39, 'vendor', 'cnc c', '2026-09-30 11:02:02', '2026-09-30 11:02:02'),
(124, 28, 40, 'bill_period', 'nfnfn', '2026-09-30 11:02:02', '2026-09-30 11:02:02'),
(125, 29, NULL, 'to', 'Finance', '2026-09-30 11:03:43', '2026-09-30 11:03:43'),
(126, 29, NULL, 'from', 'Imanthi Abeyratne', '2026-09-30 11:03:43', '2026-09-30 11:03:43'),
(127, 29, NULL, 'through', 'Danushka', '2026-09-30 11:03:43', '2026-09-30 11:03:43'),
(128, 29, NULL, 'date', '2026-09-30', '2026-09-30 11:03:43', '2026-09-30 11:03:43'),
(129, 29, 41, 'Payment_description', 'bdb cbc', '2026-09-30 11:03:43', '2026-09-30 11:03:43'),
(130, 29, 42, 'vendor', 'bcbc', '2026-09-30 11:03:43', '2026-09-30 11:03:43'),
(131, 29, 43, 'bill_period', 'bgbbd', '2026-09-30 11:03:43', '2026-09-30 11:03:43'),
(132, 30, NULL, 'to', 'gergeg', '2026-10-02 10:40:47', '2026-10-02 10:40:47'),
(133, 30, NULL, 'from', 'Imanthi Abeyratne', '2026-10-02 10:40:47', '2026-10-02 10:40:47'),
(134, 30, NULL, 'through', 'gergerg', '2026-10-02 10:40:47', '2026-10-02 10:40:47'),
(135, 30, NULL, 'date', '2026-10-02', '2026-10-02 10:40:47', '2026-10-02 10:40:47'),
(136, 30, 38, 'Payment_description', 'gerger', '2026-10-02 10:40:47', '2026-10-02 10:40:47'),
(137, 30, 39, 'vendor', 'regregr', '2026-10-02 10:40:47', '2026-10-02 10:40:47'),
(138, 30, 40, 'bill_period', 'rgre', '2026-10-02 10:40:47', '2026-10-02 10:40:47'),
(139, 31, NULL, 'to', 'Finance', '2026-10-02 10:48:13', '2026-10-02 10:48:13'),
(140, 31, NULL, 'from', 'Imanthi Abeyratne', '2026-10-02 10:48:13', '2026-10-02 10:48:13'),
(141, 31, NULL, 'through', 'Danushka', '2026-10-02 10:48:13', '2026-10-02 10:48:13'),
(142, 31, NULL, 'date', '2026-10-02', '2026-10-02 10:48:13', '2026-10-02 10:48:13'),
(143, 31, 38, 'Payment_description', 'frg4g4', '2026-10-02 10:48:13', '2026-10-02 10:48:13'),
(144, 31, 39, 'vendor', 'g4g45', '2026-10-02 10:48:13', '2026-10-02 10:48:13'),
(145, 31, 40, 'bill_period', 'g45g45', '2026-10-02 10:48:13', '2026-10-02 10:48:13'),
(146, 32, NULL, 'to', 'Finance', '2026-10-05 03:41:54', '2026-10-05 03:41:54'),
(147, 32, NULL, 'from', 'Imanthi Abeyratne', '2026-10-05 03:41:54', '2026-10-05 03:41:54'),
(148, 32, NULL, 'through', 'fddfd', '2026-10-05 03:41:54', '2026-10-05 03:41:54'),
(149, 32, NULL, 'date', '2026-10-05', '2026-10-05 03:41:54', '2026-10-05 03:41:54'),
(150, 32, 41, 'Payment_description', 'fddfdf', '2026-10-05 03:41:54', '2026-10-05 03:41:54'),
(151, 32, 42, 'vendor', 'fdfd', '2026-10-05 03:41:54', '2026-10-05 03:41:54'),
(152, 32, 43, 'bill_period', 'fdfdf', '2026-10-05 03:41:54', '2026-10-05 03:41:54'),
(153, 33, NULL, 'to', 'v v', '2026-10-05 03:52:24', '2026-10-05 03:52:24'),
(154, 33, NULL, 'from', 'Imanthi Abeyratne', '2026-10-05 03:52:24', '2026-10-05 03:52:24'),
(155, 33, NULL, 'through', 'fggnfgn', '2026-10-05 03:52:24', '2026-10-05 03:52:24'),
(156, 33, NULL, 'date', '2026-10-05', '2026-10-05 03:52:24', '2026-10-05 03:52:24'),
(157, 33, 38, 'Payment_description', 'nfnfnfn', '2026-10-05 03:52:24', '2026-10-05 03:52:24'),
(158, 33, 39, 'vendor', 'fnfnfn', '2026-10-05 03:52:24', '2026-10-05 03:52:24'),
(159, 33, 40, 'bill_period', 'nfnfnf', '2026-10-05 03:52:24', '2026-10-05 03:52:24'),
(160, 34, NULL, 'to', 'Finance', '2026-10-05 03:57:28', '2026-10-05 03:57:28'),
(161, 34, NULL, 'from', 'Imanthi Abeyratne', '2026-10-05 03:57:28', '2026-10-05 03:57:28'),
(162, 34, NULL, 'through', 'Danushka', '2026-10-05 03:57:28', '2026-10-05 03:57:28'),
(163, 34, NULL, 'date', '2026-10-05', '2026-10-05 03:57:28', '2026-10-05 03:57:28'),
(164, 34, 38, 'Payment_description', 'bbebe', '2026-10-05 03:57:28', '2026-10-05 03:57:28'),
(165, 34, 39, 'vendor', 'dialog', '2026-10-05 03:57:28', '2026-10-05 03:57:28'),
(166, 34, 40, 'bill_period', 'September 2026', '2026-10-05 03:57:28', '2026-10-05 03:57:28'),
(167, 35, NULL, 'to', 'Finance', '2026-10-05 07:36:42', '2026-10-05 07:36:42'),
(168, 35, NULL, 'from', 'Imanthi Abeyratne', '2026-10-05 07:36:42', '2026-10-05 07:36:42'),
(169, 35, NULL, 'through', 'Danushka', '2026-10-05 07:36:42', '2026-10-05 07:36:42'),
(170, 35, NULL, 'date', '2026-10-05', '2026-10-05 07:36:42', '2026-10-05 07:36:42'),
(171, 35, 47, 'payment_description', 'umumumu', '2026-10-05 07:36:42', '2026-10-05 07:36:42'),
(172, 35, 48, 'vendor', 'mukmukm', '2026-10-05 07:36:42', '2026-10-05 07:36:42'),
(173, 35, 49, 'bill_period', 'mumu', '2026-10-05 07:36:42', '2026-10-05 07:36:42'),
(174, 36, NULL, 'to', 'Finance\r\nDuminda Silva', '2026-10-06 06:34:31', '2026-10-06 06:34:31'),
(175, 36, NULL, 'from', 'System Admin', '2026-10-06 06:34:31', '2026-10-06 06:34:31'),
(176, 36, NULL, 'through', 'Danushka', '2026-10-06 06:34:31', '2026-10-06 06:34:31'),
(177, 36, NULL, 'date', '2026-10-06', '2026-10-06 06:34:31', '2026-10-06 06:34:31'),
(178, 36, 38, 'Payment_description', 'wvwvwvvWbb', '2026-10-06 06:34:31', '2026-10-06 06:34:31'),
(179, 36, 39, 'vendor', 'bebeb', '2026-10-06 06:34:31', '2026-10-06 06:34:31'),
(180, 36, 40, 'bill_period', 'bebeb', '2026-10-06 06:34:31', '2026-10-06 06:34:31'),
(181, 37, NULL, 'to', 'vevfeve', '2026-10-06 10:54:09', '2026-10-06 10:54:09'),
(182, 37, NULL, 'from', 'System Admin', '2026-10-06 10:54:09', '2026-10-06 10:54:09'),
(183, 37, NULL, 'through', 'vevef', '2026-10-06 10:54:09', '2026-10-06 10:54:09'),
(184, 37, NULL, 'date', '2026-10-06', '2026-10-06 10:54:09', '2026-10-06 10:54:09'),
(185, 38, NULL, 'to', '5hyey5', '2026-10-08 06:06:05', '2026-10-08 06:06:05'),
(186, 38, NULL, 'from', 'System Admin', '2026-10-08 06:06:05', '2026-10-08 06:06:05'),
(187, 38, NULL, 'through', 'yh4y4', '2026-10-08 06:06:05', '2026-10-08 06:06:05'),
(188, 38, NULL, 'date', '2026-10-08', '2026-10-08 06:06:05', '2026-10-08 06:06:05'),
(189, 38, 41, 'Payment_description', 'y4yy', '2026-10-08 06:06:05', '2026-10-08 06:06:05'),
(190, 38, 42, 'vendor', 'y4y54', '2026-10-08 06:06:05', '2026-10-08 06:06:05'),
(191, 38, 43, 'bill_period', 'y4y5', '2026-10-08 06:06:05', '2026-10-08 06:06:05'),
(192, 39, NULL, 'to', 'mymyu', '2026-10-08 06:07:40', '2026-10-08 06:07:40'),
(193, 39, NULL, 'from', 'System Admin', '2026-10-08 06:07:40', '2026-10-08 06:07:40'),
(194, 39, NULL, 'through', 'muymy', '2026-10-08 06:07:40', '2026-10-08 06:07:40'),
(195, 39, NULL, 'date', '2026-10-08', '2026-10-08 06:07:40', '2026-10-08 06:07:40'),
(196, 39, 38, 'Payment_description', 'myumyu', '2026-10-08 06:07:40', '2026-10-08 06:07:40'),
(197, 39, 39, 'vendor', 'myum', '2026-10-08 06:07:40', '2026-10-08 06:07:40'),
(198, 39, 40, 'bill_period', 'myumym', '2026-10-08 06:07:40', '2026-10-08 06:07:40');

-- --------------------------------------------------------

--
-- Table structure for table `memo_number_sequences`
--

DROP TABLE IF EXISTS `memo_number_sequences`;
CREATE TABLE IF NOT EXISTS `memo_number_sequences` (
  `date` date NOT NULL,
  `last_number` int UNSIGNED NOT NULL DEFAULT '0',
  PRIMARY KEY (`date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `memo_number_sequences`
--

INSERT INTO `memo_number_sequences` (`date`, `last_number`) VALUES
('2026-09-17', 3),
('2026-09-28', 1),
('2026-09-30', 6),
('2026-10-02', 2),
('2026-10-05', 3),
('2026-10-06', 2),
('2026-10-08', 2);

-- --------------------------------------------------------

--
-- Table structure for table `memo_status_labels`
--

DROP TABLE IF EXISTS `memo_status_labels`;
CREATE TABLE IF NOT EXISTS `memo_status_labels` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `status_key` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `label` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `memo_status_labels_status_key_unique` (`status_key`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `memo_status_labels`
--

INSERT INTO `memo_status_labels` (`id`, `status_key`, `label`, `created_at`, `updated_at`) VALUES
(1, 'draft', 'Draft', '2026-10-05 10:25:27', '2026-10-08 06:10:10'),
(2, 'pending', 'Pending', '2026-10-05 10:25:27', '2026-10-08 06:10:10'),
(3, 'approved', 'Approved', '2026-10-05 10:25:27', '2026-10-05 10:25:27');

-- --------------------------------------------------------

--
-- Table structure for table `memo_table_rows`
--

DROP TABLE IF EXISTS `memo_table_rows`;
CREATE TABLE IF NOT EXISTS `memo_table_rows` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `memo_id` bigint UNSIGNED NOT NULL,
  `template_table_id` bigint UNSIGNED NOT NULL,
  `row_data` json NOT NULL,
  `row_order` int UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `memo_table_rows_template_table_id_foreign` (`template_table_id`),
  KEY `memo_table_rows_memo_id_template_table_id_row_order_index` (`memo_id`,`template_table_id`,`row_order`)
) ENGINE=MyISAM AUTO_INCREMENT=43 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `memo_table_rows`
--

INSERT INTO `memo_table_rows` (`id`, `memo_id`, `template_table_id`, `row_data`, `row_order`, `created_at`, `updated_at`) VALUES
(1, 15, 3, '{\"Amount\": \"500\", \"description\": \"Voice\"}', 0, '2026-09-11 03:07:45', '2026-09-11 03:07:45'),
(2, 15, 3, '{\"Amount\": \"1200\", \"description\": \"Government Taxes and Levies\"}', 1, '2026-09-11 03:07:45', '2026-09-11 03:07:45'),
(3, 15, 3, '{\"Amount\": \"300\", \"description\": \"VAT\"}', 2, '2026-09-11 03:07:45', '2026-09-11 03:07:45'),
(4, 15, 3, '{\"Amount\": \"2000\", \"description\": \"Total Charge\"}', 3, '2026-09-11 03:07:45', '2026-09-11 03:07:45'),
(5, 16, 3, '{\"Amount\": \"1500\", \"description\": \"Voice\"}', 0, '2026-09-11 04:02:40', '2026-09-11 04:02:40'),
(6, 16, 3, '{\"Amount\": \"300\", \"description\": \"VAT\"}', 1, '2026-09-11 04:02:40', '2026-09-11 04:02:40'),
(7, 16, 3, '{\"Amount\": \"1800\", \"description\": \"Total\"}', 2, '2026-09-11 04:02:40', '2026-09-11 04:02:40'),
(8, 17, 2, '{\"Amount\": \"4544\", \"Description\": \"mymyj\"}', 0, '2026-09-11 04:22:46', '2026-09-11 04:22:46'),
(9, 18, 2, '{\"Amount\": \"1000\", \"Description\": \"VAT\"}', 0, '2026-09-11 04:43:23', '2026-09-11 04:43:23'),
(10, 19, 2, '{\"Amount\": \"1500\", \"Description\": \"Voice\"}', 0, '2026-09-16 04:55:25', '2026-09-16 04:55:25'),
(11, 19, 2, '{\"Amount\": \"500\", \"Description\": \"Vat\"}', 1, '2026-09-16 04:55:25', '2026-09-16 04:55:25'),
(12, 19, 2, '{\"Amount\": \"2000\", \"Description\": \"Total\"}', 2, '2026-09-16 04:55:25', '2026-09-16 04:55:25'),
(13, 20, 3, '{\"Amount\": \"25000\", \"description\": \"Total Amount\"}', 0, '2026-09-17 03:29:55', '2026-09-17 03:29:55'),
(14, 21, 3, '{\"Amount\": \"4343\", \"description\": \"gengnd\"}', 0, '2026-09-17 03:31:45', '2026-09-17 03:31:45'),
(15, 22, 3, '{\"Amount\": \"8667\", \"description\": \"dgndnd\"}', 0, '2026-09-17 04:31:12', '2026-09-17 04:31:12'),
(16, 23, 2, '{\"Amount\": \"44646\", \"Description\": \"hrtnfgndnd\"}', 0, '2026-09-28 03:11:47', '2026-09-28 03:11:47'),
(17, 24, 2, '{\"Amount\": \"43334\", \"Description\": \"5gtgrr\"}', 0, '2026-09-30 10:18:06', '2026-09-30 10:18:06'),
(18, 24, 2, '{\"Amount\": \"4333.97\", \"Description\": \"rbrrbrtr\"}', 1, '2026-09-30 10:18:06', '2026-09-30 10:18:06'),
(19, 25, 2, '{\"Amount\": \"43334\", \"Description\": \"5gtgrr\"}', 0, '2026-09-30 10:20:32', '2026-09-30 10:20:32'),
(20, 25, 2, '{\"Amount\": \"4333.97\", \"Description\": \"rbrrbrtr\"}', 1, '2026-09-30 10:20:32', '2026-09-30 10:20:32'),
(21, 27, 2, '{\"Amount\": \"4353\", \"Description\": \"geger\"}', 0, '2026-09-30 10:43:31', '2026-09-30 10:43:31'),
(22, 27, 2, '{\"Amount\": \"53535\", \"Description\": \"bgnbgfbgfbfg\"}', 1, '2026-09-30 10:43:31', '2026-09-30 10:43:31'),
(23, 27, 2, '{\"Amount\": \"434343\", \"Description\": \"gfnfnbfggb\"}', 2, '2026-09-30 10:43:31', '2026-09-30 10:43:31'),
(24, 27, 2, '{\"Amount\": \"44343\", \"Description\": \"43r4rtgfdgfg\"}', 3, '2026-09-30 10:43:31', '2026-09-30 10:43:31'),
(25, 27, 2, '{\"Amount\": \"54454\", \"Description\": \"bgfnghngbf\"}', 4, '2026-09-30 10:43:31', '2026-09-30 10:43:31'),
(26, 27, 15, '{\"fbfbfgb\": \"bbgfbfgbf\", \"bfgbgfbgb\": \"bfgbgfbgf\"}', 0, '2026-09-30 10:43:31', '2026-09-30 10:43:31'),
(27, 27, 15, '{\"fbfbfgb\": \"bfgbgbgfbg\", \"bfgbgfbgb\": \"bfgbgfbgbg\"}', 1, '2026-09-30 10:43:31', '2026-09-30 10:43:31'),
(28, 27, 15, '{\"fbfbfgb\": \"bfggbgfb\", \"bfgbgfbgb\": \"bfgbgbgfbg\"}', 2, '2026-09-30 10:43:31', '2026-09-30 10:43:31'),
(29, 28, 2, '{\"Amount\": \"5444\", \"Description\": \"nffnf\"}', 0, '2026-09-30 11:02:02', '2026-09-30 11:02:02'),
(30, 28, 2, '{\"Amount\": \"45454\", \"Description\": \"mngmm5\"}', 1, '2026-09-30 11:02:02', '2026-09-30 11:02:02'),
(31, 29, 3, '{\"Amount\": \"5535\", \"description\": \"bdgbdgb\"}', 0, '2026-09-30 11:03:43', '2026-09-30 11:03:43'),
(32, 29, 14, '{\"ngnh\": \"gdg\", \"ngngngh\": \"gdgdgd\", \"nghnhngh\": \"bbd\"}', 0, '2026-09-30 11:03:43', '2026-09-30 11:03:43'),
(33, 29, 14, '{\"ngnh\": \"gdgd\", \"ngngngh\": \"gdgdg\", \"nghnhngh\": \"gfdgfd\"}', 1, '2026-09-30 11:03:43', '2026-09-30 11:03:43'),
(34, 30, 2, '{\"Amount\": \"3232\", \"Description\": \"grege\"}', 0, '2026-10-02 10:40:47', '2026-10-02 10:40:47'),
(35, 31, 2, '{\"Amount\": \"43343\", \"Description\": \"g4g54\"}', 0, '2026-10-02 10:48:13', '2026-10-02 10:48:13'),
(36, 32, 3, '{\"Amount\": \"43343\", \"description\": \"fdfd\"}', 0, '2026-10-05 03:41:54', '2026-10-05 03:41:54'),
(37, 33, 2, '{\"Amount\": \"42442\", \"Description\": \"nfnf\"}', 0, '2026-10-05 03:52:24', '2026-10-05 03:52:24'),
(38, 34, 2, '{\"Amount\": \"646464\", \"Description\": \"rnnrnry45\"}', 0, '2026-10-05 03:57:28', '2026-10-05 03:57:28'),
(39, 35, 9, '{\"amount\": \"7786.98\", \"description\": \"mumum\"}', 0, '2026-10-05 07:36:42', '2026-10-05 07:36:42'),
(40, 36, 2, '{\"Amount\": \"45453.98\", \"Description\": \"bebet\"}', 0, '2026-10-06 06:34:31', '2026-10-06 06:34:31'),
(41, 38, 3, '{\"Amount\": \"6666654\", \"description\": \"y4y45\"}', 0, '2026-10-08 06:06:05', '2026-10-08 06:06:05'),
(42, 39, 2, '{\"Amount\": \"7675.97\", \"Description\": \"myu\"}', 0, '2026-10-08 06:07:40', '2026-10-08 06:07:40');

-- --------------------------------------------------------

--
-- Table structure for table `memo_templates`
--

DROP TABLE IF EXISTS `memo_templates`;
CREATE TABLE IF NOT EXISTS `memo_templates` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `department_id` bigint UNSIGNED NOT NULL,
  `template_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `template_code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `document_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `allow_optional_text` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  UNIQUE KEY `memo_templates_department_id_template_code_unique` (`department_id`,`template_code`),
  KEY `memo_templates_created_by_foreign` (`created_by`)
) ENGINE=MyISAM AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `memo_templates`
--

INSERT INTO `memo_templates` (`id`, `department_id`, `template_name`, `template_code`, `description`, `document_path`, `status`, `created_by`, `created_at`, `updated_at`, `allow_optional_text`) VALUES
(1, 1, 'Awissawella Dialog', 'AWISSAWELLA-DIALOG', 'Awissawella Dialog Router Bill Payment.', 'templates/IT/Awissawella Dialog.docx', 1, NULL, '2026-08-31 23:28:00', '2026-09-01 05:40:06', 1),
(32, 1, 'General Memo', 'GEN-MEMO', NULL, NULL, 1, 1, '2026-09-08 03:04:59', '2026-09-08 03:04:59', 1),
(3, 1, 'Branch VPN General', 'Branch-VPN-General', NULL, NULL, 1, 1, '2026-09-03 23:46:28', '2026-09-03 23:46:28', 1),
(4, 1, 'Branch VPN Life', 'Branch-VPN-Life', NULL, NULL, 1, 1, '2026-09-03 23:47:25', '2026-09-03 23:47:25', 1),
(5, 1, 'Brightway  Purchasing IT Assessories', 'Brightway-Purchasing-IT-Assessories', NULL, NULL, 1, 1, '2026-09-03 23:48:11', '2026-09-03 23:48:11', 1),
(6, 1, 'Brightway  Purchasing IT Assessories 93,94,95', 'Brightway-Purchasing-IT-Assessories-93,94,95', NULL, NULL, 1, 1, '2026-09-03 23:48:57', '2026-09-03 23:48:57', 1),
(7, 1, 'Brightway  Purchasing IT Assessories Inv 96', 'Brightway-Purchasing-IT-Assessories-Inv-96', NULL, NULL, 1, 1, '2026-09-03 23:49:24', '2026-09-03 23:49:24', 1),
(8, 1, 'Hosting Payment For Inv 76 (Turbogen)', 'Hosting-Payment-For-Inv-76-(Turbogen)', NULL, NULL, 1, 1, '2026-09-03 23:50:23', '2026-09-03 23:50:23', 1),
(9, 1, 'Hutch 3cx', 'Hutch-3cx', NULL, NULL, 1, 1, '2026-09-03 23:50:57', '2026-09-03 23:50:57', 1),
(10, 1, 'Hutch Bulk SMS - General', 'Hutch-Bulk-SMS-General', NULL, NULL, 1, 1, '2026-09-03 23:51:27', '2026-09-03 23:51:27', 1),
(11, 1, 'Hutch Bulk SMS - Life', 'Hutch-Bulk-SMS-Life', NULL, NULL, 1, 1, '2026-09-03 23:51:52', '2026-09-03 23:51:52', 1),
(12, 1, 'Hutch call center payment', 'Hutch-call-center-payment', NULL, NULL, 1, 1, '2026-09-03 23:52:33', '2026-09-03 23:52:33', 1),
(13, 1, 'Hutch GUW 3cx Payment', 'Hutch-GUW-3cx-Payment', NULL, NULL, 1, 1, '2026-09-04 00:01:57', '2026-09-04 00:01:57', 1),
(14, 1, 'Hutch  Wifi', 'Hutch-Wifi', NULL, NULL, 1, 1, '2026-09-04 00:02:31', '2026-09-04 00:02:31', 1),
(15, 1, 'Inv 86,87,88', 'Inv-86,87,88', NULL, NULL, 1, 1, '2026-09-04 00:04:53', '2026-09-04 00:04:53', 1),
(16, 1, 'IT Department Dialog Router', 'IT-Department-Dialog-Router', NULL, NULL, 1, 1, '2026-09-04 00:05:25', '2026-09-04 00:05:25', 1),
(17, 1, 'Kanthale Life Dialog', 'Kanthale-Life-Dialog', NULL, NULL, 1, 1, '2026-09-04 00:05:44', '2026-09-04 00:05:44', 1),
(18, 1, 'Kohuwala Call Center Dialog', 'Kohuwala-Call-Center-Dialog', NULL, NULL, 1, 1, '2026-09-04 00:06:03', '2026-09-04 00:06:03', 1),
(19, 1, 'Mahiyanganaya Dialog', 'Mahiyanganaya-Dialog', NULL, NULL, 1, 1, '2026-09-04 00:06:19', '2026-09-04 00:06:19', 1),
(20, 1, 'Monthly Charge Lankacom', 'Monthly-Charge-Lankacom', NULL, NULL, 1, 1, '2026-09-04 00:06:47', '2026-09-04 00:06:47', 1),
(21, 1, 'Payment Arrangement – ID Card Printer Monthly Rental', 'Payment-Arrangement-ID-Card-Printer-Monthly-Rental', NULL, NULL, 1, 1, '2026-09-04 00:07:22', '2026-09-04 00:07:22', 1),
(22, 1, 'Payment of SLT IDC Services Outstanding Balance', 'Payment-of-SLT-IDC-Services-Outstanding-Balance', NULL, NULL, 1, 1, '2026-09-04 00:08:10', '2026-09-04 00:08:10', 1),
(23, 1, 'Recommendation for Purchase of Aruba Access Points from Adapt Information Technologies (Pvt) Ltd', 'Recommendation-for-Purchase-of-Aruba-Access-Points', NULL, NULL, 1, 1, '2026-09-04 00:08:47', '2026-09-04 00:08:47', 1),
(24, 1, 'Reimbursement of Claude AI Subscription Charges - Arkam IT', 'Reimbursement-of-Claude-AI-Subscription-Charges-Arkam IT', NULL, NULL, 1, 1, '2026-09-04 00:09:18', '2026-09-04 00:09:18', 1),
(25, 1, 'rental lap for General comtronics', 'rental-lap-for-General-comtronics', NULL, NULL, 1, 1, '2026-09-04 00:09:43', '2026-09-04 00:09:43', 1),
(26, 1, 'rental lap for Life comtronics', 'rental-lap-for-Life-comtronics', NULL, NULL, 1, 1, '2026-09-04 00:10:16', '2026-09-04 00:10:16', 1),
(27, 1, 'Request to Purchase Switch', 'Request-to-Purchase-Switch', NULL, NULL, 1, 1, '2026-09-04 00:10:36', '2026-09-04 00:10:36', 1),
(28, 1, 'SBU3 Dialog', 'SBU3-Dialog', NULL, NULL, 1, 1, '2026-09-04 00:10:51', '2026-09-04 00:10:51', 1),
(29, 1, 'Softvision BusyAMC', 'Softvision BusyAMC', NULL, NULL, 1, 1, '2026-09-04 00:11:24', '2026-09-04 00:11:24', 1),
(30, 1, 'Tryonics Hosting Payment', 'Tryonics-Hosting-Payment', NULL, NULL, 1, 1, '2026-09-04 00:11:46', '2026-09-04 00:11:46', 1),
(31, 1, 'Uploading document through Medical or Life CMS', 'Uploading-document-through-Medical-or-Life-CMS', NULL, NULL, 1, 1, '2026-09-04 00:12:25', '2026-09-04 00:12:25', 1),
(33, 3, 'Finance-01', 'FIN-01', NULL, NULL, 1, 1, '2026-09-16 06:06:34', '2026-09-16 06:06:34', 1),
(34, 3, 'Finance-02', 'FIN-02', NULL, NULL, 1, 1, '2026-09-16 10:02:30', '2026-09-16 10:02:30', 1);

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_08_31_095600_create_departments_table', 1),
(5, '2026_08_31_100254_create_roles_table', 2),
(6, '2026_08_31_100433_add_memo_fields_to_users_table', 2),
(7, '2026_08_31_100519_create_user_signatures_table', 2),
(8, '2026_08_31_110628_create_memo_templates_table', 3),
(9, '2026_08_31_110809_create_template_fields_table', 3),
(10, '2026_08_31_110942_create_memos_table', 4),
(11, '2026_08_31_111054_create_memo_field_values_table', 4),
(12, '2026_08_31_111609_create_approval_workflows_table', 5),
(13, '2026_08_31_111639_create_approval_steps_table', 5),
(14, '2026_08_31_111716_create_memo_approvals_table', 5),
(15, '2026_09_03_091634_create_template_tables_table', 6),
(16, '2026_09_03_091651_create_template_table_columns_table', 6),
(17, '2026_09_03_091700_create_template_approval_steps_table', 6),
(18, '2026_09_11_000000_create_memo_table_rows_table', 7),
(19, '2026_09_11_000001_add_approval_role_to_memo_approvals_table', 8),
(20, '2026_09_17_000001_create_memo_number_sequences_table', 9),
(21, '2026_09_25_000001_add_text_blocks_to_memos_table', 10),
(22, '2026_09_30_120000_create_memo_attachments_table', 11),
(23, '2026_09_30_160000_add_inserted_items_to_memos_table', 12),
(24, '2026_10_01_120000_add_table_formats_to_memos', 13),
(25, '2026_10_05_120000_add_workflow_config_to_memos', 14),
(26, '2026_09_25_120000_add_optional_text_to_memo_templates_table', 15),
(27, '2026_10_05_000001_add_menu_access_to_roles_table', 15),
(28, '2026_10_05_130000_add_menu_access_to_users_table', 16),
(29, '2026_10_05_140000_create_memo_status_labels_table', 17),
(30, '2026_10_05_150000_create_sidebar_menu_items_table', 18),
(31, '2026_10_06_000000_rename_edit_sidebar_item_to_update', 19),
(32, '2026_10_06_010000_remove_all_memos_sidebar_link', 20);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE IF NOT EXISTS `password_reset_tokens` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `role_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `menu_access` json DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_role_name_unique` (`role_name`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `role_name`, `description`, `status`, `created_at`, `updated_at`, `menu_access`) VALUES
(1, 'Employee', 'Regular employee who can create and submit memos.', 1, '2026-09-03 05:23:08', '2026-09-03 05:23:08', NULL),
(2, 'Approver', 'User who can review and approve memos.', 1, '2026-09-03 05:23:08', '2026-09-03 05:23:08', NULL),
(3, 'Admin', 'System administrator', 1, '2026-10-05 04:35:13', '2026-10-06 04:15:38', '[\"dashboard\", \"new_memo\", \"templates\", \"memos\", \"all_memos\", \"approvals\", \"user_registration\", \"access_menu\", \"edit\"]');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('WII75ldUPQog2B6BvSBFR9khMKUYoOJnCj6p0iF0', 5, '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/152.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiUGRMWlMxT0Jwb1FkT3JiSU42TDF2NDNMUUpwVXh6dGpka3B0QTlBaCI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjY1OiJodHRwOi8vbG9jYWxob3N0L21lbW9fbWFuYWdlbWVudF9zeXN0ZW0vcHVibGljL2FkbWluL3VzZXJzLzEvZWRpdCI7czo1OiJyb3V0ZSI7czoxNjoiYWRtaW4udXNlcnMuZWRpdCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjU7fQ==', 1791440219);

-- --------------------------------------------------------

--
-- Table structure for table `sidebar_menu_items`
--

DROP TABLE IF EXISTS `sidebar_menu_items`;
CREATE TABLE IF NOT EXISTS `sidebar_menu_items` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `item_key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `label` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `route_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `parent_id` bigint UNSIGNED DEFAULT NULL,
  `icon` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'document',
  `access_key` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `admin_only` tinyint(1) NOT NULL DEFAULT '0',
  `is_group` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `sort_order` int UNSIGNED NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sidebar_menu_items_item_key_unique` (`item_key`),
  KEY `sidebar_menu_items_parent_id_foreign` (`parent_id`)
) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sidebar_menu_items`
--

INSERT INTO `sidebar_menu_items` (`id`, `item_key`, `label`, `route_name`, `url`, `parent_id`, `icon`, `access_key`, `admin_only`, `is_group`, `is_active`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'memos_group', 'Memos', NULL, NULL, NULL, 'document', NULL, 0, 1, 1, 30, '2026-10-05 11:09:11', '2026-10-08 05:38:33'),
(2, 'settings_group', 'Settings', NULL, NULL, NULL, 'grid', NULL, 1, 1, 1, 50, '2026-10-05 11:09:11', '2026-10-05 11:09:11'),
(3, 'dashboard', 'Dashboard', 'dashboard', NULL, NULL, 'grid', NULL, 0, 0, 1, 10, '2026-10-05 11:09:11', '2026-10-05 11:09:11'),
(4, 'new_memo', 'New Memo', 'memos.new', NULL, NULL, 'plus', 'new_memo', 0, 0, 1, 20, '2026-10-05 11:09:11', '2026-10-08 05:38:33'),
(5, 'templates', 'Templates', 'templates.index', NULL, 1, 'document', 'templates', 0, 0, 1, 10, '2026-10-05 11:09:11', '2026-10-05 11:09:11'),
(6, 'my_memos', 'Memos', 'memos.my', NULL, 1, 'document', 'memos', 0, 0, 1, 20, '2026-10-05 11:09:11', '2026-10-05 11:09:11'),
(7, 'all_memos', 'All memos', 'memos.all', NULL, 1, 'grid', NULL, 1, 0, 0, 30, '2026-10-05 11:09:11', '2026-10-08 05:38:33'),
(8, 'approvals', 'Approvals', 'approvals.index', NULL, NULL, 'check', 'approvals', 0, 0, 1, 40, '2026-10-05 11:09:11', '2026-10-08 05:38:33'),
(9, 'user_registration', 'User Registration', 'admin.users.create', NULL, 2, 'plus', NULL, 1, 0, 1, 10, '2026-10-05 11:09:11', '2026-10-05 11:09:11'),
(10, 'access_menu', 'Access Menu', 'admin.access-menu', NULL, 2, 'grid', NULL, 1, 0, 1, 20, '2026-10-05 11:09:11', '2026-10-05 11:09:11'),
(11, 'edit', 'Update Labels', 'admin.roles.index', NULL, 2, 'document', NULL, 1, 0, 1, 30, '2026-10-05 11:09:11', '2026-10-05 11:09:11');

-- --------------------------------------------------------

--
-- Table structure for table `template_approval_steps`
--

DROP TABLE IF EXISTS `template_approval_steps`;
CREATE TABLE IF NOT EXISTS `template_approval_steps` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `template_id` bigint UNSIGNED NOT NULL,
  `step_label` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `step_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'approval',
  `step_order` int NOT NULL DEFAULT '0',
  `is_required` tinyint(1) NOT NULL DEFAULT '1',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `template_approval_steps_template_id_foreign` (`template_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `template_fields`
--

DROP TABLE IF EXISTS `template_fields`;
CREATE TABLE IF NOT EXISTS `template_fields` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `template_id` bigint UNSIGNED NOT NULL,
  `field_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `field_label` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `field_type` enum('text','textarea','number','date','datetime','email','select','radio','checkbox','file','richtext') COLLATE utf8mb4_unicode_ci NOT NULL,
  `placeholder` text COLLATE utf8mb4_unicode_ci,
  `options` json DEFAULT NULL,
  `is_required` tinyint(1) NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `field_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `template_fields_template_id_foreign` (`template_id`)
) ENGINE=MyISAM AUTO_INCREMENT=56 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `template_fields`
--

INSERT INTO `template_fields` (`id`, `template_id`, `field_name`, `field_label`, `field_type`, `placeholder`, `options`, `is_required`, `is_active`, `field_order`, `created_at`, `updated_at`) VALUES
(45, 9, 'vendor', 'Vendor', 'text', NULL, NULL, 0, 1, 2, '2026-09-24 04:09:38', '2026-09-24 04:09:38'),
(46, 9, 'bill_period', 'Bill Period', 'text', NULL, NULL, 0, 1, 3, '2026-09-24 04:09:38', '2026-09-24 04:09:38'),
(47, 4, 'payment_description', 'Payment Description', 'textarea', NULL, NULL, 0, 1, 1, '2026-09-24 07:23:06', '2026-09-24 07:23:06'),
(48, 4, 'vendor', 'Vendor', 'text', NULL, NULL, 0, 1, 2, '2026-09-24 07:23:07', '2026-09-24 07:23:07'),
(49, 4, 'bill_period', 'Bill Period', 'text', NULL, NULL, 0, 1, 3, '2026-09-24 07:23:07', '2026-09-24 07:23:07'),
(50, 5, 'payment_description', 'Payment Description', 'textarea', NULL, NULL, 0, 1, 1, '2026-09-24 07:25:42', '2026-09-24 07:25:42'),
(44, 9, 'Payment_description', 'Payment Description', 'textarea', NULL, NULL, 0, 1, 1, '2026-09-24 04:09:38', '2026-09-24 04:09:38'),
(43, 3, 'bill_period', 'Bill Period', 'text', NULL, NULL, 0, 1, 3, '2026-09-11 02:40:33', '2026-09-11 02:40:33'),
(42, 3, 'vendor', 'Vendor', 'text', NULL, NULL, 0, 1, 2, '2026-09-11 02:40:33', '2026-09-11 02:40:33'),
(38, 1, 'Payment_description', 'Payment Description', 'textarea', NULL, NULL, 0, 1, 1, '2026-09-03 23:38:09', '2026-09-03 23:38:09'),
(39, 1, 'vendor', 'Vendor', 'text', NULL, NULL, 0, 1, 2, '2026-09-03 23:38:09', '2026-09-03 23:38:09'),
(40, 1, 'bill_period', 'Bill Period', 'text', NULL, NULL, 0, 1, 3, '2026-09-03 23:38:09', '2026-09-03 23:38:09'),
(41, 3, 'Payment_description', 'Payment Description', 'textarea', NULL, NULL, 0, 1, 1, '2026-09-11 02:40:33', '2026-09-11 02:40:33'),
(51, 5, 'vendor', 'Vendor', 'text', NULL, NULL, 0, 1, 2, '2026-09-24 07:25:42', '2026-09-24 07:25:42'),
(52, 6, 'payment_description', 'Payment Description', 'textarea', NULL, NULL, 0, 1, 1, '2026-09-24 07:29:13', '2026-09-24 07:29:13'),
(53, 6, 'vendor', 'Vendor', 'text', NULL, NULL, 0, 1, 2, '2026-09-24 07:29:13', '2026-09-24 07:29:13'),
(54, 7, 'payment_description', 'Payment Description', 'textarea', NULL, NULL, 0, 1, 1, '2026-09-24 07:37:09', '2026-09-24 07:37:09'),
(55, 7, 'vendor', 'Vendor', 'text', NULL, NULL, 0, 1, 2, '2026-09-24 07:37:09', '2026-09-24 07:37:09');

-- --------------------------------------------------------

--
-- Table structure for table `template_tables`
--

DROP TABLE IF EXISTS `template_tables`;
CREATE TABLE IF NOT EXISTS `template_tables` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `template_id` bigint UNSIGNED NOT NULL,
  `table_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `table_label` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `table_order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `template_tables_template_id_foreign` (`template_id`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `template_tables`
--

INSERT INTO `template_tables` (`id`, `template_id`, `table_name`, `table_label`, `is_active`, `table_order`, `created_at`, `updated_at`) VALUES
(2, 1, 'payment_details', 'Payment Details', 1, 1, '2026-09-03 05:25:44', '2026-09-03 05:25:44'),
(3, 3, 'payment_details', NULL, 1, 1, '2026-09-11 02:41:11', '2026-10-02 05:09:25'),
(7, 9, 'payment_details', 'Payment Details', 1, 1, '2026-09-24 04:58:48', '2026-09-24 04:58:48'),
(8, 9, 'Taxes', 'Please find below the list of users assigned to the following numbers.', 1, 2, '2026-09-24 04:58:48', '2026-09-24 04:58:48'),
(9, 4, 'table', NULL, 1, 1, '2026-09-24 07:23:49', '2026-09-24 07:23:49'),
(10, 5, 'table', NULL, 1, 1, '2026-09-24 07:26:50', '2026-09-24 07:26:50'),
(11, 6, 'table', NULL, 1, 1, '2026-09-24 07:29:46', '2026-09-24 07:29:46'),
(12, 7, 'table', NULL, 1, 1, '2026-09-24 07:37:51', '2026-09-24 07:37:51'),
(13, 24, 'custom_fa92ba344af64f83ab47bf308cffd37c', 'Payment deatils', 1, 1, '2026-09-25 09:59:20', '2026-09-25 09:59:20');

-- --------------------------------------------------------

--
-- Table structure for table `template_table_columns`
--

DROP TABLE IF EXISTS `template_table_columns`;
CREATE TABLE IF NOT EXISTS `template_table_columns` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `template_table_id` bigint UNSIGNED NOT NULL,
  `column_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `column_label` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `column_type` enum('text','number','decimal','date','textarea') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'text',
  `placeholder` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_required` tinyint(1) NOT NULL DEFAULT '0',
  `is_calculated` tinyint(1) NOT NULL DEFAULT '0',
  `calculation` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `column_order` int NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `template_table_columns_template_table_id_foreign` (`template_table_id`)
) ENGINE=MyISAM AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `template_table_columns`
--

INSERT INTO `template_table_columns` (`id`, `template_table_id`, `column_name`, `column_label`, `column_type`, `placeholder`, `is_required`, `is_calculated`, `calculation`, `column_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 'description', 'Description', 'textarea', NULL, 1, 0, NULL, 1, 1, '2026-09-03 05:14:33', '2026-09-03 05:14:33'),
(2, 2, 'Description', 'Description', 'textarea', NULL, 1, 0, NULL, 1, 1, '2026-09-03 05:25:44', '2026-09-03 05:25:44'),
(3, 2, 'Amount', 'Amount', 'decimal', NULL, 1, 0, NULL, 2, 1, '2026-09-03 05:25:44', '2026-09-03 05:25:44'),
(4, 3, 'description', 'Description', 'textarea', NULL, 0, 0, NULL, 1, 1, '2026-09-11 02:41:11', '2026-09-11 02:41:11'),
(5, 3, 'Amount', 'Amount', 'decimal', NULL, 0, 0, NULL, 2, 1, '2026-09-11 02:41:11', '2026-09-11 02:41:11'),
(6, 4, 'Description', 'Description', 'text', NULL, 0, 0, NULL, 1, 1, '2026-09-24 04:53:26', '2026-09-24 04:53:26'),
(7, 4, 'Total_Amount', 'TotalAmount', 'text', NULL, 0, 0, NULL, 2, 1, '2026-09-24 04:53:26', '2026-09-24 04:53:26'),
(8, 5, 'Description', 'Description', 'text', NULL, 0, 0, NULL, 1, 1, '2026-09-24 04:57:00', '2026-09-24 04:57:00'),
(9, 5, 'Total_Amount', 'TotalAmount', 'text', NULL, 0, 0, NULL, 2, 1, '2026-09-24 04:57:00', '2026-09-24 04:57:00'),
(10, 6, 'name', 'Name', 'text', NULL, 0, 0, NULL, 1, 1, '2026-09-24 04:57:00', '2026-09-24 04:57:00'),
(11, 7, 'Description', 'Description', 'text', NULL, 0, 0, NULL, 1, 1, '2026-09-24 04:58:48', '2026-09-24 04:58:48'),
(12, 7, 'Total_Amount', 'TotalAmount', 'text', NULL, 0, 0, NULL, 2, 1, '2026-09-24 04:58:48', '2026-09-24 04:58:48'),
(13, 8, 'name', 'Name', 'text', NULL, 0, 0, NULL, 1, 1, '2026-09-24 04:58:48', '2026-09-24 04:58:48'),
(14, 8, 'phone_number', 'Phone Number', 'number', NULL, 0, 0, NULL, 2, 1, '2026-09-24 04:58:48', '2026-09-24 04:58:48'),
(15, 8, '3cx_no', '3cx no', 'number', NULL, 0, 0, NULL, 3, 1, '2026-09-24 04:58:48', '2026-09-24 04:58:48'),
(16, 9, 'description', 'Description', 'text', NULL, 0, 0, NULL, 1, 1, '2026-09-24 07:23:49', '2026-09-24 07:23:49'),
(17, 9, 'amount', 'Amount', 'decimal', NULL, 0, 0, NULL, 2, 1, '2026-09-24 07:23:49', '2026-09-24 07:23:49'),
(18, 10, 'description', 'Description', 'text', NULL, 0, 0, NULL, 1, 1, '2026-09-24 07:26:50', '2026-09-24 07:26:50'),
(19, 10, 'qty', 'QTY', 'number', NULL, 0, 0, NULL, 2, 1, '2026-09-24 07:26:50', '2026-09-24 07:26:50'),
(20, 10, 'unit_price_lkr', 'Unit Price (LKR)', 'decimal', NULL, 0, 0, NULL, 3, 1, '2026-09-24 07:26:50', '2026-09-24 07:26:50'),
(21, 10, 'total_price_lkr', 'Total Price (LKR)', 'decimal', NULL, 0, 0, NULL, 4, 1, '2026-09-24 07:26:50', '2026-09-24 07:26:50'),
(22, 11, 'description', 'Description', 'textarea', NULL, 0, 0, NULL, 1, 1, '2026-09-24 07:29:46', '2026-09-24 07:29:46'),
(23, 11, 'qty', 'QTY', 'number', NULL, 0, 0, NULL, 2, 1, '2026-09-24 07:29:46', '2026-09-24 07:29:46'),
(24, 11, 'unit_price_lkr', 'Unit Price (LKR)', 'decimal', NULL, 0, 0, NULL, 3, 1, '2026-09-24 07:29:46', '2026-09-24 07:29:46'),
(25, 11, 'total_price_lkr', 'Total Price (LKR)', 'decimal', NULL, 0, 0, NULL, 4, 1, '2026-09-24 07:29:46', '2026-09-24 07:29:46'),
(26, 12, 'description', 'Description', 'textarea', NULL, 0, 0, NULL, 1, 1, '2026-09-24 07:37:51', '2026-09-24 07:37:51'),
(27, 12, 'qty', 'QTY', 'number', NULL, 0, 0, NULL, 2, 1, '2026-09-24 07:37:51', '2026-09-24 07:37:51'),
(28, 12, 'unit_price_lkr', 'Unit Price (LKR)', 'decimal', NULL, 0, 0, NULL, 3, 1, '2026-09-24 07:37:51', '2026-09-24 07:37:51'),
(29, 12, 'total_price_lkr', 'Total Price (LKR)', 'decimal', NULL, 0, 0, NULL, 4, 1, '2026-09-24 07:37:51', '2026-09-24 07:37:51'),
(30, 13, 'description', 'Description', 'text', NULL, 0, 0, NULL, 1, 1, '2026-09-25 09:59:20', '2026-09-25 09:59:20'),
(31, 13, 'amount', 'Amount', 'text', NULL, 0, 0, NULL, 2, 1, '2026-09-25 09:59:20', '2026-09-25 09:59:20'),
(32, 14, 'ngngngh', 'ngngngh', 'text', NULL, 0, 0, NULL, 1, 1, '2026-09-30 10:40:58', '2026-09-30 10:40:58'),
(33, 14, 'nghnhngh', 'nghnhngh', 'text', NULL, 0, 0, NULL, 2, 1, '2026-09-30 10:40:58', '2026-09-30 10:40:58'),
(34, 14, 'ngnh', 'ngnh', 'text', NULL, 0, 0, NULL, 3, 1, '2026-09-30 10:40:58', '2026-09-30 10:40:58'),
(35, 15, 'fbfbfgb', 'fbfbfgb', 'text', NULL, 0, 0, NULL, 1, 1, '2026-09-30 10:43:10', '2026-09-30 10:43:10'),
(36, 15, 'bfgbgfbgb', 'bfgbgfbgb', 'text', NULL, 0, 0, NULL, 2, 1, '2026-09-30 10:43:10', '2026-09-30 10:43:10');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `department_id` bigint UNSIGNED DEFAULT NULL,
  `role_id` bigint UNSIGNED DEFAULT NULL,
  `employee_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `designation` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `menu_access` json DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_username_unique` (`username`),
  UNIQUE KEY `users_employee_id_unique` (`employee_id`),
  KEY `users_department_id_foreign` (`department_id`),
  KEY `users_role_id_foreign` (`role_id`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `department_id`, `role_id`, `employee_id`, `designation`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `menu_access`) VALUES
(1, 'IMANTHI.OSHADI', 1, 1, 'EMP1234', 'Associate Developer', 'Imanthi Abeyratne', 'imanthioshadhi@gmail.com', NULL, '$2y$12$IkkYYG4lYc7CHUwAwi2UM.TL7my1czZVSnn/bk7Qt54yzKN26l9NW', NULL, '2026-09-02 23:54:34', '2026-09-24 07:28:53', NULL),
(2, 'NIMUTHU.SILVA', 1, 2, 'EMP2345', 'Trainee-IT', 'Nimuthu Silva', 'nimuthu.silva@takaful.lk', NULL, '$2y$12$cAE5Z5MBuX/lNidIwZrNUe2DKtatKk6dUYrHQFqZZ/pyH9pVLnfSO', NULL, '2026-09-11 02:44:51', '2026-09-11 02:44:51', NULL),
(3, 'SAJANI.UPEKSHA', 1, 2, 'EMP3456', 'IT Executive', 'Sajani Upeksha', 'sajani.upeksha@takaful.lk', NULL, '$2y$12$8L1HqDS9dPLqd4TBZ84yaOAo1ORXak1OqMwcz1yU3cumJYn5f54Lu', NULL, '2026-09-11 02:47:24', '2026-09-11 02:47:24', NULL),
(4, 'DANUSHAKA.LIYANAGE', 1, 2, 'EMP4567', 'Head of IT', 'Danushka Liyanage', 'danushka.liyanage@takaful.lk', NULL, '$2y$12$jUkmBfJBLk87u4O7S7q9ee/8ZoH7FS/nVoO2eQ04uyuSpGogNqgFm', NULL, '2026-09-11 02:48:49', '2026-09-11 02:48:49', NULL),
(5, 'Admin', 1, 3, 'ADMIN001', 'System Administrator', 'System Admin', 'admin@gmail.com', NULL, '$2y$12$H9RPMe1lb2iz/r6lTgapDu.HdhwUsfDWVsXYcolN1BOg.TupfUuCm', NULL, '2026-10-05 04:35:13', '2026-10-05 05:13:17', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_signatures`
--

DROP TABLE IF EXISTS `user_signatures`;
CREATE TABLE IF NOT EXISTS `user_signatures` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id` bigint UNSIGNED NOT NULL,
  `signature_path` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `uploaded_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_signatures_user_id_foreign` (`user_id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_signatures`
--

INSERT INTO `user_signatures` (`id`, `user_id`, `signature_path`, `is_active`, `uploaded_at`, `created_at`, `updated_at`) VALUES
(1, 3, 'signatures/ahpyOCCgYRfyeGuhgRFzCnRGMYWoOtv3B4hnTx1k.png', 1, '2026-09-11 02:47:24', '2026-09-11 02:47:24', '2026-09-11 02:47:24'),
(2, 1, 'signatures/0TPaBC6D9NCbqQFiLrWqtSmXyS37ZRsfKclCClNJ.png', 1, '2026-09-17 04:30:37', '2026-09-17 04:30:37', '2026-09-17 04:30:37');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
