<?php

declare(strict_types=1);

namespace Lwc\SettingsBundle\Factory;

use Lwc\SettingsBundle\Entity\Setting\SettingInterface;
use Lwc\SettingsBundle\Settings\SettingsInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

interface SettingFactoryInterface extends FactoryInterface
{
    public function createNewFromGlobalSettings(SettingsInterface $settings, ?LocaleInterface $locale): SettingInterface;
}
