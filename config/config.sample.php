<?php

return [
    // App Settings
    'app' => [
        'name' => 'Osclass Classifieds',
        'url' => 'http://localhost:8080',
        'environment' => 'development', // development, production
        'timezone' => 'UTC',
    ],

    // Database
    'database' => [
        'host' => 'localhost',
        'name' => 'osclass_db',
        'user' => 'root',
        'password' => '',
    ],

    // Email
    'email' => [
        'from_address' => 'noreply@yoursite.com',
        'from_name' => 'Osclass Classifieds',
        'smtp_host' => 'smtp.gmail.com',
        'smtp_port' => 587,
        'smtp_user' => '',
        'smtp_password' => '',
        'smtp_secure' => 'tls',
    ],

    // Payment Gateways
    'payments' => [
        'stripe' => [
            'enabled' => true,
            'public_key' => 'pk_test_...',
            'secret_key' => 'sk_test_...',
            'webhook_secret' => 'whsec_...',
        ],
        'paypal' => [
            'enabled' => true,
            'mode' => 'sandbox', // sandbox, live
            'client_id' => '',
            'secret' => '',
        ],
    ],

    // Features
    'features' => [
        'user_registration' => true,
        'email_verification' => true,
        'moderation_required' => false,
        'featured_listings' => true,
        'user_reviews' => true,
        'social_sharing' => true,
    ],

    // Listing Settings
    'listings' => [
        'free_duration_days' => 30,
        'max_images_per_listing' => 8,
        'require_phone' => false,
        'allow_anonymous' => false,
    ],

    // Cache
    'cache' => [
        'enabled' => true,
        'ttl' => 3600, // 1 hour
    ],

    // SEO
    'seo' => [
        'default_title' => 'Osclass Classifieds - Buy and Sell',
        'default_description' => 'Find great deals on classified ads',
        'default_keywords' => 'classifieds,ads,buy,sell',
        'sitemap_enabled' => true,
    ],
];

