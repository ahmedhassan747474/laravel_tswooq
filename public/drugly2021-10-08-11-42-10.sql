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




DROP TABLE oauth_refresh_tokens;

CREATE TABLE `oauth_refresh_tokens` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `access_token_id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `oauth_refresh_tokens_access_token_id_index` (`access_token_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE password_resets;

CREATE TABLE `password_resets` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE permission_role;

CREATE TABLE `permission_role` (
  `permission_id` int(10) unsigned NOT NULL,
  `role_id` int(10) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `permission_role_role_id_foreign` (`role_id`),
  CONSTRAINT `permission_role_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `permission_role_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO permission_role VALUES("1","1");
INSERT INTO permission_role VALUES("2","1");
INSERT INTO permission_role VALUES("3","1");
INSERT INTO permission_role VALUES("4","1");
INSERT INTO permission_role VALUES("5","1");
INSERT INTO permission_role VALUES("6","1");
INSERT INTO permission_role VALUES("7","1");
INSERT INTO permission_role VALUES("8","1");
INSERT INTO permission_role VALUES("9","1");
INSERT INTO permission_role VALUES("10","1");
INSERT INTO permission_role VALUES("11","1");
INSERT INTO permission_role VALUES("12","1");
INSERT INTO permission_role VALUES("13","1");
INSERT INTO permission_role VALUES("14","1");
INSERT INTO permission_role VALUES("15","1");
INSERT INTO permission_role VALUES("16","1");
INSERT INTO permission_role VALUES("17","1");
INSERT INTO permission_role VALUES("18","1");
INSERT INTO permission_role VALUES("19","1");
INSERT INTO permission_role VALUES("20","1");
INSERT INTO permission_role VALUES("21","1");
INSERT INTO permission_role VALUES("22","1");
INSERT INTO permission_role VALUES("23","1");
INSERT INTO permission_role VALUES("24","1");
INSERT INTO permission_role VALUES("25","1");
INSERT INTO permission_role VALUES("26","1");
INSERT INTO permission_role VALUES("27","1");
INSERT INTO permission_role VALUES("28","1");
INSERT INTO permission_role VALUES("29","1");
INSERT INTO permission_role VALUES("30","1");
INSERT INTO permission_role VALUES("31","1");
INSERT INTO permission_role VALUES("32","1");
INSERT INTO permission_role VALUES("33","1");
INSERT INTO permission_role VALUES("34","1");
INSERT INTO permission_role VALUES("35","1");
INSERT INTO permission_role VALUES("36","1");
INSERT INTO permission_role VALUES("37","1");
INSERT INTO permission_role VALUES("38","1");
INSERT INTO permission_role VALUES("39","1");
INSERT INTO permission_role VALUES("40","1");
INSERT INTO permission_role VALUES("41","1");
INSERT INTO permission_role VALUES("42","1");
INSERT INTO permission_role VALUES("43","1");
INSERT INTO permission_role VALUES("44","1");
INSERT INTO permission_role VALUES("45","1");
INSERT INTO permission_role VALUES("46","1");
INSERT INTO permission_role VALUES("47","1");
INSERT INTO permission_role VALUES("48","1");
INSERT INTO permission_role VALUES("49","1");
INSERT INTO permission_role VALUES("50","1");
INSERT INTO permission_role VALUES("51","1");
INSERT INTO permission_role VALUES("52","1");
INSERT INTO permission_role VALUES("53","1");
INSERT INTO permission_role VALUES("54","1");
INSERT INTO permission_role VALUES("55","1");
INSERT INTO permission_role VALUES("56","1");



DROP TABLE permission_user;

CREATE TABLE `permission_user` (
  `permission_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `user_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`user_id`,`permission_id`,`user_type`),
  KEY `permission_user_permission_id_foreign` (`permission_id`),
  CONSTRAINT `permission_user_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE permissions;

CREATE TABLE `permissions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO permissions VALUES("1","create-users","Create Users","Create Users","2021-08-28 01:46:23","2021-08-28 01:46:23");
INSERT INTO permissions VALUES("2","read-users","Read Users","Read Users","2021-08-28 01:46:23","2021-08-28 01:46:23");
INSERT INTO permissions VALUES("3","update-users","Update Users","Update Users","2021-08-28 01:46:23","2021-08-28 01:46:23");
INSERT INTO permissions VALUES("4","delete-users","Delete Users","Delete Users","2021-08-28 01:46:23","2021-08-28 01:46:23");
INSERT INTO permissions VALUES("5","create-roles","Create Roles","Create Roles","2021-08-28 01:46:23","2021-08-28 01:46:23");
INSERT INTO permissions VALUES("6","read-roles","Read Roles","Read Roles","2021-08-28 01:46:23","2021-08-28 01:46:23");
INSERT INTO permissions VALUES("7","update-roles","Update Roles","Update Roles","2021-08-28 01:46:24","2021-08-28 01:46:24");
INSERT INTO permissions VALUES("8","delete-roles","Delete Roles","Delete Roles","2021-08-28 01:46:24","2021-08-28 01:46:24");
INSERT INTO permissions VALUES("9","create-categories","Create Categories","Create Categories","2021-08-28 01:46:24","2021-08-28 01:46:24");
INSERT INTO permissions VALUES("10","read-categories","Read Categories","Read Categories","2021-08-28 01:46:24","2021-08-28 01:46:24");
INSERT INTO permissions VALUES("11","update-categories","Update Categories","Update Categories","2021-08-28 01:46:24","2021-08-28 01:46:24");
INSERT INTO permissions VALUES("12","delete-categories","Delete Categories","Delete Categories","2021-08-28 01:46:24","2021-08-28 01:46:24");
INSERT INTO permissions VALUES("13","create-brands","Create Brands","Create Brands","2021-08-28 01:46:24","2021-08-28 01:46:24");
INSERT INTO permissions VALUES("14","read-brands","Read Brands","Read Brands","2021-08-28 01:46:24","2021-08-28 01:46:24");
INSERT INTO permissions VALUES("15","update-brands","Update Brands","Update Brands","2021-08-28 01:46:24","2021-08-28 01:46:24");
INSERT INTO permissions VALUES("16","delete-brands","Delete Brands","Delete Brands","2021-08-28 01:46:24","2021-08-28 01:46:24");
INSERT INTO permissions VALUES("17","create-products","Create Products","Create Products","2021-08-28 01:46:24","2021-08-28 01:46:24");
INSERT INTO permissions VALUES("18","read-products","Read Products","Read Products","2021-08-28 01:46:25","2021-08-28 01:46:25");
INSERT INTO permissions VALUES("19","update-products","Update Products","Update Products","2021-08-28 01:46:25","2021-08-28 01:46:25");
INSERT INTO permissions VALUES("20","delete-products","Delete Products","Delete Products","2021-08-28 01:46:25","2021-08-28 01:46:25");
INSERT INTO permissions VALUES("21","create-appsettings","Create Appsettings","Create Appsettings","2021-08-28 01:46:25","2021-08-28 01:46:25");
INSERT INTO permissions VALUES("22","read-appsettings","Read Appsettings","Read Appsettings","2021-08-28 01:46:25","2021-08-28 01:46:25");
INSERT INTO permissions VALUES("23","update-appsettings","Update Appsettings","Update Appsettings","2021-08-28 01:46:25","2021-08-28 01:46:25");
INSERT INTO permissions VALUES("24","delete-appsettings","Delete Appsettings","Delete Appsettings","2021-08-28 01:46:25","2021-08-28 01:46:25");
INSERT INTO permissions VALUES("25","create-stores","Create Stores","Create Stores","2021-08-28 01:46:25","2021-08-28 01:46:25");
INSERT INTO permissions VALUES("26","read-stores","Read Stores","Read Stores","2021-08-28 01:46:25","2021-08-28 01:46:25");
INSERT INTO permissions VALUES("27","update-stores","Update Stores","Update Stores","2021-08-28 01:46:25","2021-08-28 01:46:25");
INSERT INTO permissions VALUES("28","delete-stores","Delete Stores","Delete Stores","2021-08-28 01:46:25","2021-08-28 01:46:25");
INSERT INTO permissions VALUES("29","create-notifications","Create Notifications","Create Notifications","2021-08-28 01:46:25","2021-08-28 01:46:25");
INSERT INTO permissions VALUES("30","read-notifications","Read Notifications","Read Notifications","2021-08-28 01:46:26","2021-08-28 01:46:26");
INSERT INTO permissions VALUES("31","update-notifications","Update Notifications","Update Notifications","2021-08-28 01:46:26","2021-08-28 01:46:26");
INSERT INTO permissions VALUES("32","delete-notifications","Delete Notifications","Delete Notifications","2021-08-28 01:46:26","2021-08-28 01:46:26");
INSERT INTO permissions VALUES("33","create-stagnants","Create Stagnants","Create Stagnants","2021-08-28 01:46:26","2021-08-28 01:46:26");
INSERT INTO permissions VALUES("34","read-stagnants","Read Stagnants","Read Stagnants","2021-08-28 01:46:26","2021-08-28 01:46:26");
INSERT INTO permissions VALUES("35","update-stagnants","Update Stagnants","Update Stagnants","2021-08-28 01:46:26","2021-08-28 01:46:26");
INSERT INTO permissions VALUES("36","delete-stagnants","Delete Stagnants","Delete Stagnants","2021-08-28 01:46:26","2021-08-28 01:46:26");
INSERT INTO permissions VALUES("37","create-areas","Create Areas","Create Areas","2021-08-28 01:46:26","2021-08-28 01:46:26");
INSERT INTO permissions VALUES("38","read-areas","Read Areas","Read Areas","2021-08-28 01:46:26","2021-08-28 01:46:26");
INSERT INTO permissions VALUES("39","update-areas","Update Areas","Update Areas","2021-08-28 01:46:26","2021-08-28 01:46:26");
INSERT INTO permissions VALUES("40","delete-areas","Delete Areas","Delete Areas","2021-08-28 01:46:26","2021-08-28 01:46:26");
INSERT INTO permissions VALUES("41","create-subscriptions","Create Subscriptions","Create Subscriptions","2021-08-28 01:46:26","2021-08-28 01:46:26");
INSERT INTO permissions VALUES("42","read-subscriptions","Read Subscriptions","Read Subscriptions","2021-08-28 01:46:26","2021-08-28 01:46:26");
INSERT INTO permissions VALUES("43","update-subscriptions","Update Subscriptions","Update Subscriptions","2021-08-28 01:46:26","2021-08-28 01:46:26");
INSERT INTO permissions VALUES("44","delete-subscriptions","Delete Subscriptions","Delete Subscriptions","2021-08-28 01:46:26","2021-08-28 01:46:26");
INSERT INTO permissions VALUES("45","create-subscribers","Create Subscribers","Create Subscribers","2021-08-28 01:46:27","2021-08-28 01:46:27");
INSERT INTO permissions VALUES("46","read-subscribers","Read Subscribers","Read Subscribers","2021-08-28 01:46:27","2021-08-28 01:46:27");
INSERT INTO permissions VALUES("47","update-subscribers","Update Subscribers","Update Subscribers","2021-08-28 01:46:27","2021-08-28 01:46:27");
INSERT INTO permissions VALUES("48","delete-subscribers","Delete Subscribers","Delete Subscribers","2021-08-28 01:46:27","2021-08-28 01:46:27");
INSERT INTO permissions VALUES("49","create-advertisements","Create Advertisements","Create Advertisements","2021-08-28 01:46:27","2021-08-28 01:46:27");
INSERT INTO permissions VALUES("50","read-advertisements","Read Advertisements","Read Advertisements","2021-08-28 01:46:27","2021-08-28 01:46:27");
INSERT INTO permissions VALUES("51","update-advertisements","Update Advertisements","Update Advertisements","2021-08-28 01:46:27","2021-08-28 01:46:27");
INSERT INTO permissions VALUES("52","delete-advertisements","Delete Advertisements","Delete Advertisements","2021-08-28 01:46:27","2021-08-28 01:46:27");
INSERT INTO permissions VALUES("53","create-logs","Create Logs","Create Logs","2021-08-28 01:46:27","2021-08-28 01:46:27");
INSERT INTO permissions VALUES("54","read-logs","Read Logs","Read Logs","2021-08-28 01:46:27","2021-08-28 01:46:27");
INSERT INTO permissions VALUES("55","update-logs","Update Logs","Update Logs","2021-08-28 01:46:27","2021-08-28 01:46:27");
INSERT INTO permissions VALUES("56","delete-logs","Delete Logs","Delete Logs","2021-08-28 01:46:27","2021-08-28 01:46:27");



DROP TABLE product_log_translations;

CREATE TABLE `product_log_translations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_log_id` int(10) unsigned NOT NULL,
  `locale` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_log_translations_product_log_id_locale_unique` (`product_log_id`,`locale`),
  KEY `product_log_translations_locale_index` (`locale`),
  CONSTRAINT `product_log_translations_product_log_id_foreign` FOREIGN KEY (`product_log_id`) REFERENCES `product_logs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE product_logs;

CREATE TABLE `product_logs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(10) unsigned NOT NULL,
  `amount` double NOT NULL,
  `unit` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit_price` double NOT NULL,
  `expiry_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_logs_product_id_foreign` (`product_id`),
  CONSTRAINT `product_logs_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE product_translations;

CREATE TABLE `product_translations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` int(10) unsigned NOT NULL,
  `locale` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_translations_product_id_locale_unique` (`product_id`,`locale`),
  KEY `product_translations_locale_index` (`locale`),
  CONSTRAINT `product_translations_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE products;

CREATE TABLE `products` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `owner_id` int(10) unsigned NOT NULL,
  `category_id` int(10) unsigned NOT NULL,
  `brand_id` int(10) unsigned DEFAULT NULL,
  `amount` double NOT NULL,
  `unit_price` double NOT NULL,
  `discount` float DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `brand` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `active` tinyint(4) NOT NULL DEFAULT 0,
  `image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `products_owner_id_foreign` (`owner_id`),
  KEY `products_category_id_foreign` (`category_id`),
  KEY `products_brand_id_foreign` (`brand_id`),
  CONSTRAINT `products_brand_id_foreign` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE CASCADE,
  CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `products_owner_id_foreign` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE role_user;

CREATE TABLE `role_user` (
  `role_id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL,
  `user_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`,`user_type`),
  KEY `role_user_role_id_foreign` (`role_id`),
  CONSTRAINT `role_user_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO role_user VALUES("1","1","App\\User");
INSERT INTO role_user VALUES("1","2","App\\User");



DROP TABLE roles;

CREATE TABLE `roles` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `display_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO roles VALUES("1","super","Super","Super","2021-08-28 01:46:23","2021-08-28 01:46:23");



DROP TABLE stagnants;

CREATE TABLE `stagnants` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount` double DEFAULT NULL,
  `price` double DEFAULT NULL,
  `discount` double DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `stagnant_id` int(10) unsigned DEFAULT NULL,
  `status` tinyint(4) DEFAULT NULL,
  `owner_id` int(10) unsigned NOT NULL,
  `active` tinyint(4) NOT NULL DEFAULT 1,
  `image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stagnants_stagnant_id_foreign` (`stagnant_id`),
  KEY `stagnants_owner_id_foreign` (`owner_id`),
  CONSTRAINT `stagnants_owner_id_foreign` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `stagnants_stagnant_id_foreign` FOREIGN KEY (`stagnant_id`) REFERENCES `stagnants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO stagnants VALUES("1","Forrest Glenn","Asperiores dolores f","37","582","46","1991-03-11","","","1","1","","2021-08-29 00:43:16","2021-08-29 00:43:16");
INSERT INTO stagnants VALUES("2","Castor Burgess","Veritatis facere nul","85","295","35","1998-09-26","","","2","1","","2021-10-06 18:42:19","2021-10-06 18:42:19");
INSERT INTO stagnants VALUES("3","Jordan Farmer","Quia id non quibusda","8","382","36","1985-10-05","1","","2","1","","2021-10-06 18:43:08","2021-10-06 18:43:08");
INSERT INTO stagnants VALUES("4","Barclay Ryan","Quam accusantium exp","66","327","59","1994-06-10","1","","2","1","","2021-10-06 18:49:08","2021-10-06 18:49:08");



DROP TABLE store_translations;

CREATE TABLE `store_translations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `store_id` int(10) unsigned NOT NULL,
  `locale` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `about_us` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `privacy_policy` text COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `store_translations_store_id_locale_unique` (`store_id`,`locale`),
  KEY `store_translations_locale_index` (`locale`),
  CONSTRAINT `store_translations_store_id_foreign` FOREIGN KEY (`store_id`) REFERENCES `stores` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE stores;

CREATE TABLE `stores` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `owner_id` int(10) unsigned NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('medical_store','beauty_company') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'medical_store',
  `facebook_link` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `twitter_link` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `instagram_link` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `youtube_link` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `stores_email_unique` (`email`),
  UNIQUE KEY `stores_phone_unique` (`phone`),
  KEY `stores_owner_id_foreign` (`owner_id`),
  CONSTRAINT `stores_owner_id_foreign` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




DROP TABLE subscribers;

CREATE TABLE `subscribers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `subscription_id` int(10) unsigned NOT NULL,
  `subscriber_id` int(10) unsigned NOT NULL,
  `payment_method` enum('cash','visa','vodafone_cash') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cash',
  `status` enum('waiting','accepting','rejecting','finished') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'waiting',
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subscribers_subscription_id_foreign` (`subscription_id`),
  KEY `subscribers_subscriber_id_foreign` (`subscriber_id`),
  CONSTRAINT `subscribers_subscriber_id_foreign` FOREIGN KEY (`subscriber_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `subscribers_subscription_id_foreign` FOREIGN KEY (`subscription_id`) REFERENCES `subscriptions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO subscribers VALUES("1","1","2","cash","accepting","2021-08-28","2021-09-28","2021-08-28 02:07:48","2021-08-28 02:12:54");
INSERT INTO subscribers VALUES("2","1","6","cash","waiting","","","2021-09-17 17:56:11","2021-09-17 17:56:11");



DROP TABLE subscription_translations;

CREATE TABLE `subscription_translations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `subscription_id` int(10) unsigned NOT NULL,
  `locale` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `subscription_translations_subscription_id_locale_unique` (`subscription_id`,`locale`),
  KEY `subscription_translations_locale_index` (`locale`),
  CONSTRAINT `subscription_translations_subscription_id_foreign` FOREIGN KEY (`subscription_id`) REFERENCES `subscriptions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO subscription_translations VALUES("1","1","ar","Plan A","description adfdfdf");
INSERT INTO subscription_translations VALUES("2","1","en","planA","fkdfdlf");



DROP TABLE subscriptions;

CREATE TABLE `subscriptions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `price` decimal(5,2) NOT NULL,
  `duriation` int(11) NOT NULL,
  `type` enum('medical_store','beauty_company','pharmacy') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'medical_store',
  `active` tinyint(4) NOT NULL DEFAULT 1,
  `created_by` int(10) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `subscriptions_created_by_foreign` (`created_by`),
  CONSTRAINT `subscriptions_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO subscriptions VALUES("1","100.00","1","pharmacy","1","1","2021-08-28 02:03:50","2021-08-28 02:03:50");



DROP TABLE users;

CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `active` tinyint(4) NOT NULL DEFAULT 1,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `national_id_image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `license_image` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` enum('super_admin','medical_store','beauty_company','pharmacy') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'medical_store',
  `areas` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `store_id` int(10) unsigned DEFAULT NULL,
  `app_setting_id` int(10) unsigned DEFAULT NULL,
  `fcm_token` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `avatar` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'avatar.png',
  `messenger_color` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '#2180f3',
  `dark_mode` tinyint(1) NOT NULL DEFAULT 0,
  `active_status` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_phone_unique` (`phone`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO users VALUES("1","Super","super@eg.com","","","1","$2y$10$tUshZzRsY3bVDpzG8/MA6Ood1XvVrtsppTOcgu5ClP4cPZYegTEt2","","","","super_admin","","","","","","2021-08-28 01:46:32","2021-09-03 14:08:35","7ecbd1d3-687f-44d0-abdc-1d40db1a5bba.png","#2180f3","0","1");
INSERT INTO users VALUES("2","Emma Buckley","pulove@mailinator.com","0112321346","","1","$2y$10$tUshZzRsY3bVDpzG8/MA6Ood1XvVrtsppTOcgu5ClP4cPZYegTEt2","UjVkbuCal2TVEv4WJVmp2I2LM3SdQ1qYCZMBFIMA.jpg","c8vlf1K0ySe2QHYLLy7zF8twRIKq7q48GatkcuB0.png","s49dWrsN82NCpIQ8mVDAjpb24bKTGHKW048PL37x.png","pharmacy","1","","","","","2021-08-28 02:07:48","2021-08-29 01:36:12","avatar.png","#2180f3","0","1");
INSERT INTO users VALUES("3","ahmed","agmed@eg.com","0121212","","1","$2y$10$tUshZzRsY3bVDpzG8/MA6Ood1XvVrtsppTOcgu5ClP4cPZYegTEt2","","","","medical_store","","","","","","","2021-09-03 14:09:47","avatar.png","#2180f3","0","0");
INSERT INTO users VALUES("5","Mohammed Ali","mohamed@eg.com","012121287","","1","$2y$10$tUshZzRsY3bVDpzG8/MA6Ood1XvVrtsppTOcgu5ClP4cPZYegTEt2","","","","medical_store","","","","","","","","avatar.png","#2180f3","0","0");
INSERT INTO users VALUES("6","Kelly English","lanaholafe@mailinator.com","011231233232","","1","$2y$10$zfn5bhrE2KVptFvQ4zff7.SDfgMOL2NGnewxPd1LoNQLIcvrLCQ/O","vlqJXmfbCSVGYw67d2s3PqyUhEUEHRgKrZto4tYw.jpg","nI2N4BnABeu8biUnXVYky5D2SSjRtn3Qx9mNs0Jo.png","wQmldKu26icexSY1rAfJq9c3BVxe3gYxtPSlF2Ji.png","pharmacy","1","","","","","2021-09-17 17:56:11","2021-09-17 17:56:11","avatar.png","#2180f3","0","0");



