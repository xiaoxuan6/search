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

namespace Vinhson\Search\Commands\Support;

class UploadSupport
{
    protected array $parse;

    protected string $command = 'curl -k -sD - --upload-file "%REALPATH" https://transfer.sh/"%FILENAME"';

    protected bool $disableShowDelUrl = true;

    protected string $delCommand = ' | grep -i -E "transfer\.sh|x-url-delete"';

    protected string $urlCommand = ' | grep -i -E "^https://transfer\.sh"';

    protected string $pasCommand = '';

    public function __construct(array $needles, $password = '')
    {
        $this->parse = $needles;
        $this->pasCommand = $password ? ' -H "X-Encrypt-Password: ' . $password . '"' : '';
    }

    public function setPassword(string $password): UploadSupport
    {
        $this->pasCommand = ' -H "X-Encrypt-Password: ' . $password . '"';

        return $this;
    }

    public function disableShowDelUrl(bool $disable = false): UploadSupport
    {
        $this->disableShowDelUrl = $disable;

        return $this;
    }

    public function toString(): string
    {
        $command = str_replace(["%REALPATH", "%FILENAME"], $this->parse, $this->command);

        return $this->disableShowDelUrl ? sprintf("%s%s%s", $command, $this->pasCommand, $this->delCommand) : sprintf("%s%s%s", $command, $this->pasCommand, $this->urlCommand);
    }
}
