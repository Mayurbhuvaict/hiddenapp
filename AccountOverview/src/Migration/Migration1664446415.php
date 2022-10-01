<?php declare(strict_types=1);

namespace AccountOverview\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1664446415 extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1664446415;
    }

    public function update(Connection $connection): void
    {
       $connection->executeStatement('CREATE TABLE `customer_extension` (
            `id` BINARY(16) NOT NULL,
            `address` VARCHAR(255) NULL,
            `employee_code` VARCHAR(255) NULL,
            `category_id` VARCHAR(255) NULL,
            `mobile_number` VARCHAR(255) NULL,
            `customer_id` BINARY(16) NULL,
            `created_at` DATETIME(3) NOT NULL,
            `updated_at` DATETIME(3) NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;');
    }

    public function updateDestructive(Connection $connection): void
    {
        // implement update destructive
    }
}
