<?php

/*
 * This file is part of Monsieur Biz's Sylius Case Study Plugin for Sylius.
 * (c) Monsieur Biz <sylius@monsieurbiz.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace MonsieurBiz\SyliusCaseStudyPlugin\Fixture\Factory;

use Closure;
use DateTime;
use DateTimeInterface;
use Faker\Factory;
use Faker\Generator;
use MonsieurBiz\SyliusCaseStudyPlugin\Entity\CaseStudyInterface;
use MonsieurBiz\SyliusCaseStudyPlugin\Entity\CaseStudyTranslationInterface;
use MonsieurBiz\SyliusCaseStudyPlugin\Entity\TagInterface;
use MonsieurBiz\SyliusCaseStudyPlugin\Repository\TagRepositoryInterface;
use MonsieurBiz\SyliusMediaManagerPlugin\Exception\CannotReadCurrentFolderException;
use MonsieurBiz\SyliusMediaManagerPlugin\Helper\FileHelperInterface;
use MonsieurBiz\SyliusMediaManagerPlugin\Model\File;
use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
use Sylius\Bundle\CoreBundle\Fixture\Factory\AbstractExampleFactory;
use Sylius\Bundle\CoreBundle\Fixture\OptionsResolver\LazyOption;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Formatter\StringInflector;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Config\FileLocatorInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CaseStudyFixtureFactory extends AbstractExampleFactory
{
    private OptionsResolver $optionsResolver;

    private OptionsResolver $translationOptionsResolver;

    private Generator $faker;

    /**
     * @param FactoryInterface<CaseStudyInterface> $caseStudyFactory
     * @param FactoryInterface<CaseStudyTranslationInterface> $caseStudyTranslationFactory
     * @param TagRepositoryInterface<TagInterface> $tagRepository
     * @param RepositoryInterface<LocaleInterface> $localeRepository
     * @param ChannelRepositoryInterface<ChannelInterface> $channelRepository
     */
    public function __construct(
        private FactoryInterface $caseStudyFactory,
        private FactoryInterface $caseStudyTranslationFactory,
        private TagRepositoryInterface $tagRepository,
        private StateMachineFactoryInterface $stateMachineFactory,
        private RepositoryInterface $localeRepository,
        private ChannelRepositoryInterface $channelRepository,
        private FileLocatorInterface $fileLocator,
        private FileHelperInterface $fileHelper,
    ) {
        $this->faker = Factory::create();

        $this->optionsResolver = new OptionsResolver();
        $this->configureOptions($this->optionsResolver);

        $this->translationOptionsResolver = new OptionsResolver();
        $this->configureTranslationOptions($this->translationOptionsResolver);
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function create(array $options = []): CaseStudyInterface
    {
        $options = $this->optionsResolver->resolve($options);

        /** @var CaseStudyInterface $caseStudy */
        $caseStudy = $this->caseStudyFactory->createNew();
        $caseStudy->setEnabled($options['enabled']);
        $caseStudy->addTag($options['tag']);
        $caseStudy->setImage($options['image']);
        $caseStudy->setImageThumbnail($options['image_thumbnail']);
        $channels = $this->channelRepository->findAll();
        /** @var ChannelInterface $channel */
        foreach ($channels as $channel) {
            $caseStudy->addChannel($channel);
        }
        $this->createTranslations($caseStudy, $options);

        if ($options['is_published']) {
            $this->applyTransition($caseStudy, CaseStudyInterface::TRANSITION_PUBLISH);
        }
        if (CaseStudyInterface::STATE_PUBLISHED === $caseStudy->getState() && null !== $options['publish_date']) {
            $caseStudy->setPublishedAt($options['publish_date']);
        }

        return $caseStudy;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function configureOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('enabled', function (Options $options): bool {
                return $this->faker->boolean(80);
            })

            ->setDefault('image', $this->lazyImageDefault(80))
            ->setAllowedTypes('image', ['string', 'null'])

            ->setDefault('image_thumbnail', $this->lazyImageDefault(80))
            ->setAllowedTypes('image_thumbnail', ['string', 'null'])

            ->setDefault('tag', LazyOption::randomOne($this->tagRepository))
            ->setAllowedTypes('tag', ['string', TagInterface::class])
            ->setNormalizer('tag', function (Options $options, $previousValue): ?object {
                if (null === $previousValue || \is_object($previousValue)) {
                    return $previousValue;
                }

                return $this->tagRepository->findOneByName($previousValue, 'fr');
            })

            ->setDefault('translations', [])
            ->setAllowedTypes('translations', ['array'])

            ->setDefault('is_published', fn (Options $options): bool => $this->faker->boolean(80))
            ->setAllowedTypes('is_published', ['bool'])

            ->setDefault('publish_date', fn (Options $options): DateTimeInterface => $this->faker->dateTimeBetween('-1 years', 'now'))
            ->setAllowedTypes('publish_date', ['null', DateTime::class])
        ;
    }

    private function createTranslations(CaseStudyInterface $caseStudy, array $options): void
    {
        // add translation for each defined locales
        foreach ($this->getLocales() as $localeCode) {
            $translation = $options['translations'][$localeCode] ?? [];
            $translation = $this->translationOptionsResolver->resolve($translation);
            /** @var CaseStudyTranslationInterface $caseStudyTranslation */
            $caseStudyTranslation = $this->caseStudyTranslationFactory->createNew();
            $caseStudyTranslation->setLocale($localeCode);
            $caseStudyTranslation->setTitle($translation['title']);
            $caseStudyTranslation->setSlug($translation['slug'] ?? StringInflector::nameToCode($translation['title']));
            $caseStudyTranslation->setDescription($translation['description']);
            $caseStudyTranslation->setContent($translation['content']);

            $caseStudy->addTranslation($caseStudyTranslation);
        }
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    private function configureTranslationOptions(OptionsResolver $resolver): void
    {
        $resolver
            ->setDefault('title', fn (Options $options): string => /** @phpstan-ignore-line */ $this->faker->words(3, true))
            ->setDefault('slug', null)
            ->setDefault('description', fn (Options $options): string => $this->faker->paragraph)
            ->setDefault('content', fn (Options $options): string => $this->faker->paragraph)
        ;
    }

    private function applyTransition(CaseStudyInterface $caseStudy, string $transition): void
    {
        $this->stateMachineFactory->get($caseStudy, CaseStudyInterface::GRAPH)->apply($transition);
    }

    private function getLocales(): iterable
    {
        /** @var LocaleInterface[] $locales */
        $locales = $this->localeRepository->findAll();
        foreach ($locales as $locale) {
            yield $locale->getCode();
        }
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    private function lazyImageDefault(int $chanceOfRandomOne): Closure
    {
        return function (Options $options) use ($chanceOfRandomOne): ?string {
            if (random_int(1, 100) > $chanceOfRandomOne) {
                return null;
            }

            $random = random_int(1, 5);
            $sourcePath = $this->fileLocator->locate(\sprintf('@MonsieurBizSyliusCaseStudyPlugin/Resources/fixtures/case-study-%d.jpg', $random));
            $existingImage = $this->findExistingImage(basename($sourcePath));
            if (null !== $existingImage) {
                return $existingImage;
            }

            $file = new UploadedFile($sourcePath, basename($sourcePath));
            $filename = $this->fileHelper->upload($file, 'case-study', 'gallery/images');

            return 'gallery/images/case-study/' . $filename;
        };
    }

    private function findExistingImage(string $filename): ?string
    {
        try {
            $files = $this->fileHelper->list('case-study', 'gallery/images');
        } catch (CannotReadCurrentFolderException) {
            $this->fileHelper->createFolder('case-study', '', 'gallery/images'); // Create the folder if it does not exist
            $files = [];
        }

        /** @var File $file */
        foreach ($files as $file) {
            if ($filename === $file->getName()) {
                return 'gallery/images/' . $file->getPath();
            }
        }

        return null;
    }
}
