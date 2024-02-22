<?php
namespace App\Traits;
use App\SmsContent;
use Twilio\Rest\Client;


trait TwilioTrait
{
    /**
     * Send an OTP to the given phone number.
     *
     * @param string $phoneNumber
     * @param string $body
     * @return bool
    */
    public function sendOtpToMobile($phoneNumber, $body)
    {
        try {
            $sid = env('TWILIO_SID');
            $token = env('TWILIO_AUTH_TOKEN');
            $twilio = new Client($sid, $token);
            $phoneNumber = '+91' . $phoneNumber;

            $message = $twilio->messages->create(
                $phoneNumber,
                [
                    'from' => env('TWILIO_NUMBER'),
                    'body' => $body,
                ]
            );
            \Log::info($message);
            return $message;
        } catch (\Exception $exception) {
            // Log the error
            \Log::error($exception->getMessage());
            return false;
        }
    }
}
