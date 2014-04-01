<?php

namespace Cube\Bundle\CubeBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * Class CubeExtension
 * @package Cube\Bundle\CubeBundle\DependencyInjection
 * @author Luciano Mammino <lmammino@oryzone.com>
 */
class CubeExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        if (true === $config['enabled']) {
            $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
            $loader->load('services.xml');

            $container->getDefinition('cube_client_factory')->replaceArgument(0, $config['clients']);
            $container->setParameter('cube_client.default', $config['default_client']);
        }
    }
}
