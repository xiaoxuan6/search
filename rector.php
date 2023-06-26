<?php

declare(strict_types=1);

/*
 * This file is part of james.xue/search.
 *
 * (c) vinhson <15227736751@qq.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 *
 */

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\{LevelSetList, SetList};

return static function (RectorConfig $rectorConfig) {

    $rectorConfig->importNames();
    $rectorConfig->importShortClasses(false);

    $rectorConfig->paths([
        __DIR__ . '/src',
    ]);

    $rectorConfig->skip([
        __DIR__ . '/src/Commands/EnvCommand.php',
        __DIR__ . '/src/Commands/Actions/FileUploadCommand.php',
        __DIR__ . '/src/Response.php',
    ]);

    $rectorConfig->sets([
        LevelSetList::UP_TO_PHP_74,
        SetList::PHP_74,
        SetList::EARLY_RETURN,
        SetList::TYPE_DECLARATION,
    ]);
};
