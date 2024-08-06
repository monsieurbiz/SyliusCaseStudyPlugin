<?php

/*
 * This file is part of Monsieur Biz's Sylius Case Study Plugin for Sylius.
 * (c) Monsieur Biz <sylius@monsieurbiz.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MonsieurBiz\SyliusCaseStudyPlugin\Form\Type;

use MonsieurBiz\SyliusCaseStudyPlugin\Entity\Tag;
use MonsieurBiz\SyliusCaseStudyPlugin\Entity\TagInterface;
use MonsieurBiz\SyliusCaseStudyPlugin\Repository\TagRepositoryInterface;
use MonsieurBiz\SyliusMediaManagerPlugin\Form\Type\ImageType;
use Sylius\Bundle\ChannelBundle\Form\Type\ChannelChoiceType;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceTranslationsType;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;

final class CaseStudyType extends AbstractResourceType
{
    public function __construct(
        private LocaleContextInterface $localeContext,
        string $dataClass,
        array $validationGroups = [],
    ) {
        parent::__construct($dataClass, $validationGroups);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameters)
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('enabled', CheckboxType::class, [
                'required' => false,
                'label' => 'sylius.ui.enabled',
            ])
            ->add('channels', ChannelChoiceType::class, [
                'multiple' => true,
                'expanded' => true,
                'label' => 'sylius.form.product.channels',
            ])
            ->add('tags', EntityType::class, [
                'label' => 'monsieurbiz_case_study.form.case_study.tags',
                'required' => true,
                'multiple' => true,
                'class' => Tag::class,
                'query_builder' => function (TagRepositoryInterface $tagRepository) {
                    return $tagRepository->createListQueryBuilder($this->localeContext->getLocaleCode());
                },
                'choice_label' => function (TagInterface $tag) {
                    return $tag->getName();
                },
            ])
            ->add('image', ImageType::class, [
                'label' => 'monsieurbiz_case_study.form.case_study.image',
                'required' => false,
            ])
            ->add('image_thumbnail', ImageType::class, [
                'label' => 'monsieurbiz_case_study.form.case_study.image_thumbnail',
                'required' => false,
            ])
            ->add('translations', ResourceTranslationsType::class, [
                'entry_type' => CaseStudyTranslationType::class,
            ])
        ;
    }
}
