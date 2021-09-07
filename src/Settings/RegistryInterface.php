<?php

declare(strict_types=1);

namespace Lwc\SettingsBundle\Settings;

use Countable;
use Lwc\SettingsBundle\Exception\SettingsAlreadyExistsException;

interface RegistryInterface extends Countable
{
    /**
     * @return int
     */
    public function count(): int;

    /**
     * @param SettingsInterface $settings
     *
     * @throws SettingsAlreadyExistsException
     */
    public function addSettingsInstance(SettingsInterface $settings): void;

    /**
     * @param SettingsInterface $settings
     *
     * @return bool
     */
    public function hasSettingsInstance(SettingsInterface $settings): bool;

    /**
     * @return array<SettingsInterface>
     */
    public function getAllSettings(): array;

    /**
     * @param string $alias
     *
     * @return SettingsInterface|null
     */
    public function getByAlias(string $alias): ?SettingsInterface;
}
