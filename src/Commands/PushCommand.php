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

namespace Vinhson\Search\Commands;

use Illuminate\Support\Collection;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\{InputArgument, InputInterface, InputOption};

use function Laravel\Prompts\text;

class PushCommand extends Command
{
    protected function configure()
    {
        $this->setName('git:push')
            ->setAliases(['gh'])
            ->setDescription('git 提交数据')
            ->addArgument('message', InputArgument::OPTIONAL, 'git 提交信息', '')
            ->addOption('amend', 'a', InputOption::VALUE_OPTIONAL, '是否修改最后一次提交信息', false)
            ->addOption('no-edit', 'e', InputOption::VALUE_OPTIONAL, '是否使用最后一次提交信息', false)
            ->addOption('force', 'f', InputOption::VALUE_OPTIONAL, '是否强制提交', false)
            ->addOption('tag', 't', InputOption::VALUE_OPTIONAL, 'tag 版本号', '');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        return collect([
            'git pull',
            'git status',
            'git add .'
        ])->tap(function () use ($input, &$target, &$noEdit, &$message): void {
            $target = true;
            $noEdit = $input->getOption('no-edit');

            $message = str($input->getArgument('message'))
                ->trim()
                ->whenEmpty(fn (): string => text(
                    label: '请输入提交信息',
                    validate: fn ($value): string => str($value)->trim()->isEmpty() ? '提交信息不能为空' : true
                ))
                ->toString();
        })->when($input->getOption('amend') && $noEdit, function (Collection $collect, $value) use (&$target): Collection {
            $target = false;

            return $collect->push(...[
                'git commit --amend --no-edit',
                'git push -f'
            ]);
        })->when($input->getOption('amend') && ! $noEdit, function (Collection $collect, $amend) use ($message, &$target): Collection {
            $target = false;

            return $collect->push(...[
                'git commit --amend -m"' . $message . '"',
                'git push -f'
            ]);
        })->when($input->getOption('force'), function (Collection $collect, $force) use ($message, &$target): Collection {
            $target = false;

            return $collect->push(...[
                'git commit -m"' . $message . '"',
                'git push -f'
            ]);
        })->when($target, fn (Collection $collect, $value): Collection => $collect->push(...[
            'git commit -m"' . $message . '"',
            'git push'
        ]))->when($input->getOption('tag'), fn (Collection $collect, $tag): Collection => $collect->push(...["git tag {$tag}", "git push origin {$tag}"]))->pipe(function (Collection $collect) use ($output): int {
            $process = Process::fromShellCommandline($collect->join(' && '), getcwd());
            $process->run(fn ($type, $line) => $output->writeln($line));

            if (! $process->isSuccessful()) {
                $output->writeln("<error>提交失败：{$process->getErrorOutput()}</error>");

                return self::FAILURE;
            }

            return self::SUCCESS;
        });
    }
}
