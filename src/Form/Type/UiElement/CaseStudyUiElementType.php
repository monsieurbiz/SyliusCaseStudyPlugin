<?php

/*
 * This file is part of Monsieur Biz's Sylius Case Study Plugin for Sylius.
 * (c) Monsieur Biz <sylius@monsieurbiz.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MonsieurBiz\SyliusCaseStudyPlugin\Form\Type\UiElement;

use MonsieurBiz\SyliusCaseStudyPlugin\Form\Type\CaseStudyElementType;
use MonsieurBiz\SyliusRichEditorPlugin\Attribute\AsUiElement;
use MonsieurBiz\SyliusRichEditorPlugin\Attribute\TemplatesUiElement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[AsUiElement(
    code: 'monsieurbiz_case_study.case_study_ui_element',
    icon: 'crosshairs',
    uiElement: 'MonsieurBiz\SyliusCaseStudyPlugin\UiElement\CaseStudyUiElement',
    title: 'monsieurbiz_case_study.ui_element.case_study_ui_element.title',
    description: 'monsieurbiz_case_study.ui_element.case_study_ui_element.description',
    templates: new TemplatesUiElement(
        adminRender: '@MonsieurBizSyliusCaseStudyPlugin/Admin/UiElement/case_study.html.twig',
        frontRender: '@MonsieurBizSyliusCaseStudyPlugin/Shop/UiElement/case_study.html.twig',
    ),
    tags: [],
)]
class CaseStudyUiElementType extends AbstractType
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'monsieurbiz_case_study.ui_element.case_study_ui_element.fields.title',
                'required' => false,
            ])
            ->add('case_studies', CollectionType::class, [
                'label' => 'monsieurbiz_case_study.ui_element.case_study_ui_element.fields.case_studies',
                'entry_type' => CaseStudyElementType::class,
                'prototype_name' => '__case_study__',
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'delete_empty' => true,
                'attr' => [
                    'class' => 'ui segment secondary collection--flex',
                ],
                'constraints' => [
                    new Assert\Count(['min' => 1, 'max' => 2]),
                    new Assert\Valid(),
                ],
            ])
        ;
    }
}
