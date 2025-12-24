# 🚀 Quick Start Guide - Cinema Booking System

## XAMPP Setup (5 Minutes)

### 1. Start XAMPP
- Open XAMPP Control Panel
- Start **Apache** and **MySQL**

### 2. Create Database
```sql
-- Open phpMyAdmin: http://localhost/phpmyadmin
-- Create database: cinemadb
-- Collation: utf8mb4_unicode_ci
```

### 3. Configure Environment
Create `.env.local` in project root:
```env
DATABASE_URL="mysql://root:@127.0.0.1:3306/cinemadb?serverVersion=8.0&charset=utf8mb4"
APP_SECRET=change-this-to-random-string
MAILER_DSN=null://null
```

### 4. Run Setup Commands
```bash
# Install dependencies (if not done)
composer install

# Run migrations
php bin/console doctrine:migrations:migrate

# Create admin user
php bin/console app:create-admin
```

### 5. Access Application
- **Public Site**: http://localhost/cenima_ticket/public/
- **Admin Panel**: http://localhost/cenima_ticket/public/admin/dashboard
- **Login**: http://localhost/cenima_ticket/public/login

## Default Admin Credentials
After running `app:create-admin`, use:
- Email: (what you entered)
- Password: (what you entered)

## Features Overview

### 🎨 Modern Design Features
- ✨ Glassmorphism effects
- 🎭 Smooth animations
- 🌈 Gradient backgrounds
- 📱 Fully responsive
- 🎯 Modern typography (Poppins + Playfair Display)
- 💫 Hover effects and transitions

### 👤 User Roles
- **ROLE_USER**: Can browse and book tickets
- **ROLE_ADMIN**: Full access to admin panel

## Next Steps
1. Login as admin
2. Add movies via Admin Dashboard
3. Add showtimes for movies
4. Register as customer
5. Book tickets!

## Troubleshooting

**Database Connection Error?**
- Check MySQL is running in XAMPP
- Verify database name in `.env.local`
- Default XAMPP MySQL: user=`root`, password=(empty)

**CSS Not Loading?**
- Clear cache: `php bin/console cache:clear`
- Check browser console for errors

**Migration Errors?**
- Drop database and recreate
- Run: `php bin/console doctrine:database:drop --force`
- Then: `php bin/console doctrine:database:create`
- Finally: `php bin/console doctrine:migrations:migrate`

