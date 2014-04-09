<?php

namespace Cube\Bundle\CubeBundle\Client;

use Cube\Client;
use Cube\Connection\Connection;

/**
 * A factory class for Cube clients
 * @package Cube\Bundle\CubeBundle\Client
 * @author Luciano Mammino <lmammino@oryzone.com>
 */
class Factory
{
    /**
     * @var \Cube\Client $client
     */
    protected $clients;

    /**
     * @var array $instances
     */
    protected $instances;

    /**
     * Constructor
     * @param array $clients
     */
    public function __construct($clients)
    {
        $this->clients = $clients;
        $this->instances = array();
    }

    /**
     * Creates an instance
     * @param string $name
     * @return \Cube\Client
     * @throws \InvalidArgumentException if the name is not valid
     */
    public function create($name)
    {
        if (!isset($this->instances[$name])) {

            if (!isset($this->clients[$name])) {
                throw new \InvalidArgumentException(
                    sprintf('Invalid name "%s" for cube client. Valid names are: %s', $name, json_encode(array_keys($this->clients)))
                );
            }

            $connectionClass = $this->clients[$name]['connection_class'];
            $connection = new $connectionClass(array(
                'collector' => array(
                    'host' => $this->clients[$name]['collector']['host'],
                    'port' => $this->clients[$name]['collector']['port']
                ),
                'evaluator' => array(
                    'host' => $this->clients[$name]['evaluator']['host'],
                    'port' => $this->clients[$name]['evaluator']['port']
                ),
                'secure' => $this->clients[$name]['secure']
            ));

            if (!$connection instanceof Connection) {
                throw new \InvalidArgumentException(
                    sprintf('The class "%s" is not an instance of \Cube\Connection\Connection', $connectionClass)
                );
            }

            $instance = new Client($connection);
            $this->instances[$name] = $instance;
        }

        return $this->instances[$name];
    }

}
