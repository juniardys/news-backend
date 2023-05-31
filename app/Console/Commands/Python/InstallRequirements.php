<?php

namespace App\Console\Commands\Python;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class InstallRequirements extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'python:install-requirements';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Installing python requirements';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $process = new Process(['pip', 'install', '-r', 'python/requirements.txt']);
        $process->run(function($type, $buffer) {
            if (Process::ERR === $type) {
                echo 'ERR > '.$buffer;
            } else {
                echo 'OUT > '.$buffer;
            }
        });

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }
    }
}
