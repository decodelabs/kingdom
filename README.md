# Kingdom

[![PHP from Packagist](https://img.shields.io/packagist/php-v/decodelabs/kingdom?style=flat)](https://packagist.org/packages/decodelabs/kingdom)
[![Latest Version](https://img.shields.io/packagist/v/decodelabs/kingdom.svg?style=flat)](https://packagist.org/packages/decodelabs/kingdom)
[![Total Downloads](https://img.shields.io/packagist/dt/decodelabs/kingdom.svg?style=flat)](https://packagist.org/packages/decodelabs/kingdom)
[![GitHub Workflow Status](https://img.shields.io/github/actions/workflow/status/decodelabs/kingdom/integrate.yml?branch=develop)](https://github.com/decodelabs/kingdom/actions/workflows/integrate.yml)
[![PHPStan](https://img.shields.io/badge/PHPStan-enabled-44CC11.svg?longCache=true&style=flat)](https://github.com/phpstan/phpstan)
[![License](https://img.shields.io/packagist/l/decodelabs/kingdom?style=flat)](https://packagist.org/packages/decodelabs/kingdom)

### Service container management system

Kingdom provides a set of simple but powerful interfaces for building structures that can _contain_ your whole application logic and services.

---

## Installation

Install via Composer:

```bash
composer require decodelabs/kingdom
```

## Usage

Kingdom works in tandem with [Monarch](https://github.com/decodelabs/monarch) to give developers an easy way to manage dependency injection, application containment, service access and more.

An implementation of the `Kingdom` interface can be considered the _root_ of your application in which all other services are contained. A project can have multiple `Kingdom` instances which are in turn controlled by the static `Monarch` class. Only one `Kingdom` instance can be considered active at a time.

You can think of `Monarch` and `Kingdom` in a similar way to how you would think of a monarch and a kingdom in real life - the monarch rules over the kingdom and the kingdom has a boundary around it, isolating it from the outside world.

You shouldn't need to interact with the monarch for many things as the kingdom should be able to provide most of the resources that are needed; only bother the monarch when you have to!


### Container

The heart of a `Kingdom` is its container. Instead of dealing directly with PSR-11 containers which only provide a rudimentary access scheme, `Kingdom` uses an adapter system that allows implementations to create a richer and more powerful service access mechanism.

[Pandora](https://github.com/decodelabs/pandora) implements both the PSR-11 interface _and_ Kingdom's `ContainerAdapter` interface, allowing it to function directly as a service container for Kingdom without any additional configuration.

If you are building your application on top of [Fabric](https://github.com/decodelabs/fabric), a `Kingdom` implementation is provided for you using `Pandora` as the container by default.

See the [ContainerAdapter](./src/Kingdom/ContainerAdapter.php) interface for more information on the methods available.


### Services

Items held within the container can implement the `Service` interface to set them apart from other items in the container. The interface requires the implementation of a method to provide an instance of the service based on the contents on the container. A default implementation is provided in the `ServiceTrait` trait.

Services can be accessed in a `Kingdom` instance using the `getService` method.

```php
use DecodeLabs\Kingdom\Service;
use DecodeLabs\Kingdom\ServiceTrait;

class MyService implements Service
{
    use ServiceTrait;
}

$service = $myKingdom->getService(MyService::class);
```

However, because a `Kingdom` is intended to be isolated and managed by the `Monarch` package, it is recommended to use the `Monarch::getService` method instead, which will delegate to the active `Kingdom` instance.

```php
use DecodeLabs\Monarch;

$service = Monarch::getService(MyService::class);
```

The active `Kingdom` instance should be registered with `Monarch` during your bootstrap process. If you use [Genesis](https://github.com/decodelabs/genesis) for your bootstrapping, this will be taken care of for you, otherwise:

```php
use DecodeLabs\Monarch;

Monarch::setKingdom($myKingdom);
```


### Runtime

Once a `Kingdom` has been initialized, it's main purpose is to run the application. This is determined by an instance of the `Runtime` interface, which acts like a kernel for your application.

There are default implementations of HTTP and CLI runtimes provided by [Harvest](https://github.com/decodelabs/harvest) and [Clip](https://github.com/decodelabs/clip) respectively.

Once bootstrapped, run your application:

```php
$myKingdom->run();
```

Past this point, the container and service structure should be used to auto-wire the loading process with automatic dependency injection when constructing objects and services.

This can generally be handled in your own libraries by using [Slingshot](https://github.com/decodelabs/slingshot), or in a pinch, using `Monarch::getService()`.


### Fabric

When using `Kingdom` in a [Fabric](https://github.com/decodelabs/fabric) application, you can use your `Kingdom` implementation as the place to set up your application's container and services.

```php
namespace My\Namespace;

use DecodeLabs\Fabric\Kingdom as FabricKingdom;
use DecodeLabs\Harvest\Profile as HttpProfile;
use DecodeLabs\Harvest\Middleware\Cors;

class Kingdom extends FabricKingdom
{
    public protected(set) string $name = 'DecodeLabs Playground';

    public function initialize(): void
    {
        parent::initialize();

        Monarch::getPaths()->alias('@public', '@run/public');
        Monarch::getPaths()->alias('@components', '@run/src/@components');

        $this->container->prepare(
            HttpProfile::class,
            fn (HttpProfile $profile) => $profile->add(Cors::class, allow: ['*'])
        );

        $this->container->setFactory(
            SomeOtherThing::class,
            fn () => new SomeOtherThing()
        );

        // ...etc
    }
}
```


## Licensing

Kingdom is licensed under the MIT License. See [LICENSE](./LICENSE) for the full license text.
