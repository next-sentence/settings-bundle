<?php

declare(strict_types=1);

namespace Lwc\SettingsBundle\Form;

use Lwc\SettingsBundle\Exception\SettingsException;
use Lwc\SettingsBundle\Settings\Settings;
use Lwc\SettingsBundle\Settings\SettingsInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

final class MainSettingsType extends AbstractType implements MainSettingsTypeInterface
{
    /**
     * @var RepositoryInterface
     */
    private RepositoryInterface $localeRepository;

    /**
     * MainSettingsType constructor.
     *
     * @param RepositoryInterface $localeRepository
     */
    public function __construct(
        RepositoryInterface $localeRepository
    ) {
        $this->localeRepository = $localeRepository;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setRequired([
            'settings',
        ])->setAllowedTypes('settings', [SettingsInterface::class]);
    }

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     *
     * @throws SettingsException
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $settings = $options['settings'];
        $data = $options['data'];
        $builder->add(
            $key = Settings::DEFAULT_KEY . '-' . Settings::DEFAULT_KEY, $settings->getFormClass(), [
                'settings' => $settings,
                'label' => false,
                'show_default_checkboxes' => false,
                'data' => $data[$key] ?? null,
                'constraints' => [
                    new Assert\Valid(),
                ],
            ]);

        $this->addDefaultLocales($builder, $settings, $data);

    }

    /**
     * @param FormBuilderInterface $builder
     * @param SettingsInterface $settings
     * @param array $data
     *
     * @throws SettingsException
     */
    private function addDefaultLocales(FormBuilderInterface $builder, SettingsInterface $settings, array $data): void
    {
        if ($settings->showLocalesInForm()) {
            /** @var LocaleInterface $locale */
            foreach ($this->localeRepository->findAll() as $locale) {
                $builder->add(
                    $key = Settings::DEFAULT_KEY . '-' . $locale->getCode(), $settings->getFormClass(), [
                        'settings' => $settings,
                        'label' => false,
                        'show_default_checkboxes' => true,
                        'data' => $data[$key] ?? null,
                        'constraints' => [
                            new Assert\Valid(),
                        ],
                    ]);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix(): string
    {
        return 'settings';
    }
}
