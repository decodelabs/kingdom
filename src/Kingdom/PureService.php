<?php

/**
 * @package Kingdom
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Kingdom;

interface PureService extends Service
{
    public static function providePureService(): static;
    public function __construct();
}
