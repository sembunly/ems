CREATE TABLE IF NOT EXISTS `user_groups` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(150) NOT NULL,
    `ordering` INT NOT NULL DEFAULT 0,
    `status` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `user_groups_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `user_group_members` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_group_id` BIGINT UNSIGNED NOT NULL,
    `user_id` BIGINT UNSIGNED NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `user_group_members_group_user_unique` (`user_group_id`, `user_id`),
    KEY `user_group_members_user_id_index` (`user_id`),
    CONSTRAINT `user_group_members_group_id_foreign`
        FOREIGN KEY (`user_group_id`) REFERENCES `user_groups` (`id`)
        ON DELETE CASCADE,
    CONSTRAINT `user_group_members_user_id_foreign`
        FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `module_types` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(150) NOT NULL,
    `ordering` INT NOT NULL DEFAULT 0,
    `status` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `module_types_name_unique` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `modules` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `module_type_id` BIGINT UNSIGNED NOT NULL,
    `name` VARCHAR(150) NOT NULL,
    `ordering` INT NOT NULL DEFAULT 0,
    `status` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `modules_type_name_unique` (`module_type_id`, `name`),
    KEY `modules_module_type_id_index` (`module_type_id`),
    CONSTRAINT `modules_module_type_id_foreign`
        FOREIGN KEY (`module_type_id`) REFERENCES `module_types` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `module_details` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `module_id` BIGINT UNSIGNED NOT NULL,
    `controllers` VARCHAR(150) NOT NULL,
    `views` VARCHAR(150) NOT NULL,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `module_details_module_controller_view_unique` (`module_id`, `controllers`, `views`),
    KEY `module_details_module_id_index` (`module_id`),
    CONSTRAINT `module_details_module_id_foreign`
        FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `group_permissions` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_group_id` BIGINT UNSIGNED NOT NULL,
    `module_id` BIGINT UNSIGNED NOT NULL,
    `is_allowed` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NULL DEFAULT NULL,
    `updated_at` TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `group_permissions_group_module_unique` (`user_group_id`, `module_id`),
    KEY `group_permissions_module_id_index` (`module_id`),
    CONSTRAINT `group_permissions_group_id_foreign`
        FOREIGN KEY (`user_group_id`) REFERENCES `user_groups` (`id`)
        ON DELETE CASCADE,
    CONSTRAINT `group_permissions_module_id_foreign`
        FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `user_groups` (`name`, `ordering`, `status`, `created_at`, `updated_at`)
VALUES
('Admin', 1, 1, NOW(), NOW());

INSERT IGNORE INTO `module_types` (`name`, `ordering`, `status`, `created_at`, `updated_at`)
VALUES
('Dashboard', 1, 1, NOW(), NOW()),
('Products', 2, 1, NOW(), NOW()),
('Categories', 3, 1, NOW(), NOW()),
('Orders', 4, 1, NOW(), NOW()),
('Report', 5, 1, NOW(), NOW()),
('Users', 6, 1, NOW(), NOW());

INSERT IGNORE INTO `modules` (`module_type_id`, `name`, `ordering`, `status`, `created_at`, `updated_at`)
VALUES
((SELECT `id` FROM `module_types` WHERE `name` = 'Dashboard' LIMIT 1), 'Dashboard (View)', 1, 1, NOW(), NOW()),
((SELECT `id` FROM `module_types` WHERE `name` = 'Products' LIMIT 1), 'Products (View)', 1, 1, NOW(), NOW()),
((SELECT `id` FROM `module_types` WHERE `name` = 'Products' LIMIT 1), 'Products (Add)', 2, 1, NOW(), NOW()),
((SELECT `id` FROM `module_types` WHERE `name` = 'Products' LIMIT 1), 'Products (Edit)', 3, 1, NOW(), NOW()),
((SELECT `id` FROM `module_types` WHERE `name` = 'Products' LIMIT 1), 'Products (Delete)', 4, 1, NOW(), NOW()),
((SELECT `id` FROM `module_types` WHERE `name` = 'Categories' LIMIT 1), 'Categories (View)', 1, 1, NOW(), NOW()),
((SELECT `id` FROM `module_types` WHERE `name` = 'Categories' LIMIT 1), 'Categories (Add)', 2, 1, NOW(), NOW()),
((SELECT `id` FROM `module_types` WHERE `name` = 'Categories' LIMIT 1), 'Categories (Edit)', 3, 1, NOW(), NOW()),
((SELECT `id` FROM `module_types` WHERE `name` = 'Categories' LIMIT 1), 'Categories (Delete)', 4, 1, NOW(), NOW()),
((SELECT `id` FROM `module_types` WHERE `name` = 'Orders' LIMIT 1), 'Orders (View)', 1, 1, NOW(), NOW()),
((SELECT `id` FROM `module_types` WHERE `name` = 'Orders' LIMIT 1), 'Orders (Edit)', 2, 1, NOW(), NOW()),
((SELECT `id` FROM `module_types` WHERE `name` = 'Orders' LIMIT 1), 'Orders (Delete)', 3, 1, NOW(), NOW()),
((SELECT `id` FROM `module_types` WHERE `name` = 'Orders' LIMIT 1), 'Orders (Export)', 4, 1, NOW(), NOW()),
((SELECT `id` FROM `module_types` WHERE `name` = 'Report' LIMIT 1), 'Sale Report (View)', 1, 1, NOW(), NOW()),
((SELECT `id` FROM `module_types` WHERE `name` = 'Report' LIMIT 1), 'Sale Report (Export)', 2, 1, NOW(), NOW()),
((SELECT `id` FROM `module_types` WHERE `name` = 'Users' LIMIT 1), 'Users (View)', 1, 1, NOW(), NOW()),
((SELECT `id` FROM `module_types` WHERE `name` = 'Users' LIMIT 1), 'Users (Add)', 2, 1, NOW(), NOW()),
((SELECT `id` FROM `module_types` WHERE `name` = 'Users' LIMIT 1), 'Users (Edit)', 3, 1, NOW(), NOW()),
((SELECT `id` FROM `module_types` WHERE `name` = 'Users' LIMIT 1), 'Users (Delete)', 4, 1, NOW(), NOW()),
((SELECT `id` FROM `module_types` WHERE `name` = 'Users' LIMIT 1), 'Users (Permission)', 5, 1, NOW(), NOW());

INSERT IGNORE INTO `module_details` (`module_id`, `controllers`, `views`, `created_at`, `updated_at`)
VALUES
((SELECT `id` FROM `modules` WHERE `name` = 'Dashboard (View)' LIMIT 1), 'dashboard', 'index', NOW(), NOW()),
((SELECT `id` FROM `modules` WHERE `name` = 'Products (View)' LIMIT 1), 'products', 'index', NOW(), NOW()),
((SELECT `id` FROM `modules` WHERE `name` = 'Products (View)' LIMIT 1), 'products', 'show', NOW(), NOW()),
((SELECT `id` FROM `modules` WHERE `name` = 'Products (Add)' LIMIT 1), 'products', 'create', NOW(), NOW()),
((SELECT `id` FROM `modules` WHERE `name` = 'Products (Add)' LIMIT 1), 'products', 'store', NOW(), NOW()),
((SELECT `id` FROM `modules` WHERE `name` = 'Products (Edit)' LIMIT 1), 'products', 'edit', NOW(), NOW()),
((SELECT `id` FROM `modules` WHERE `name` = 'Products (Edit)' LIMIT 1), 'products', 'update', NOW(), NOW()),
((SELECT `id` FROM `modules` WHERE `name` = 'Products (Delete)' LIMIT 1), 'products', 'destroy', NOW(), NOW()),
((SELECT `id` FROM `modules` WHERE `name` = 'Categories (View)' LIMIT 1), 'categories', 'index', NOW(), NOW()),
((SELECT `id` FROM `modules` WHERE `name` = 'Categories (Add)' LIMIT 1), 'categories', 'create', NOW(), NOW()),
((SELECT `id` FROM `modules` WHERE `name` = 'Categories (Add)' LIMIT 1), 'categories', 'store', NOW(), NOW()),
((SELECT `id` FROM `modules` WHERE `name` = 'Categories (Edit)' LIMIT 1), 'categories', 'edit', NOW(), NOW()),
((SELECT `id` FROM `modules` WHERE `name` = 'Categories (Edit)' LIMIT 1), 'categories', 'update', NOW(), NOW()),
((SELECT `id` FROM `modules` WHERE `name` = 'Categories (Delete)' LIMIT 1), 'categories', 'destroy', NOW(), NOW()),
((SELECT `id` FROM `modules` WHERE `name` = 'Orders (View)' LIMIT 1), 'orders', 'index', NOW(), NOW()),
((SELECT `id` FROM `modules` WHERE `name` = 'Orders (View)' LIMIT 1), 'orders', 'show', NOW(), NOW()),
((SELECT `id` FROM `modules` WHERE `name` = 'Orders (Edit)' LIMIT 1), 'orders', 'edit', NOW(), NOW()),
((SELECT `id` FROM `modules` WHERE `name` = 'Orders (Edit)' LIMIT 1), 'orders', 'update', NOW(), NOW()),
((SELECT `id` FROM `modules` WHERE `name` = 'Orders (Delete)' LIMIT 1), 'orders', 'destroy', NOW(), NOW()),
((SELECT `id` FROM `modules` WHERE `name` = 'Orders (Export)' LIMIT 1), 'orders', 'export', NOW(), NOW()),
((SELECT `id` FROM `modules` WHERE `name` = 'Sale Report (View)' LIMIT 1), 'reports', 'index', NOW(), NOW()),
((SELECT `id` FROM `modules` WHERE `name` = 'Sale Report (Export)' LIMIT 1), 'reports', 'export', NOW(), NOW()),
((SELECT `id` FROM `modules` WHERE `name` = 'Users (View)' LIMIT 1), 'users', 'index', NOW(), NOW()),
((SELECT `id` FROM `modules` WHERE `name` = 'Users (Add)' LIMIT 1), 'users', 'create', NOW(), NOW()),
((SELECT `id` FROM `modules` WHERE `name` = 'Users (Add)' LIMIT 1), 'users', 'store', NOW(), NOW()),
((SELECT `id` FROM `modules` WHERE `name` = 'Users (Edit)' LIMIT 1), 'users', 'edit', NOW(), NOW()),
((SELECT `id` FROM `modules` WHERE `name` = 'Users (Edit)' LIMIT 1), 'users', 'update', NOW(), NOW()),
((SELECT `id` FROM `modules` WHERE `name` = 'Users (Delete)' LIMIT 1), 'users', 'destroy', NOW(), NOW()),
((SELECT `id` FROM `modules` WHERE `name` = 'Users (Permission)' LIMIT 1), 'permissions', 'index', NOW(), NOW()),
((SELECT `id` FROM `modules` WHERE `name` = 'Users (Permission)' LIMIT 1), 'permissions', 'store', NOW(), NOW());
