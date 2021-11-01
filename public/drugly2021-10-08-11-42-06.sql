DROP TABLE advertisement_translations;

CREATE TABLE `advertisement_translations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `advertisement_id` int(10) unsigned NOT NULL,
  `locale` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `advertisement_translations_advertisement_id_locale_unique` (`advertisement_id`,`locale`),
  KEY `advertisement_translations_locale_index` (`locale`),
  CONSTRAINT `advertisement_translations_advertisement_id_foreign` FOREIGN KEY (`advertisement_id`) REFERENCES `advertisements` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE advertisements;

CREATE TABLE `advertisements` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `end_date` date NOT NULL,
  `display_method` enum('horizontal','vertical','longitudinal') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'horizontal',
  `owner_id` int(10) unsigned DEFAULT NULL,
  `created_by` int(10) unsigned NOT NULL,
  `views` int(11) NOT NULL DEFAULT 0,
  `active` tinyint(4) NOT NULL DEFAULT 1,
  `image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `advertisements_owner_id_foreign` (`owner_id`),
  KEY `advertisements_created_by_foreign` (`created_by`),
  CONSTRAINT `advertisements_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `advertisements_owner_id_foreign` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE app_setting_translations;

CREATE TABLE `app_setting_translations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `app_setting_id` int(10) unsigned NOT NULL,
  `locale` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `about_us` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `privacy_policy` text COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `app_setting_translations_app_setting_id_locale_unique` (`app_setting_id`,`locale`),
  KEY `app_setting_translations_locale_index` (`locale`),
  CONSTRAINT `app_setting_translations_app_setting_id_foreign` FOREIGN KEY (`app_setting_id`) REFERENCES `app_settings` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




