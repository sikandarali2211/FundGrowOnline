<?php

namespace App\Services;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\OTPMail;

class OTPService
{
    /**
     * Send OTP via Email
     */
    public function sendOTPByEmail(string $email, string $otp, string $userName = ''): bool
    {
        try {
            Mail::to($email)->send(new OTPMail($otp, $userName));
            Log::info("OTP sent via email to: {$email}");
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to send OTP via email to {$email}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send OTP via WhatsApp using Twilio API
     */
    public function sendOTPByWhatsApp(string $phoneNumber, string $otp, string $userName = ''): bool
    {
        try {
            // Format phone number (remove + and add country code if needed)
            $formattedNumber = $this->formatPhoneNumber($phoneNumber);
            
            // Twilio WhatsApp API integration
            $accountSid = config('services.twilio.account_sid');
            $authToken = config('services.twilio.auth_token');
            $whatsappNumber = config('services.twilio.whatsapp_number');
            
            if (!$accountSid || !$authToken || !$whatsappNumber) {
                Log::warning('Twilio credentials not configured');
                return false;
            }

            $client = new \Twilio\Rest\Client($accountSid, $authToken);
            
            $message = "🔐 FundGrow Online Security OTP\n\n";
            $message .= "Hello " . ($userName ?: 'User') . ",\n\n";
            $message .= "Your security verification code is: *{$otp}*\n\n";
            $message .= "This code will expire in 10 minutes.\n\n";
            $message .= "If you didn't request this code, please ignore this message.\n\n";
            $message .= "Best regards,\nFundGrow Online Team";

            $client->messages->create(
                "whatsapp:{$formattedNumber}",
                [
                    'from' => "whatsapp:{$whatsappNumber}",
                    'body' => $message
                ]
            );

            Log::info("OTP sent via WhatsApp to: {$formattedNumber}");
            return true;
            
        } catch (\Exception $e) {
            Log::error("Failed to send OTP via WhatsApp to {$phoneNumber}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send OTP via SMS using Twilio SMS API
     */
    public function sendOTPBySMS(string $phoneNumber, string $otp, string $userName = ''): bool
    {
        try {
            // Format phone number
            $formattedNumber = $this->formatPhoneNumber($phoneNumber);
            
            // Twilio SMS API integration
            $accountSid = config('services.twilio.account_sid');
            $authToken = config('services.twilio.auth_token');
            $smsNumber = config('services.twilio.sms_number');
            
            if (!$accountSid || !$authToken || !$smsNumber) {
                Log::warning('Twilio credentials not configured');
                return false;
            }

            $client = new \Twilio\Rest\Client($accountSid, $authToken);
            
            $message = "FundGrow Online Security OTP: {$otp}. Valid for 10 minutes. Do not share this code.";
            $message .= ($userName ? " - {$userName}" : '');

            $client->messages->create(
                $formattedNumber,
                [
                    'from' => $smsNumber,
                    'body' => $message
                ]
            );

            Log::info("OTP sent via SMS to: {$formattedNumber}");
            return true;
            
        } catch (\Exception $e) {
            Log::error("Failed to send OTP via SMS to {$phoneNumber}: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send OTP via multiple channels (WhatsApp, Email, SMS)
     */
    public function sendOTPMultipleChannels(array $channels, string $otp, string $userName = '', string $email = '', string $phoneNumber = ''): array
    {
        $results = [];
        
        foreach ($channels as $channel) {
            switch ($channel) {
                case 'email':
                    if ($email) {
                        $results['email'] = $this->sendOTPByEmail($email, $otp, $userName);
                    }
                    break;
                    
                case 'whatsapp':
                    if ($phoneNumber) {
                        $results['whatsapp'] = $this->sendOTPByWhatsApp($phoneNumber, $otp, $userName);
                    }
                    break;
                    
                case 'sms':
                    if ($phoneNumber) {
                        $results['sms'] = $this->sendOTPBySMS($phoneNumber, $otp, $userName);
                    }
                    break;
            }
        }
        
        return $results;
    }

    /**
     * Format phone number for international use
     */
    private function formatPhoneNumber(string $phoneNumber): string
    {
        // Remove all non-digit characters
        $phoneNumber = preg_replace('/\D/', '', $phoneNumber);
        
        // Add country code if not present (assuming Pakistan +92)
        if (!str_starts_with($phoneNumber, '92') && !str_starts_with($phoneNumber, '+92')) {
            // If number starts with 0, replace with 92
            if (str_starts_with($phoneNumber, '0')) {
                $phoneNumber = '92' . substr($phoneNumber, 1);
            } else {
                // Assume it's a local number and add 92
                $phoneNumber = '92' . $phoneNumber;
            }
        }
        
        return '+' . $phoneNumber;
    }

    /**
     * Generate a 6-digit OTP
     */
    public function generateOTP(): string
    {
        return str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Validate OTP format
     */
    public function validateOTPFormat(string $otp): bool
    {
        return preg_match('/^[0-9]{6}$/', $otp);
    }
}
