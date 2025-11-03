<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Helpers\SEO;

class SitemapController extends Controller
{
    public function index()
    {
        header('Content-Type: application/xml; charset=utf-8');
        echo SEO::generateSitemap();
        exit;
    }

    public function robots()
    {
        header('Content-Type: text/plain');
        
        $robots = "User-agent: *\n";
        $robots .= "Allow: /\n";
        $robots .= "Disallow: /admin/\n";
        $robots .= "Disallow: /api/\n";
        $robots .= "Disallow: /cache/\n";
        $robots .= "\n";
        $robots .= "Sitemap: " . BASE_URL . "/sitemap.xml\n";
        
        echo $robots;
        exit;
    }
}

