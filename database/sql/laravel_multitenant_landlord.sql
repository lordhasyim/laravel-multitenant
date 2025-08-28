-- Adminer 5.3.0 MySQL 8.0.41 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `cache`;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `cache_locks`;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `failed_jobs`;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `job_batches`;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `jobs`;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `master_permissions`;
CREATE TABLE `master_permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'api',
  `category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `master_permissions` (`id`, `name`, `guard_name`, `category`, `description`, `created_at`, `updated_at`) VALUES
(1,	'create-product',	'api',	'products',	'Can create new products',	'2025-08-27 21:56:19',	'2025-08-27 21:56:19'),
(2,	'view-product',	'api',	'products',	'Can view products',	'2025-08-27 21:56:19',	'2025-08-27 21:56:19'),
(3,	'edit-product',	'api',	'products',	'Can edit existing products',	'2025-08-27 21:56:19',	'2025-08-27 21:56:19'),
(4,	'delete-product',	'api',	'products',	'Can delete products',	'2025-08-27 21:56:19',	'2025-08-27 21:56:19'),
(5,	'create-user',	'api',	'users',	'Can create new users',	'2025-08-27 21:56:19',	'2025-08-27 21:56:19'),
(6,	'view-user',	'api',	'users',	'Can view users',	'2025-08-27 21:56:19',	'2025-08-27 21:56:19'),
(7,	'edit-user',	'api',	'users',	'Can edit existing users',	'2025-08-27 21:56:19',	'2025-08-27 21:56:19'),
(8,	'delete-user',	'api',	'users',	'Can delete users',	'2025-08-27 21:56:19',	'2025-08-27 21:56:19');

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1,	'0001_01_01_000000_create_users_table',	1),
(2,	'0001_01_01_000001_create_cache_table',	1),
(3,	'0001_01_01_000002_create_jobs_table',	1),
(12,	'2019_09_15_000010_create_tenants_table',	2),
(13,	'2025_08_28_043016_create_master_permissions_table',	3);

DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `sessions`;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('BXiKYsGBboWvFnliIDgt6EEcMZDE166wD10huDUM',	NULL,	'127.0.0.1',	'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36',	'YTozOntzOjY6Il90b2tlbiI7czo0MDoiaTFMbmx3TXFYQ3hUbDlCQ3R1akxOSEVBc0F5WlV2cTM5NWUwRFpMeSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly9tdWx0aXRlbmFudC1hcGkudGVzdDo4MDAwL2NlbnRyYWwiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',	1756180024),
('SuEmtDuoXlm2mdqCTgQrffJNw0dgmiBuDwGSdI8I',	NULL,	'127.0.0.1',	'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36',	'YTozOntzOjY6Il90b2tlbiI7czo0MDoiS0dNWUpSbE9uUjZnTHpyaEtORVJncmt0YUtxSVk2ZDJDT3hWdVZJSyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NTA6Imh0dHA6Ly9hbGFtc2VnYXIubXVsdGl0ZW5hbnQtYXBpLnRlc3Q6ODAwMC9jZW50cmFsIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',	1756180098),
('X8x85zkGATLtRqFAhfM3H5pQn5Dgx1BMjMSNVwk0',	NULL,	'127.0.0.1',	'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/139.0.0.0 Safari/537.36',	'YTozOntzOjY6Il90b2tlbiI7czo0MDoid01Rb1EyN25lWVVjRE4wZnNQUEluUWJBUVJZWnVsMnFPUTVSQ3kyNyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6NDA6Imh0dHA6Ly9tdWx0aXRlbmFudC1hcGkudGVzdDo4MDAwL2NlbnRyYWwiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',	1756228771);

DROP TABLE IF EXISTS `tenants`;
CREATE TABLE `tenants` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `db_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `db_host` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '127.0.0.1',
  `db_port` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '3306',
  `db_username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'root',
  `db_password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `data` json DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `tenants` (`id`, `name`, `email`, `db_name`, `db_host`, `db_port`, `db_username`, `db_password`, `created_at`, `updated_at`, `data`) VALUES
(1,	'Alam Segar Company',	'admin@alamsegar.com',	'tenant_alamsegar',	'127.0.0.1',	'3306',	'root',	'root',	'2025-08-27 07:43:26',	'2025-08-27 07:43:26',	'{\"tenancy_db_name\": \"tenant_1\"}'),
(2,	'Kretek Jaya Company',	'admin@kretekjaya.com',	'tenant_2',	'127.0.0.1',	'3306',	'root',	'',	'2025-08-27 20:34:31',	'2025-08-27 20:34:31',	'{\"tenancy_db_name\": \"tenant_2\"}');

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1,	'Test User',	'test@example.com',	NULL,	'$2y$12$ejnGu.deiEbBGciOzb.AaOGeKTxWe/cMUsJOFaaFY/amDr12kslDy',	NULL,	'2025-08-26 10:23:06',	'2025-08-26 10:23:06'),
(2,	'Test User',	'test2@example.com',	NULL,	'$2y$12$FXIMLzNR6ohZU5At/EAqe.D6HO0za6UDQRB8foyq5ZAzpV7zTSlvS',	NULL,	'2025-08-26 10:25:05',	'2025-08-26 10:25:05'),
(3,	'Test User',	'test1@example.com',	NULL,	'$2y$12$a9WycZu8EBInKXtGnRjqH.agzQsCO0kEdUtTpwR6t1MGl0z2LaX/u',	NULL,	'2025-08-26 20:20:21',	'2025-08-26 20:20:21'),
(4,	'Alamsegar User',	'user@alamsegar.com',	NULL,	'$2y$12$c3ltWJksdIwmXv3pU97aYuXy9YBS4nqUNsBCcoZl1fGJXGrv353TK',	NULL,	'2025-08-26 20:33:21',	'2025-08-26 20:33:21');

-- 2025-08-28 05:32:00 UTC
