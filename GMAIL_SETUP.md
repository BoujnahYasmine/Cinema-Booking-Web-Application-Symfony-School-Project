# Gmail SMTP Configuration Guide

To send ticket emails via Gmail, you need to configure the MAILER_DSN in your `.env.local` file.

## Step 1: Enable Gmail App Password

1. Go to your Google Account: https://myaccount.google.com/
2. Navigate to **Security** → **2-Step Verification** (enable it if not already)
3. Go to **App passwords**: https://myaccount.google.com/apppasswords
4. Select **Mail** and **Other (Custom name)**
5. Enter "Cinema Booking System" as the name
6. Click **Generate**
7. Copy the 16-character password (you'll use this in step 3)

## Step 2: Update .env.local

Add or update these lines in your `.env.local` file:

```env
# Gmail SMTP Configuration
MAILER_DSN=smtp://your-email@gmail.com:your-app-password@smtp.gmail.com:587
MAILER_FROM=your-email@gmail.com
```

Replace:
- `your-email@gmail.com` with your Gmail address
- `your-app-password` with the 16-character app password from Step 1

## Example:

```env
MAILER_DSN=smtp://john.doe@gmail.com:abcd efgh ijkl mnop@smtp.gmail.com:587
MAILER_FROM=john.doe@gmail.com
```

**Note:** Remove spaces from the app password when adding it to the DSN.

## Step 3: Test Email Sending

After configuration, test by booking a ticket. The email will be sent to the customer's email address.

## Troubleshooting

**Email not sending?**
- Verify the app password is correct (no spaces)
- Check that 2-Step Verification is enabled
- Ensure the MAILER_DSN format is correct
- Check server logs for error messages

**For development/testing:**
You can use Mailtrap or similar services instead of Gmail for testing.

