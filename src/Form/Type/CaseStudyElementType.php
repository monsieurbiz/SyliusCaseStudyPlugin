<?php

/*
 * This file is part of Monsieur Biz's Sylius Case Study Plugin for Sylius.
 * (c) Monsieur Biz <sylius@monsieurbiz.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MonsieurBiz\SyliusCaseStudyPlugin\Form\Type;

use MonsieurBiz\SyliusCaseStudyPlugin\Entity\CaseStudy;
use MonsieurBiz\SyliusCaseStudyPlugin\Repository\CaseStudyRepositoryInterface;
use Sylius\Bundle\ResourceBundle\Form\DataTransformer\ResourceToIdentifierTransformer;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\ReversedTransformer;
use Symfony\Component\Validator\Constraints as Assert;

final class CaseStudyElementType extends AbstractType
{
    public function __construct(
        private CaseStudyRepositoryInterface $caseStudyRepository,
        private ChannelContextInterface $channelContext,
        private LocaleContextInterface $localeContext,
    ) {
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $caseStudies = $this->caseStudyRepository->createShopListQueryBuilder($this->localeContext->getLocaleCode(), $this->channelContext->getChannel(), null);
        $caseStudies = $caseStudies->orderBy('translation.title')->getQuery()->getResult();

        $builder
            ->add('case_study', EntityType::class, [
                'class' => CaseStudy::class,
                'label' => 'monsieurbiz_case_study.ui_element.case_study_ui_element.fields.case_study',
                'choice_label' => fn (CaseStudy $caseStudy) => $caseStudy->getTitle(),
                'choice_value' => fn (?CaseStudy $caseStudy) => $caseStudy ? $caseStudy->getId() : null,
                'required' => true,
                'choices' => $caseStudies,
            ])
            ->add('position', IntegerType::class, [
                'label' => 'monsieurbiz_case_study.ui_element.case_study_ui_element.fields.position',
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(),
                    new Assert\GreaterThan(0),
                ],
            ])
        ;

        $builder->get('case_study')->addModelTransformer(
            new ReversedTransformer(new ResourceToIdentifierTransformer($this->caseStudyRepository, 'id')),
        );
    }
}
