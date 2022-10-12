<?php declare(strict_types=1);

namespace OnboardingPagesOverview\Core\Content\CmsPagesOverview;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                add(CmsPagesOverviewEntity $entity)
 * @method void                set(string $key, CmsPagesOverviewEntity $entity)
 * @method CmsPagesOverviewEntity[]    getIterator()
 * @method CmsPagesOverviewEntity[]    getElements()
 * @method CmsPagesOverviewEntity|null get(string $key)
 * @method CmsPagesOverviewEntity|null first()
 * @method CmsPagesOverviewEntity|null last()
 */
class CmsPagesOverviewCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return CmsPagesOverviewEntity::class;
    }
}