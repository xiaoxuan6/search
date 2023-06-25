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

use Symfony\Component\Process\Process;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\{InputArgument, InputInterface};

class NewCommand extends Command
{
    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('new')
            ->setDescription('Create a new Laravel application')
            ->addArgument('name', InputArgument::OPTIONAL, '', 'laravel');
    }

    /**
     * Execute the command.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->write(PHP_EOL . '  <fg=red> _                               _
  | |                             | |
  | |     __ _ _ __ __ ___   _____| |
  | |    / _` | \'__/ _` \ \ / / _ \ |
  | |___| (_| | | | (_| |\ V /  __/ |
  |______\__,_|_|  \__,_| \_/ \___|_|</>' . PHP_EOL . PHP_EOL);

        $directory = getcwd() . '/' . $input->getArgument('name');

        $commands = [
            "composer create-project laravel/laravel \"$directory\" --remove-vcs --prefer-dist",
        ];

        if (PHP_OS_FAMILY != 'Windows') {
            $commands[] = "chmod +x \"$directory/artisan\"";
        }

        if (($process = $this->runCommands($commands, $input, $output))->isSuccessful()) {
            $output->writeln('  <bg=blue;fg=white> INFO </> Application ready! <options=bold>Build something amazing.</>' . PHP_EOL);
        }

        return $process->getExitCode();
    }

    /**
     * Run the given commands.
     *
     * @param array $commands
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return Process
     */
    protected function runCommands(array $commands, InputInterface $input, OutputInterface $output): Process
    {
        $process = Process::fromShellCommandline(implode(' && ', $commands));

        $process->setTimeout(600);
        $process->run(function ($type, $line) use ($output): void {
            $output->write('    ' . $line);
        });

        return $process;
    }
}
