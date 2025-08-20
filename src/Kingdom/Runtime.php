<?php

/**
 * @package Kairos
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Kingdom;

interface Runtime
{
    public RuntimeMode $mode { get; }

    public function initialize(): void;
    public function run(): void;
    public function shutdown(): never;
}
