<?php

/*
 * This file is part of james.xue/search.
 *
 * (c) xiaoxuan6 <1527736751@qq.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 *
 */

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\{LevelSetList, SetList};

return [
    'php-cs-fixer' => [
        'header' => <<<HEADER
This file is part of james.xue/search.

(c) xiaoxuan6 <1527736751@qq.com>

This source file is subject to the MIT license that is bundled
with this source code in the file LICENSE.

HEADER,

        'in' => [
            __DIR__,
            __DIR__ . DIRECTORY_SEPARATOR . 'bin',
        ],

        /**
         * Adds rules that filenames must not match.
         *
         * You can use patterns (delimited with / sign) or simple strings.
         *
         *     $finder->notPath('some/special/dir')
         *     $finder->notPath('/some\/special\/dir/') // same as above
         *     $finder->notPath(['some/file.txt', 'another/file.log'])
         */
        'not-path' => [

        ],

        /**
         * Excludes directories.
         *
         * Directories passed as argument must be relative to the ones defined with the `in()` method. For example:
         *
         *     $finder->in(__DIR__)->exclude('ruby');
         */
        'exclude' => [
            __DIR__ . '/vendor',
        ],


        /**
         * Adds rules that files must match.
         *
         * You can use patterns (delimited with / sign), globs or simple strings.
         *
         *     $finder->name('/\.php$/')
         *     $finder->name('*.php') // same as above, without dot files
         *     $finder->name('test.php')
         *     $finder->name(['test.py', 'test.php'])
         */
        'name' => [
            '*.php',
            'packet'
        ],

        /**
         * Adds rules that files must not match.
         */
        'not-name' => [

        ],

        'rules' => [

        ]
    ],

    'rector' => [
        'path' => [
            __DIR__ . DIRECTORY_SEPARATOR . 'bin',
            __DIR__ . DIRECTORY_SEPARATOR . 'src',
        ],

        'sets' => [
            LevelSetList::UP_TO_PHP_82,
            SetList::INSTANCEOF,
            SetList::TYPE_DECLARATION,
            SetList::EARLY_RETURN,
            SetList::PHP_82,
        ],

        'callable' => function (RectorConfig $rectorConfig) {
            $rectorConfig->skip([
                __DIR__ . '/src/Commands/EnvCommand.php',
                __DIR__ . '/src/Commands/Actions/FileUploadCommand.php',
                __DIR__ . '/src/Response.php',
                __DIR__ . '/src/Api/Config/Client.php'
            ]);
        }
    ]
];
