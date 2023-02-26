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

namespace Vinhson\Search\Commands;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Symfony\Component\Console\Exception\ExceptionInterface;

trait CallTrait
{
    /**
     * @throws ExceptionInterface
     */
    public function call($name, $arguments, $output = NullOutput::class)
    {
        $command = $this->getApplication()->find($name);
        $greetInput = new ArrayInput($arguments);
        $command->run($greetInput, new $output());
    }
}
