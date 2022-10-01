<?php declare(strict_types=1);

namespace AccountOverview\Core\Content\RegisterVerification;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                add(AccountRegisterVerificationEntity $entity)
 * @method void                set(string $key, AccountRegisterVerificationEntity $entity)
 * @method AccountRegisterVerificationEntity[]    getIterator()
 * @method AccountRegisterVerificationEntity[]    getElements()
 * @method AccountRegisterVerificationEntity|null get(string $key)
 * @method AccountRegisterVerificationEntity|null first()
 * @method AccountRegisterVerificationEntity|null last()
 */
class AccountRegisterVerificationCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return AccountRegisterVerificationEntity::class;
    }
}