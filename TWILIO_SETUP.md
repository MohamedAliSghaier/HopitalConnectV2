# Twilio SMS Setup

To enable SMS functionality in this application, you need to set up your Twilio credentials. Follow these steps:

## 1. Create a Twilio Account

If you don't already have a Twilio account, sign up at [https://www.twilio.com/try-twilio](https://www.twilio.com/try-twilio).

## 2. Get Your Twilio Credentials

Once you have a Twilio account, you'll need to get your Account SID and Auth Token from the Twilio Console.

## 3. Configure Your Environment

Create a `.env.local` file in the root of your project with the following content:

```
# Twilio Configuration
TWILIO_ACCOUNT_SID=your_account_sid_here
TWILIO_AUTH_TOKEN=your_auth_token_here
TWILIO_FROM_NUMBER=your_twilio_phone_number_here
TWILIO_DSN=twilio://your_account_sid_here:your_auth_token_here@default?from=your_twilio_phone_number_here
```

Replace the placeholder values with your actual Twilio credentials:
- `your_account_sid_here`: Your Twilio Account SID
- `your_auth_token_here`: Your Twilio Auth Token
- `your_twilio_phone_number_here`: Your Twilio phone number (in E.164 format, e.g., +1234567890)

## 4. Test Your Configuration

You can test your Twilio configuration by running the following command:

```bash
php bin/console app:test-sms +1234567890 "Test message"
```

Replace `+1234567890` with the phone number you want to send the test SMS to.

## Troubleshooting

If you're having issues with SMS sending, check the following:

1. Make sure your Twilio credentials are correct
2. Ensure your Twilio account has sufficient credits
3. Check that the phone number format is correct (should be in E.164 format)
4. Verify that your Twilio account is not in trial mode (trial accounts can only send to verified numbers)

For more information, refer to the [Twilio documentation](https://www.twilio.com/docs). 