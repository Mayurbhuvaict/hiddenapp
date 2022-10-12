<?php declare(strict_types=1);

namespace OnboardingPagesOverview\Core\Content\CmsPagesDetail;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                add(CmsPagesDetailEntity $entity)
 * @method void                set(string $key, CmsPagesDetailEntity $entity)
 * @method CmsPagesDetailEntity[]    getIterator()
 * @method CmsPagesDetailEntity[]    getElements()
 * @method CmsPagesDetailEntity|null get(string $key)
 * @method CmsPagesDetailEntity|null first()
 * @method CmsPagesDetailEntity|null last()
 */
class CmsPagesDetailCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return CmsPagesDetailEntity::class;
    }
}