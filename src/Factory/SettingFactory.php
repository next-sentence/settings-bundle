<?php

declare(strict_types=1);

namespace Lwc\SettingsBundle\Factory;

use Lwc\SettingsBundle\Entity\Setting\SettingInterface;
use Lwc\SettingsBundle\Settings\SettingsInterface;
use Sylius\Component\Locale\Model\LocaleInterface;

final class SettingFactory implements SettingFactoryInterface
{
    /**
     * @var string
     */
    private $className;

    /**
     * @param string $className
     */
    public function __construct(string $className)
    {
        $this->className = $className;
    }

    /**
     * @return SettingInterface
     */
    public function createNew()
    {
        return new $this->className();
    }

    /**
     * {@inheritdoc}
     */
    public function createNewFromGlobalSettings(SettingsInterface $settings, ?LocaleInterface $locale): SettingInterface
    {
        $aliases = $settings->getAliasAsArray();

        $setting = $this->createNew();
        $setting->setLocaleCode(null === $locale ? null : $locale->getCode());
        $setting->setVendor($aliases['vendor']);
        $setting->setPlugin($aliases['plugin']);

        return $setting;
    }
}
