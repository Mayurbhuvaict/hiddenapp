<?php declare(strict_types=1);

namespace AccountOverview\Core\Content\CustomerExtension;

use Shopware\Core\Checkout\Customer\CustomerDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class CustomerExtensionDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'customer_extension';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return CustomerExtensionEntity::class;
    }

    public function getCollectionClass(): string
    {
        return CustomerExtensionCollection::class;
    }

    public function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id','id'))->addFlags(new ApiAware(),new Required(),new PrimaryKey()),
            (new StringField('address','address'))->addFlags(new ApiAware()),
            (new StringField('employee_code','employeeCode'))->addFlags(new ApiAware()),
            (new StringField('category_id','categoryId'))->addFlags(new ApiAware()),
            (new StringField('mobile_number','mobileNumber'))->addFlags(new ApiAware()),
            (new FkField('customer_id', 'customerId', CustomerDefinition::class))->addFlags(new ApiAware()),
            new OneToOneAssociationField(
                'customer',
                'customer_id',
                'id',
                CustomerDefinition::class,
                false
            )
        ]);
    }
}
