<?php

/**
 * @package Kingdom
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Kingdom;

trait PureServiceTrait
{
    public static function provideService(
        ContainerAdapter $container
    ): static {
        if ($container->has(static::class)) {
            return $container->get(static::class);
        }

        return static::providePureService();
    }

    public static function providePureService(): static
    {
        return new static();
    }
}
