<?php
// src/Service/SmsService.php
namespace App\Service;

use Twilio\Rest\Client;
use Twilio\Exceptions\TwilioException;

class SmsService
{
    private Client $client;
    private string $twilioNumber;

    public function __construct(string $sid, string $token, string $twilioNumber)
    {
        $this->client = new Client($sid, $token);
        $this->twilioNumber = $twilioNumber;
    }

    public function sendSms(string $telephone, string $message): bool
    {
        try {
            $this->client->messages->create(
                $this->formatPhoneNumber($telephone),
                [
                    'from' => $this->twilioNumber,
                    'body' => $message
                ]
            );
            return true;
        } catch (TwilioException $e) {
            // Loguer l'erreur si nécessaire
            return false;
        }
    }

    private function formatPhoneNumber(string $number): string
    {
        // Normalisation du numéro de téléphone
        return '+'.preg_replace('/[^0-9]/', '', $number);
    }
}