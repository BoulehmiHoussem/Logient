<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Link;
use Carbon\Carbon;

class DeleteExpiredLinks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'links:delete-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes links older than 24 hours.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
         // Get all links older than 24 hours
         Link::where('created_at', '<=', Carbon::now()->subHours(24))->delete();
        
         $this->info('Expired links have been deleted.');
 
         return 0;
    }
}
