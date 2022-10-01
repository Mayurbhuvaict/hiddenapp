<?php declare(strict_types=1);

namespace AccountOverview\Core\Content\CustomerExtension;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                add(CustomerExtensionEntity $entity)
 * @method void                set(string $key, CustomerExtensionEntity $entity)
 * @method CustomerExtensionEntity[]    getIterator()
 * @method CustomerExtensionEntity[]    getElements()
 * @method CustomerExtensionEntity|null get(string $key)
 * @method CustomerExtensionEntity|null first()
 * @method CustomerExtensionEntity|null last()
 */
class CustomerExtensionCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return CustomerExtensionEntity::class;
    }
}