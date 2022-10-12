<?php declare(strict_types=1);

namespace OnboardingPagesOverview\Core\Content\CmsPagesDetail\Aggregate\CmsPagesDetailTranslation;


use OnboardingPagesOverview\Core\Content\CmsPagesDetail\CmsPagesDetailDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\AllowHtml;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\EntityTranslationDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class CmsPagesDetailTranslationDefinition extends EntityTranslationDefinition
{
    public const ENTITY_NAME = 'cms_pages_detail_translation';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return CmsPagesDetailTranslationEntity::class;
    }
    public function getCollectionClass(): string
    {
        return CmsPagesDetailTranslationCollection::class;
    }

    public function since(): ?string
    {
        return '6.0.0.0';
    }

    protected function getParentDefinitionClass(): string
    {
        return CmsPagesDetailDefinition::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new StringField('title', 'title'))->addFlags(new Required(),new ApiAware()),
            (new StringField('description', 'description'))->addFlags(new ApiAware(),new AllowHtml())
        ]);
    }
}
