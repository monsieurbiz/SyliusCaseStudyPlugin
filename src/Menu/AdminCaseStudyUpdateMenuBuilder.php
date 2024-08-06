<?php

/*
 * This file is part of Monsieur Biz's Sylius Case Study Plugin for Sylius.
 * (c) Monsieur Biz <sylius@monsieurbiz.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MonsieurBiz\SyliusCaseStudyPlugin\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use MonsieurBiz\SyliusCaseStudyPlugin\Entity\CaseStudyInterface;
use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

final class AdminCaseStudyUpdateMenuBuilder
{
    public function __construct(
        private FactoryInterface $factory,
        private StateMachineFactoryInterface $stateMachineFactory,
        private CsrfTokenManagerInterface $csrfTokenManager,
    ) {
    }

    public function createMenu(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('root');

        $caseStudy = $options['case_study'] ?? null;
        if (!$caseStudy instanceof CaseStudyInterface) {
            return $menu;
        }

        $stateMachine = $this->stateMachineFactory->get($caseStudy, CaseStudyInterface::GRAPH);
        if ($stateMachine->can(CaseStudyInterface::TRANSITION_PUBLISH)) {
            $menu
                ->addChild('publish', [
                    'route' => 'monsieurbiz_case_study_admin_case_study_update_state',
                    'routeParameters' => [
                        'id' => $caseStudy->getId(),
                        'state' => CaseStudyInterface::TRANSITION_PUBLISH,
                        '_csrf_token' => $this->csrfTokenManager->getToken((string) $caseStudy->getId())->getValue(),
                    ],
                ])
                ->setAttribute('type', 'transition')
                ->setLabel('monsieurbiz_case_study.ui.publish')
                ->setLabelAttribute('icon', 'check')
                ->setLabelAttribute('color', 'green')
            ;
        }

        return $menu;
    }
}
