<?php declare(strict_types=1);

namespace OnboardingPagesOverview\Core\Content\CmsPagesOverview\Aggregate\CmsPagesOverviewTranslation;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use OnboardingPagesOverview\Core\Content\CmsPagesOverview\CmsPagesOverviewEntity;
use Shopware\Core\System\Language\LanguageEntity;

class CmsPagesOverviewTranslationEntity extends Entity
{
    use EntityIdTrait;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var \DateTimeInterface
     */
    protected $createdAt;

    /**
     * @var \DateTimeInterface|null
     */
    protected $updatedAt;

    /**
     * @var string
     */
    protected $cmsPagesOverviewId;

    /**
     * @var string
     */
    protected $languageId;

    /**
     * @var CmsPagesOverviewEntity|null
     */
    protected $cmsPagesOverview;

    /**
     * @var LanguageEntity|null
     */
    protected $language;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getCmsPagesOverviewId(): string
    {
        return $this->cmsPagesOverviewId;
    }

    public function setCmsPagesOverviewId(string $cmsPagesOverviewId): void
    {
        $this->cmsPagesOverviewId = $cmsPagesOverviewId;
    }

    public function getLanguageId(): string
    {
        return $this->languageId;
    }

    public function setLanguageId(string $languageId): void
    {
        $this->languageId = $languageId;
    }

    public function getCmsPagesOverview(): ?CmsPagesOverviewEntity
    {
        return $this->cmsPagesOverview;
    }

    public function setCmsPagesOverview(?CmsPagesOverviewEntity $cmsPagesOverview): void
    {
        $this->cmsPagesOverview = $cmsPagesOverview;
    }

    public function getLanguage(): ?LanguageEntity
    {
        return $this->language;
    }

    public function setLanguage(?LanguageEntity $language): void
    {
        $this->language = $language;
    }
}
