<?php declare(strict_types=1);

namespace OnboardingPagesOverview\Core\Content\CmsPagesOverview\Aggregate\CmsPagesOverviewTranslation;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                add(CmsPagesOverviewTranslationEntity $entity)
 * @method void                set(string $key, CmsPagesOverviewTranslationEntity $entity)
 * @method CmsPagesOverviewTranslationEntity[]    getIterator()
 * @method CmsPagesOverviewTranslationEntity[]    getElements()
 * @method CmsPagesOverviewTranslationEntity|null get(string $key)
 * @method CmsPagesOverviewTranslationEntity|null first()
 * @method CmsPagesOverviewTranslationEntity|null last()
 */
class CmsPagesOverviewTranslationCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return CmsPagesOverviewTranslationEntity::class;
    }
}