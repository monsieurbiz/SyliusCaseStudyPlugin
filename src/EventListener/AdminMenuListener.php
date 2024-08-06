<?php

/*
 * This file is part of Monsieur Biz's Sylius Case Study Plugin for Sylius.
 * (c) Monsieur Biz <sylius@monsieurbiz.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MonsieurBiz\SyliusCaseStudyPlugin\EventListener;

use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

final class AdminMenuListener
{
    public function __invoke(MenuBuilderEvent $event): void
    {
        $menu = $event->getMenu();

        $caseStudiesMenu = $menu
            ->addChild('monsieurbiz-case-studies')
            ->setLabel('monsieurbiz_case_study.ui.menu_case_studies')
        ;

        $caseStudiesMenu->addChild('monsieurbiz-case-studies-tags', ['route' => 'monsieurbiz_case_study_admin_tag_index'])
            ->setLabel('monsieurbiz_case_study.ui.tags')
            ->setLabelAttribute('icon', 'tags')
        ;

        $caseStudiesMenu->addChild('monsieurbiz-case-studies-case-studies', ['route' => 'monsieurbiz_case_study_admin_case_study_index'])
            ->setLabel('monsieurbiz_case_study.ui.case_studies')
            ->setLabelAttribute('icon', 'crosshairs')
        ;
    }
}
