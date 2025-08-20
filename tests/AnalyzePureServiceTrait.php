<?php

/**
 * @package Kingdom
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Kingdom\Tests;

use DecodeLabs\Kingdom\PureService;
use DecodeLabs\Kingdom\PureServiceTrait;

class AnalyzePureServiceTrait implements PureService
{
    use PureServiceTrait;

    public function __construct()
    {
    }
}
