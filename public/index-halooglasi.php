<?php
/**
 * Halooglasi-Style Homepage
 * Modern classified ads interface
 */

// Load configuration
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../vendor/autoload.php';

// Initialize database
use App\Core\Database;
use App\Models\Category;
use App\Models\Listing;

$db = Database::getInstance();
$categoryModel = new Category();
$listingModel = new Listing();

// Get categories
$categories = $categoryModel->all();

// Get featured listings (first 8)
$featuredListings = $db->fetchAll("SELECT * FROM listings WHERE status = 'active' ORDER BY created_at DESC LIMIT 8", []);

// Get recent listings (next 12)
$recentListings = $db->fetchAll("SELECT * FROM listings WHERE status = 'active' ORDER BY created_at DESC LIMIT 12 OFFSET 8", []);

// Stats
$totalListings = $db->fetchOne("SELECT COUNT(*) as count FROM listings WHERE status = 'active'")['count'] ?? 0;
$totalUsers = $db->fetchOne("SELECT COUNT(*) as count FROM users WHERE status = 'active'")['count'] ?? 0;
$totalCategories = count($categories);
?>
<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halooglasi - Besplatni Oglasi | Classified Ads</title>
    <meta name="description" content="Halooglasi - Najveća platforma za besplatne oglase. Kupovina, prodaja, iznajmljivanje - sve na jednom mestu!">
    <link rel="stylesheet" href="css/halooglasi-style.css">
</head>
<body>

    <!-- Header Top Bar -->
    <div class="header-top">
        <div class="container">
            <div class="header-left">
                <span>📞 Pozovite nas: +381 11 123 4567</span>
            </div>
            <div class="header-right">
                <a href="#login">Prijavi se</a>
                <a href="#register">Registruj se</a>
                <a href="#language">🌐 Jezik</a>
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <header class="header">
        <div class="header-main">
            <div class="container">
                <a href="index-halooglasi.php" class="logo">
                    🏠 Halooglasi
                </a>
                
                <div class="search-bar">
                    <input type="text" 
                           class="search-input" 
                           placeholder="Pretraži oglase... (npr. auto, stan, posao)">
                    <button class="search-btn">🔍 Pretraži</button>
                </div>
                
                <a href="#post-ad" class="btn-post-ad">
                    ➕ Dodaj oglas
                </a>
            </div>
        </div>
        
        <!-- Navigation -->
        <nav class="nav-main">
            <div class="container">
                <ul>
                    <li><a href="#nekretnine">🏢 Nekretnine</a></li>
                    <li><a href="#vozila">🚗 Vozila</a></li>
                    <li><a href="#posao">💼 Posao</a></li>
                    <li><a href="#usluge">🔧 Usluge</a></li>
                    <li><a href="#sve-kategorije">📂 Sve kategorije</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <!-- Hero Banner -->
    <section class="hero-banner">
        <div class="container">
            <h1>Dobrodošli na Halooglasi</h1>
            <p>Najveća platforma za besplatne oglase u Srbiji</p>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="categories-section">
        <div class="container">
            <h2 class="section-title">Popularne kategorije</h2>
            
            <div class="categories-grid">
                <?php 
                $categoryIcons = [
                    'vehicles' => '🚗',
                    'real-estate' => '🏠',
                    'electronics' => '💻',
                    'jobs' => '💼',
                    'services' => '🔧',
                    'furniture' => '🛋️',
                    'fashion' => '👕',
                    'pets' => '🐾'
                ];
                
                foreach ($categories as $index => $category): 
                    $icon = $categoryIcons[$category['slug']] ?? '📦';
                    $count = $db->fetchOne("SELECT COUNT(*) as count FROM listings WHERE category_id = :id AND status = 'active'", ['id' => $category['id']])['count'] ?? 0;
                ?>
                    <a href="category.php?slug=<?= htmlspecialchars($category['slug']) ?>" class="category-card fade-in">
                        <div class="category-icon"><?= $icon ?></div>
                        <div class="category-name"><?= htmlspecialchars($category['name']) ?></div>
                        <div class="category-count"><?= number_format($count) ?> oglasa</div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Featured Listings -->
    <?php if (!empty($featuredListings)): ?>
    <section class="listings-section">
        <div class="container">
            <h2 class="section-title">⭐ Istaknuti oglasi</h2>
            
            <div class="listings-grid">
                <?php foreach ($featuredListings as $listing): ?>
                    <a href="listing.php?id=<?= $listing['id'] ?>" class="listing-card fade-in">
                        <div class="listing-image">
                            <?php
                            // Get first image
                            $image = $db->fetchOne("SELECT * FROM listing_images WHERE listing_id = :id LIMIT 1", ['id' => $listing['id']]);
                            if ($image): ?>
                                <img src="uploads/<?= htmlspecialchars($image['filename']) ?>" alt="<?= htmlspecialchars($listing['title']) ?>">
                            <?php else: ?>
                                📷
                            <?php endif; ?>
                        </div>
                        <div class="listing-content">
                            <h3 class="listing-title"><?= htmlspecialchars($listing['title']) ?></h3>
                            <div class="listing-price">
                                <?= $listing['price'] > 0 ? '€' . number_format($listing['price'], 2) : 'Dogovor' ?>
                            </div>
                            <div class="listing-location">
                                📍 <?php
                                $location = $db->fetchOne("SELECT name FROM locations WHERE id = :id", ['id' => $listing['location_id']]);
                                echo htmlspecialchars($location['name'] ?? 'N/A');
                                ?>
                            </div>
                            <div class="listing-date">
                                🕐 <?= date('d.m.Y', strtotime($listing['created_at'])) ?>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- Recent Listings -->
    <section class="listings-section" style="background: var(--bg-light);">
        <div class="container">
            <h2 class="section-title">📋 Najnoviji oglasi</h2>
            
            <div class="listings-grid">
                <?php foreach ($recentListings as $listing): ?>
                    <a href="listing.php?id=<?= $listing['id'] ?>" class="listing-card fade-in">
                        <div class="listing-image">
                            <?php
                            $image = $db->fetchOne("SELECT * FROM listing_images WHERE listing_id = :id LIMIT 1", ['id' => $listing['id']]);
                            if ($image): ?>
                                <img src="uploads/<?= htmlspecialchars($image['filename']) ?>" alt="<?= htmlspecialchars($listing['title']) ?>">
                            <?php else: ?>
                                📷
                            <?php endif; ?>
                        </div>
                        <div class="listing-content">
                            <h3 class="listing-title"><?= htmlspecialchars($listing['title']) ?></h3>
                            <div class="listing-price">
                                <?= $listing['price'] > 0 ? '€' . number_format($listing['price'], 2) : 'Dogovor' ?>
                            </div>
                            <div class="listing-location">
                                📍 <?php
                                $location = $db->fetchOne("SELECT name FROM locations WHERE id = :id", ['id' => $listing['location_id']]);
                                echo htmlspecialchars($location['name'] ?? 'N/A');
                                ?>
                            </div>
                            <div class="listing-date">
                                🕐 <?= date('d.m.Y', strtotime($listing['created_at'])) ?>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
            
            <div class="text-center mt-3">
                <a href="listings.php" class="btn btn-primary">Pogledaj sve oglase</a>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="stats-section">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-item">
                    <h3><?= number_format($totalListings) ?>+</h3>
                    <p>Aktivnih oglasa</p>
                </div>
                <div class="stat-item">
                    <h3><?= number_format($totalUsers) ?>+</h3>
                    <p>Registrovanih korisnika</p>
                </div>
                <div class="stat-item">
                    <h3><?= $totalCategories ?></h3>
                    <p>Kategorija</p>
                </div>
                <div class="stat-item">
                    <h3>100%</h3>
                    <p>Besplatno</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-grid">
                <div class="footer-section">
                    <h3>O nama</h3>
                    <ul>
                        <li><a href="#about">Ko smo mi</a></li>
                        <li><a href="#contact">Kontakt</a></li>
                        <li><a href="#careers">Karijera</a></li>
                        <li><a href="#press">Za medije</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h3>Pomoć</h3>
                    <ul>
                        <li><a href="#faq">Česta pitanja</a></li>
                        <li><a href="#safety">Saveti za sigurnost</a></li>
                        <li><a href="#rules">Pravila korišćenja</a></li>
                        <li><a href="#support">Podrška</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h3>Za korisnike</h3>
                    <ul>
                        <li><a href="#post-ad">Dodaj oglas</a></li>
                        <li><a href="#my-ads">Moji oglasi</a></li>
                        <li><a href="#favorites">Omiljeni</a></li>
                        <li><a href="#messages">Poruke</a></li>
                    </ul>
                </div>
                
                <div class="footer-section">
                    <h3>Pratite nas</h3>
                    <ul>
                        <li><a href="#facebook">📘 Facebook</a></li>
                        <li><a href="#instagram">📷 Instagram</a></li>
                        <li><a href="#twitter">🐦 Twitter</a></li>
                        <li><a href="#youtube">📺 YouTube</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> Halooglasi. Sva prava zadržana.</p>
                <p>
                    <a href="#privacy" style="color: inherit; margin: 0 10px;">Politika privatnosti</a> | 
                    <a href="#terms" style="color: inherit; margin: 0 10px;">Uslovi korišćenja</a> | 
                    <a href="#cookies" style="color: inherit; margin: 0 10px;">Kolačići</a>
                </p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script>
        // Add fade-in animation on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        document.querySelectorAll('.fade-in').forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(20px)';
            el.style.transition = 'opacity 0.6s ease-out, transform 0.6s ease-out';
            observer.observe(el);
        });

        // Search functionality
        document.querySelector('.search-btn').addEventListener('click', function() {
            const searchTerm = document.querySelector('.search-input').value;
            if (searchTerm.trim()) {
                window.location.href = `search.php?q=${encodeURIComponent(searchTerm)}`;
            }
        });

        // Enter key search
        document.querySelector('.search-input').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                document.querySelector('.search-btn').click();
            }
        });
    </script>

</body>
</html>


