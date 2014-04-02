CubeBundle
==========
This bundle allows you to integrate the library [ShowClix/cube-php](https://github.com/ShowClix/cube-php) in your
Symfony2 application and interact with [Cube](https://github.com/square/cube) collectors and evaluators.


## A little appetizer
Just to have a quick idea on how the bundle works have a look at this piece of code:

```php
#Inside a controller action

$client = $this->get('cube_client');

$events = $client->metricGet(array(
    'expression' => 'sum(cube_request)',
    'step' => \Cube\Client::INT_ONE_MINUTE,
    'limit' => 100,
));

foreach ($event in $events) {
    echo "There were {$event['value']} hits during {$event['time']} \n";
}
```


## Documentation
You can learn more about the bundle in its [documentation](Resources/doc/index.md).


## License
This bundle is distributed under the [MIT license](Resources/meta/LICENSE).


## Credits and contributions
This bundle is maintained by [Luciano Mammino](http://loige.com).

If you want to improve or fix something feel free to submit a pull request.
Contributions are always VERY welcome :wink: