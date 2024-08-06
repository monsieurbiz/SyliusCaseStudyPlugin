<?php

/*
 * This file is part of Monsieur Biz's Sylius Case Study Plugin for Sylius.
 * (c) Monsieur Biz <sylius@monsieurbiz.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MonsieurBiz\SyliusCaseStudyPlugin\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\TimestampableTrait;
use Sylius\Component\Resource\Model\ToggleableTrait;
use Sylius\Component\Resource\Model\TranslatableTrait;

class Tag implements TagInterface
{
    use TimestampableTrait;
    use ToggleableTrait;
    use TranslatableTrait {
        TranslatableTrait::__construct as private initializeTranslationsCollection;
        TranslatableTrait::getTranslation as private doGetTranslation;
    }

    private ?int $id = null;

    /**
     * @var bool
     */
    protected $enabled = true;

    protected ?int $position = null;

    /**
     * @var Collection<array-key, CaseStudyInterface>
     */
    protected Collection $caseStudies;

    public function __construct()
    {
        $this->initializeTranslationsCollection();
        $this->caseStudies = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->getTranslation()->getName();
    }

    public function setName(?string $name): void
    {
        $this->getTranslation()->setName($name);
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): void
    {
        $this->position = $position;
    }

    public function getSlug(): ?string
    {
        return $this->getTranslation()->getSlug();
    }

    public function setSlug(?string $slug): void
    {
        $this->getTranslation()->setSlug($slug);
    }

    public function addCaseStudy(CaseStudyInterface $caseStudy): void
    {
        if (!$this->hasCaseStudy($caseStudy)) {
            $this->caseStudies->add($caseStudy);
        }
    }

    public function removeCaseStudy(CaseStudyInterface $caseStudy): void
    {
        if ($this->hasCaseStudy($caseStudy)) {
            $this->caseStudies->removeElement($caseStudy);
        }
    }

    public function hasCaseStudy(CaseStudyInterface $caseStudy): bool
    {
        return $this->caseStudies->contains($caseStudy);
    }

    public function getCaseStudies(): Collection
    {
        return $this->caseStudies;
    }

    public function getTranslation(?string $locale = null): TagTranslationInterface
    {
        /** @phpstan-ignore-next-line */
        return $this->doGetTranslation($locale);
    }

    protected function createTranslation(): TagTranslationInterface
    {
        return new TagTranslation();
    }
}
