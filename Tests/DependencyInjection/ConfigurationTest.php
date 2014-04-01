<?php

namespace Cube\Bundle\CubeBundle\Tests\DependencyInjection;

use Cube\Bundle\CubeBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Processor;

/**
 * Class ConfigurationTest
 * @package Cube\Bundle\CubeBundle\Tests\DependencyInjection
 * @author Luciano Mammino <lmammino@oryzone.com>
 */
class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaultConfig()
    {
        $configs = array(
            array()
        );
        $conf = $this->process($configs);

        $this->assertHasConfKeys($conf);
    }

    public function testNullConfig()
    {
        $configs = array(null);
        $conf = $this->process($configs);

        $this->assertHasConfKeys($conf);
    }

    public function testSingleClient()
    {
        $configs = array(
            array(
                'default_client' => 'client1',
                'collector' => array(
                    'host' => '128.0.0.1',
                    'port' => 55421
                )
            )
        );
        $conf = $this->process($configs);

        $this->assertEquals('128.0.0.1', $conf['clients']['client1']['collector']['host']);
        $this->assertEquals(55421, $conf['clients']['client1']['collector']['port']);
    }

    public function testSingleClientInClients()
    {
        $configs = array(
            array(
                'default_client' => 'client1',
                'clients' => array(
                    'client1' => array(
                        'collector' => array(
                            'host' => '128.0.0.1',
                            'port' => 55421
                        )
                    )
                )
            )
        );
        $conf = $this->process($configs);

        $this->assertEquals('128.0.0.1', $conf['clients']['client1']['collector']['host']);
        $this->assertEquals(55421, $conf['clients']['client1']['collector']['port']);
    }

    public function testMultipleClients()
    {
        $configs = array(
            array(
                'default_client' => 'client1',
                'clients' => array(
                    'client1' => array(),
                    'client2' => array()
                )
            )
        );

        $conf = $this->process($configs);
        $this->assertClientHasConfKeys($conf['clients']['client1']);
        $this->assertClientHasConfKeys($conf['clients']['client2']);
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage Invalid type for path "cube.enabled". Expected boolean, but got string
     */
    public function testInvalidEnabled()
    {
        $configs = array(
            array(
                'enabled' => 'client1',
            )
        );

        $this->process($configs);
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage Invalid type for path "cube.clients.default.secure". Expected boolean, but got string
     */
    public function testInvalidSecure()
    {
        $configs = array(
            array(
                'secure' => "insecure",
            )
        );

        $this->process($configs);
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage Invalid configuration for path "cube.clients.default.connection_class": Invalid class ""\\Nonexistent\\Class"": class not found
     */
    public function testNonexistentConnectionClass()
    {
        $configs = array(
            array(
                'connection_class' => '\Nonexistent\Class',
            )
        );

        $this->process($configs);
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage The value 75535 is too big for path "cube.clients.default.collector.port". Should be less than or equal to 65535
     */
    public function testInvalidPort2()
    {
        $configs = array(
            array(
                'collector' => array(
                    "port" => 75535
                )
            )
        );

        $this->process($configs);
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage The value -55 is too small for path "cube.clients.default.collector.port". Should be greater than or equal to 0
     */
    public function testInvalidPort()
    {
        $configs = array(
            array(
                'collector' => array(
                    "port" => -55
                )
            )
        );

        $this->process($configs);
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     * @expectedExceptionMessage Invalid configuration for path "cube": The default client has not been defined in the clients configuration
     */
    public function testNoDefaultClient()
    {
        $configs = array(
            array(
                'clients' => array(
                    'client1' => array(),
                    'client2' => array()
                )
            )
        );

        $this->process($configs);
    }



    protected function assertHasConfKeys($conf)
    {
        $this->assertArrayHasKey('default_client', $conf);
        $this->assertArrayHasKey('clients', $conf);
        $this->assertArrayHasKey('enabled', $conf);
        $this->assertArrayHasKey('default', $conf['clients']);
        $this->assertArrayNotHasKey('connection_class', $conf);
        $this->assertArrayNotHasKey('secure', $conf);
        $this->assertArrayNotHasKey('collector', $conf);
        $this->assertArrayNotHasKey('evaluator', $conf);
    }

    protected function assertClientHasConfKeys($client)
    {
        $this->assertArrayHasKey('connection_class', $client);
        $this->assertArrayHasKey('secure', $client);
        $this->assertArrayHasKey('collector', $client);
        $this->assertArrayHasKey('evaluator', $client);
    }

    /**
     * Processes an array of configurations and returns a compiled version.
     *
     * @param array $configs An array of raw configurations
     *
     * @return array A normalized array
     */
    protected function process($configs)
    {
        $processor = new Processor();

        return $processor->processConfiguration(new Configuration(), $configs);
    }
}
