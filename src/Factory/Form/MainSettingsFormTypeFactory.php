<?php

declare(strict_types=1);

namespace Lwc\SettingsBundle\Factory\Form;

use Lwc\SettingsBundle\Settings\Settings;
use Lwc\SettingsBundle\Settings\SettingsInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

final class MainSettingsFormTypeFactory implements MainSettingsFormTypeFactoryInterface
{
    /**
     * @var FormFactoryInterface
     */
    private FormFactoryInterface $formFactory;

    /**
     * @var RepositoryInterface
     */
    private RepositoryInterface $localeRepository;

    /**
     * MainSettingsFormTypeFactory constructor.
     *
     * @param FormFactoryInterface $formFactory
     * @param RepositoryInterface $localeRepository
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        RepositoryInterface $localeRepository
    ) {
        $this->formFactory = $formFactory;
        $this->localeRepository = $localeRepository;
    }

    /**
     * @param SettingsInterface $settings
     * @param string $type
     * @param array $options
     *
     * @return FormInterface
     */
    public function createNew(SettingsInterface $settings, string $type, array $options = []): FormInterface
    {
        return $this->formFactory->create(
            $type,
            $this->getInitialFormData($settings),
            $options
        );
    }

    /**
     * @param SettingsInterface $settings
     *
     * @return array
     */
    private function getInitialFormData(SettingsInterface $settings): array
    {
        $data = [
            Settings::DEFAULT_KEY . '-' . Settings::DEFAULT_KEY => $settings->getSettingsValuesByLocale() + $settings->getDefaultValues(),
        ];

        if ($settings->showLocalesInForm()) {
            /** @var LocaleInterface $locale */
            foreach ($this->localeRepository->findAll() as $locale) {
                $data[Settings::DEFAULT_KEY . '-' . $locale->getCode()] = $settings->getSettingsValuesByLocale($locale->getCode());
            }
        }

        return $data;
    }
}
