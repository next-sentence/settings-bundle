<?php

declare(strict_types=1);

namespace Lwc\SettingsBundle\Settings\Metadata;

use Lwc\SettingsBundle\Settings\Metadata;
use Lwc\SettingsBundle\Settings\MetadataInterface;

final class Registry implements RegistryInterface
{
    /**
     * @var array|MetadataInterface[]
     */
    private array $metadata = [];

    /**
     * {@inheritdoc}
     */
    public function get(string $alias): MetadataInterface
    {
        if (!\array_key_exists($alias, $this->metadata)) {
            throw new \InvalidArgumentException(sprintf('Resource "%s" does not exist.', $alias));
        }

        return $this->metadata[$alias];
    }

    /**
     * {@inheritdoc}
     */
    public function add(MetadataInterface $metadata): void
    {
        $this->metadata[$metadata->getAlias()] = $metadata;
    }

    /**
     * {@inheritdoc}
     */
    public function addFromAliasAndConfiguration(string $alias, array $configuration): void
    {
        $this->add(Metadata::fromAliasAndConfiguration($alias, $configuration));
    }
}
