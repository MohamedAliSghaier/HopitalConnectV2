<?php

namespace App\Service;

use Twilio\Rest\Client;
use Twilio\Exceptions\TwilioException;
use Psr\Log\LoggerInterface;

class TwilioService
{
    private $client;
    private $from;
    private $isDummy;
    private $logger;

    public function __construct(string $sid, string $token, string $from, LoggerInterface $logger = null)
    {
        $this->from = $from;
        $this->logger = $logger;
        
        try {
            $this->client = new Client($sid, $token);
            $this->isDummy = false;
            
            if ($this->logger) {
                $this->logger->info('Twilio client initialized successfully');
            }
        } catch (\Exception $e) {
            if ($this->logger) {
                $this->logger->error('Failed to initialize Twilio client: ' . $e->getMessage());
            }
            throw $e;
        }
    }

    public function sendSms(string $to, string $message): string
    {
        if ($this->logger) {
            $this->logger->info('Attempting to send SMS', [
                'to' => $to,
                'from' => $this->from,
                'message_length' => strlen($message)
            ]);
        }

        // Format the phone number to ensure it's in E.164 format
        $formattedNumber = $this->formatPhoneNumber($to);
        
        if ($this->isDummy) {
            // Log the SMS instead of sending it
            error_log("DUMMY SMS: To: $formattedNumber, From: {$this->from}, Message: $message");
            return 'dummy_sid_' . uniqid();
        }

        try {
            $sms = $this->client->messages->create(
                $formattedNumber,
                [
                    'from' => $this->from,
                    'body' => $message
                ]
            );
            
            if ($this->logger) {
                $this->logger->info('SMS sent successfully', ['sid' => $sms->sid]);
            }
            
            return $sms->sid;
        } catch (TwilioException $e) {
            if ($this->logger) {
                $this->logger->error('Twilio SMS Error: ' . $e->getMessage(), [
                    'to' => $formattedNumber,
                    'from' => $this->from,
                    'error_code' => $e->getCode()
                ]);
            }
            error_log("Twilio SMS Error: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Format phone number to E.164 format
     * 
     * @param string $phoneNumber
     * @return string
     */
    private function formatPhoneNumber(string $phoneNumber): string
    {
        // Remove any non-digit characters
        $number = preg_replace('/[^0-9+]/', '', $phoneNumber);
        
        // If the number doesn't start with +, assume it's a French number and add +33
        if (!str_starts_with($number, '+')) {
            // Remove leading 0 if present
            $number = ltrim($number, '0');
            $number = '+33' . $number;
        }
        
        if ($this->logger) {
            $this->logger->info('Formatted phone number', [
                'original' => $phoneNumber,
                'formatted' => $number
            ]);
        }
        
        // Log the formatted number for debugging
        error_log("Formatted phone number: " . $number);
        
        return $number;
    }
}