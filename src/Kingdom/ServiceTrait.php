<?php

/**
 * @package Kairos
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Kingdom;

/**
 * @phpstan-require-implements Service
 */
trait ServiceTrait
{
    public static function provideService(
        ContainerAdapter $container
    ): static {
        return $container->getOrCreate(static::class);
    }
}
