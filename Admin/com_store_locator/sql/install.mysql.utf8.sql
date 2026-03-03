CREATE TABLE IF NOT EXISTS `#__store_locator_locations` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`state` TINYINT(1)  NULL  DEFAULT 1,
`ordering` INT(11)  NULL  DEFAULT 0,
`checked_out` INT(11)  UNSIGNED,
`checked_out_time` DATETIME NULL  DEFAULT NULL ,
`created_by` INT(11)  NULL  DEFAULT 0,
`modified_by` INT(11)  NULL  DEFAULT 0,
`locationlistingtitle` VARCHAR(255)  NOT NULL ,
`user` INT(11)  NULL  DEFAULT 0,
`catid` INT(11)  NULL  DEFAULT 0,
`email` VARCHAR(255)  NULL  DEFAULT "",
`website` VARCHAR(255)  NULL  DEFAULT "",
`phone` VARCHAR(255)  NULL  DEFAULT "",
`image` TEXT NULL ,
`opening_times` TEXT NULL ,
`street` VARCHAR(100) DEFAULT NULL,
`city` VARCHAR(50) DEFAULT NULL,
`user_state` VARCHAR(50) DEFAULT NULL,
`zip_code` VARCHAR(20) DEFAULT NULL,
`country` VARCHAR(50) DEFAULT NULL,
`latitude` DECIMAL(10, 8) NULL DEFAULT NULL,
`longitude` DECIMAL(11, 8) NULL DEFAULT NULL,
PRIMARY KEY (`id`)
,KEY `idx_state` (`state`)
,KEY `idx_checked_out` (`checked_out`)
,KEY `idx_created_by` (`created_by`)
,KEY `idx_modified_by` (`modified_by`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__store_locator_maps` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`state` TINYINT(1)  NULL  DEFAULT 1,
`ordering` INT(11)  NULL  DEFAULT 0,
`checked_out` INT(11)  UNSIGNED,
`checked_out_time` DATETIME NULL  DEFAULT NULL ,
`created_by` INT(11)  NULL  DEFAULT 0,
`modified_by` INT(11)  NULL  DEFAULT 0,
`maptitle` VARCHAR(255)  NOT NULL ,
`ip` VARCHAR(255)  NULL  DEFAULT "",
`radius_search` VARCHAR(255)  NULL  DEFAULT "",
`max_results` VARCHAR(255)  NULL  DEFAULT "",
`unit` TEXT NULL ,
`map_width` TEXT NULL ,
`results_width` TEXT NULL ,
`location_order` INT(11)  NULL  DEFAULT 0 ,
`map_order` INT(11)  NULL  DEFAULT 0 ,
`map_theme` INT(11)  NULL  DEFAULT 0 ,
`map_skin` INT(11)  NULL  DEFAULT 0 ,
`filter_position` INT(11)  NULL  DEFAULT 0 ,
`filter` INT(11)  NULL  DEFAULT 0 ,
`location_result` INT(11)  NULL  DEFAULT 0 ,
`location_card` INT(11)  NULL  DEFAULT 0 ,
`location_details` INT(11)  NULL  DEFAULT 0 ,
PRIMARY KEY (`id`)
,KEY `idx_state` (`state`)
,KEY `idx_checked_out` (`checked_out`)
,KEY `idx_created_by` (`created_by`)
,KEY `idx_modified_by` (`modified_by`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__store_locator_map_themes` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`state` TINYINT(1)  NULL  DEFAULT 1,
`ordering` INT(11)  NULL  DEFAULT 0,
`checked_out` INT(11)  UNSIGNED,
`checked_out_time` DATETIME NULL  DEFAULT NULL ,
`created_by` INT(11)  NULL  DEFAULT 0,
`modified_by` INT(11)  NULL  DEFAULT 0,
`theme_title` VARCHAR(255)  NOT NULL ,
`description` VARCHAR(255)  NULL  DEFAULT "",
`theme_icon` VARCHAR(255)  NULL  DEFAULT "",
`toolbar_bg` VARCHAR(9)  NULL  DEFAULT "",
`results_bg` VARCHAR(9)  NULL  DEFAULT "",
`button_color` VARCHAR(9)  NULL  DEFAULT "",
`link_color` VARCHAR(9)  NULL  DEFAULT "",
PRIMARY KEY (`id`)
,KEY `idx_state` (`state`)
,KEY `idx_checked_out` (`checked_out`)
,KEY `idx_created_by` (`created_by`)
,KEY `idx_modified_by` (`modified_by`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__store_locator_map_skins` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`state` TINYINT(1)  NULL  DEFAULT 1,
`ordering` INT(11)  NULL  DEFAULT 0,
`checked_out` INT(11)  UNSIGNED,
`checked_out_time` DATETIME NULL  DEFAULT NULL ,
`created_by` INT(11)  NULL  DEFAULT 0,
`modified_by` INT(11)  NULL  DEFAULT 0,
`skin_title` VARCHAR(255)  NOT NULL ,
`description` VARCHAR(255)  NULL  DEFAULT "",
`skin_data` text NULL,
`skin_icon` VARCHAR(255)  NULL  DEFAULT "",
PRIMARY KEY (`id`)
,KEY `idx_state` (`state`)
,KEY `idx_checked_out` (`checked_out`)
,KEY `idx_created_by` (`created_by`)
,KEY `idx_modified_by` (`modified_by`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `#__store_locator_frontend_filter` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`state` TINYINT(1)  NULL  DEFAULT 1,
`ordering` INT(11)  NULL  DEFAULT 0,
`checked_out` INT(11)  UNSIGNED,
`checked_out_time` DATETIME NULL  DEFAULT NULL ,
`created_by` INT(11)  NULL  DEFAULT 0,
`modified_by` INT(11)  NULL  DEFAULT 0,
`filter_title` VARCHAR(255)  NOT NULL ,
`description` VARCHAR(255)  NULL  DEFAULT "",
`filter_position` INT(11)  NULL  DEFAULT 0,
`show_text_search` INT(11)  NULL  DEFAULT 0,
`filter_data` text NOT NULL,
PRIMARY KEY (`id`)
,KEY `idx_state` (`state`)
,KEY `idx_checked_out` (`checked_out`)
,KEY `idx_created_by` (`created_by`)
,KEY `idx_modified_by` (`modified_by`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__store_locator_card_template` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`state` TINYINT(1)  NULL  DEFAULT 1,
`ordering` INT(11)  NULL  DEFAULT 0,
`checked_out` INT(11)  UNSIGNED,
`checked_out_time` DATETIME NULL  DEFAULT NULL ,
`created_by` INT(11)  NULL  DEFAULT 0,
`modified_by` INT(11)  NULL  DEFAULT 0,
`template_title` VARCHAR(255)  NOT NULL ,
`description` VARCHAR(255)  NULL  DEFAULT "",
`open_position` INT(11)  NULL  DEFAULT 0,
`close_position` INT(11)  NULL  DEFAULT 0,
`template` text NOT NULL,
PRIMARY KEY (`id`)
,KEY `idx_state` (`state`)
,KEY `idx_checked_out` (`checked_out`)
,KEY `idx_created_by` (`created_by`)
,KEY `idx_modified_by` (`modified_by`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__store_locator_location_results` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`state` TINYINT(1)  NULL  DEFAULT 1,
`ordering` INT(11)  NULL  DEFAULT 0,
`checked_out` INT(11)  UNSIGNED,
`checked_out_time` DATETIME NULL  DEFAULT NULL ,
`created_by` INT(11)  NULL  DEFAULT 0,
`modified_by` INT(11)  NULL  DEFAULT 0,
`template_title` VARCHAR(255)  NOT NULL ,
`description` VARCHAR(255)  NULL  DEFAULT "",
`css_class` VARCHAR(255)  NOT NULL ,
`template` text NOT NULL,
PRIMARY KEY (`id`)
,KEY `idx_state` (`state`)
,KEY `idx_checked_out` (`checked_out`)
,KEY `idx_created_by` (`created_by`)
,KEY `idx_modified_by` (`modified_by`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `#__store_locator_location_details` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
`state` TINYINT(1)  NULL  DEFAULT 1,
`ordering` INT(11)  NULL  DEFAULT 0,
`checked_out` INT(11)  UNSIGNED,
`checked_out_time` DATETIME NULL  DEFAULT NULL ,
`created_by` INT(11)  NULL  DEFAULT 0,
`modified_by` INT(11)  NULL  DEFAULT 0,
`template_title` VARCHAR(255)  NOT NULL ,
`description` VARCHAR(255)  NULL  DEFAULT "",
`css_class` VARCHAR(255)  NOT NULL ,
`template` text NOT NULL,
PRIMARY KEY (`id`)
,KEY `idx_state` (`state`)
,KEY `idx_checked_out` (`checked_out`)
,KEY `idx_created_by` (`created_by`)
,KEY `idx_modified_by` (`modified_by`)
) DEFAULT COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `#__store_locator_fields` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `state` TINYINT(1)  NULL  DEFAULT 1,
    `ordering` INT(11)  NULL  DEFAULT 0,
    `checked_out` INT(11)  UNSIGNED,
    `checked_out_time` DATETIME NULL  DEFAULT NULL ,
    `created_by` INT(11)  NULL  DEFAULT 0,
    `modified_by` INT(11)  NULL  DEFAULT 0,
    `title` varchar(255) NOT NULL,
	`description` VARCHAR(255)  NULL  DEFAULT "",
    `field_group` int(11) NOT NULL,
    `type` varchar(50) NOT NULL,
    `params` text,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `#__store_locator_field_groups` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `state` TINYINT(1)  NULL  DEFAULT 1,
    `ordering` INT(11)  NULL  DEFAULT 0,
    `checked_out` INT(11)  UNSIGNED,
    `checked_out_time` DATETIME NULL  DEFAULT NULL ,
    `created_by` INT(11)  NULL  DEFAULT 0,
    `modified_by` INT(11)  NULL  DEFAULT 0,
    `title` varchar(255) NOT NULL,
    `description` text,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;


CREATE TABLE IF NOT EXISTS `#__store_locator_custom_data` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `state` TINYINT(1)  NULL  DEFAULT 1,
    `ordering` INT(11)  NULL  DEFAULT 0,
    `checked_out` INT(11)  UNSIGNED,
    `checked_out_time` DATETIME NULL  DEFAULT NULL ,
    `created_by` INT(11)  NULL  DEFAULT 0,
    `modified_by` INT(11)  NULL  DEFAULT 0,
    `location_id`  INT(11)  NULL  DEFAULT 0,
    `field_id`  INT(11)  NULL  DEFAULT 0,
    `field_value` text,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 DEFAULT COLLATE=utf8mb4_unicode_ci;