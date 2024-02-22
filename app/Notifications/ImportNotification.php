<?php
namespace App\Notifications;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Config;
use Modules\Ecommerce\Entities\ImportResult;
class ImportNotification extends Notification implements ShouldQueue
{
    use Queueable;
    protected $token;
    protected $appUrl;
    protected $result_id;
    /**
    * Create a new notification instance.
    *
    * @return void
    */
    public function __construct($result_id)
    {
        $this->result_id = $result_id;
    }
    /**
    * Get the notification's delivery channels.
    *
    * @param  mixed  $notifiable
    * @return array
    */
    public function via($notifiable)
    {
        return ['database'];
    }
    /**
    * Get the array representation of the notification.
    *
    * @param  mixed  $notifiable
    * @return array
    */
    public function toArray($notifiable)
    {
        $checkResult = ImportResult::findOrfail($this->result_id);
        $checkResult->rows_imported = 898989;
        $checkResult->save();

        return [
            //
        ];
    }
}