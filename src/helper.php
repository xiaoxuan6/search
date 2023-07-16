<?php

/*
 * This file is part of james.xue/search.
 *
 * (c) vinhson <15227736751@qq.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 *
 */

use TitasGailius\Terminal\Terminal;
use Symfony\Component\Process\Process;
use Vinhson\Search\Services\CacheService;
use Vinhson\Search\Exceptions\RuntimeException;

if (! function_exists('is_valid_url')) {
    /**
     * @param $url
     * @return bool
     */
    function is_valid_url($url): bool
    {
        return filter_var($url, FILTER_VALIDATE_URL);
    }
}

if (! function_exists('is_win')) {
    /**
     * @return bool
     */
    function is_win(): bool
    {
        return ('win' == mb_substr(strtolower(PHP_OS), 0, 3));
    }
}

if (! function_exists('tap')) {
    function tap($value, Closure $closure)
    {
        $closure($value);

        return $value;
    }
}

if (! function_exists('cache')) {
    /**
     * @param null $key
     * @param string $message
     */
    function cache($key = null, string $message = '')
    {
        if ($key) {
            $class = new class () {
                /**
                 * @param $key
                 * @param $message
                 * @return string
                 */
                public function getConfig($key, $message): string
                {
                    $response = Terminal::builder()
                        ->with([
                            'key' => $key
                        ])
                        ->run('git config search.{{ $key }}');

                    return tap_abort(trim($response->output()), ! empty($message) ? $message : sprintf("获取 %s 失败，值为空", $key));
                }
            };

            return (new $class())->getConfig($key, $message);
        }

        return new CacheService();
    }
}

if (! function_exists('tap_abort')) {
    /**
     * @param $val
     * @param $message
     * @return mixed
     * @throws RuntimeException
     */
    function tap_abort($val, $message)
    {
        return tap($val, function ($item) use ($message) {
            if (! $item) {
                throw new RuntimeException($message);
            }
        });
    }
}

if(! function_exists('check_file')) {
    /**
     * @param $file
     * @return array
     */
    function check_file($file): array
    {
        if (strpos($file, './') !== false) {
            $file = getcwd() . trim($file, '.');
        }

        if (! $file or ! file_exists(realpath($file))) {
            return [false, "文件 {$file} 不存在"];
        }

        return [true, $file];
    }
}

if (! function_exists('create_process')) {
    /**
     * @param $command
     * @param $env
     * @return Process
     */
    function create_process($command, $env): Process
    {
        $process = Process::fromShellCommandline($command, null, $env);
        $process->run();

        return $process;
    }
}
