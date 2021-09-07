<?php

declare(strict_types=1);

namespace Lwc\SettingsBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class LwcSettingsExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $config, ContainerBuilder $container): void
    {
        $configuration = $this->getConfiguration([], $container);
        $config = $this->processConfiguration(/** @scrutinizer ignore-type */ $configuration, $config);
        $container->setParameter('lwc.settings.config.plugins', $config['plugins']);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');
    }

    public function getAlias()
    {
        return str_replace('monsieur_biz', 'lwc', parent::getAlias());
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container): void
    {
        $doctrineConfig = $container->getExtensionConfig('doctrine_migrations');
        $container->prependExtensionConfig('doctrine_migrations', [
            'migrations_paths' => array_merge(array_pop($doctrineConfig)['migrations_paths'] ?? [], [
                'Lwc\SettingsBundle\Migrations' => '@LwcSettingsBundle/Migrations',
            ]),
        ]);
    }
}
