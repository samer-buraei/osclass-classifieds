<?php
/**
 * Halooglasi-Style Category Page with Filters
 */

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Database;
use App\Models\Category;
use App\Models\Listing;
use App\Models\Location;

$db = Database::getInstance();
$categoryModel = new Category();
$listingModel = new Listing();
$locationModel = new Location();

// Get category slug from URL
$categorySlug = $_GET['slug'] ?? 'vehicles';
$categories = $db->fetchAll("SELECT * FROM categories WHERE slug = :slug LIMIT 1", ['slug' => $categorySlug]);
$category = !empty($categories) ? $categories[0] : null;

if (!$category) {
    header('Location: index-halooglasi.php');
    exit;
}

// Get filters
$priceMin = $_GET['price_min'] ?? '';
$priceMax = $_GET['price_max'] ?? '';
$locationId = $_GET['location'] ?? '';
$sortBy = $_GET['sort'] ?? 'newest';

// Build query
$where = "category_id = :category_id AND status = 'active'";
$params = ['category_id' => $category['id']];

if ($priceMin) {
    $where .= ' AND price >= :price_min';
    $params['price_min'] = $priceMin;
}

if ($priceMax) {
    $where .= ' AND price <= :price_max';
    $params['price_max'] = $priceMax;
}

if ($locationId) {
    $where .= ' AND location_id = :location_id';
    $params['location_id'] = $locationId;
}

// Sort
$orderBy = 'created_at DESC';
if ($sortBy === 'price_asc') {
    $orderBy = 'price ASC';
} elseif ($sortBy === 'price_desc') {
    $orderBy = 'price DESC';
} elseif ($sortBy === 'oldest') {
    $orderBy = 'created_at ASC';
}

// Get listings
$sql = "SELECT * FROM listings WHERE $where ORDER BY $orderBy";
$listings = $db->fetchAll($sql, $params);
$locations = $locationModel->all();
?>
<!DOCTYPE html>
<html lang="sr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($category['name']) ?> - Halooglasi</title>
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
                    <input type="text" class="search-input" placeholder="Pretraži oglase...">
                    <button class="search-btn">🔍 Pretraži</button>
                </div>
                
                <a href="#post-ad" class="btn-post-ad">➕ Dodaj oglas</a>
            </div>
        </div>
        
        <nav class="nav-main">
            <div class="container">
                <ul>
                    <li><a href="category-halooglasi.php?slug=real-estate">🏢 Nekretnine</a></li>
                    <li><a href="category-halooglasi.php?slug=vehicles">🚗 Vozila</a></li>
                    <li><a href="category-halooglasi.php?slug=jobs">💼 Posao</a></li>
                    <li><a href="category-halooglasi.php?slug=services">🔧 Usluge</a></li>
                    <li><a href="index-halooglasi.php#sve-kategorije">📂 Sve kategorije</a></li>
                </ul>
            </div>
        </nav>
    </header>

    <!-- Breadcrumb -->
    <div class="container">
        <div class="breadcrumb">
            <a href="index-halooglasi.php">🏠 Početna</a>
            <span>/</span>
            <strong><?= htmlspecialchars($category['name']) ?></strong>
        </div>
    </div>

    <!-- Main Content with Sidebar -->
    <div class="container">
        <div class="content-wrapper">
            
            <!-- Filters Sidebar -->
            <aside class="filters-sidebar">
                <h2 style="margin-bottom: 20px;">🔍 Filteri</h2>
                
                <form method="GET" action="">
                    <input type="hidden" name="slug" value="<?= htmlspecialchars($categorySlug) ?>">
                    
                    <!-- Price Filter -->
                    <div class="filter-group">
                        <h3>💰 Cena</h3>
                        <input type="number" 
                               name="price_min" 
                               placeholder="Od (€)" 
                               value="<?= htmlspecialchars($priceMin) ?>">
                        <input type="number" 
                               name="price_max" 
                               placeholder="Do (€)" 
                               value="<?= htmlspecialchars($priceMax) ?>">
                    </div>
                    
                    <!-- Location Filter -->
                    <div class="filter-group">
                        <h3>📍 Lokacija</h3>
                        <select name="location">
                            <option value="">Sve lokacije</option>
                            <?php foreach ($locations as $location): ?>
                                <option value="<?= $location['id'] ?>" 
                                        <?= $locationId == $location['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($location['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- Sort Filter -->
                    <div class="filter-group">
                        <h3>📊 Sortiraj</h3>
                        <select name="sort">
                            <option value="newest" <?= $sortBy === 'newest' ? 'selected' : '' ?>>Najnovije</option>
                            <option value="oldest" <?= $sortBy === 'oldest' ? 'selected' : '' ?>>Najstarije</option>
                            <option value="price_asc" <?= $sortBy === 'price_asc' ? 'selected' : '' ?>>Cena: rastuće</option>
                            <option value="price_desc" <?= $sortBy === 'price_desc' ? 'selected' : '' ?>>Cena: opadajuće</option>
                        </select>
                    </div>
                    
                    <!-- Featured Only -->
                    <div class="filter-group">
                        <h3>⭐ Dodatno</h3>
                        <label>
                            <input type="checkbox" name="featured" value="1">
                            Samo istaknuti
                        </label>
                        <label>
                            <input type="checkbox" name="with_images" value="1">
                            Samo sa slikama
                        </label>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        Primeni filtere
                    </button>
                    <a href="category-halooglasi.php?slug=<?= htmlspecialchars($categorySlug) ?>" 
                       class="btn btn-secondary" 
                       style="width: 100%; margin-top: 10px; text-align: center;">
                        Resetuj filtere
                    </a>
                </form>
            </aside>

            <!-- Listings Content -->
            <main>
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
                    <h1 style="font-size: 28px; font-weight: 700;">
                        <?= htmlspecialchars($category['name']) ?>
                    </h1>
                    <span style="color: var(--text-muted);">
                        <?= count($listings) ?> oglasa
                    </span>
                </div>

                <?php if (empty($listings)): ?>
                    <div style="text-align: center; padding: 60px 20px; background: var(--bg-white); border-radius: 12px;">
                        <div style="font-size: 64px; margin-bottom: 20px;">🔍</div>
                        <h2 style="margin-bottom: 10px;">Nema oglasa</h2>
                        <p style="color: var(--text-muted); margin-bottom: 20px;">
                            Trenutno nema oglasa koji odgovaraju vašim kriterijumima.
                        </p>
                        <a href="category-halooglasi.php?slug=<?= htmlspecialchars($categorySlug) ?>" class="btn btn-primary">
                            Pogledaj sve oglase
                        </a>
                    </div>
                <?php else: ?>
                    <div class="listings-grid">
                        <?php foreach ($listings as $listing): ?>
                            <a href="listing-detail.php?id=<?= $listing['id'] ?>" class="listing-card fade-in">
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

                    <!-- Pagination -->
                    <div class="pagination">
                        <a href="#">« Prethodna</a>
                        <span class="active">1</span>
                        <a href="#">2</a>
                        <a href="#">3</a>
                        <a href="#">4</a>
                        <a href="#">5</a>
                        <a href="#">Sledeća »</a>
                    </div>
                <?php endif; ?>
            </main>

        </div>
    </div>

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
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Pomoć</h3>
                    <ul>
                        <li><a href="#faq">Česta pitanja</a></li>
                        <li><a href="#safety">Saveti za sigurnost</a></li>
                        <li><a href="#rules">Pravila korišćenja</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Za korisnike</h3>
                    <ul>
                        <li><a href="#post-ad">Dodaj oglas</a></li>
                        <li><a href="#my-ads">Moji oglasi</a></li>
                        <li><a href="#favorites">Omiljeni</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h3>Pratite nas</h3>
                    <ul>
                        <li><a href="#facebook">📘 Facebook</a></li>
                        <li><a href="#instagram">📷 Instagram</a></li>
                        <li><a href="#twitter">🐦 Twitter</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> Halooglasi. Sva prava zadržana.</p>
            </div>
        </div>
    </footer>

    <script>
        // Fade-in animation
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
    </script>

</body>
</html>


