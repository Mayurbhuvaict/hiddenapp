<?php declare(strict_types=1);

namespace OnboardingPagesOverview\Core\Content\CmsPagesOverview;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use OnboardingPagesOverview\Core\Content\CmsPagesOverview\Aggregate\CmsPagesOverviewTranslation\CmsPagesOverviewTranslationCollection;
use OnboardingPagesOverview\Core\Content\CmsPagesDetail\CmsPagesDetailEntity;

class CmsPagesOverviewEntity extends Entity
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
     * @var string
     */
    protected $slug;

    /**
     * @var CmsPagesOverviewTranslationCollection
     */
    protected $translations;

    /**
     * @var \DateTimeInterface
     */
    protected $createdAt;

    /**
     * @var \DateTimeInterface|null
     */
    protected $updatedAt;

    /**
     * @var CmsPagesDetailEntity|null
     */
    protected $page;

    /**
     * @var array|null
     */
    protected $translated;

    public function getId(): string
    {
        return $this->id;
    }

    public function setId(string $id): void
    {
        $this->id = $id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public function getTranslations(): CmsPagesOverviewTranslationCollection
    {
        return $this->translations;
    }

    public function setTranslations(CmsPagesOverviewTranslationCollection $translations): void
    {
        $this->translations = $translations;
    }

    public function getCreatedAt(): \DateTimeInterface
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

    public function getPage(): ?CmsPagesDetailEntity
    {
        return $this->page;
    }

    public function setPage(?CmsPagesDetailEntity $page): void
    {
        $this->page = $page;
    }

    public function getTranslated(): array
    {
        return $this->translated;
    }

    public function setTranslated(?array $translated): void
    {
        $this->translated = $translated;
    }
}
