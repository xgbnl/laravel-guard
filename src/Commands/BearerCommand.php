<?php

namespace Xgbnl\Bearer\Commands;

use Illuminate\Console\Command;

class BearerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'guard:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install laravel guard';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->call('vendor:publish', [
            "--provider" => "Xgbnl\Bearer\Providers\BearerServiceProvider"
        ]);
    }
}
