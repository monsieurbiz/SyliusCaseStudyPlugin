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
use Sylius\Component\Channel\Model\ChannelInterface;

/**
 * @template T of CaseStudyInterface
 *
 * @implements CaseStudyRepositoryInterface<T>
 */
final class CaseStudyRepository extends EntityRepository implements CaseStudyRepositoryInterface
{
    public function createListQueryBuilder(string $localeCode): QueryBuilder
    {
        return $this->createQueryBuilder('o')
            ->addSelect('translation')
            ->leftJoin('o.translations', 'translation', 'WITH', 'translation.locale = :localeCode')
            ->setParameter('localeCode', $localeCode)
        ;
    }

    public function createShopListQueryBuilder(string $localeCode, ChannelInterface $channel, ?TagInterface $tag): QueryBuilder
    {
        $queryBuilder = $this->createListQueryBuilder($localeCode)
            ->andWhere(':channel MEMBER OF o.channels')
            ->andWhere('o.enabled = true')
            ->andWhere('o.state = :state')
            ->setParameter('channel', $channel)
            ->setParameter('state', CaseStudyInterface::STATE_PUBLISHED)
        ;

        if (null !== $tag) {
            $queryBuilder
                ->andWhere(':tag MEMBER OF o.tags')
                ->setParameter('tag', $tag)
            ;
        }

        return $queryBuilder;
    }

    public function findAllEnabledAndPublishedByTag(string $localeCode, ChannelInterface $channel, TagInterface $tag, int $limit): array
    {
        /** @phpstan-ignore-next-line */
        return $this->createShopListQueryBuilder($localeCode, $channel, $tag)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
     * @throws NonUniqueResultException
     */
    public function findOneBySlug(string $slug, string $localeCode): ?CaseStudyInterface
    {
        /** @phpstan-ignore-next-line */
        return $this->createListQueryBuilder($localeCode)
            ->andWhere('translation.slug = :slug')
            ->setParameter('slug', $slug)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findOnePublishedBySlug(string $slug, string $localeCode, ChannelInterface $channel): ?CaseStudyInterface
    {
        /** @phpstan-ignore-next-line */
        return $this->createListQueryBuilder($localeCode)
            ->andWhere('translation.slug = :slug')
            ->andWhere(':channel MEMBER OF o.channels')
            ->andWhere('o.enabled = true')
            ->andWhere('o.state = :state')
            ->setParameter('slug', $slug)
            ->setParameter('channel', $channel)
            ->setParameter('state', CaseStudyInterface::STATE_PUBLISHED)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
}
