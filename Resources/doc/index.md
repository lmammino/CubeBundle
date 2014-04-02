CubeBundle
==========

## Introduction
CubeBundle allows you to integrate the library [ShowClix/cube-php](https://github.com/ShowClix/cube-php) in your
Symfony2 application and interact with [Cube](https://github.com/square/cube) collectors and evaluators.

## Installation
Installation is done through composer

```bash
composer require lmammino/cube-bundle
```

or add the package to your `composer.json` directly.

Note that this bundle is in a very early stage and a stable version is not yet available.

After you have installed the package, you just need to add the bundle to your `AppKernel.php` file:

```php
// in AppKernel::registerBundles()
$bundles = array(
    // ...
    new Cube\Bundle\CubeBundle\CubeBundle(),
    // ...
);
```


## Configuration
CubeBundle configuration adopts sensible defaults that should be fine in most of cases.
If your cube collector and evaluator run in localhost on their default ports you are ready to go yet.
Otherwise, you need to define a configuration block in your `config.yml`:

```yaml
cube:
    collector:
        host: <collector_host>
        port: <collector_port>
    evaluator:
        host: <evaluator_host>
        port: <evaluator_port>
```

You can achieve a finer level of configuration if needed, for example you can also define many clients (collectors/evaluators)
couples if you use more than once.
For all available configuration options, please see the [configuration reference](/Resources/doc/configuration.md).

## Usage
The configured cube client is available as `cube_client` service. You can easily inject it in other services or fetch
it within your controller actions.

### Register an event
With the following snippet you can send a new event to the cube collector:

```php
client = $this->get('cube_client');

$client->eventPut(array(
    'type' => 'example',
    'time' => time(),
    'data' => array(
        'key1' => 'value1',
    ),
));
```

### Read data from the evaluator
With the following snippet you can read data from the cube evaluator:

```php
client = $this->get('cube_client');

$events = $client->metricGet(array(
    'expression' => 'sum(cube_request)',
    'step' => \Cube\Client::INT_ONE_MINUTE,
    'limit' => 100,
));

foreach ($event in $events) {
    echo "There were {$event['value']} hits during {$event['time']} \n";
}
```


## License
The code is released under the [MIT license](/Resources/meta/LICENSE).


