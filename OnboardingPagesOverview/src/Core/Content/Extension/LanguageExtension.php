<?php declare(strict_types=1);

namespace OnboardingPagesOverview\Core\Content\Extension;

use OnboardingPagesOverview\Core\Content\CmsPagesDetail\Aggregate\CmsPagesDetailTranslation\CmsPagesDetailTranslationDefinition;
use OnboardingPagesOverview\Core\Content\CmsPagesOverview\Aggregate\CmsPagesOverviewTranslation\CmsPagesOverviewTranslationDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityExtension;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToManyAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\System\Language\LanguageDefinition;

class LanguageExtension extends EntityExtension
{
    public function extendFields(FieldCollection $collection): void
    {
        $collection->add(
            new OneToManyAssociationField(
                'languageDetailId',
                CmsPagesDetailTranslationDefinition::class,
                'cms_pages_detail_id',
            )
        );
        $collection->add(
            new OneToManyAssociationField(
                'languageOverviewId',
                CmsPagesOverviewTranslationDefinition::class,
                'cms_pages_overview_id',
            )
        );
    }
    public function getDefinitionClass(): string
    {
        return LanguageDefinition::class;
    }
}
