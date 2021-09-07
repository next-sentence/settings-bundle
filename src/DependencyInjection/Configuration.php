<?php

declare(strict_types=1);

namespace Lwc\SettingsBundle\DependencyInjection;

use Lwc\SettingsBundle\Settings\Settings;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('lwc_settings');
        $rootNode = $treeBuilder->getRootNode();

        $this->addPlugins($rootNode);

        return $treeBuilder;
    }

    /**
     * @param ArrayNodeDefinition $rootNode
     */
    private function addPlugins(ArrayNodeDefinition $rootNode): void
    {
        /** @scrutinizer ignore-call */
        $rootNode
                ->children()
                ->arrayNode('plugins')
                    ->useAttributeAsKey('name', false)
                    ->defaultValue([])
                    ->arrayPrototype()
                        ->children()
                            ->scalarNode('vendor_name')->isRequired()->cannotBeEmpty()->end()
                            ->scalarNode('vendor_url')->defaultNull()->end()
                            ->scalarNode('plugin_name')->isRequired()->cannotBeEmpty()->end()
                            ->scalarNode('description')->isRequired()->cannotBeEmpty()->end()
                            ->scalarNode('icon')->isRequired()->cannotBeEmpty()->end()
                            ->booleanNode('use_locales')->end()
                            ->arrayNode('classes')
                                ->addDefaultsIfNotSet()
                                ->children()
                                    ->scalarNode('settings')->defaultValue(Settings::class)->end()
                                    ->scalarNode('form')->isRequired()->cannotBeEmpty()->end()
                                ->end()
                            ->end()
                            ->arrayNode('default_values')
                                ->variablePrototype()->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end()
        ;
    }
}
