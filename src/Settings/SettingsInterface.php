<?php

declare(strict_types=1);

namespace Lwc\SettingsBundle\Settings;

use Lwc\SettingsBundle\Exception\SettingsException;
use Lwc\SettingsBundle\Repository\SettingRepositoryInterface;

interface SettingsInterface
{
    public function __construct(Metadata $metadata, SettingRepositoryInterface $settingRepository);

    public function getAlias(): string;

    public function getAliasAsArray(): array;

    public function getVendorName(): string;

    public function getVendorUrl(): ?string;

    public function getPluginName(): string;

    public function getDescription(): string;

    public function getIcon(): string;

    /**
     * @throws SettingsException
     */
    public function getFormClass(): string;

    public function getSettingsByLocale(?string $localeCode = null, bool $withDefault = false): array;

    public function getSettingsValuesByLocale(?string $localeCode = null): array;

    public function getCurrentValue(?string $localeCode, string $path);

    public function getDefaultValues(): array;

    public function getDefaultValue(string $path);

    public function showLocalesInForm(): bool;
}
