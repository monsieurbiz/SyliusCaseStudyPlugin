<?php

/*
 * This file is part of Monsieur Biz's Sylius Case Study Plugin for Sylius.
 * (c) Monsieur Biz <sylius@monsieurbiz.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MonsieurBiz\SyliusCaseStudyPlugin\Fixture;

use Doctrine\ORM\EntityManagerInterface;
use MonsieurBiz\SyliusCaseStudyPlugin\Fixture\Factory\CaseStudyFixtureFactory;
use Sylius\Bundle\CoreBundle\Fixture\AbstractResourceFixture;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

final class CaseStudyFixture extends AbstractResourceFixture
{
    public function __construct(
        EntityManagerInterface $caseStudyManager,
        CaseStudyFixtureFactory $exampleFactory
    ) {
        parent::__construct($caseStudyManager, $exampleFactory);
    }

    public function getName(): string
    {
        return 'monsieubiz_case_study';
    }

    protected function configureResourceNode(ArrayNodeDefinition $resourceNode): void
    {
        /** @phpstan-ignore-next-line */
        $resourceNode
            ->children()
                ->booleanNode('enabled')->defaultTrue()->end()
                ->scalarNode('image')->defaultNull()->end()
                ->scalarNode('image_thumbnail')->defaultNull()->end()
                ->scalarNode('tag')->defaultNull()->end()
                ->scalarNode('is_published')->defaultTrue()->end()
                ->scalarNode('publish_date')->cannotBeEmpty()->end()
                ->arrayNode('translations')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('title')->cannotBeEmpty()->end()
                            ->scalarNode('slug')->cannotBeEmpty()->end()
                            ->scalarNode('description')->cannotBeEmpty()->end()
                            ->scalarNode('content')->cannotBeEmpty()->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
