<?php

declare(strict_types=1);

namespace Lwc\SettingsBundle\Processor;

use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Lwc\SettingsBundle\Factory\SettingFactoryInterface;
use Lwc\SettingsBundle\Repository\SettingRepositoryInterface;
use Lwc\SettingsBundle\Settings\Settings;
use Lwc\SettingsBundle\Settings\SettingsInterface;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class SettingsProcessor implements SettingsProcessorInterface
{
    /**
     * @var RepositoryInterface
     */
    private RepositoryInterface $localeRepository;

    /**
     * @var SettingRepositoryInterface
     */
    private SettingRepositoryInterface $settingRepository;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

    /**
     * @var SettingFactoryInterface
     */
    private SettingFactoryInterface $settingFactory;

    /**
     * SettingsProcessor constructor.
     *
     * @param RepositoryInterface $localeRepository
     * @param SettingRepositoryInterface $settingRepository
     * @param EntityManagerInterface $em
     * @param SettingFactoryInterface $settingFactory
     */
    public function __construct(
        RepositoryInterface $localeRepository,
        SettingRepositoryInterface $settingRepository,
        EntityManagerInterface $em,
        SettingFactoryInterface $settingFactory
    ) {
        $this->localeRepository = $localeRepository;
        $this->settingRepository = $settingRepository;
        $this->em = $em;
        $this->settingFactory = $settingFactory;
    }

    /**
     * @param SettingsInterface $settings
     * @param array $data
     */
    public function processData(SettingsInterface $settings, array $data): void
    {
        foreach ($data as $settingsIdentifier => $settingsData) {
            if (!\is_array($settingsData)) {
                continue;
            }
            $localeCode = $this->getLocaleCodeFromSettingKey($settingsIdentifier);
            $this->saveSettings($settings, $localeCode, $settingsData);
        }
    }

    /**
     * @param $settingKey
     *
     * @return ?string
     */
    private function getLocaleCodeFromSettingKey($settingKey): ?string
    {
        switch (true) {
            // Default website + Default locale
            case sprintf('%1$s-%1$s', Settings::DEFAULT_KEY) === $settingKey:
            case 1 === preg_match(sprintf('`^channel-(?P<channelId>[0-9]+)-%1$s$`', Settings::DEFAULT_KEY), $settingKey, $matches):
                return null;
            // Default website + locale
            case 1 === preg_match(sprintf('`^channel-(?P<channelId>[0-9]+)-(?!%1$s)(?P<localeCode>.+)$`', Settings::DEFAULT_KEY), $settingKey, $matches):
            case 1 === preg_match(sprintf('`^%1$s-(?!%1$s)(?P<localeCode>.+)$`', Settings::DEFAULT_KEY), $settingKey, $matches):
                return $matches['localeCode'];
            default:
                throw new LogicException("Format of the setting's key is incorrect.");
        }
    }

    /**
     * @param SettingsInterface $settings
     * @param string|null $localeCode
     * @param array $data
     */
    private function saveSettings(SettingsInterface $settings, ?string $localeCode, array $data): void
    {
        /** @var LocaleInterface|null $locale */
        $locale = null !== $localeCode ? $this->localeRepository->findOneBy(['code' => $localeCode]) : null;

        $actualSettings = $settings->getSettingsByLocale(
            $localeCode
        );

        $this->removeUnusedSettings($data, $actualSettings);
        $this->saveNewAndExistingSettings($data, $actualSettings, $settings, $locale);

        $this->em->flush();
    }

    /**
     * @param array $data
     * @param array $settings
     */
    private function removeUnusedSettings(array &$data, array $settings): void
    {
        // Manage defaults, and remove actual settings with "use default value" checked
        foreach ($data as $key => $value) {
            // Is the setting a "use default value"?
            if (1 === preg_match(sprintf('`^(?P<key>.*)(?:___%1$s)$`', Settings::DEFAULT_KEY), $key, $matches)) {
                if (true === $data[$key]) {
                    if (isset($settings[$matches['key']])) {
                        $this->em->remove($settings[$matches['key']]);
                    }
                    unset($data[$matches['key']]);
                }
                unset($data[$key]);
            }
        }
    }

    /**
     * @param array $data
     * @param array $actualSettings
     * @param SettingsInterface $settings
     * @param LocaleInterface|null $locale
     */
    private function saveNewAndExistingSettings(array $data, array $actualSettings, SettingsInterface $settings, ?LocaleInterface $locale): void
    {
        foreach ($data as $key => $value) {
            if (isset($actualSettings[$key])) {
                $setting = $actualSettings[$key];
                try {
                    $setting->setValue($value);
                    $this->em->persist($setting);
                    continue;
                } catch (\TypeError $e) {
                    // The type doesn't match, it could be normal, let's find the type out of the value.
                }
            }

            $setting = $this->settingFactory->createNewFromGlobalSettings($settings, $locale);
            $setting->setPath($key);
            $setting->setStorageTypeFromValue($value);
            $setting->setValue($value);
            $this->em->persist($setting);
        }
    }
}
