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




DROP TABLE app_settings;

CREATE TABLE `app_settings` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `owner_id` int(10) unsigned NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `facebook_link` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `twitter_link` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `instagram_link` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `youtube_link` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ad_price` decimal(8,2) DEFAULT NULL,
  `ad_duration` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `app_settings_email_unique` (`email`),
  UNIQUE KEY `app_settings_phone_unique` (`phone`),
  KEY `app_settings_owner_id_foreign` (`owner_id`),
  CONSTRAINT `app_settings_owner_id_foreign` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE area_translations;

CREATE TABLE `area_translations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `area_id` int(10) unsigned NOT NULL,
  `locale` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `area_translations_area_id_locale_unique` (`area_id`,`locale`),
  KEY `area_translations_locale_index` (`locale`),
  CONSTRAINT `area_translations_area_id_foreign` FOREIGN KEY (`area_id`) REFERENCES `areas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO area_translations VALUES("1","1","ar","Macy Barber");
INSERT INTO area_translations VALUES("2","1","en","Nissim Knowles");



DROP TABLE areas;

CREATE TABLE `areas` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `created_by` int(10) unsigned NOT NULL,
  `parent_id` int(10) unsigned DEFAULT NULL,
  `active` tinyint(4) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `areas_created_by_foreign` (`created_by`),
  KEY `areas_parent_id_foreign` (`parent_id`),
  CONSTRAINT `areas_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `areas_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `areas` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO areas VALUES("1","1","","1","2021-08-28 02:05:52","2021-08-28 02:05:52");



DROP TABLE brand_translations;

CREATE TABLE `brand_translations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `brand_id` int(10) unsigned NOT NULL,
  `locale` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `brand_translations_brand_id_locale_unique` (`brand_id`,`locale`),
  KEY `brand_translations_locale_index` (`locale`),
  CONSTRAINT `brand_translations_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE brands;

CREATE TABLE `brands` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `store_id` int(10) unsigned NOT NULL,
  `created_by` int(10) unsigned NOT NULL,
  `active` tinyint(4) NOT NULL DEFAULT 1,
  `image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `brands_created_by_foreign` (`created_by`),
  CONSTRAINT `brands_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE categories;

CREATE TABLE `categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `store_id` int(10) unsigned NOT NULL,
  `created_by` int(10) unsigned NOT NULL,
  `parent_id` int(10) unsigned DEFAULT NULL,
  `active` tinyint(4) NOT NULL DEFAULT 1,
  `image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `categories_created_by_foreign` (`created_by`),
  KEY `categories_parent_id_foreign` (`parent_id`),
  CONSTRAINT `categories_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE category_translations;

CREATE TABLE `category_translations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(10) unsigned NOT NULL,
  `locale` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `category_translations_category_id_locale_unique` (`category_id`,`locale`),
  KEY `category_translations_locale_index` (`locale`),
  CONSTRAINT `category_translations_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




