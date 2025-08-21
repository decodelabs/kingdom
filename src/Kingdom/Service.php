<?php

/**
 * @package Kingdom
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Kingdom;

interface Service
{
    public static function provideService(
        ContainerAdapter $container
    ): static;
}
