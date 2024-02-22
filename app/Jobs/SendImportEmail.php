<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Modules\Ecommerce\Entities\ImportResult;
class SendImportEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $result_id;
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
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $checkResult = ImportResult::findOrfail($this->result_id);
        $checkResult->rows_imported = 1212;
        $checkResult->save();
    }
}