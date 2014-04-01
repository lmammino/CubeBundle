<?php

namespace Cube\Bundle\CubeBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('cube');

        $rootNode
            ->canBeDisabled()
            ->children()
                ->scalarNode('default_client')
                    ->defaultValue('default')
                ->end()
            ->end()
        ;

        $rootNode->append($this->createClientsNode());

        return $treeBuilder;
    }

    /**
     * @return \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition|\Symfony\Component\Config\Definition\Builder\NodeDefinition
     */
    protected function createClientsNode()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('clients');
        $node
            ->requiresAtLeastOneElement()
            ->prototype('array')
                ->children()
                    ->scalarNode('connection_class')
                        ->defaultValue('\Cube\Connection\HttpConnection')
                        ->validate()
                            ->ifTrue(function($value){
                                class_exists($value);
                            })
                            ->thenInvalid('Invalid class name: class not found')
                        ->end()
                    ->end()
                    ->booleanNode('secure')
                        ->defaultFalse()
                    ->end()
                    ->append($this->createServerNode('collector', 1080))
                    ->append($this->createServerNode('evaluator', 1081))
                ->end()
            ->end()
        ;

        return $node;
    }

    /**
     * @param string $rootName
     * @param int $defaultPort
     * @param string $defaultHost
     * @return \Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition|\Symfony\Component\Config\Definition\Builder\NodeDefinition
     */
    protected function createServerNode($rootName, $defaultPort, $defaultHost = 'localhost')
    {
        $treeBuilder = new TreeBuilder();

        $node = $treeBuilder->root($rootName);

        $node
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('host')
                    ->defaultValue($defaultHost)
                ->end()
                ->integerNode('port')
                    ->defaultValue($defaultPort)
                    ->min(0)
                    ->max(65535)
                ->end()
            ->end()
        ;

        return $node;
    }
}