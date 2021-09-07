<?php

declare(strict_types=1);

namespace Lwc\SettingsBundle\Twig\Extension;

use Lwc\SettingsBundle\Settings\RegistryInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Twig\Extension\AbstractExtension;
use Twig\Extension\ExtensionInterface;
use Twig\TwigFunction;

final class SettingsExtension extends AbstractExtension implements ExtensionInterface
{
    /**
     * @var RegistryInterface
     */
    private RegistryInterface $settingsRegistry;

    /**
     * @var LocaleContextInterface
     */
    private LocaleContextInterface $localeContext;

    public function __construct(
        RegistryInterface $settingsRegistry,
        LocaleContextInterface $localeContext
    ) {
        $this->settingsRegistry = $settingsRegistry;
        $this->localeContext = $localeContext;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('setting', [$this, 'getSettingValue'], [
                'needs_context' => true,
            ]),
        ];
    }

    /**
     * @param array $context
     * @param string $alias
     * @param string $path
     *
     * @return mixed
     */
    public function getSettingValue(array $context, string $alias, string $path)
    {
        if ($settingsInstance = $this->settingsRegistry->getByAlias($alias)) {
            return $settingsInstance->getCurrentValue(
                $this->localeContext->getLocaleCode(),
                $path
            );
        }

        return null;
    }
}
