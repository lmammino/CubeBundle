<?php

namespace Cube\Bundle\CubeBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 * @package Cube\Bundle\CubeBundle\DependencyInjection
 * @author Luciano Mammino <lmammino@oryzone.com>
 */
class Configuration implements ConfigurationInterface
{
    protected static $DEFAULT_CLIENT = array(
        'connection_class' => '\Cube\Connection\HttpConnection',
        'secure' => false,
        'collector' => array(
            'host' => 'localhost',
            'port' => 1080
        ),
        'evaluator' => array(
            'host' => 'localhost',
            'port' => 1081
        )
    );

    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('cube');

        $rootNode
            ->canBeDisabled()
            ->beforeNormalization()
                ->ifTrue(function ($v) { return is_array($v) && !array_key_exists('clients', $v) && !array_key_exists('client', $v); })
                ->then(function ($v) {
                    // Key that should not be moved to the clients config
                    $excludedKeys = array('default_client' => true, 'enabled' => true);
                    $client = array();
                    foreach ($v as $key => $value) {
                        if (isset($excludedKeys[$key])) {
                            continue;
                        }
                        $client[$key] = $v[$key];
                        unset($v[$key]);
                    }
                    $v['default_client'] = isset($v['default_client']) ? (string) $v['default_client'] : 'default';
                    $v['clients'] = array($v['default_client'] => $client);

                    return $v;
                })
            ->end()
            ->validate()
                ->ifTrue(function($v) { return ! array_key_exists($v['default_client'], $v['clients']); })
                ->thenInvalid('The default client has not been defined in the clients configuration')
            ->end()
            ->children()
                ->scalarNode('default_client')
                    ->defaultValue('default')
                ->end()
                ->scalarNode('connection_class')->end()
                ->booleanNode('secure')->end()
                ->arrayNode('collector')->end()
                ->arrayNode('evaluator')->end()
            ->end()
            ->fixXmlConfig('client')
            ->append($this->getClientsNode())
        ;

        return $treeBuilder;
    }

    /**
     * Creates clients node
     *
     * @return ArrayNodeDefinition|NodeDefinition
     */
    protected function getClientsNode()
    {
        $treeBuilder = new TreeBuilder();
        $node = $treeBuilder->root('clients');

        $node
            ->defaultValue(array(
                'default' => self::$DEFAULT_CLIENT
            ))
            ->useAttributeAsKey('name')
            ->prototype('array')
                ->children()
                    ->scalarNode('connection_class')
                        ->defaultValue(self::$DEFAULT_CLIENT['connection_class'])
                        ->validate()
                            ->ifTrue(function($value){
                                return !class_exists($value);
                            })
                            ->thenInvalid('Invalid class "%s": class not found')
                        ->end()
                    ->end()
                    ->booleanNode('secure')
                        ->defaultValue(self::$DEFAULT_CLIENT['secure'])
                    ->end()
                    ->append($this->createServerNode('collector', self::$DEFAULT_CLIENT['collector']['port']))
                    ->append($this->createServerNode('evaluator', self::$DEFAULT_CLIENT['evaluator']['port']))
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