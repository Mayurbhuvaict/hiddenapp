<?php declare(strict_types=1);

namespace OnboardingPagesOverview\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1663839307 extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1663839307;
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement("CREATE TABLE IF NOT EXISTS `cms_pages_overview` (
    `id` BINARY(16) NOT NULL,
    `slug` VARCHAR(255) NOT NULL,
    `created_at` DATETIME(3) NOT NULL,
    `updated_at` DATETIME(3) NULL,
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

        $connection->executeStatement("CREATE TABLE IF NOT EXISTS `cms_pages_overview_translation` (
                `id` BINARY(16) NOT NULL,
            `name` VARCHAR(255) NOT NULL,
            `created_at` DATETIME(3) NOT NULL,
            `updated_at` DATETIME(3) NULL,
            `cms_pages_overview_id` BINARY(16) NOT NULL,
            `language_id` BINARY(16) NOT NULL,
            PRIMARY KEY (`id`,`cms_pages_overview_id`,`language_id`),
            KEY `fk.cms_pages_overview_translation.cms_pages_overview_id` (`cms_pages_overview_id`),
            KEY `fk.cms_pages_overview_translation.language_id` (`language_id`),
            CONSTRAINT `fk.cms_pages_overview_translation.cms_pages_overview_id` FOREIGN KEY (`cms_pages_overview_id`) REFERENCES `cms_pages_overview` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
            CONSTRAINT `fk.cms_pages_overview_translation.language_id` FOREIGN KEY (`language_id`) REFERENCES `language` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");
        $connection->executeStatement("CREATE TABLE IF NOT EXISTS `cms_pages_detail` (
            `id` BINARY(16) NOT NULL,
            `page_id` BINARY(16) NOT NULL,
            `media_id` BINARY(16) NULL,
            `created_at` DATETIME(3) NOT NULL,
            `updated_at` DATETIME(3) NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
        $connection->executeStatement("CREATE TABLE IF NOT EXISTS `cms_pages_detail_translation` (
            `title` VARCHAR(255) NOT NULL,
            `description` VARCHAR(255) NULL,
            `created_at` DATETIME(3) NOT NULL,
            `updated_at` DATETIME(3) NULL,
            `cms_pages_detail_id` BINARY(16) NOT NULL,
            `language_id` BINARY(16) NOT NULL,
            PRIMARY KEY (`cms_pages_detail_id`,`language_id`),
            KEY `fk.cms_pages_detail_translation.cms_pages_detail_id` (`cms_pages_detail_id`),
            KEY `fk.cms_pages_detail_translation.language_id` (`language_id`),
            CONSTRAINT `fk.cms_pages_detail_translation.cms_pages_detail_id` FOREIGN KEY (`cms_pages_detail_id`) REFERENCES `cms_pages_detail` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
            CONSTRAINT `fk.cms_pages_detail_translation.language_id` FOREIGN KEY (`language_id`) REFERENCES `language` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
