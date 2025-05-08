<?php
// src/Service/SmsService.php
namespace App\Service;

use Twilio\Rest\Client;

class SmsService
{
    private Client $client;
    private string $twilioNumber;

    public function __construct(string $sid, string $token, string $twilioNumber)
    {
        $this->client = new Client($sid, $token);
        $this->twilioNumber = $twilioNumber;
    }

    public function sendResetCode(string $phoneNumber, string $code): void
    {
        $this->client->messages->create(
            $phoneNumber,            // ex: +2162937XXXX
            [
                'from' => $this->twilioNumber, // ex: +1XXXYYYZZZZ (votre Twilio number)
                'body' => "Votre code de réinitialisation est : $code"
            ]
        );
    }
}