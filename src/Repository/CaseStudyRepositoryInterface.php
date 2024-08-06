<?php

/*
 * This file is part of Monsieur Biz's Sylius Case Study Plugin for Sylius.
 * (c) Monsieur Biz <sylius@monsieurbiz.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MonsieurBiz\SyliusCaseStudyPlugin\Repository;

use Doctrine\ORM\QueryBuilder;
use MonsieurBiz\SyliusCaseStudyPlugin\Entity\CaseStudyInterface;
use MonsieurBiz\SyliusCaseStudyPlugin\Entity\TagInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @template T of CaseStudyInterface
 *
 * @extends RepositoryInterface<T>
 */
interface CaseStudyRepositoryInterface extends RepositoryInterface
{
    public function createListQueryBuilder(string $localeCode): QueryBuilder;

    public function createShopListQueryBuilder(string $localeCode, ChannelInterface $channel, ?TagInterface $tag): QueryBuilder;

    /**
     * @return CaseStudyInterface[]
     */
    public function findAllEnabledAndPublishedByTag(string $localeCode, ChannelInterface $channel, TagInterface $tag, int $limit): array;

    public function findEnabledAndPublishedByIds(array $caseStudyIds, string $localeCode, ChannelInterface $channel, ?int $number = null): array;

    public function findOneBySlug(string $slug, string $localeCode): ?CaseStudyInterface;

    public function findOnePublishedBySlug(string $slug, string $localeCode, ChannelInterface $channel): ?CaseStudyInterface;
}
