<?php

namespace App\Jobs;

use App\Http\Controllers\PushNotificationController;
use App\Models\Device;
use App\Models\NotificationJob;
use App\Models\Notification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendToDeviceJob implements ShouldQueue
{
    use Dispatchable, Queueable;

    public function __construct(
        public int $notificationJobId,
        public int $deviceId
    ) {}

    public function handle(): void
    {
        $notificationJob = NotificationJob::find($this->notificationJobId);
        $device = Device::find($this->deviceId);

        $status = 'failed';

        if (!$notificationJob || !$device) {
            Log::error('Notification job or device not found', [
                'notification_id' => $this->notificationJobId,
                'device_id' => $this->deviceId,
            ]);
        } else {
            $notificationSender = (new PushNotificationController)->sendPushNotification($device->fcm_token, $notificationJob->text);

            Log::debug("Notification ID {$notificationJob->id} Device ID {$device->id} FCM {$device->fcm_token}");

            if ($notificationSender) {
                $status = 'sent';
                Log::info("Notification ID {$notificationJob->id} sent to device with ID {$device->id}");
            } else {
                Log::error('Error sending notification', [
                    'notification_id' => $notificationJob->id,
                    'device_id' => $device->id,
                ]);
            }
        }

        $notification = new Notification();
        $notification->device_id = $device?->id;
        $notification->notification_job_id = $notificationJob?->id;
        $notification->status = $status;
        $notification->save();
    }
}
