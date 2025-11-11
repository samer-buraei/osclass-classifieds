# 🎨 Halooglasi Theme for Osclass

**Modern, clean classified ads design inspired by Halooglasi.com**

---

## 📋 What's Included

### Files Created
1. **`public/css/halooglasi-style.css`** - Complete theme stylesheet
2. **`public/index-halooglasi.php`** - Homepage with categories and featured listings
3. **`public/category-halooglasi.php`** - Category page with filters sidebar

---

## 🎨 Design Features

### Visual Design
✅ **Clean, modern interface** matching Halooglasi style
✅ **Category grid layout** with icons and counts
✅ **Card-based listings** with images, price, location
✅ **Gradient hero banner**
✅ **Sticky header** with search bar
✅ **Smooth animations** and hover effects
✅ **Professional color scheme** (blues and whites)

### Functional Features
✅ **Advanced search bar** in header
✅ **Category navigation** menu
✅ **Featured listings** section
✅ **Recent listings** section
✅ **Stats counter** section
✅ **Filters sidebar** (price, location, sort)
✅ **Responsive design** (mobile, tablet, desktop)
✅ **Breadcrumb navigation**
✅ **Pagination** support

---

## 🚀 How to Use

### Method 1: Quick Test
Visit the new homepage:
```
http://localhost/osclass/public/index-halooglasi.php
```

Visit category page:
```
http://localhost/osclass/public/category-halooglasi.php?slug=vehicles
```

### Method 2: Replace Default Homepage
To make Halooglasi the default theme:

1. **Rename files:**
```bash
# Backup old homepage
mv public/index.php public/index-old.php

# Make Halooglasi the default
cp public/index-halooglasi.php public/index.php
```

2. **Update CSS reference in existing files:**
Change from:
```html
<link rel="stylesheet" href="css/style.css">
```
To:
```html
<link rel="stylesheet" href="css/halooglasi-style.css">
```

### Method 3: Create Theme Switcher
Add a theme selector to allow users to choose:

```php
// In your config
$theme = $_SESSION['theme'] ?? 'halooglasi'; // or 'default'

// Load appropriate CSS
if ($theme === 'halooglasi') {
    echo '<link rel="stylesheet" href="css/halooglasi-style.css">';
} else {
    echo '<link rel="stylesheet" href="css/style.css">';
}
```

---

## 🎨 Color Scheme

```css
Primary Blue:    #3a7bd5
Primary Hover:   #2d5ea8
Secondary:       #00d2ff
Text Dark:       #333333
Text Light:      #666666
Text Muted:      #999999
Border:          #e0e0e0
Background:      #f5f7fa
White:           #ffffff
Accent Orange:   #ff6b35
```

---

## 📱 Responsive Breakpoints

- **Desktop**: 1024px+
- **Tablet**: 768px - 1023px
- **Mobile**: < 768px
- **Small Mobile**: < 480px

---

## 🧩 Components

### Header
- **Top Bar**: Contact info, login/register links
- **Main Header**: Logo, search bar, "Add Listing" button
- **Navigation**: Category links

### Homepage Sections
1. **Hero Banner** - Welcome message with gradient
2. **Categories Grid** - 8 main categories with icons
3. **Featured Listings** - Premium ads with badge
4. **Recent Listings** - Latest posted ads
5. **Stats Section** - Numbers with gradient background
6. **Footer** - 4 columns with links

### Category Page
- **Filters Sidebar**:
  - Price range (min/max)
  - Location selector
  - Sort options (newest, oldest, price)
  - Featured only checkbox
  - With images checkbox
  
- **Listings Grid**: 3-4 columns responsive
- **Pagination**: Page numbers

---

## 🎯 Customization Guide

### Change Colors
Edit `public/css/halooglasi-style.css`:

```css
:root {
    --primary-color: #YOUR_COLOR;
    --primary-hover: #YOUR_HOVER_COLOR;
    /* ... etc */
}
```

### Change Layout
**Category Grid Columns:**
```css
.categories-grid {
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    /* Change 200px to adjust minimum card width */
}
```

**Listings Grid Columns:**
```css
.listings-grid {
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    /* Change 280px for different card sizes */
}
```

### Add Icons
Category icons are emojis by default. Change in PHP:

```php
$categoryIcons = [
    'vehicles' => '🚗',
    'real-estate' => '🏠',
    'your-category' => '🎯', // Add your own
];
```

Or use Font Awesome:
```html
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<!-- Then use -->
<i class="fas fa-car"></i> <!-- Instead of emoji -->
```

---

## 🔧 Advanced Features

### Add Fade-In Animations
Already included! Just add class `fade-in` to any element:

```html
<div class="fade-in">This will fade in on scroll</div>
```

### Enable Search Functionality
Search button already has JavaScript. To connect to backend:

1. Create `search.php`:
```php
<?php
$query = $_GET['q'] ?? '';
// Search in listings...
```

2. The button already redirects there.

### Add More Filters
Edit `category-halooglasi.php`, add to sidebar:

```html
<div class="filter-group">
    <h3>🏷️ Condition</h3>
    <label>
        <input type="checkbox" name="condition[]" value="new">
        Novo
    </label>
    <label>
        <input type="checkbox" name="condition[]" value="used">
        Polovn o
    </label>
</div>
```

---

## 📊 Features Comparison

| Feature | Old Theme | Halooglasi Theme |
|---------|-----------|------------------|
| Design | Basic | Modern, Professional |
| Categories | List | Grid with icons |
| Search | Basic | Prominent header bar |
| Filters | None | Full sidebar |
| Mobile | Basic | Fully responsive |
| Animations | None | Smooth fade-ins |
| Layout | Simple | Card-based |
| Colors | Basic | Professional gradient |

---

## 🐛 Troubleshooting

### Images Not Showing
Check upload path in listings:
```php
<img src="uploads/<?= $image['filename'] ?>">
```
Should match your uploads directory.

### Categories Not Displaying
Ensure database has categories:
```sql
SELECT * FROM categories;
```

### Filters Not Working
Check form method and field names:
```html
<form method="GET" action="">
    <input name="price_min" ...>
</form>
```

### Responsive Issues
Check viewport meta tag:
```html
<meta name="viewport" content="width=device-width, initial-scale=1.0">
```

---

## 🎨 Screenshots

### Desktop View
- Clean header with search
- Grid of 4 category cards per row
- Featured listings carousel
- Full-width footer

### Mobile View
- Hamburger menu (if added)
- Stacked categories (2 per row)
- Single column listings
- Touch-friendly buttons

---

## 📝 Translation

Current language: **Serbian (Cyrillic/Latin)**

To change to English, replace text in PHP files:

```php
// Serbian
<h1>Dobrodošli na Halooglasi</h1>

// English
<h1>Welcome to Halooglasi</h1>
```

Or integrate with multi-language system:
```php
<?= __('welcome_message') ?>
```

---

## 🚀 Performance Tips

1. **Optimize Images**: Use WebP format
2. **Enable Caching**: Add cache headers
3. **Minify CSS**: Use CSS minifier
4. **CDN**: Host static files on CDN
5. **Lazy Loading**: Add `loading="lazy"` to images

---

## 🔄 Future Enhancements

Possible additions:
- [ ] Save search filters
- [ ] User dashboard
- [ ] Real-time chat
- [ ] Image slider for listings
- [ ] Map integration
- [ ] Social sharing
- [ ] Compare listings
- [ ] Saved searches

---

## 📞 Support

For issues or questions:
1. Check `ARCHITECTURE.md` for code structure
2. Check `QUICK_REFERENCE.md` for common tasks
3. Review CSS comments in `halooglasi-style.css`

---

## ✅ Testing Checklist

Before going live:
- [ ] Test on Chrome, Firefox, Safari
- [ ] Test on mobile devices
- [ ] Test all filter combinations
- [ ] Test search functionality
- [ ] Verify all links work
- [ ] Check image uploads
- [ ] Test pagination
- [ ] Verify responsive layout
- [ ] Check loading speed
- [ ] Test with real data

---

## 🎉 Enjoy Your New Theme!

You now have a professional, Halooglasi-inspired design for your classified ads platform!

**Key URLs:**
- Homepage: `http://localhost/osclass/public/index-halooglasi.php`
- Categories: `http://localhost/osclass/public/category-halooglasi.php`

---

**Version**: 1.0
**Created**: 2024
**Compatible With**: Osclass Platform v1.0+
**Browser Support**: Chrome, Firefox, Safari, Edge (modern browsers)


