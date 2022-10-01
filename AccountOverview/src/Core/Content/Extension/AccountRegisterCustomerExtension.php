<?php declare(strict_types=1);

namespace AccountOverview\Core\Content\Extension;


use AccountOverview\Core\Content\RegisterCustomer\AccountRegisterCustomerDefinition;
use Shopware\Core\Checkout\Customer\CustomerDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class AccountRegisterCustomerExtension extends EntityExtension
{
    /**
     * @inheritDoc
     */
    public function getDefinitionClass(): string
    {
        return CustomerDefinition::class;
    }

    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            new OneToOneAssociationField(
                'customerIds',
                'id',
                'customer_id',
                AccountRegisterCustomerDefinition::class,
                false
            )
        );
    }
}
