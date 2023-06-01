<?php

namespace App\Console\Commands\Python;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class FetchSourcesAndCategories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'python:fetch-sources-and-categories';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch sources and categories';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $process = new Process(['python3', '-u', 'python/fetch_news.py']);
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
