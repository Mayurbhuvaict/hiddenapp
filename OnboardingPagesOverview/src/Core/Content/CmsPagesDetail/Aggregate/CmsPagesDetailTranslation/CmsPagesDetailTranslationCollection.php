<?php declare(strict_types=1);

namespace OnboardingPagesOverview\Core\Content\CmsPagesDetail\Aggregate\CmsPagesDetailTranslation;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                add(CmsPagesDetailTranslationEntity $entity)
 * @method void                set(string $key, CmsPagesDetailTranslationEntity $entity)
 * @method CmsPagesDetailTranslationEntity[]    getIterator()
 * @method CmsPagesDetailTranslationEntity[]    getElements()
 * @method CmsPagesDetailTranslationEntity|null get(string $key)
 * @method CmsPagesDetailTranslationEntity|null first()
 * @method CmsPagesDetailTranslationEntity|null last()
 */
class CmsPagesDetailTranslationCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return CmsPagesDetailTranslationEntity::class;
    }
}