<?php
  
namespace App\Console\Commands;

use App\Jobs\Backup;
use Illuminate\Console\Command;
use Carbon\Carbon;
   
class DatabaseBackUp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database:backup';
  
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    
  
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }
  
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
      
        Backup::dispatch();
    }
}