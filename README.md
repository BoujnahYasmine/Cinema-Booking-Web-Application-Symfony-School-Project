# 🎬 Cinema Booking Web Application

A complete Symfony-based cinema booking system with three distinct spaces: Public, Admin, and Customer areas.

## Features

### 🌐 Public Space (No login required)
- **Home Page**: Display trending and coming soon movies
- **All Movies Page**: Browse all available movies with images and descriptions
- **Movie Details Page**: View full movie information, showtimes, and remaining seats
- **Contact Us Page**: Contact form for customer inquiries

### 👨‍💼 Admin Space (ROLE_ADMIN required)
- **Dashboard**: Overview of movies and bookings statistics
- **Movie Management**: Full CRUD operations for movies
  - Add movies with images, descriptions, and seat capacity
  - Update movie information
  - Delete movies
  - Mark movies as trending or coming soon
- **Showtime Management**: Add and manage showtimes for movies
  - Automatically calculates remaining seats
- **View Bookings**: See all bookings with customer details

### 👤 Customer Space (ROLE_USER required)
- **User Registration & Login**: Secure authentication system
- **Browse Movies**: View all available movies
- **Book Tickets**: 
  - Select movie, date, and time
  - Choose number of seats
  - Automatic seat availability validation
- **My Tickets**: View all booking history
- **Email Tickets**: Automatic email delivery with booking details

## Technical Stack

- **Framework**: Symfony 7.4
- **Database**: MySQL (Doctrine ORM)
- **Frontend**: Twig templates + CSS
- **Authentication**: Symfony Security Component
- **Email**: Symfony Mailer
- **Image Upload**: File upload handling for movie images

## Installation

### Prerequisites
- PHP 8.2 or higher
- MySQL 5.7 or higher
- Composer
- Symfony CLI (optional but recommended)

### Setup Steps

1. **Clone the repository**
   ```bash
   cd cenima_ticket
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Configure environment**
   Create a `.env.local` file in the root directory:
   ```env
   DATABASE_URL="mysql://username:password@127.0.0.1:3306/cinema_db?serverVersion=8.0&charset=utf8mb4"
   MAILER_DSN=null://null
   APP_SECRET=your-secret-key-here
   ```
   
   For email functionality, configure MAILER_DSN:
   ```env
   MAILER_DSN=smtp://user:pass@smtp.example.com:587
   ```

4. **Create database**
   ```bash
   php bin/console doctrine:database:create
   ```

5. **Run migrations**
   ```bash
   
   ```

6. **Create admin user**
   ```bash
   php bin/console app:create-admin
   ```
   Follow the prompts to create your admin account.

7. **Create uploads directory** (if not already created)
   ```bash
   mkdir -p public/uploads/movies
   ```

8. **Start the development server**
   ```bash
   symfony server:start
   # or
   php -S localhost:8000 -t public
   ```

9. **Access the application**
   - Public area: http://localhost:8000
   - Admin area: http://localhost:8000/admin/dashboard
   - Customer area: http://localhost:8000/customer/movies

## Usage

### Creating an Admin User
Run the command:
```bash
php bin/console app:create-admin
```

### Adding Movies (Admin)
1. Login as admin
2. Go to Admin Dashboard
3. Click "Add New Movie"
4. Fill in movie details and upload an image
5. Save the movie

### Adding Showtimes (Admin)
1. Go to Movies list
2. Click "Showtimes" for a movie
3. Click "Add Showtime"
4. Select date and time
5. Save

### Booking Tickets (Customer)
1. Register/Login as a customer
2. Browse available movies
3. Select a movie and showtime
4. Choose number of seats
5. Confirm booking
6. Receive ticket via email

## Project Structure

```
cenima_ticket/
├── config/          # Configuration files
├── migrations/      # Database migrations
├── public/          # Web root
│   └── uploads/     # Uploaded movie images
├── src/
│   ├── Command/     # Console commands
│   ├── Controller/  # Controllers (Public, Admin, Customer)
│   ├── Entity/      # Doctrine entities
│   ├── Form/        # Form types
│   └── Repository/  # Repository classes
├── templates/       # Twig templates
│   ├── admin/       # Admin templates
│   ├── customer/    # Customer templates
│   ├── emails/      # Email templates
│   ├── public/      # Public templates
│   └── security/    # Auth templates
└── assets/          # CSS and JavaScript
```

## Database Schema

- **User**: Users (customers and admins)
- **Movie**: Movie information
- **Showtime**: Movie showtimes (date and time)
- **Booking**: Customer bookings

## Security

- Role-based access control (ROLE_ADMIN, ROLE_USER)
- Password hashing with Symfony's password hasher
- CSRF protection on forms
- Secure file upload validation

## Email Configuration

The application sends booking confirmation emails. Configure your mailer in `.env.local`:

```env
MAILER_DSN=smtp://user:password@smtp.example.com:587
```

For development, you can use Mailtrap or similar services.

## License

This is a school project for educational purposes.

## Author

Symfony School Project - Cinema Booking System

