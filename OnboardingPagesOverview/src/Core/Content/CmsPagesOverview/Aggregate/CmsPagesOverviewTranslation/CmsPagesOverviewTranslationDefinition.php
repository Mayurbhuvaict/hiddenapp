<?php declare(strict_types=1);

namespace OnboardingPagesOverview\Core\Content\CmsPagesOverview\Aggregate\CmsPagesOverviewTranslation;

use OnboardingPagesOverview\Core\Content\CmsPagesOverview\CmsPagesOverviewDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\EntityTranslationDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class CmsPagesOverviewTranslationDefinition extends EntityTranslationDefinition
{
    public const ENTITY_NAME = 'cms_pages_overview_translation';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return CmsPagesOverviewTranslationEntity::class;
    }
    public function getCollectionClass(): string
    {
        return CmsPagesOverviewTranslationCollection::class;
    }

    public function since(): ?string
    {
        return '6.0.0.0';
    }

    protected function getParentDefinitionClass(): string
    {
        return CmsPagesOverviewDefinition::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id','id'))->addFlags(new Required(),new PrimaryKey(),new ApiAware()),
            (new StringField('name', 'name'))->addFlags(new Required(),new ApiAware()),
        ]);
    }
}
