# Gmail Setup Instructions for yasmineboujnah07@gmail.com

## Step 1: Enable Gmail App Password

1. Go to your Google Account: https://myaccount.google.com/
2. Click on **Security** (left sidebar)
3. Under "How you sign in to Google", find **2-Step Verification**
   - If it's OFF, click it and enable it (you'll need your phone)
   - If it's already ON, proceed to step 4
4. After 2-Step Verification is enabled, go back to Security
5. Scroll down and click on **App passwords** (or go directly to: https://myaccount.google.com/apppasswords)
6. You may need to sign in again
7. Select:
   - **App**: Mail
   - **Device**: Other (Custom name)
   - **Name**: Enter "Cinema Booking System"
8. Click **Generate**
9. **Copy the 16-character password** (it looks like: `abcd efgh ijkl mnop`)
   - ⚠️ **IMPORTANT**: You can only see this password once! Copy it now.

## Step 2: Update .env.local

Open your `.env.local` file and update the MAILER_DSN line:

```env
MAILER_DSN=smtp://yasmineboujnah07@gmail.com:YOUR_APP_PASSWORD_HERE@smtp.gmail.com:587
MAILER_FROM=yasmineboujnah07@gmail.com
```

**Replace `YOUR_APP_PASSWORD_HERE`** with the 16-character password from Step 1 (remove spaces).

### Example:
If your app password is `abcd efgh ijkl mnop`, the line should be:
```env
MAILER_DSN=smtp://yasmineboujnah07@gmail.com:abcdefghijklmnop@smtp.gmail.com:587
```

## Step 3: Test Email Sending

1. Clear cache: `php bin/console cache:clear`
2. Book a ticket as a customer
3. Check the customer's email inbox for the ticket

## Troubleshooting

**"Invalid credentials" error?**
- Make sure you're using the App Password, NOT your regular Gmail password
- Verify 2-Step Verification is enabled
- Check that there are no spaces in the app password in the DSN

**Email not received?**
- Check spam/junk folder
- Verify the customer's email address is correct
- Check server logs: `var/log/dev.log`

**Still not working?**
- Try using port 465 with SSL instead:
  ```env
  MAILER_DSN=smtp://yasmineboujnah07@gmail.com:YOUR_APP_PASSWORD@smtp.gmail.com:465
  ```

## Security Note

⚠️ **Never commit `.env.local` to git!** It contains sensitive information.

