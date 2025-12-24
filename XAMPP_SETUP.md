# XAMPP MySQL Setup Guide

## Database Configuration for XAMPP

### Step 1: Start XAMPP Services
1. Open XAMPP Control Panel
2. Start **Apache** and **MySQL** services

### Step 2: Create Database
1. Open phpMyAdmin: http://localhost/phpmyadmin
2. Create a new database named: `cinemadb`
3. Set collation to: `utf8mb4_unicode_ci`

### Step 3: Configure Environment
Create a `.env.local` file in the project root with:

```env
# XAMPP MySQL Configuration (Default: no password)
DATABASE_URL="mysql://root:@127.0.0.1:3306/cinemadb?serverVersion=8.0&charset=utf8mb4"

# If you have a MySQL password, use:
# DATABASE_URL="mysql://root:yourpassword@127.0.0.1:3306/cinemadb?serverVersion=8.0&charset=utf8mb4"

# Application Secret
APP_SECRET=your-secret-key-here-change-in-production

# Mailer (for development)
MAILER_DSN=null://null
```

### Step 4: Run Migrations
```bash
php bin/console doctrine:migrations:migrate
```

### Step 5: Create Admin User
```bash
php bin/console app:create-admin
```

### Step 6: Access Application
- Public: http://localhost/cenima_ticket/public/
- Admin: http://localhost/cenima_ticket/public/admin/dashboard

## Troubleshooting

### Connection Refused
- Make sure MySQL is running in XAMPP
- Check if port 3306 is available
- Verify database name matches in `.env.local`

### Access Denied
- Default XAMPP MySQL user is `root` with no password
- If you set a password, update `DATABASE_URL` accordingly

### Database Not Found
- Create the database manually in phpMyAdmin
- Or use: `php bin/console doctrine:database:create`

