<?php

namespace Cube\Bundle\CubeBundle\Client;

use Cube\Client;
use Cube\Connection\Connection;

class Factory
{
    protected $clients;

    protected $instances;

    public function __construct($clients)
    {
        $this->clients = $clients;
        $this->instances = array();
    }

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