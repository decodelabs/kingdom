# Kingdom — Package Specification

> **Cluster:** `runtime`
> **Language:** `php`
> **Milestone:** `m1`
> **Repo:** `https://github.com/decodelabs/kingdom`
> **Role:** App containment

## Overview

### Purpose

Kingdom provides a service container and management system for PHP applications. It defines interfaces and traits for building application containment structures that manage services, dependency injection, and application lifecycle. Kingdom enables:

- Service container abstraction via `ContainerAdapter` interface
- Service registration and resolution with automatic dependency injection
- Application runtime management (HTTP/CLI)
- Service lifecycle management through `Service` interface
- Integration with Monarch for active kingdom management
- PSR-11 container compatibility via adapter pattern

Kingdom is designed to be the foundation for application containment in the Decode Labs ecosystem, providing a clean separation between service management and application execution.

### Non-Goals

- Kingdom does not provide a concrete container implementation (uses Pandora or other PSR-11 containers via adapter)
- It does not implement dependency injection resolution (delegates to container adapter)
- It does not provide service auto-wiring (handled by container implementations)
- It does not manage service scopes or lifetimes beyond container capabilities
- It does not provide service decorators or proxies
- It does not handle service configuration or metadata

## Role in the Ecosystem

### Cluster & Positioning

Kingdom belongs to the **runtime** cluster, providing core application containment infrastructure. It sits at the foundation of the runtime stack, alongside Monarch (configuration), Slingshot (dependency injection), and Pandora (PSR-11 container).

### Usage Contexts

Kingdom is used for:

- Application bootstrapping and initialization
- Service container management and access
- Runtime mode detection and execution
- Framework-level service organization
- Application lifecycle management
- Dependency injection coordination

## Public Surface

### Key Types

- **`Kingdom`** — Main interface for application containment. Defines methods for initialization, execution, and service access.

- **`KingdomTrait`** — Trait providing default implementation of `Kingdom` interface. Handles runtime detection, service resolution, and lifecycle management.

- **`Kingdom\ContainerAdapter`** — Interface extending PSR-11 `ContainerInterface` with additional methods for factory registration, type binding, and service preparation.

- **`Kingdom\Service`** — Interface for services that can be provided via the container. Requires `provideService()` static method.

- **`Kingdom\ServiceTrait`** — Trait providing default implementation of `Service` interface using `getOrCreate()`.

- **`Kingdom\PureService`** — Interface extending `Service` for services that don't require container access. Requires parameterless constructor and `providePureService()` method.

- **`Kingdom\PureServiceTrait`** — Trait providing default implementation of `PureService` interface.

- **`Kingdom\EagreService`** — Marker interface extending `Service` for services that are eagerly instantiated.

- **`Kingdom\Runtime`** — Interface for application runtime implementations. Defines initialization, execution, and shutdown methods.

- **`Kingdom\RuntimeMode`** — Enum defining runtime modes: `Http` and `Cli`.

### Main Entry Points

- **`Kingdom::__construct(ContainerAdapter $container)`** — Creates a kingdom instance with the given container adapter. Registers itself in the container.

- **`Kingdom::initialize(): void`** — Initializes the kingdom and its services. Called before `run()`.

- **`Kingdom::run(): void`** — Runs the application by delegating to the runtime.

- **`Kingdom::shutdown(): never`** — Shuts down the application by delegating to the runtime. Never returns.

- **`Kingdom::getService(string $class): Service`** — Resolves a service from the container. Handles interface resolution and service provision.

- **`Kingdom::runtime: Runtime`** — Property that lazily loads the runtime from the container.

- **`ContainerAdapter::get(string $type): object`** — Gets a service instance from the container. Throws exception if not found.

- **`ContainerAdapter::tryGet(string $type): ?object`** — Attempts to get a service instance. Returns null if not found.

- **`ContainerAdapter::getOrCreate(string $type): object`** — Gets a service instance or creates it if not found.

- **`ContainerAdapter::has(string $type): bool`** — Checks if a service is registered.

- **`ContainerAdapter::set(string $type, object $instance): void`** — Registers a service instance.

- **`ContainerAdapter::setFactory(string $type, Closure $factory): void`** — Registers a factory closure for service creation.

- **`ContainerAdapter::setType(string $type, string $instanceType, array $parameters): void`** — Binds a type to an implementation with constructor parameters.

- **`ContainerAdapter::prepare(string $type, Closure $callback): void`** — Registers a preparation callback for a service type.

- **`ContainerAdapter::getPsrContainer(): ContainerInterface`** — Returns the underlying PSR-11 container.

- **`Service::provideService(ContainerAdapter $container): static`** — Static factory method for creating service instances.

- **`PureService::providePureService(): static`** — Static factory method for creating pure service instances without container access.

- **`Runtime::initialize(): void`** — Initializes the runtime.

- **`Runtime::run(): void`** — Runs the application.

- **`Runtime::shutdown(): never`** — Shuts down the application. Never returns.

## Dependencies

### Decode Labs

- **`exceptional`** — Used for exception handling throughout the package.

### External

- **`psr/container`** — PSR-11 container interface for standardized service container access.

## Behaviour & Contracts

### Invariants

- A kingdom instance is registered in its own container under `Kingdom::class`
- Only one kingdom can be active at a time (managed by Monarch)
- Runtime is lazily loaded from the container on first access
- Service resolution follows this order: container lookup, interface resolution, service provision
- `getService()` only works with classes implementing `Service` interface
- `shutdown()` never returns (application terminates)
- Runtime mode is detected automatically based on SAPI and environment variables

### Input & Output Contracts

- **`Kingdom::__construct(ContainerAdapter $container): Kingdom`** — Creates a kingdom instance. Registers itself in the container. Container must be initialized.

- **`Kingdom::initialize(): void`** — Initializes the kingdom. Should be called once before `run()`. Sets up services and configuration.

- **`Kingdom::run(): void`** — Runs the application by delegating to runtime. Should be called after `initialize()`.

- **`Kingdom::shutdown(): never`** — Shuts down the application. Terminates execution and never returns.

- **`Kingdom::getService(string $class): Service`** — Resolves a service. Returns service instance. Throws `Runtime` if class is not a service. Handles interface resolution via `getOrCreate()`.

- **`ContainerAdapter::get(string $type): object`** — Returns service instance. Throws exception if not found.

- **`ContainerAdapter::tryGet(string $type): ?object`** — Returns service instance or null if not found.

- **`ContainerAdapter::getOrCreate(string $type): object`** — Returns service instance, creating it if necessary. Uses container's creation logic.

- **`ContainerAdapter::has(string $type): bool`** — Returns true if service is registered, false otherwise.

- **`ContainerAdapter::set(string $type, object $instance): void`** — Registers a service instance. Overwrites existing registration.

- **`ContainerAdapter::setFactory(string $type, Closure $factory): void`** — Registers a factory for service creation. Factory receives container and returns service instance.

- **`ContainerAdapter::setType(string $type, string $instanceType, array $parameters): void`** — Binds a type to an implementation. Parameters are passed to constructor.

- **`ContainerAdapter::prepare(string $type, Closure $callback): void`** — Registers a preparation callback. Callback receives service instance after creation for configuration.

- **`Service::provideService(ContainerAdapter $container): static`** — Creates and returns a service instance. Uses container for dependency resolution.

- **`PureService::providePureService(): static`** — Creates and returns a pure service instance without container access.

- **`Runtime::initialize(): void`** — Initializes the runtime. Sets up runtime-specific services and configuration.

- **`Runtime::run(): void`** — Executes the application. Handles request processing or command execution.

- **`Runtime::shutdown(): never`** — Shuts down the application. Performs cleanup and terminates execution.

## Error Handling

Kingdom uses the Exceptional pattern for error handling. Key exception types:

- **`Runtime`** — Thrown when `getService()` is called with a non-service class, or when runtime mode cannot be detected.

- **`UnexpectedValue`** — Thrown when runtime mode detection fails for unknown SAPI.

Exceptions preserve the original service context and include detailed error messages.

## Configuration & Extensibility

### Extension Points

- **Custom Container Adapters** — Implement `ContainerAdapter` interface to integrate with any PSR-11 container or custom container implementation.

- **Custom Runtime Implementations** — Implement `Runtime` interface to provide custom application execution logic.

- **Service Types** — Implement `Service`, `PureService`, or `EagreService` interfaces to define service behavior.

- **Kingdom Implementations** — Implement `Kingdom` interface or use `KingdomTrait` to create custom application containers.

### Configuration

- **Container Setup** — Container adapter is provided during kingdom construction. Services are registered via container adapter methods.

- **Runtime Detection** — Runtime mode is automatically detected based on `HTTP_HOST` server variable, `argv` server variable, or PHP SAPI.

- **Service Registration** — Services are registered via container adapter using `set()`, `setFactory()`, `setType()`, or `prepare()` methods.

- **Service Resolution** — Services are resolved via `getService()` which handles container lookup, interface resolution, and service provision.

## Interactions with Other Packages

- **Monarch** — Used to manage the active kingdom instance. Kingdom instances are registered with Monarch for global access.

- **Pandora** — Provides a concrete `ContainerAdapter` implementation that can be used as Kingdom's container.

- **Slingshot** — Used by services for dependency injection when instantiating dependencies.

- **Harvest** — Provides HTTP runtime implementation for Kingdom.

- **Clip** — Provides CLI runtime implementation for Kingdom.

- **Genesis** — Integrates with Kingdom for application bootstrapping and initialization.

- **Fabric** — Provides a base `Kingdom` implementation using Pandora as the container.

## Usage Examples

### Basic Kingdom Setup

```php
use DecodeLabs\Kingdom;
use DecodeLabs\Kingdom\ContainerAdapter;
use DecodeLabs\Kingdom\KingdomTrait;
use DecodeLabs\Pandora;

class MyKingdom implements Kingdom
{
    use KingdomTrait;

    public string $name { get => 'My Application'; }

    public function __construct()
    {
        $container = new Pandora();
        parent::__construct($container);
    }

    public function initialize(): void
    {
        // Set up services
        $this->container->setFactory(
            MyService::class,
            fn() => new MyService()
        );
    }
}
```

### Service Implementation

```php
use DecodeLabs\Kingdom\Service;
use DecodeLabs\Kingdom\ServiceTrait;
use DecodeLabs\Kingdom\ContainerAdapter;

class MyService implements Service
{
    use ServiceTrait;

    public function __construct(
        protected OtherService $other
    ) {
    }
}

// Service is automatically resolved with dependencies
$service = $kingdom->getService(MyService::class);
```

### Pure Service

```php
use DecodeLabs\Kingdom\PureService;
use DecodeLabs\Kingdom\PureServiceTrait;

class ConfigService implements PureService
{
    use PureServiceTrait;

    public function __construct()
    {
        // No dependencies needed
    }
}

$service = $kingdom->getService(ConfigService::class);
```

### Container Operations

```php
use DecodeLabs\Kingdom\ContainerAdapter;

$container = $kingdom->container;

// Register instance
$container->set(MyService::class, new MyService());

// Register factory
$container->setFactory(
    MyService::class,
    fn(ContainerAdapter $c) => new MyService($c->get(OtherService::class))
);

// Register type binding
$container->setType(
    ServiceInterface::class,
    ConcreteService::class,
    ['param1', 'param2']
);

// Prepare service
$container->prepare(
    MyService::class,
    fn(MyService $service) => $service->configure()
);

// Get service
$service = $container->get(MyService::class);

// Try get (nullable)
$service = $container->tryGet(MyService::class);

// Get or create
$service = $container->getOrCreate(MyService::class);
```

### Runtime Usage

```php
// Initialize kingdom
$kingdom = new MyKingdom();
$kingdom->initialize();

// Run application
$kingdom->run();

// Application terminates, shutdown() never returns
```

### Integration with Monarch

```php
use DecodeLabs\Monarch;

// Register kingdom
$kingdom = new MyKingdom();
Monarch::setKingdom($kingdom);

// Access services globally
$service = Monarch::getService(MyService::class);
```

### Fabric Integration

```php
use DecodeLabs\Fabric\Kingdom as FabricKingdom;

class Kingdom extends FabricKingdom
{
    public protected(set) string $name = 'My Application';

    public function initialize(): void
    {
        parent::initialize();

        // Configure container
        $this->container->setFactory(
            MyService::class,
            fn() => new MyService()
        );

        // Prepare services
        $this->container->prepare(
            HttpProfile::class,
            fn($profile) => $profile->add(Cors::class)
        );
    }
}
```

## Implementation Notes (for Contributors)

### Architecture

- **Container Adapter Pattern** — Kingdom uses an adapter pattern to abstract container implementation, allowing any PSR-11 container to be used while providing extended functionality.

- **Service Resolution** — Service resolution follows a three-step process: container lookup, interface resolution via `getOrCreate()`, and static service provision via `provideService()`.

- **Runtime Detection** — Runtime mode is detected by checking `HTTP_HOST` (HTTP), `argv` (CLI), or PHP SAPI. This allows automatic runtime selection.

- **Lazy Runtime Loading** — Runtime is loaded lazily from the container on first access, allowing runtime to be configured during initialization.

- **Service Types** — Three service types provide different instantiation patterns: `Service` (container-dependent), `PureService` (container-independent), and `EagreService` (marker for eager loading).

- **Kingdom Registration** — Kingdom instance is automatically registered in its own container under `Kingdom::class`, allowing services to access the kingdom if needed.

- **Lifecycle Management** — Kingdom manages application lifecycle through `initialize()`, `run()`, and `shutdown()` methods, delegating execution to runtime.

### Performance Considerations

- Lazy runtime loading avoids unnecessary instantiation
- Service resolution uses container caching when available
- Interface resolution via `getOrCreate()` provides efficient service creation

### Design Decisions

- **Adapter Pattern** — Using container adapter allows flexibility in container implementation while providing extended functionality beyond PSR-11.

- **Service Interface** — Requiring `Service` interface for `getService()` provides type safety and clear service boundaries.

- **Runtime Abstraction** — Separating runtime from kingdom allows different execution models (HTTP, CLI, etc.) without modifying kingdom logic.

- **Monarch Integration** — Delegating active kingdom management to Monarch provides global access while maintaining kingdom isolation.

- **Pure Services** — `PureService` interface allows services that don't need container access, simplifying service creation.

- **Trait-Based Implementation** — Providing traits for common implementations reduces boilerplate while allowing customization.

## Testing & Quality

**Code Quality:** 4/5 — Mature, well-structured codebase with comprehensive functionality and type safety.

**README Quality:** 3.5/5 — Good documentation with clear usage examples and integration guidance.

**Documentation:** 0/5 — No formal documentation beyond README.

**Tests:** 0/5 — No test suite currently.

See `composer.json` for supported PHP versions.

## Roadmap & Future Ideas

- Enhanced documentation and API reference
- Test suite implementation
- Service scope management (singleton, transient, scoped)
- Service decorator support
- Enhanced dependency injection features
- Performance optimizations
- Service metadata and introspection
- Additional runtime implementations

## References

- [PSR-11: Container Interface](https://www.php-fig.org/psr/psr-11/)
- [Decode Labs Chorus](https://github.com/decodelabs/chorus)
- [Kingdom Repository](https://github.com/decodelabs/kingdom)

