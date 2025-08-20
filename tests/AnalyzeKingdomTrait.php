<?php

/**
 * @package Kingdom
 * @license http://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Kingdom\Tests;

use DecodeLabs\Kingdom;
use DecodeLabs\KingdomTrait;

class AnalyzeKingdomTrait implements Kingdom
{
    use KingdomTrait;

    public function initialize(): void
    {
    }
}
