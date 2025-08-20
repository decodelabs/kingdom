<?php

/**
 * @package Kingdom
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs;

use DecodeLabs\Kingdom\ContainerAdapter;
use DecodeLabs\Kingdom\Runtime;
use DecodeLabs\Kingdom\Service;

interface Kingdom
{
    public string $name { get; }
    public Runtime $runtime { get; }
    public ContainerAdapter $container { get; }

    public function initialize(): void;
    public function run(): void;
    public function shutdown(): never;

    /**
     * @template T of Service
     * @param class-string<T> $class
     * @return T
     */
    public function getService(
        string $class,
    ): Service;
}
