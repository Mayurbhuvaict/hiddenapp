<?php declare(strict_types=1);

namespace OnboardingPagesOverview\Core\Content\CmsPagesOverview;


use OnboardingPagesOverview\Core\Content\CmsPagesOverview\Aggregate\CmsPagesOverviewTranslation\CmsPagesOverviewTranslationDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslatedField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslationsAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class CmsPagesOverviewDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'cms_pages_overview';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return CmsPagesOverviewEntity::class;
    }

    public function getCollectionClass(): string
    {
        return CmsPagesOverviewCollection::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new Required(), new PrimaryKey(), new ApiAware()),
            (new TranslatedField('name')),
            (new StringField('slug',"slug"))->addFlags(new Required(), new ApiAware()),

            (new TranslationsAssociationField(CmsPagesOverviewTranslationDefinition::class,'cms_pages_overview_id'))->addFlags(new Required()),
        ]);
    }
}
