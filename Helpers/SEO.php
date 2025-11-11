<?php

namespace App\Helpers;

class SEO
{
    private static $title = '';
    private static $description = '';
    private static $keywords = '';
    private static $image = '';
    private static $url = '';
    private static $type = 'website';
    
    /**
     * Set page title
     */
    public static function setTitle($title)
    {
        self::$title = $title;
    }
    
    /**
     * Get page title
     */
    public static function getTitle()
    {
        return self::$title ?: APP_NAME;
    }
    
    /**
     * Set meta description
     */
    public static function setDescription($description)
    {
        self::$description = $description;
    }
    
    /**
     * Get meta description
     */
    public static function getDescription()
    {
        return self::$description ?: DEFAULT_META_DESCRIPTION;
    }
    
    /**
     * Set meta keywords
     */
    public static function setKeywords($keywords)
    {
        self::$keywords = is_array($keywords) ? implode(', ', $keywords) : $keywords;
    }
    
    /**
     * Get meta keywords
     */
    public static function getKeywords()
    {
        return self::$keywords ?: DEFAULT_META_KEYWORDS;
    }
    
    /**
     * Set OG image
     */
    public static function setImage($image)
    {
        self::$image = $image;
    }
    
    /**
     * Get OG image
     */
    public static function getImage()
    {
        return self::$image ?: BASE_URL . '/images/og-default.jpg';
    }
    
    /**
     * Set canonical URL
     */
    public static function setUrl($url)
    {
        self::$url = $url;
    }
    
    /**
     * Get canonical URL
     */
    public static function getUrl()
    {
        return self::$url ?: self::getCurrentUrl();
    }
    
    /**
     * Set content type
     */
    public static function setType($type)
    {
        self::$type = $type;
    }
    
    /**
     * Get current URL
     */
    private static function getCurrentUrl()
    {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        return $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }
    
    /**
     * Render meta tags
     */
    public static function renderMetaTags()
    {
        $html = '';
        
        // Basic meta tags
        $html .= '<title>' . htmlspecialchars(self::getTitle()) . '</title>' . PHP_EOL;
        $html .= '<meta name="description" content="' . htmlspecialchars(self::getDescription()) . '">' . PHP_EOL;
        $html .= '<meta name="keywords" content="' . htmlspecialchars(self::getKeywords()) . '">' . PHP_EOL;
        $html .= '<link rel="canonical" href="' . htmlspecialchars(self::getUrl()) . '">' . PHP_EOL;
        
        // Open Graph tags
        $html .= '<meta property="og:title" content="' . htmlspecialchars(self::getTitle()) . '">' . PHP_EOL;
        $html .= '<meta property="og:description" content="' . htmlspecialchars(self::getDescription()) . '">' . PHP_EOL;
        $html .= '<meta property="og:image" content="' . htmlspecialchars(self::getImage()) . '">' . PHP_EOL;
        $html .= '<meta property="og:url" content="' . htmlspecialchars(self::getUrl()) . '">' . PHP_EOL;
        $html .= '<meta property="og:type" content="' . htmlspecialchars(self::$type) . '">' . PHP_EOL;
        $html .= '<meta property="og:site_name" content="' . htmlspecialchars(APP_NAME) . '">' . PHP_EOL;
        
        // Twitter Card tags
        $html .= '<meta name="twitter:card" content="summary_large_image">' . PHP_EOL;
        $html .= '<meta name="twitter:title" content="' . htmlspecialchars(self::getTitle()) . '">' . PHP_EOL;
        $html .= '<meta name="twitter:description" content="' . htmlspecialchars(self::getDescription()) . '">' . PHP_EOL;
        $html .= '<meta name="twitter:image" content="' . htmlspecialchars(self::getImage()) . '">' . PHP_EOL;
        
        return $html;
    }
    
    /**
     * Generate SEO-friendly URL slug
     */
    public static function generateSlug($text)
    {
        // Replace non-letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        
        // Transliterate
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        
        // Remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);
        
        // Trim
        $text = trim($text, '-');
        
        // Remove duplicate -
        $text = preg_replace('~-+~', '-', $text);
        
        // Lowercase
        $text = strtolower($text);
        
        return empty($text) ? 'n-a' : $text;
    }
    
    /**
     * Generate structured data (JSON-LD)
     */
    public static function generateStructuredData($type, $data)
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => $type
        ];
        
        $schema = array_merge($schema, $data);
        
        return '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . '</script>';
    }
    
    /**
     * Generate listing structured data
     */
    public static function listingStructuredData($listing)
    {
        return self::generateStructuredData('Product', [
            'name' => $listing['title'],
            'description' => strip_tags($listing['description']),
            'image' => $listing['image'] ?? self::getImage(),
            'offers' => [
                '@type' => 'Offer',
                'price' => $listing['price'],
                'priceCurrency' => 'USD',
                'availability' => 'https://schema.org/InStock',
                'url' => BASE_URL . '/listing/' . $listing['id']
            ]
        ]);
    }
    
    /**
     * Generate breadcrumbs
     */
    public static function generateBreadcrumbs($items)
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => []
        ];
        
        foreach ($items as $position => $item) {
            $schema['itemListElement'][] = [
                '@type' => 'ListItem',
                'position' => $position + 1,
                'name' => $item['name'],
                'item' => $item['url'] ?? null
            ];
        }
        
        return '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES) . '</script>';
    }
    
    /**
     * Generate sitemap
     */
    public static function generateSitemap()
    {
        $db = \App\Core\Database::getInstance();
        
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;
        
        // Homepage
        $xml .= self::addSitemapUrl(BASE_URL, '1.0', 'daily');
        
        // Categories
        $categories = $db->fetchAll('SELECT * FROM categories WHERE is_active = 1');
        foreach ($categories as $category) {
            $url = BASE_URL . '/category/' . $category['slug'];
            $xml .= self::addSitemapUrl($url, '0.8', 'weekly');
        }
        
        // Listings
        $listings = $db->fetchAll('SELECT * FROM listings WHERE status = "active" ORDER BY updated_at DESC LIMIT 1000');
        foreach ($listings as $listing) {
            $url = BASE_URL . '/listing/' . $listing['id'];
            $xml .= self::addSitemapUrl($url, '0.6', 'weekly', $listing['updated_at']);
        }
        
        $xml .= '</urlset>';
        
        return $xml;
    }
    
    /**
     * Add URL to sitemap
     */
    private static function addSitemapUrl($loc, $priority = '0.5', $changefreq = 'monthly', $lastmod = null)
    {
        $xml = '  <url>' . PHP_EOL;
        $xml .= '    <loc>' . htmlspecialchars($loc) . '</loc>' . PHP_EOL;
        
        if ($lastmod) {
            $xml .= '    <lastmod>' . date('Y-m-d', strtotime($lastmod)) . '</lastmod>' . PHP_EOL;
        }
        
        $xml .= '    <changefreq>' . $changefreq . '</changefreq>' . PHP_EOL;
        $xml .= '    <priority>' . $priority . '</priority>' . PHP_EOL;
        $xml .= '  </url>' . PHP_EOL;
        
        return $xml;
    }
}

