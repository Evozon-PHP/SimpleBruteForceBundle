<?php

namespace EvozonPhp\SimpleBruteForceBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('simple_brute_force');

        $rootNode
            ->children()
                ->arrayNode('limits')
                    ->addDefaultsIfNotSet()
                    ->children()

                        ->integerNode('max_attempts')
                            ->info('Max failed login attempts before blocking.')
                            ->defaultValue(5)
                        ->end()

                        ->scalarNode('block_period')
                            ->info('Duration the user is blocked since the last login attempt. DateInterval duration spec format (ISO 8601).')
                            ->cannotBeEmpty()
                            ->defaultValue('PT5M')
                        ->end()

                        ->integerNode('alert_attempts')
                            ->info('Alert when failed attempts exceed this value.')
                            ->defaultValue(25)
                        ->end()
                    ->end()
                ->end()

                ->arrayNode('response')
                    ->addDefaultsIfNotSet()
                    ->children()

                        ->integerNode('error_code')
                            ->info('HTTP response status code.')
                            ->defaultValue(401)
                        ->end()

                        ->scalarNode('error_message')
                            ->info('HTTP esponse message for blocked users. Don\'t give too much info away.')
                            ->cannotBeEmpty()
                            ->defaultValue('Unauthorized!')
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
