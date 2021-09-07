<?php

declare(strict_types=1);

namespace Lwc\SettingsBundle\Repository;

use Sylius\Component\Resource\Repository\RepositoryInterface;

interface SettingRepositoryInterface extends RepositoryInterface
{
    public function findAllByLocale(string $vendor, string $plugin, ?string $localeCode = null): array;

    public function findAllByLocaleWithDefault(string $vendor, string $plugin, ?string $localeCode = null): array;
}
