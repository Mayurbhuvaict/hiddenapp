<?php declare(strict_types=1);

namespace OnboardingPagesOverview\Core\Content\Extension;

use OnboardingPagesOverview\Core\Content\CmsPagesOverview\Aggregate\CmsPagesOverviewTranslation\CmsPagesOverviewTranslationDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\System\Language\LanguageDefinition;

class OverviewLanguageExtension extends EntityExtension
{
    public function extendFields(FieldCollection $collection): void
    {

        $collection->add(
            new OneToManyAssociationField(
                'languageId',
                CmsPagesOverviewTranslationDefinition::class,
                'id',
            )
        );
    }
    public function getDefinitionClass(): string
    {
        return LanguageDefinition::class;
    }
}
