<?php

declare(strict_types=1);

namespace PhpMyAdmin\ConfigStorage\Features;

use PhpMyAdmin\Dbal\TableName;
use PhpMyAdmin\Dbal\DatabaseName;

/**
 * @psalm-immutable
 */
final class CentralColumnsFeature
{
    /** @var DatabaseName */
    public $database;

    /** @var TableName */
    public $centralColumns;

    public function __construct(DatabaseName $database, TableName $centralColumns)
    {
        $this->database = $database;
        $this->centralColumns = $centralColumns;
    }
}
