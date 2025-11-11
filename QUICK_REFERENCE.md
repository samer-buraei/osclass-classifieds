# 📋 Quick Reference Card - Osclass Platform

**Print this page and keep it handy!**

---

## 🚀 Start Development (2 minutes)

```
1. Open XAMPP Control Panel
2. Start Apache & MySQL
3. Visit: http://localhost/osclass/public/test-homepage.php
4. All green? You're ready! ✅
```

---

## 📖 Documentation Quick Links

| What You Need | Read This | Time |
|---------------|-----------|------|
| **Understand code** | ARCHITECTURE.md | 2 hours |
| **Find a file** | FILES.md | 5 mins |
| **Add a feature** | ARCHITECTURE.md → "Adding Features" | 30 mins |
| **Create plugin** | ARCHITECTURE.md → "Plugin System" | 30 mins |
| **Deploy** | DEPLOYMENT.md | 30 mins |
| **Onboard** | HANDOVER.md | 1 hour |
| **Quick setup** | QUICKSTART.md | 10 mins |

---

## 📁 Where is Everything?

| What | Location | Purpose |
|------|----------|---------|
| **Controllers** | `app/Controllers/` | Request handlers |
| **Models** | `app/Models/` | Database logic |
| **Views** | `app/Views/` | Templates (create here) |
| **Helpers** | `app/Helpers/` | Utility functions |
| **Plugins** | `plugins/` | Extensions |
| **Config** | `config/` | Settings |
| **Database** | `database/schema.sql` | DB structure |
| **Languages** | `languages/` | Translations |
| **Tests** | `public/test-*.php` | Test pages |
| **Entry point** | `public/index.php` | App starts here |

---

## 🔧 Common Commands

### Test Pages
```
http://localhost/osclass/public/test-homepage.php    # Main dashboard
http://localhost/osclass/public/test-models.php      # Database tests
http://localhost/osclass/public/test-plugins.php     # Plugin tests
http://localhost/osclass/public/diagnose.php         # Full diagnostic
```

### Database
```
http://localhost/phpmyadmin                           # PHPMyAdmin
Database: osclass_db
Username: root
Password: (empty)
```

### Logs
```
Apache errors: C:\xampp\apache\logs\error.log
PHP errors: Enable in config/constants.php
```

---

## 💻 Code Patterns

### Create Controller
```php
// app/Controllers/MyController.php
namespace App\Controllers;
use App\Core\Controller;

class MyController extends Controller {
    public function index() {
        $model = $this->model('MyModel');
        $data = $model->all();
        $this->view('my/index', ['data' => $data]);
    }
}
// URL: /my/index
```

### Create Model
```php
// app/Models/MyModel.php
namespace App\Models;
use App\Core\Model;

class MyModel extends Model {
    protected $table = 'my_table';
    
    public function getActive() {
        return $this->where('status = :status', ['status' => 'active']);
    }
}
```

### Use Translation
```php
echo __('home');  // Returns translated string
```

### Use Database
```php
// Via Model (preferred)
$model = new \App\Models\Category();
$categories = $model->all();

// Direct query
$db = \App\Core\Database::getInstance();
$result = $db->fetchAll("SELECT * FROM categories");
```

---

## 🎯 Feature Addition Steps

1. **Plan** (5 min)
   - Check if database table exists
   - Read similar feature code

2. **Model** (10 min)
   - Create in `app/Models/`
   - Extend `Model` class

3. **Controller** (15 min)
   - Create in `app/Controllers/`
   - Extend `Controller` class

4. **View** (20 min)
   - Create in `app/Views/`
   - Use translations

5. **Test** (10 min)
   - Visit URL
   - Check for errors

**Total: ~1 hour**

---

## 🔌 Plugin Creation Steps

1. Create `plugins/my-plugin/plugin.php`
2. Copy structure from `car-attributes/`
3. Define class with `init()` method
4. Register hooks:
   ```php
   \add_action('listing_form_fields', [$this, 'renderFields']);
   ```
5. Create views in `views/` folder

**Time: 30-60 minutes**

---

## 🐛 Troubleshooting

| Problem | Solution |
|---------|----------|
| Can't access site | Check XAMPP - Apache running? |
| Database error | Check `config/database.php` credentials |
| Plugin not loading | Run `test-plugins.php` for error |
| Function not found | Run `diagnose.php` for analysis |
| Need to understand X | Search ARCHITECTURE.md |

---

## 📊 Project Stats

```
Lines of Code:    5,000+
Files:            50+
Controllers:      5
Models:           5
Helpers:          5
Plugins:          2 (Car, Real Estate)
Languages:        3 (EN, ES, FR)
Database Tables:  12
Test Pages:       6
Documentation:    8 files
```

---

## ✅ Daily Checklist

**Morning:**
- [ ] Start XAMPP (Apache + MySQL)
- [ ] Check test-homepage.php
- [ ] All green? Good to go!

**Development:**
- [ ] Make changes
- [ ] Refresh browser (no restart needed)
- [ ] Check for errors
- [ ] Test in browser

**End of Day:**
- [ ] Run tests
- [ ] Check error logs
- [ ] Commit changes (if using git)

---

## 🎓 Learning Path

**Week 1:** Read docs, understand structure
**Week 2:** Make small changes, test everything
**Week 3:** Add complete feature
**Week 4:** Create plugin, fully productive

---

## 📞 Emergency Contacts

**When Stuck:**
1. Check ARCHITECTURE.md
2. Run diagnose.php
3. Check test pages
4. Look at error logs
5. Search existing code

**90% of answers in ARCHITECTURE.md!**

---

## 🔑 Key URLs (Bookmark These)

```
Local Site:        http://localhost/osclass/public/
Test Dashboard:    http://localhost/osclass/public/test-homepage.php
PHPMyAdmin:        http://localhost/phpmyadmin
Diagnostic:        http://localhost/osclass/public/diagnose.php
```

---

## 💡 Pro Tips

1. **Always check test-homepage.php first**
2. **ARCHITECTURE.md is your best friend**
3. **Copy existing patterns, don't reinvent**
4. **Use diagnose.php when debugging**
5. **No server restart needed - just refresh**
6. **PHPMyAdmin for quick database checks**
7. **Enable debug mode when developing**

---

## 🎯 Success Metrics

You're productive when you can:
- [ ] Add a feature in < 2 hours
- [ ] Debug an issue in < 30 minutes
- [ ] Create a plugin in < 1 hour
- [ ] Find any file in < 1 minute
- [ ] Understand any code section

---

**Keep this handy! Print it out! 📋**

**Version 1.0** | **Last Updated: 2024**

