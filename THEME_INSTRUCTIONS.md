# 🎨 Quick Start - Halooglasi Theme

**Your new modern theme is ready to use!**

---

## 🚀 How to View Your New Theme

### Option 1: View Directly (Easiest)

Open your browser and go to:

```
http://localhost/osclass/public/index-halooglasi.php
```

**That's it!** Your new homepage is ready.

To view a category page with filters:
```
http://localhost/osclass/public/category-halooglasi.php?slug=vehicles
```

---

### Option 2: Make It the Default

To replace your old homepage with the new Halooglasi theme:

#### Windows (PowerShell):
```powershell
cd C:\xampp\htdocs\osclass\public

# Backup old files
Copy-Item index.php index-old.php

# Replace with new theme
Copy-Item index-halooglasi.php index.php
```

#### Or Manually:
1. Rename `public/index.php` to `public/index-old.php`
2. Copy `public/index-halooglasi.php` to `public/index.php`

Now visit: `http://localhost/osclass/public/` - You'll see the new theme!

---

## 📸 What You'll See

### Homepage Features:
✅ **Modern Header** with search bar
✅ **Hero Banner** with gradient
✅ **Category Grid** (8 categories with icons)
✅ **Featured Listings** section
✅ **Recent Listings** section
✅ **Stats Counter** (total ads, users, etc.)
✅ **Professional Footer**

### Category Page Features:
✅ **Filters Sidebar** (price, location, sort)
✅ **Breadcrumb Navigation**
✅ **Listings Grid** with images
✅ **Pagination**
✅ **Responsive Design**

---

## 🎨 What's Different from Before

| Feature | Old Design | New Halooglasi Design |
|---------|-----------|----------------------|
| **Layout** | Basic | Professional grid |
| **Categories** | Simple list | Cards with icons & counts |
| **Search** | Basic | Prominent header bar |
| **Filters** | ❌ None | ✅ Full sidebar |
| **Design** | Plain | Modern gradients |
| **Animations** | ❌ None | ✅ Smooth fade-ins |
| **Mobile** | Basic | Fully responsive |
| **Look** | Basic PHP | Modern like Halooglasi.com |

---

## 🔧 Quick Customization

### Change Colors

Edit `public/css/halooglasi-style.css` (around line 11):

```css
:root {
    --primary-color: #3a7bd5;     /* Change this! */
    --secondary-color: #00d2ff;   /* And this! */
}
```

**Try these color combinations:**
- **Purple**: `#7c3aed` and `#a78bfa`
- **Green**: `#10b981` and `#6ee7b7`
- **Red**: `#ef4444` and `#fca5a5`
- **Orange**: `#f97316` and `#fdba74`

### Change Logo

Edit `public/index-halooglasi.php` (line 71):

```php
<a href="index-halooglasi.php" class="logo">
    🏠 Halooglasi  <!-- Change this text! -->
</a>
```

Or add an image:
```php
<a href="index-halooglasi.php" class="logo">
    <img src="images/your-logo.png" alt="Logo" height="40">
</a>
```

### Change Language

The theme is currently in Serbian. To change to English:

Edit `public/index-halooglasi.php` and replace:
- `Dobrodošli na Halooglasi` → `Welcome to Halooglasi`
- `Popularne kategorije` → `Popular Categories`
- `Istaknuti oglasi` → `Featured Ads`
- `Najnoviji oglasi` → `Recent Listings`
- `oglasa` → `ads`

---

## 📱 Mobile Preview

The theme is fully responsive! Test it by:

1. Open in browser
2. Press `F12` (Developer Tools)
3. Click the device icon (toggle device toolbar)
4. Select different devices:
   - iPhone 12
   - iPad
   - Desktop

Everything adjusts automatically!

---

## 🧪 Test Different Categories

Try these URLs:

```
http://localhost/osclass/public/category-halooglasi.php?slug=vehicles
http://localhost/osclass/public/category-halooglasi.php?slug=real-estate
http://localhost/osclass/public/category-halooglasi.php?slug=electronics
http://localhost/osclass/public/category-halooglasi.php?slug=jobs
```

Each shows listings from that category with working filters!

---

## ✅ Quick Checklist

- [ ] Visited `index-halooglasi.php` - Homepage works?
- [ ] Clicked on a category - Category page works?
- [ ] Tried the search bar - Search works?
- [ ] Tested filters - Filtering works?
- [ ] Checked on mobile - Responsive works?
- [ ] Happy with colors - Or want to customize?

---

## 🎯 Next Steps

### If You Like It:
1. Make it the default (see Option 2 above)
2. Customize colors to match your brand
3. Add your logo
4. Translate to your language
5. Go live!

### Want to Learn More?
Read the full documentation:
- **`HALOOGLASI_THEME_README.md`** - Complete theme guide
- **`ARCHITECTURE.md`** - How the code works
- **`QUICK_REFERENCE.md`** - Common tasks

---

## 🐛 Troubleshooting

### "Page not found"
**Solution**: Make sure XAMPP Apache is running!

### "No categories showing"
**Solution**: Database needs categories. Run:
```
http://localhost/osclass/public/test-setup.php
```

### "Images not showing"
**Solution**: Check that `public/uploads/` folder exists

### "Filters not working"
**Solution**: This is normal - filters need listings data

---

## 📞 Need Help?

1. Check `HALOOGLASI_THEME_README.md` for detailed guide
2. Check `ARCHITECTURE.md` for code structure
3. Check `diagnose.php` for system status:
   ```
   http://localhost/osclass/public/diagnose.php
   ```

---

## 🎉 Enjoy Your New Theme!

You now have a professional, modern design for your classified ads platform!

**Your URLs:**
- **New Homepage**: `http://localhost/osclass/public/index-halooglasi.php`
- **Categories**: `http://localhost/osclass/public/category-halooglasi.php`
- **Test Page**: `http://localhost/osclass/public/test-homepage.php`

---

**Theme Version**: 1.0
**Created**: 2024
**Inspired By**: Halooglasi.com
**Style**: Modern, Clean, Professional


