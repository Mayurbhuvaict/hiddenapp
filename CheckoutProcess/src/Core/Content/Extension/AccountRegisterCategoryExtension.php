<?php declare(strict_types=1);

namespace AccountOverview\Core\Content\Extension;


use AccountOverview\Core\Content\RegisterCustomer\AccountRegisterCustomerDefinition;
use Shopware\Core\Content\Category\CategoryDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class AccountRegisterCategoryExtension extends EntityExtension
{
    /**
     * @inheritDoc
     */
    public function getDefinitionClass(): string
    {
        return CategoryDefinition::class;
    }

    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            new OneToOneAssociationField(
                'categoryIds',
                'id',
                'category_id',
                AccountRegisterCustomerDefinition::class,
                false
            )
        );
    }
}
