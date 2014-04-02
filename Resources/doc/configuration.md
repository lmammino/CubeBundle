Cube Bundle configuration
-------------------------
Cube Bundle configuration adopts sensible defaults that should be fine in most of cases.
If your cube collector and evaluator run in localhost and uses their default ports you are ready to go yet.
Otherwise, you can tweak the default configuration option by defining a `cube` configuration block in your `config.yml`:

```yaml
cube:
    enabled: <enabled> #default:true
    connection_class: <connection_class> #default: "\Cube\Connection\HttpConnection"
    secure: <secure> #default: false
    collector:
        host: <collector_host> #default: "localhost"
        port: <collector_port> #default: 1080
    evaluator:
        host: <evaluator_host> #default: "localhost"
        port: <evaluator_port> #default: 1081
```


## Configuration overview
Here's a detailed explanation of what every configuration parameter means:

- **enabled** (boolean): allows you to enable/disable the bundle (useful if you want to disable it on specific environments, eg. staging)
- **connection_class** (string): allows you to define a different connection class, just in case you want to use a custom one.
  Keep in mind the class must extend `\Cube\Connection\Connection` to work properly.
- **secure** (boolean): allows you to enable/disable ssl connection
- **collector** : defined the host and the port for your cube collector service
- **evaluator** : defined the host and the port for your cube evaluator service


## Advanced configuration
Sometime you may need to define more than one couple of cube collector/evaluator. The bundle configuration allows you to do so.
In fact the configuration contains a special field called `clients` and one called `default_client`. By default the
configuration parameters are normalized to the following structure:

```yaml
cube:
    enabled: <enabled> #default:true
    default_client: <default_client> #default: "default"
    clients:
        default:
            connection_class: <connection_class> #default: "\Cube\Connection\HttpConnection"
            secure: <secure> #default: false
            collector:
                host: <collector_host> #default: "localhost"
                port: <collector_port> #default: 1080
            evaluator:
                host: <evaluator_host> #default: "localhost"
                port: <evaluator_port> #default: 1081
```

If you need to define several "clients" you can use this different configuration as shown in the following example:

```yaml
cube:
    default_client: "client1"
    clients:
        client1:
            collector:
                host: "128.1.2.3"
            evaluator:
                host: "128.1.2.3"
        client2:
            collector:
                host: "128.1.2.3"
                port: 1082
            evaluator:
                host: "128.1.2.3"
                host: 1083
```

Now with this configuration, if you try to get the `cube_client` service you will get the `client1` client (as it is defined as default client).
If you want to retrieve the client2 you have two options:

### 1. Define a secondary client as a service

In this case you need to create a new service:

```xml
<service id="cube_client_client2" class="%cube_client.class%"
         factory-service="cube_client_factory" factory-method="create"
         public="false">
    <argument>client2</argument>
</service>
```

and then you can retrieve ore inject it by using the id `cube_client_client2`;

### 2. Get a secondary client with the Factory

If you prefer to not define a new service, you can leverage an existing client factory service (identified as the `cube_client_factory` service).
Once you got the factory instance you can retrieve the client instance as the following snippet show:

```php
$factory = $container->get('cube_client_factory');
$client2 = $factory->create('client2');
```