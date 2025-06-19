<?php

namespace App\Jobs;

use App\Models\Device;
use App\Models\NotificationJob;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendNotificationJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public NotificationJob $notificationJob)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info("Starting notification processing. ID: {$this->notificationJob->id}");

        $error = null;

        Device::chunk(200, function ($devices) use (&$error) {
            try {
                foreach ($devices as $device) {
                    SendToDeviceJob::dispatch($this->notificationJob->id, $device->id);
                }
            } catch (Throwable $e) {
                $error = $e;
                return false;
            }
        });

        if ($error) {
            Log::error('Error while dispatching notification to devices', [
                'notification_id' => $this->notificationJob->id,
                'message' => $error->getMessage(),
            ]);

            $this->notificationJob->update(['status' => 'failed']);
        } else {
            $this->notificationJob->update(['status' => 'dispatched']);

            Log::info("Device jobs queued successfully. NotificationJob ID: {$this->notificationJob->id}");
        }
    }

}
