<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Helpers;
use Modules\Ecommerce\Entities\PaymentCollection;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendMailable;


class SendNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $jobData;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($jobData)
    {
        $this->jobData = $jobData;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $receiver = $this->jobData['receiver'];
        $bodies = $this->jobData['bodies'];
        $channels = $this->jobData['channels'];
        $mailSubject = $this->jobData['mailSubject'];
        $details = $this->jobData['details'];

        Helpers::sendNotifications($receiver,$bodies,$channels,$mailSubject,$details);
        // return true;
    }
}
