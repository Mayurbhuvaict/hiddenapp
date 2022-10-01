<?php declare(strict_types=1);

namespace AccountOverview\Core\Content\RegisterVerification;

use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IntField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class AccountRegisterVerificationDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'account_register_verification';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return AccountRegisterVerificationEntity::class;
    }

    public function getCollectionClass(): string
    {
        return AccountRegisterVerificationCollection::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new Required(), new PrimaryKey(), new ApiAware()),
            (new StringField('email', 'email'))->addFlags(new Required(), new ApiAware()),
            (new StringField('password', 'password'))->addFlags(new Required(), new ApiAware()),
            (new StringField('confirm_password', 'confirmPassword'))->addFlags(new Required(), new ApiAware()),
            (new IntField('otp', 'otp'))->addFlags(new ApiAware())
        ]);
    }
}
