<?php declare(strict_types=1);

namespace AccountOverview\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\InheritanceUpdaterTrait;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1663927497 extends MigrationStep
{
    use InheritanceUpdaterTrait;
    public function getCreationTimestamp(): int
    {
        return 1663927497;
    }

    public function update(Connection $connection): void
    {
       $connection->executeStatement("CREATE TABLE IF NOT EXISTS `account_register_verification` (
            `id` BINARY(16) NOT NULL,
            `email` VARCHAR(255) NOT NULL,
            `password` VARCHAR(255) NOT NULL,
            `confirm_password` VARCHAR(255) NOT NULL,
            `otp` INT(11) NULL,
            `created_at` DATETIME(3) NOT NULL,
            `updated_at` DATETIME(3) NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
       $connection->executeStatement("CREATE TABLE IF NOT EXISTS `account_register_customer` (
            `id` BINARY(16) NOT NULL,
            `customer_id` BINARY(16) NOT NULL,
            `category_id` BINARY(16) NOT NULL,
            `category_version_id` BINARY(16) NOT NULL,
            `created_at` DATETIME(3) NOT NULL,
            `updated_at` DATETIME(3) NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");



    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
