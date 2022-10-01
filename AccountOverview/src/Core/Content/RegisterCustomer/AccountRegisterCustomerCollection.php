<?php declare(strict_types=1);

namespace AccountOverview\Core\Content\RegisterCustomer;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                add(AccountRegisterCustomerEntity $entity)
 * @method void                set(string $key, AccountRegisterCustomerEntity $entity)
 * @method AccountRegisterCustomerEntity[]    getIterator()
 * @method AccountRegisterCustomerEntity[]    getElements()
 * @method AccountRegisterCustomerEntity|null get(string $key)
 * @method AccountRegisterCustomerEntity|null first()
 * @method AccountRegisterCustomerEntity|null last()
 */
class AccountRegisterCustomerCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return AccountRegisterCustomerEntity::class;
    }
}