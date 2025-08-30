# Net On You - Laravel Platform

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-10.x-red.svg" alt="Laravel Version">
  <img src="https://img.shields.io/badge/PHP-8.1+-blue.svg" alt="PHP Version">
  <img src="https://img.shields.io/badge/License-MIT-green.svg" alt="License">
</p>

## 🚀 About Net On You

Net On You is a comprehensive Laravel-based platform that provides a complete business management solution with advanced features including user management, subscription handling, referral systems, commission tracking, and multi-language support.

## ✨ Key Features

### 🔐 Authentication & Authorization
- **Multi-level Admin System**: Super admin, admin, and user roles with granular permissions
- **Secure Authentication**: Laravel Sanctum integration with session management
- **Role-based Access Control**: Comprehensive middleware for route protection

### 💰 Business Management
- **Subscription Management**: Handle user subscriptions with flexible plans
- **Transaction Tracking**: Complete financial transaction history and management
- **Commission System**: Automated referral commission calculation and distribution
- **Payout Management**: Batch processing for commission payouts

### 🌍 Multi-Language Support
- **Dynamic Language Switching**: Real-time language changes without page reload
- **Translation Management**: Admin interface for managing translations
- **Google Translate Integration**: Automated translation services
- **Language Preferences**: User-specific language settings

### 📧 Communication System
- **Email Templates**: Customizable email templates with dynamic content
- **Email Logging**: Complete audit trail of all communications
- **Notification Settings**: User-configurable notification preferences

### 📊 Analytics & Reporting
- **Dashboard Analytics**: Real-time business metrics and insights
- **User Analytics**: Comprehensive user behavior tracking
- **Commission Reports**: Detailed referral and commission analytics
- **Transaction Reports**: Financial reporting and auditing

### 🔒 Security Features
- **Audit Logging**: Track all sensitive changes and admin actions
- **Security Policies**: Configurable security settings and policies
- **API Key Management**: Secure API access with key rotation
- **Session Management**: Advanced session handling and security

## 🛠️ Technology Stack

- **Backend**: Laravel 10.x
- **Database**: SQLite (configurable for MySQL/PostgreSQL)
- **Frontend**: Blade templates with Tailwind CSS
- **Authentication**: Laravel Sanctum
- **Queue System**: Laravel Queue for background processing
- **Testing**: PHPUnit with comprehensive test coverage

## 📋 Requirements

- PHP 8.1 or higher
- Composer
- Node.js & NPM (for asset compilation)
- SQLite/MySQL/PostgreSQL

## 🚀 Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/Azeem-Shahid/net-on-you-laravel.git
   cd net-on-you-laravel
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Environment setup**
   ```bash
   cp env.example .env
   # Configure your .env file with database and other settings
   ```

5. **Generate application key**
   ```bash
   php artisan key:generate
   ```

6. **Run database migrations**
   ```bash
   php artisan migrate
   ```

7. **Seed the database**
   ```bash
   php artisan db:seed
   ```

8. **Compile assets**
   ```bash
   npm run build
   ```

9. **Start the development server**
   ```bash
   php artisan serve
   ```

## 🔧 Configuration

### Database Configuration
Update your `.env` file with your database credentials:
```env
DB_CONNECTION=sqlite
DB_DATABASE=/path/to/database.sqlite
```

### Multi-Language Setup
1. Configure Google Translate API key in `.env`
2. Run language setup script: `./install_multilanguage.sh`
3. Access admin panel to manage translations

### Payment Integration
1. Configure CoinPayments credentials in `.env`
2. Set up webhook endpoints
3. Configure commission rates in admin panel

## 📚 Documentation

- [Admin System Guide](ADMIN_SYSTEM_SUMMARY.md)
- [Admin User Guide](ADMIN_USER_GUIDE.md)
- [Payment Module Guide](PAYMENT_MODULE_README.md)
- [Multi-Language Module Guide](MULTI_LANGUAGE_MODULE_README.md)
- [Security Module Guide](SECURITY_MODULE_README.md)
- [Email Module Guide](EMAIL_MODULE_README.md)

## 🧪 Testing

Run the test suite:
```bash
php artisan test
```

Run specific test files:
```bash
php artisan test tests/Feature/ComprehensiveBusinessRulesTest.php
```

## 🚀 Deployment

### cPanel Deployment
Use the provided deployment script:
```bash
./deploy.sh
```

### Manual Deployment
1. Upload files to your server
2. Set proper permissions on storage and bootstrap/cache directories
3. Configure your web server to point to the public directory
4. Run `php artisan config:cache` and `php artisan route:cache`

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 👥 Team

- **Lead Developer**: Azeem Shahid
- **Project**: Net On You Platform

## 📞 Support

For support and questions:
- Create an issue in the GitHub repository
- Check the documentation files in the project root
- Review the admin guides for specific functionality

---

**Net On You** - Empowering businesses with comprehensive management solutions.
