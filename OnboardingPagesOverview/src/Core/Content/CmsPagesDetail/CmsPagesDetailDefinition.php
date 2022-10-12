<?php declare(strict_types=1);

namespace OnboardingPagesOverview\Core\Content\CmsPagesDetail;


use OnboardingPagesOverview\Core\Content\CmsPagesDetail\Aggregate\CmsPagesDetailTranslation\CmsPagesDetailTranslationDefinition;
use OnboardingPagesOverview\Core\Content\CmsPagesOverview\CmsPagesOverviewDefinition;
use Shopware\Core\Content\Media\MediaDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\OneToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslatedField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\TranslationsAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class CmsPagesDetailDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'cms_pages_detail';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return CmsPagesDetailEntity::class;
    }

    public function getCollectionClass(): string
    {
        return CmsPagesDetailCollection::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new Required(), new PrimaryKey(), new ApiAware()),
            (new FkField('page_id',"pageId",CmsPagesOverviewDefinition::class))->addFlags(new Required(),new ApiAware()),
            (new TranslatedField('title')),
            (new TranslatedField('description')),

            (new FkField('media_id', 'mediaId', MediaDefinition::class))->addFlags(new ApiAware()),

            (new OneToOneAssociationField('media', 'media_id', 'id', MediaDefinition::class)),
            (new OneToOneAssociationField('cmsPagesOverview', 'page_id', 'id', CmsPagesOverviewDefinition::class)),
            (new TranslationsAssociationField(CmsPagesDetailTranslationDefinition::class,'cms_pages_detail_id'))->addFlags(new Required()),
        ]);
    }
}

