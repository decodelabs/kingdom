<?php

/**
 * @package Kingdom
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs;

use DecodeLabs\Kingdom\ContainerAdapter;
use DecodeLabs\Kingdom\Runtime;
use DecodeLabs\Kingdom\RuntimeMode;
use DecodeLabs\Kingdom\Service;

use const PHP_SAPI;

/**
 * @phpstan-require-implements Kingdom
 */
trait KingdomTrait
{
    public Runtime $runtime {
        get => $this->runtime ??= $this->container->get(Runtime::class);
    }

    public protected(set) ContainerAdapter $container;

    public function __construct(
        ContainerAdapter $container
    ) {
        $this->container = $container;
        $container->set(Kingdom::class, $this);
    }

    protected function detectRuntimeMode(): RuntimeMode
    {
        if (isset($_SERVER['HTTP_HOST'])) {
            return RuntimeMode::Http;
        } elseif (isset($_SERVER['argv'])) {
            return RuntimeMode::Cli;
        }

        switch (PHP_SAPI) {
            case 'cli':
            case 'phpdbg':
                return RuntimeMode::Cli;

            case 'apache':
            case 'apache2filter':
            case 'apache2handler':
            case 'fpm-fcgi':
            case 'cgi-fcgi':
            case 'phttpd':
            case 'pi3web':
            case 'thttpd':
                return RuntimeMode::Http;
        }

        throw Exceptional::UnexpectedValue(
            message: 'Unable to detect run mode (' . PHP_SAPI . ')'
        );
    }

    public function run(): void
    {
        $this->runtime->initialize();
        $this->runtime->run();
    }

    public function shutdown(): never
    {
        $this->runtime->shutdown();
    }


    public function getService(
        string $class,
    ): Service {
        // @phpstan-ignore-next-line
        if (!is_a($class, Service::class, true)) {
            throw Exceptional::Runtime(
                message: 'Type "' . $class . '" is not a service'
            );
        }

        if ($this->container->has($class)) {
            return $this->container->get($class);
        }

        if (interface_exists($class)) {
            return $this->container->getOrCreate($class);
        }

        return $class::provideService($this->container);
    }
}
