<?php

/**
 * @package Kingdom
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Kingdom;

use Closure;
use Psr\Container\ContainerInterface;

interface ContainerAdapter
{
    /**
     * @template T of object
     * @param class-string<T> $type
     * @return ?T
     */
    public function tryGet(
        string $type
    ): ?object;

    /**
     * @template T of object
     * @param class-string<T> $type
     * @return T
     */
    public function get(
        string $type
    ): object;

    /**
     * @template T of object
     * @param class-string<T> $type
     * @return T
     */
    public function getOrCreate(
        string $type
    ): object;

    /**
     * @template T of object
     * @param class-string<T> $type
     */
    public function has(
        string $type
    ): bool;

    /**
     * @template T of object
     * @param class-string<T> $type
     * @param T $instance
     */
    public function set(
        string $type,
        object $instance
    ): void;

    /**
     * @template T of object
     * @param class-string<T> $type
     * @param Closure:T $factory
     */
    public function setFactory(
        string $type,
        Closure $factory
    ): void;

    /**
     * @template T of object
     * @template T2 of T
     * @param class-string<T> $type
     * @param class-string<T2> $instanceType
     */
    public function setType(
        string $type,
        string $instanceType
    ): void;

    public function getPsrContainer(): ContainerInterface;

    /**
     * @param class-string $type
     */
    public function prepare(
        string $type,
        Closure $callback
    ): void;
}
