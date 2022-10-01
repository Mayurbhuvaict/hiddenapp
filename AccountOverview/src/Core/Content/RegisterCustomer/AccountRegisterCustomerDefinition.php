<?php declare(strict_types=1);

namespace AccountOverview\Core\Content\RegisterCustomer;

use Shopware\Core\Checkout\Customer\CustomerDefinition;
use Shopware\Core\Content\Category\CategoryDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ReferenceVersionField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class AccountRegisterCustomerDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'account_register_customer';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return AccountRegisterCustomerEntity::class;
    }

    public function getCollectionClass(): string
    {
        return AccountRegisterCustomerCollection::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new Required(), new PrimaryKey(), new ApiAware()),
            (new FkField('customer_id', 'customerId', CustomerDefinition::class))->addFlags(new Required(),new ApiAware()),
            (new FkField('category_id', 'categoryId', CategoryDefinition::class))->addFlags(new Required(),new ApiAware()),
            (new ReferenceVersionField(CategoryDefinition::class))->addFlags(new Required()),
            new OneToOneAssociationField(
                'customer',
                'customer_id',
                'id',
                CustomerDefinition::class,
                false
            ),
            new OneToOneAssociationField(
                'category',
                'category_id',
                'id',
                CategoryDefinition::class,
                false
            ),

        ]);
    }
}
