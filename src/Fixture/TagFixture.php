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
use MonsieurBiz\SyliusCaseStudyPlugin\Fixture\Factory\TagFixtureFactory;
use Sylius\Bundle\CoreBundle\Fixture\AbstractResourceFixture;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

final class TagFixture extends AbstractResourceFixture
{
    public function __construct(
        EntityManagerInterface $caseStudyTagManager,
        TagFixtureFactory $exampleFactory
    ) {
        parent::__construct($caseStudyTagManager, $exampleFactory);
    }

    public function getName(): string
    {
        return 'monsieurbiz_case_study_tag';
    }

    protected function configureResourceNode(ArrayNodeDefinition $resourceNode): void
    {
        /** @phpstan-ignore-next-line */
        $resourceNode
            ->children()
                ->booleanNode('enabled')->defaultTrue()->end()
                ->arrayNode('translations')
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('name')->cannotBeEmpty()->end()
                            ->scalarNode('slug')->cannotBeEmpty()->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
