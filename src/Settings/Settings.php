<?php

declare(strict_types=1);

namespace Lwc\SettingsBundle\Settings;

use Lwc\SettingsBundle\Entity\Setting\SettingInterface;
use Lwc\SettingsBundle\Exception\SettingsException;
use Lwc\SettingsBundle\Form\AbstractSettingsType;
use Lwc\SettingsBundle\Repository\SettingRepositoryInterface;

final class Settings implements SettingsInterface
{
    public const DEFAULT_KEY = 'default';

    /**
     * @var Metadata
     */
    private Metadata $metadata;

    /**
     * @var SettingRepositoryInterface
     */
    private SettingRepositoryInterface $settingRepository;

    /**
     * @var array|null
     */
    private ?array $settingsByLocale;

    /**
     * @var array|null
     */
    private ?array $settingsByLocaleWithDefault;

    /**
     * Settings constructor.
     *
     * @param Metadata $metadata
     * @param SettingRepositoryInterface $settingRepository
     */
    public function __construct(Metadata $metadata, SettingRepositoryInterface $settingRepository)
    {
        $this->metadata = $metadata;
        $this->settingRepository = $settingRepository;
    }

    public function getAlias(): string
    {
        return $this->metadata->getAlias();
    }

    public function getAliasAsArray(): array
    {
        return [
            'vendor' => $this->metadata->getApplicationName(true),
            'plugin' => $this->metadata->getName(true),
        ];
    }

    public function getVendorName(): string
    {
        return $this->metadata->getParameter('vendor_name');
    }

    public function getVendorUrl(): ?string
    {
        return $this->metadata->getParameter('vendor_url');
    }

    public function getPluginName(): string
    {
        return $this->metadata->getParameter('plugin_name');
    }

    public function getDescription(): string
    {
        return $this->metadata->getParameter('description');
    }

    public function getIcon(): string
    {
        return $this->metadata->getParameter('icon');
    }

    /**
     * @throws SettingsException
     *
     * @return string
     */
    public function getFormClass(): string
    {
        $className = $this->metadata->getClass('form');
        if (!\in_array(AbstractSettingsType::class, class_parents($className), true)) {
            throw new SettingsException(sprintf('Class %s should extend %s', $className, AbstractSettingsType::class));
        }

        return $className;
    }

    /**
     * @param string $channelIdentifier
     * @param string $localeIdentifier
     * @param bool $withDefault
     *
     * @return array|null
     */
    private function getCachedSettingsByLocale(string $channelIdentifier, string $localeIdentifier, bool $withDefault): ?array
    {
        // With default?
        $varName = $withDefault ? 'settingsByLocaleWithDefault' : 'settingsByLocale';
        if (!isset($this->{$varName}[$channelIdentifier])) {
            $this->{$varName}[$channelIdentifier] = [];

            return null;
        }
        if (!isset($this->{$varName}[$channelIdentifier][$localeIdentifier])) {
            return null;
        }

        return $this->{$varName}[$channelIdentifier][$localeIdentifier];
    }

    /**
     * @param string|null $localeCode
     * @param bool $withDefault
     *
     * @return array
     */
    public function getSettingsByLocale(?string $localeCode = null, bool $withDefault = false): array
    {
        $channelIdentifier = self::DEFAULT_KEY;
        $localeIdentifier = null === $localeCode ? '___' . self::DEFAULT_KEY : $localeCode;

        if (null === $settings = $this->getCachedSettingsByLocale($channelIdentifier, $localeIdentifier, $withDefault)) {
            if ($withDefault) {
                $settings = $this->stackSettings(
                    $this->settingRepository->findAllByLocaleWithDefault(
                        $this->metadata->getApplicationName(),
                        $this->metadata->getName(true),
                        $localeCode
                    )
                );
                $this->settingsByLocaleWithDefault[$channelIdentifier][$localeIdentifier] = $settings;
            } else {
                $settings = $this->stackSettings(
                    $this->settingRepository->findAllByLocaleWithDefault(
                        $this->metadata->getApplicationName(),
                        $this->metadata->getName(true),
                        $localeCode
                    )
                );
                $this->settingsByLocale[$channelIdentifier][$localeIdentifier] = $settings;
            }
        }

        return $settings;
    }

    /**
     * @param array $allSettings
     *
     * @return array
     */
    private function stackSettings(array $allSettings): array
    {
        $settings = [];
        /** @var SettingInterface $setting */
        // If we have the default values as well, the order is primordial.
        // We will store the default first, so the no default values will override the default if needed.
        foreach ($allSettings as $setting) {
            if (\is_array($setting)) {
                $setting = current($setting);
            }
            $settings[$setting->getPath()] = $setting;
        }

        return $settings;
    }

    /**
     * @param string|null $localeCode
     *
     * @return array
     */
    public function getSettingsValuesByLocale(?string $localeCode = null): array
    {
        $allSettings = $this->getSettingsByLocale($localeCode);
        $settingsValues = [];
        /** @var SettingInterface $setting */
        foreach ($allSettings as $setting) {
            $settingsValues[$setting->getPath()] = $setting->getValue();
        }

        return $settingsValues;
    }

    /**
     * @param string|null $localeCode
     * @param string $path
     *
     * @return mixed
     */
    public function getCurrentValue(?string $localeCode, string $path)
    {
        $settings = $this->getSettingsByLocale($localeCode, true);
        if (isset($settings[$path])) {
            return $settings[$path]->getValue();
        }

        return $this->getDefaultValue($path);
    }

    public function getDefaultValues(): array
    {
        return $this->metadata->getDefaultValues();
    }

    /**
     * @param string $path
     *
     * @return mixed
     */
    public function getDefaultValue(string $path)
    {
        $defaultValues = $this->getDefaultValues();
        if (\array_key_exists($path, $defaultValues)) {
            return $defaultValues[$path];
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function showLocalesInForm(): bool
    {
        return $this->metadata->useLocales();
    }
}
