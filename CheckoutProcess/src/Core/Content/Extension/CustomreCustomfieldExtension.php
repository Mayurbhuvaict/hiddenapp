<?php declare(strict_types=1);

namespace AccountOverview\Core\Content\Extension;

use AccountOverview\Core\Content\CustomerExtension\CustomerExtensionDefinition;
use Shopware\Core\Checkout\Customer\CustomerDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class CustomreCustomfieldExtension extends EntityExtension
{
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            new OneToOneAssociationField('customerExtension','id','customer_id',CustomerExtensionDefinition::class,false)
        );
    }
    public function getDefinitionClass(): string
    {
        return CustomerDefinition::class;
    }
}
