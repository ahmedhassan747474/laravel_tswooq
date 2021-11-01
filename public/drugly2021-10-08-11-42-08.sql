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




DROP TABLE ch_favorites;

CREATE TABLE `ch_favorites` (
  `id` bigint(20) NOT NULL,
  `user_id` bigint(20) NOT NULL,
  `favorite_id` bigint(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO ch_favorites VALUES("66042881","1","3","2021-09-03 14:14:33","2021-09-03 14:14:33");



DROP TABLE ch_messages;

CREATE TABLE `ch_messages` (
  `id` bigint(20) NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `from_id` bigint(20) NOT NULL,
  `to_id` bigint(20) NOT NULL,
  `body` varchar(5000) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attachment` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `seen` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO ch_messages VALUES("1813563242","user","1","3","","{\"new_name\":\"499a66d5-6fc2-4248-8d05-8c6622614213.jpg\",\"old_name\":\"146887260631827780522553_d39f6d59ce_b (1).jpg\"}","1","2021-09-03 14:09:24","2021-09-03 14:09:30");
INSERT INTO ch_messages VALUES("1863952561","user","3","1","كيف حالك","","1","2021-09-03 14:08:52","2021-09-03 14:08:53");
INSERT INTO ch_messages VALUES("1951705398","user","1","3","السلام عليكم","","1","2021-09-03 14:04:46","2021-09-03 14:06:51");
INSERT INTO ch_messages VALUES("2143089453","user","1","3","انا بخير","","1","2021-09-03 14:09:00","2021-09-03 14:09:01");
INSERT INTO ch_messages VALUES("2306124680","user","3","1","وعليكم السلام","","1","2021-09-03 14:07:44","2021-09-03 14:08:38");



DROP TABLE locations;

CREATE TABLE `locations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `owner_id` int(10) unsigned NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `longitude` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `latitude` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `locations_owner_id_foreign` (`owner_id`),
  CONSTRAINT `locations_owner_id_foreign` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE logs;

CREATE TABLE `logs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `log_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `log_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `action_by` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `logs_action_by_foreign` (`action_by`),
  CONSTRAINT `logs_action_by_foreign` FOREIGN KEY (`action_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO logs VALUES("1","stagnants","1","stagnant_has_been_added","1","2021-08-29 00:43:16","2021-08-29 00:43:16");
INSERT INTO logs VALUES("2","stagnants","2","stagnant_has_been_added","2","2021-10-06 18:42:19","2021-10-06 18:42:19");
INSERT INTO logs VALUES("3","stagnants","3","stagnant_has_been_added","2","2021-10-06 18:43:08","2021-10-06 18:43:08");
INSERT INTO logs VALUES("4","stagnants","4","stagnant_has_been_added","2","2021-10-06 18:49:08","2021-10-06 18:49:08");



DROP TABLE migrations;

CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO migrations VALUES("1","2013_10_09_000000_create_users_table","1");
INSERT INTO migrations VALUES("2","2014_07_10_125132_create_brands_table","1");
INSERT INTO migrations VALUES("3","2014_10_12_100000_create_password_resets_table","1");
INSERT INTO migrations VALUES("4","2016_06_01_000001_create_oauth_auth_codes_table","1");
INSERT INTO migrations VALUES("5","2016_06_01_000002_create_oauth_access_tokens_table","1");
INSERT INTO migrations VALUES("6","2016_06_01_000003_create_oauth_refresh_tokens_table","1");
INSERT INTO migrations VALUES("7","2016_06_01_000004_create_oauth_clients_table","1");
INSERT INTO migrations VALUES("8","2016_06_01_000005_create_oauth_personal_access_clients_table","1");
INSERT INTO migrations VALUES("9","2021_03_01_074238_create_categories_table","1");
INSERT INTO migrations VALUES("10","2021_03_01_074238_create_products_table","1");
INSERT INTO migrations VALUES("11","2021_03_01_074318_create_category_translations_table","1");
INSERT INTO migrations VALUES("12","2021_03_01_074318_create_product_logs_table","1");
INSERT INTO migrations VALUES("13","2021_03_01_074318_create_product_translations_table","1");
INSERT INTO migrations VALUES("14","2021_03_01_074418_create_product_log_translations_table","1");
INSERT INTO migrations VALUES("15","2021_03_22_191340_laratrust_setup_tables","1");
INSERT INTO migrations VALUES("16","2021_07_10_125301_create_brand_translations_table","1");
INSERT INTO migrations VALUES("17","2021_07_15_164000_create_app_settings_table","1");
INSERT INTO migrations VALUES("18","2021_07_15_164617_create_app_setting_translations_table","1");
INSERT INTO migrations VALUES("19","2021_07_26_120229_create_notifications_table","1");
INSERT INTO migrations VALUES("20","2021_07_31_110248_create_stagnants_table","1");
INSERT INTO migrations VALUES("21","2021_08_02_130441_create_stores_table","1");
INSERT INTO migrations VALUES("22","2021_08_02_131015_create_store_translations_table","1");
INSERT INTO migrations VALUES("23","2021_08_03_154416_create_areas_table","1");
INSERT INTO migrations VALUES("24","2021_08_03_155343_create_area_translations_table","1");
INSERT INTO migrations VALUES("25","2021_08_03_155441_create_locations_table","1");
INSERT INTO migrations VALUES("26","2021_08_07_130043_create_subscriptions_table","1");
INSERT INTO migrations VALUES("27","2021_08_07_134156_create_subscription_translations_table","1");
INSERT INTO migrations VALUES("28","2021_08_07_140135_create_subscribers_table","1");
INSERT INTO migrations VALUES("29","2021_08_16_184315_create_advertisements_table","1");
INSERT INTO migrations VALUES("30","2021_08_16_184414_create_advertisement_translations_table","1");
INSERT INTO migrations VALUES("31","2021_08_17_164440_create_logs_table","1");
INSERT INTO migrations VALUES("32","2019_09_22_192348_create_messages_table","2");
INSERT INTO migrations VALUES("33","2019_10_16_211433_create_favorites_table","2");
INSERT INTO migrations VALUES("34","2019_10_18_223259_add_avatar_to_users","2");
INSERT INTO migrations VALUES("35","2019_10_20_211056_add_messenger_color_to_users","2");
INSERT INTO migrations VALUES("36","2019_10_22_000539_add_dark_mode_to_users","2");
INSERT INTO migrations VALUES("37","2019_10_25_214038_add_active_status_to_users","2");



DROP TABLE notifications;

CREATE TABLE `notifications` (
  `id` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `notifiable_id` bigint(20) unsigned NOT NULL,
  `data` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_announcement` tinyint(4) NOT NULL DEFAULT 0,
  `created_by` int(10) unsigned NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `notifications_notifiable_type_notifiable_id_index` (`notifiable_type`,`notifiable_id`),
  KEY `notifications_created_by_foreign` (`created_by`),
  CONSTRAINT `notifications_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE oauth_access_tokens;

CREATE TABLE `oauth_access_tokens` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `client_id` bigint(20) unsigned NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `scopes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_access_tokens_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE oauth_auth_codes;

CREATE TABLE `oauth_auth_codes` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `client_id` bigint(20) unsigned NOT NULL,
  `scopes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_auth_codes_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE oauth_clients;

CREATE TABLE `oauth_clients` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `secret` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `provider` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `redirect` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `personal_access_client` tinyint(1) NOT NULL,
  `password_client` tinyint(1) NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_clients_user_id_index` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE oauth_personal_access_clients;

CREATE TABLE `oauth_personal_access_clients` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




