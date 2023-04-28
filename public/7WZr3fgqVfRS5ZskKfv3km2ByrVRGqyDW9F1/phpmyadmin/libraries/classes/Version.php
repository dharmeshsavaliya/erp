<?php

declare(strict_types=1);

namespace PhpMyAdmin;

use const VERSION_SUFFIX;

/**
 * This class is generated by scripts/console.
 *
 * @see \PhpMyAdmin\Command\SetVersionCommand
 */
final class Version
{
    // The VERSION_SUFFIX constant is defined at libraries/constants.php
    public const VERSION = '5.2.0' . VERSION_SUFFIX;

    public const SERIES = '5.2';

    public const MAJOR = 5;

    public const MINOR = 2;

    public const PATCH = 0;

    public const ID = 50200;

    public const PRE_RELEASE_NAME = '';

    public const IS_DEV = false;
}
