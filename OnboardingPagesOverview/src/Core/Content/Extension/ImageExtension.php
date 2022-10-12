<?php declare(strict_types=1);

namespace OnboardingPagesOverview\Core\Content\Extension;

use OnboardingPagesOverview\Core\Content\CmsPagesDetail\CmsPagesDetailDefinition;
use Shopware\Core\Content\Media\MediaDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;


class ImageExtension extends EntityExtension
{
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            new OneToOneAssociationField(
                'media',
                'id',
                'media_id',
                CmsPagesDetailDefinition::class,
                false

            )
        );
    }
    public function getDefinitionClass(): string
    {
        return MediaDefinition::class;
    }
}
