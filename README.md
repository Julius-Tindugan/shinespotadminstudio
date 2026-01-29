<p align="center">
  <img src="public/images/logo.svg" alt="Shine Spot Studio Logo" width="200"/>
</p>

<h1 align="center">✨ Shine Spot Admin Studio</h1>

<p align="center">
  <strong>A comprehensive photography studio management system built with modern technologies</strong>
</p>

<p align="center">
  <a href="#features">Features</a> •
  <a href="#tech-stack">Tech Stack</a> •
  <a href="#installation">Installation</a> •
  <a href="#screenshots">Screenshots</a> •
  <a href="#architecture">Architecture</a> •
  <a href="#api">API</a>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP"/>
  <img src="https://img.shields.io/badge/Laravel-12.0-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel"/>
  <img src="https://img.shields.io/badge/Tailwind_CSS-3.4-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="Tailwind"/>
  <img src="https://img.shields.io/badge/Alpine.js-3.15-8BC0D0?style=for-the-badge&logo=alpine.js&logoColor=white" alt="Alpine.js"/>
  <img src="https://img.shields.io/badge/MySQL-Database-4479A1?style=for-the-badge&logo=mysql&logoColor=white" alt="MySQL"/>
</p>

---

## 🎯 Overview

**Shine Spot Admin Studio** is a full-featured, enterprise-grade photography studio management system designed to streamline operations, manage bookings, track finances, and enhance client experiences. Built as a capstone project, this system demonstrates advanced software engineering principles, clean architecture, and modern web development practices.

### 🎬 What This System Does

- **End-to-end booking management** for photography sessions
- **Real-time financial analytics** with KPI dashboards
- **Staff scheduling and performance tracking**
- **Automated notifications** via Email & SMS
- **Secure payment processing** with multiple gateways
- **Comprehensive reporting** with PDF exports

---

## ⭐ Features

### 📅 Booking Management
| Feature | Description |
|---------|-------------|
| **Smart Scheduling** | Interactive calendar with drag-and-drop, conflict detection, and availability checking |
| **Booking Lifecycle** | Complete status tracking: Pending → Confirmed → Completed/Cancelled/No-Show |
| **Client Management** | Store client details, booking history, and preferences |
| **Package Selection** | Customizable photography packages with add-ons and backdrops |
| **Real-time Availability** | Instant slot checking with business hours configuration |

### 💰 Finance & Analytics
| Feature | Description |
|---------|-------------|
| **Revenue Tracking** | Daily, monthly, and yearly revenue monitoring |
| **Expense Management** | Categorized expense tracking with reporting |
| **Profit Analysis** | Real-time profit calculation and trend analysis |
| **KPI Dashboard** | Key performance indicators with visual charts |
| **Payment Gateway** | Integrated GCash/Xendit payments + onsite cash/card |

### 👥 Staff Management
| Feature | Description |
|---------|-------------|
| **Role-Based Access** | Admin and Staff roles with granular permissions |
| **Staff Scheduling** | Assign photographers to bookings |
| **Performance Metrics** | Track booking completion rates and revenue contribution |
| **Activity Logging** | Comprehensive audit trail for all actions |

### 📧 Communication
| Feature | Description |
|---------|-------------|
| **Email Notifications** | Automated booking confirmations, reminders, and invoices |
| **SMS Integration** | Twilio-powered SMS for instant notifications |
| **Payment Links** | Send payment requests directly to clients |
| **OTP Verification** | Secure email/phone verification for registrations |

### 📊 Reporting & Export
| Feature | Description |
|---------|-------------|
| **Financial Reports** | Revenue, expense, and profit reports |
| **Booking Reports** | Daily, weekly, monthly booking summaries |
| **PDF Generation** | Professional invoice and report exports via DomPDF |
| **Data Visualization** | Interactive charts powered by Chart.js |

### 🔐 Security
| Feature | Description |
|---------|-------------|
| **Authentication** | Secure login with rate limiting and lockout protection |
| **Password Reset** | Token-based password recovery with expiration |
| **reCAPTCHA** | Google reCAPTCHA Enterprise integration |
| **Session Management** | Secure session handling with activity tracking |

---

## 🛠️ Tech Stack

### Backend
```
├── PHP 8.2+           → Modern PHP with strong typing
├── Laravel 12         → Latest Laravel framework
├── MySQL              → Relational database
├── Eloquent ORM       → Object-relational mapping
└── Repository Pattern → Clean data access layer
```

### Frontend
```
├── Blade Templates    → Laravel's templating engine
├── Tailwind CSS 3.4   → Utility-first CSS framework
├── Alpine.js 3.15     → Lightweight JavaScript framework
├── Chart.js 4.5       → Interactive data visualization
├── FullCalendar 6.1   → Professional calendar component
└── Day.js             → Modern date manipulation
```

### Third-Party Integrations
```
├── Twilio SDK         → SMS notifications
├── Brevo (Sendinblue) → Transactional emails
├── Xendit             → Payment processing
├── Firebase           → Real-time features
├── Google reCAPTCHA   → Bot protection
└── DomPDF             → PDF generation
```

### Development Tools
```
├── Vite 7.1           → Next-gen frontend tooling
├── Pest PHP           → Elegant PHP testing
├── Laravel Pint       → Code style fixer
├── Laravel Sail       → Docker development environment
└── Laravel Pail       → Real-time log viewer
```

---

## 🏗️ Architecture

### Design Patterns Implemented

```
📦 Shine Spot Admin Studio
├── 🎯 MVC Architecture
│   ├── Models (Eloquent with Relationships)
│   ├── Views (Blade + Alpine.js)
│   └── Controllers (RESTful)
│
├── 🔧 Service Layer Pattern
│   ├── KpiService (Analytics calculations)
│   ├── BookingService (Booking logic)
│   ├── NotificationService (Email/SMS)
│   ├── PaymentMonitoringService (Payment tracking)
│   └── SecurityService (Authentication)
│
├── 📚 Repository Pattern
│   └── BookingRepository (Data access abstraction)
│
├── 🔔 Observer Pattern
│   └── BookingObserver (Event handling)
│
├── 🎭 Trait Composition
│   └── LogsActivity (Audit logging)
│
└── 🛡️ Middleware Pipeline
    ├── AdminAuth (Role verification)
    ├── LoginLimit (Rate limiting)
    └── ActivityTracker (User monitoring)
```

### Database Schema Highlights

```sql
-- Core Entities
├── bookings          → Photography session bookings
├── packages          → Service packages with pricing
├── addons            → Optional add-on services
├── backdrops         → Studio backdrop options
├── booking_slots     → Available time slots
│
-- User Management
├── admins            → Administrator accounts
├── staff             → Photographer/staff accounts
├── roles             → Role definitions
├── permissions       → Granular permissions
│
-- Finance
├── payments          → Payment records
├── payment_transactions → Transaction details
├── expenses          → Business expenses
├── expense_categories → Expense categorization
│
-- System
├── activity_logs     → Audit trail
├── system_settings   → Configuration
└── business_hours    → Operating hours
```

---

## 🚀 Installation

### Prerequisites

- PHP 8.2 or higher
- Composer 2.x
- Node.js 18+ & npm
- MySQL 8.0+
- Redis (optional, for caching)

### Quick Start

```bash
# Clone the repository
git clone https://github.com/Julius-Tindugan/shinespotadminstudio.git
cd shinespotadminstudio

# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install

# Environment setup
cp .env.example .env
php artisan key:generate

# Configure your .env file with database credentials

# Run migrations
php artisan migrate

# Seed the database (optional)
php artisan db:seed

# Build frontend assets
npm run build

# Start the development server
composer dev
# Or manually:
php artisan serve
```

### Environment Configuration

```env
# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=shinespot_db
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Mail (Brevo/Sendinblue)
MAIL_MAILER=smtp
BREVO_API_KEY=your_brevo_key

# SMS (Twilio)
TWILIO_SID=your_twilio_sid
TWILIO_TOKEN=your_twilio_token
TWILIO_FROM=your_twilio_number

# Payment (Xendit)
XENDIT_SECRET_KEY=your_xendit_key

# reCAPTCHA
RECAPTCHA_SITE_KEY=your_site_key
RECAPTCHA_SECRET_KEY=your_secret_key
```

---

## 📸 Screenshots

> *Screenshots coming soon - System includes:*
> - Modern dashboard with real-time KPIs
> - Interactive booking calendar
> - Financial analytics with charts
> - Booking management interface
> - Staff assignment views
> - Mobile-responsive design

---

## 📡 API Endpoints

### Authentication
```http
POST   /login              # User authentication
POST   /logout             # End session
GET    /login/lockout-status   # Check lockout status
POST   /forgot-password    # Request password reset
```

### Bookings
```http
GET    /bookings           # List all bookings
POST   /bookings           # Create new booking
GET    /bookings/{id}      # Get booking details
PUT    /bookings/{id}      # Update booking
DELETE /bookings/{id}      # Cancel booking
POST   /bookings/{id}/confirm   # Confirm booking
```

### Calendar
```http
GET    /calendar           # Calendar view
GET    /calendar/events    # Get calendar events
GET    /calendar/slots     # Available time slots
```

### Finance
```http
GET    /finance/dashboard  # Financial overview
GET    /finance/revenue    # Revenue reports
GET    /finance/expenses   # Expense tracking
POST   /finance/expenses   # Record expense
GET    /finance/export     # Export reports
```

### Settings
```http
GET    /settings           # System settings
PUT    /settings/business-hours  # Update hours
PUT    /settings/payment-methods # Configure payments
```

---

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run with Pest
./vendor/bin/pest

# Run specific test file
php artisan test tests/Feature/BookingTest.php

# Run with coverage
php artisan test --coverage
```

---

## 📁 Project Structure

```
├── app/
│   ├── Http/
│   │   ├── Controllers/     # 20+ controllers
│   │   ├── Middleware/      # Custom middleware
│   │   └── Requests/        # Form request validation
│   ├── Models/              # 25+ Eloquent models
│   ├── Services/            # 15+ service classes
│   ├── Repositories/        # Data access layer
│   ├── Mail/               # Mailable classes
│   ├── Rules/              # Custom validation rules
│   └── Traits/             # Reusable traits
├── resources/
│   ├── views/              # Blade templates
│   ├── css/                # Stylesheets
│   └── js/                 # JavaScript files
├── routes/
│   ├── web.php             # Web routes (500+ lines)
│   └── api.php             # API routes
├── database/
│   ├── migrations/         # Database migrations
│   └── seeders/            # Data seeders
└── tests/
    └── Feature/            # Feature tests
```

---

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

---

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---

## 👨‍💻 Developer

<p align="center">
  <strong>Julius Tindugan</strong><br>
  Full Stack Developer
</p>

<p align="center">
  <a href="https://github.com/Julius-Tindugan">
    <img src="https://img.shields.io/badge/GitHub-100000?style=for-the-badge&logo=github&logoColor=white" alt="GitHub"/>
  </a>
</p>

---

<p align="center">
  <strong>⭐ If you found this project helpful, please give it a star!</strong>
</p>

<p align="center">
  Built with ❤️ using Laravel & Modern Web Technologies
</p>
