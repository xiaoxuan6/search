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
        SetList::EARLY_RETURN, # 提前返回
        SetList::TYPE_DECLARATION, # 类型声明
        # SetList::DEAD_CODE, # 死代码(去除方法声明的注释)
        # SetList::CODE_QUALITY, # 代码质量
    ]);
};
