<?php

/*
 * This file is part of Monsieur Biz's Sylius Case Study Plugin for Sylius.
 * (c) Monsieur Biz <sylius@monsieurbiz.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MonsieurBiz\SyliusCaseStudyPlugin\Repository;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use MonsieurBiz\SyliusCaseStudyPlugin\Entity\CaseStudyInterface;
use MonsieurBiz\SyliusCaseStudyPlugin\Entity\TagInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

/**
 * @template T of TagInterface
 *
 * @implements TagRepositoryInterface<T>
 */
final class TagRepository extends EntityRepository implements TagRepositoryInterface
{
    public function findRootNodes(): array
    {
        /** @phpstan-ignore-next-line */
        return $this->createQueryBuilder('o')
            ->addOrderBy('o.position')
            ->getQuery()
            ->getResult()
        ;
    }

    public function findHydratedRootNodes(): array
    {
        $this->createQueryBuilder('o')
            ->select(['o', 'ot'])
            ->leftJoin('o.translations', 'ot')
            ->getQuery()
            ->getResult()
        ;

        return $this->findRootNodes();
    }

    public function createListQueryBuilder(string $localeCode): QueryBuilder
    {
        return $this->createQueryBuilder('o')
            ->addSelect('translation')
            ->leftJoin('o.translations', 'translation', 'WITH', 'translation.locale = :localeCode')
            ->setParameter('localeCode', $localeCode)
        ;
    }

    public function createEnabledListQueryBuilder(string $localeCode): QueryBuilder
    {
        return $this->createListQueryBuilder($localeCode)
            ->join('o.caseStudies', 'caseStudies')
            ->andWhere('o.enabled = true')
            ->andWhere('caseStudies.enabled = true')
            ->andWhere('caseStudies.state = :state')
            ->setParameter('state', CaseStudyInterface::STATE_PUBLISHED)
        ;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findOneByName(string $name, string $localeCode): ?TagInterface
    {
        /** @phpstan-ignore-next-line */
        return $this->createListQueryBuilder($localeCode)
            ->andWhere('translation.name = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findOneBySlug(string $slug, string $localeCode): ?TagInterface
    {
        /** @phpstan-ignore-next-line */
        return $this->createListQueryBuilder($localeCode)
            ->andWhere('translation.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
