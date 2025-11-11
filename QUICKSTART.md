# 🚀 Osclass Classifieds - Quick Start Guide

Get your classified ads platform running in **5 minutes**!

## ⚡ Fast Docker Setup

```bash
# 1. Clone the repo
git clone [your-repo-url]
cd osclass-app

# 2. Start everything
docker-compose up -d

# 3. Open your browser
# Visit: http://localhost:8080
# PHPMyAdmin: http://localhost:8081
```

**That's it!** Your platform is now running with:
- ✅ PHP 8.2
- ✅ MySQL 8.0
- ✅ Nginx web server
- ✅ Sample database with categories

## 🎯 What You Get

### Core Features
- **MVC Architecture** - Clean, organized PHP code
- **RESTful URLs** - SEO-friendly routing
- **Authentication System** - User registration and login
- **File Uploads** - Image handling with thumbnails
- **Search & Filters** - Full-text search with MySQL
- **Responsive Design** - Mobile-first CSS framework

### Specialized Plugins

#### 🚗 Car Attributes Plugin
Perfect for vehicle listings with:
- Make, Model, Year
- Mileage, Condition
- Transmission, Fuel Type
- Body Type, Color
- Advanced search filters

#### 🏠 Real Estate Plugin
Complete property listing system:
- Property Type (House, Apartment, Condo, etc.)
- Bedrooms, Bathrooms, Area
- 20+ Amenities (Pool, Gym, Garden, etc.)
- Listing Type (Sale, Rent, Lease)
- Floor information

### Multi-Language Support
- **44+ Languages** included
- English (US), Spanish, French out of the box
- Easy to add more languages
- Auto-detection from browser

### Payment Integration
- **Stripe** - Credit card payments
- **PayPal** - PayPal checkout
- Featured listing upgrades
- Payment history tracking

### SEO Features
- Meta tags (title, description, keywords)
- Open Graph tags for social sharing
- Twitter Card support
- Structured data (JSON-LD)
- XML Sitemap generation
- Robots.txt
- SEO-friendly URLs

## 📁 Project Structure

```
osclass-app/
├── app/                        # Application code
│   ├── Controllers/           # Request handlers
│   ├── Models/               # Database models
│   ├── Views/                # Templates
│   ├── Core/                 # Framework core
│   └── Helpers/              # Utility classes
├── plugins/                   # Plugin system
│   ├── car-attributes/       # Car listings
│   └── real-estate/          # Property listings
├── public/                    # Web root
│   ├── css/                  # Stylesheets
│   ├── js/                   # JavaScript
│   ├── uploads/              # User uploads
│   └── index.php             # Entry point
├── config/                    # Configuration
├── database/                  # Database schema
├── languages/                 # Translations
├── docker/                    # Docker configs
├── docker-compose.yml         # Docker setup
└── README.md                  # Documentation
```

## 🔧 Configuration

### Database Settings
Edit `config/database.php`:
```php
return [
    'host' => 'localhost',
    'database' => 'osclass_db',
    'username' => 'root',
    'password' => '',
];
```

### Payment Gateways
Edit `config/config.php`:
```php
'payments' => [
    'stripe' => [
        'enabled' => true,
        'public_key' => 'pk_test_...',
        'secret_key' => 'sk_test_...',
    ],
    'paypal' => [
        'enabled' => true,
        'mode' => 'sandbox',
        'client_id' => '...',
        'secret' => '...',
    ],
]
```

## 🎨 Customization

### Adding a New Language
```bash
# 1. Create language directory
mkdir languages/de_DE

# 2. Create language info
cat > languages/de_DE/language.json << EOF
{
  "code": "de_DE",
  "name": "German",
  "native_name": "Deutsch",
  "direction": "ltr"
}
EOF

# 3. Create translations
cp languages/en_US/messages.php languages/de_DE/messages.php
# Edit messages.php with German translations
```

### Creating a Custom Plugin
```php
<?php
// plugins/my-plugin/plugin.php

namespace Plugins\MyPlugin;

class MyPlugin {
    public $name = 'My Plugin';
    public $version = '1.0.0';
    
    public function init() {
        // Plugin initialization
        add_action('listing_form_fields', [$this, 'renderFields']);
        add_action('listing_save', [$this, 'saveAttributes']);
    }
    
    public function renderFields($categoryId) {
        // Add custom fields to listing form
    }
    
    public function saveAttributes($listingId, $data) {
        // Save custom attributes
    }
}

$myPlugin = new MyPlugin();
$myPlugin->init();
```

## 📊 Database Schema

Key tables:
- **users** - User accounts
- **listings** - Classified ads
- **categories** - Ad categories
- **locations** - Geographic hierarchy
- **listing_images** - Image uploads
- **listing_attributes** - Plugin data
- **payments** - Payment records
- **messages** - User messaging
- **favorites** - Saved listings

## 🔐 Security Features

- CSRF token protection
- SQL injection prevention (PDO)
- XSS prevention (output escaping)
- Password hashing (bcrypt)
- File upload validation
- Rate limiting
- Secure session handling

## 🚀 Performance

- **OPcache** enabled
- Database query optimization
- Image thumbnail generation
- Gzip compression
- CDN-ready assets
- Caching system

## 📱 API Endpoints

```bash
# Listings
GET    /listing/{id}           # View listing
POST   /listing/create         # Create listing
PUT    /listing/edit/{id}      # Edit listing
DELETE /listing/delete/{id}    # Delete listing

# Search
GET    /search?q=query         # Search listings

# Authentication
POST   /auth/register          # Register user
POST   /auth/login            # Login
GET    /auth/logout           # Logout

# Payments
POST   /payment/stripe        # Process Stripe payment
POST   /payment/paypal        # Create PayPal payment
```

## 🧪 Testing

```bash
# Run unit tests
vendor/bin/phpunit

# Check code style
vendor/bin/phpcs

# Generate coverage report
vendor/bin/phpunit --coverage-html coverage/
```

## 📈 Monitoring

```bash
# View logs
tail -f logs/php_errors.log

# Monitor Docker containers
docker-compose logs -f

# Database queries
docker-compose exec db mysql -u root -p
```

## 🆘 Troubleshooting

**Port already in use?**
```bash
# Change ports in docker-compose.yml
ports:
  - "8090:80"  # Use 8090 instead
```

**Database connection failed?**
```bash
# Restart database
docker-compose restart db

# Check credentials in config/database.php
```

**Images not uploading?**
```bash
# Fix permissions
chmod -R 777 public/uploads
```

## 📚 Learn More

- [Full Documentation](README.md)
- [Deployment Guide](DEPLOYMENT.md)
- [Plugin Development](docs/plugins.md)
- [Contributing](CONTRIBUTING.md)

## 🤝 Support

- **Issues**: [GitHub Issues]
- **Discussions**: [GitHub Discussions]
- **Email**: support@yoursite.com

## 📄 License

MIT License - Free to use for personal and commercial projects.

---

**Built with ❤️ for simplicity and performance**

Ready to get started? Run `docker-compose up -d` and visit http://localhost:8080!

