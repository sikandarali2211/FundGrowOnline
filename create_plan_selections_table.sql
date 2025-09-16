CREATE TABLE `plan_selections` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `plan_name` varchar(255) NOT NULL,
  `plan_amount` decimal(15,2) NOT NULL,
  `return_percentage` decimal(5,2) NOT NULL,
  `duration_days` int(11) NOT NULL,
  `expected_return` decimal(15,2) NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `admin_notes` text,
  `processed_by` bigint(20) unsigned DEFAULT NULL,
  `processed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `plan_selections_user_id_status_index` (`user_id`,`status`),
  KEY `plan_selections_status_created_at_index` (`status`,`created_at`),
  KEY `plan_selections_user_id_foreign` (`user_id`),
  KEY `plan_selections_processed_by_foreign` (`processed_by`),
  CONSTRAINT `plan_selections_processed_by_foreign` FOREIGN KEY (`processed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `plan_selections_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

