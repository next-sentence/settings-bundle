<?php

declare(strict_types=1);

namespace Lwc\SettingsBundle\Settings\Metadata;

use Lwc\SettingsBundle\Settings\MetadataInterface;

interface RegistryInterface
{
    public function get(string $alias): MetadataInterface;

    public function add(MetadataInterface $metadata): void;

    public function addFromAliasAndConfiguration(string $alias, array $configuration): void;
}
