<?php

/**
 * Kingdom
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\Kingdom;

enum RuntimeMode
{
    case Http;
    case Cli;
}
