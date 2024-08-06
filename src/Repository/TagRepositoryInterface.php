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
use MonsieurBiz\SyliusCaseStudyPlugin\Entity\TagInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @template T of TagInterface
 *
 * @extends RepositoryInterface<T>
 */
interface TagRepositoryInterface extends RepositoryInterface
{
    /**
     * @return TagInterface[]
     */
    public function findRootNodes(): array;

    /**
     * @return TagInterface[]
     */
    public function findHydratedRootNodes(): array;

    public function createListQueryBuilder(string $localeCode): QueryBuilder;

    public function createEnabledListQueryBuilder(string $localeCode): QueryBuilder;

    public function findOneByName(string $name, string $localeCode): ?TagInterface;

    public function findOneBySlug(string $slug, string $localeCode): ?TagInterface;
}
