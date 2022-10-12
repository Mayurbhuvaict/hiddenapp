<?php declare(strict_types=1);

namespace OnboardingPagesOverview\Core\Content\Extension;

use OnboardingPagesOverview\Core\Content\CmsPagesDetail\CmsPagesDetailDefinition;
use OnboardingPagesOverview\Core\Content\CmsPagesOverview\CmsPagesOverviewDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;


class PageExtension extends EntityExtension
{
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            new OneToOneAssociationField(
                'page',
                'id',
                'page_id',
                CmsPagesDetailDefinition::class,
                false

            )
        );
    }
    public function getDefinitionClass(): string
    {
        return CmsPagesOverviewDefinition::class;
    }
}
