<?php

namespace Xgbnl\Guard\Commands;

use Illuminate\Console\Command;

class GuardCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected string $signature = 'guard:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'Install custom guard';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): void
    {
        $this->call('vendor:publish', [
            "--provider" => "Xgbnl\Guard\GuardServiceProvider"
        ]);
    }
}
