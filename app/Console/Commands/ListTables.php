<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ListTables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:list-tables';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Lists all tables in the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tables = \DB::select('SHOW TABLES');
        $this->info('Tables in database:');
        foreach ($tables as $table) {
            $this->line('- ' . reset($table));
        }
    }
}
