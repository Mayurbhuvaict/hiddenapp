<?php declare(strict_types=1);

namespace OnboardingPagesOverview\Core\Content\CmsPagesDetail\Aggregate\CmsPagesDetailTranslation;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use OnboardingPagesOverview\Core\Content\CmsPagesDetail\CmsPagesDetailEntity;
use Shopware\Core\System\Language\LanguageEntity;

class CmsPagesDetailTranslationEntity extends Entity
{
    use EntityIdTrait;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string|null
     */
    protected $description;

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
    protected $cmsPagesDetailId;

    /**
     * @var string
     */
    protected $languageId;

    /**
     * @var CmsPagesDetailEntity|null
     */
    protected $cmsPagesDetail;

    /**
     * @var LanguageEntity|null
     */
    protected $language;

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
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

    public function getCmsPagesDetailId(): string
    {
        return $this->cmsPagesDetailId;
    }

    public function setCmsPagesDetailId(string $cmsPagesDetailId): void
    {
        $this->cmsPagesDetailId = $cmsPagesDetailId;
    }

    public function getLanguageId(): string
    {
        return $this->languageId;
    }

    public function setLanguageId(string $languageId): void
    {
        $this->languageId = $languageId;
    }

    public function getCmsPagesDetail(): ?CmsPagesDetailEntity
    {
        return $this->cmsPagesDetail;
    }

    public function setCmsPagesDetail(?CmsPagesDetailEntity $cmsPagesDetail): void
    {
        $this->cmsPagesDetail = $cmsPagesDetail;
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
