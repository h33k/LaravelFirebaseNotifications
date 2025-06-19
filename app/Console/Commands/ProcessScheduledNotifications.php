<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\NotificationJob;
use App\Jobs\SendNotificationJob;

class ProcessScheduledNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-scheduled-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $notifications = NotificationJob::where('status', 'planned')
            ->where('date_sent', '<=', now())
            ->get();

        foreach ($notifications as $notification) {
            SendNotificationJob::dispatch($notification);
        }

        return Command::SUCCESS;
    }
}
