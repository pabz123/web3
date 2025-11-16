# Configuration Setup for InfinityFree Deployment

## 📁 File Structure

### Local Development (Current):
```
career_hub/
├── config/
│   └── config.php          ← Configuration file
├── includes/
│   ├── config_loader.php   ← Helper to load config
│   └── db.php             ← Database connection
└── ...
```

### InfinityFree Production (After Upload):
```
/home/youruser/
├── htdocs/                 ← Public web root (upload career_hub contents here)
│   ├── includes/
│   ├── pages/
│   ├── api/
│   └── ...
└── config/                 ← Create this folder (private, outside htdocs)
    └── config.php         ← Upload this file here
```

---

## 🚀 Deployment Steps for InfinityFree

### Step 1: Upload Your Website
1. Upload all files from `career_hub/` to `/htdocs/`
2. **Do NOT upload** the `config/` folder to `htdocs/`

### Step 2: Create Private Config Directory
1. In your InfinityFree file manager, go to the **parent directory** of `htdocs/`
2. Create a new folder called `config/`
3. Upload `config/config.php` to `/config/` (NOT to `/htdocs/config/`)

Your structure should be:
```
/home/youruser/
├── htdocs/              ← Your website files
└── config/              ← Your config file (NOT accessible from web)
    └── config.php
```

### Step 3: Update config.php with InfinityFree Credentials
Edit `/config/config.php` and update these values:
```php
'DB_HOST' => 'sqlXXX.infinityfree.com',  // Your actual DB host
'DB_USER' => 'if0_XXXXXXXX',             // Your database username
'DB_PASS' => 'your_actual_password',     // Your database password
'DB_NAME' => 'if0_XXXXXXXX_uniconnect_db', // Your database name
```

### Step 4: Update Your Files to Use Config

Update `includes/db.php`:
```php
<?php
require_once __DIR__ . '/config_loader.php';

$dbConfig = getDbConfig();

$servername = $dbConfig['host'];
$username   = $dbConfig['user'];
$password   = $dbConfig['pass'];
$dbname     = $dbConfig['name'];

try {
    $conn = new mysqli($servername, $username, $password, $dbname);
    $conn->set_charset('utf8mb4');
} catch (mysqli_sql_exception $e) {
    error_log("DB Connection failed: " . $e->getMessage());
    die("Database connection error");
}
?>
```

Update `classes/ExternalAPIService.php` constructor:
```php
public function __construct() {
    require_once __DIR__ . '/../includes/config_loader.php';
    
    $this->adzunaAppId = config('ADZUNA_APP_ID');
    $this->adzunaAppKey = config('ADZUNA_APP_KEY');
    $this->jSearchApiKey = config('RAPIDAPI_KEY');
    
    // Rest of your code...
}
```

---

## 🔒 Security Benefits

✅ **Config file is outside web root** - Cannot be accessed via URL  
✅ **Automatic environment detection** - Uses local config on localhost, production config on InfinityFree  
✅ **No .env file needed** - Works on InfinityFree without command-line access  
✅ **Centralized configuration** - All settings in one place  

---

## 🧪 Testing Locally

Your local setup will continue to work:
1. Config file detects you're on `localhost`
2. Uses `DB_HOST_LOCAL`, `DB_USER_LOCAL`, etc.
3. No changes needed to test locally

---

## 🌐 Using in Your Code

### Get Database Config:
```php
require_once __DIR__ . '/includes/config_loader.php';
$dbConfig = getDbConfig();
```

### Get Any Config Value:
```php
require_once __DIR__ . '/includes/config_loader.php';
$apiKey = config('RAPIDAPI_KEY');
$cacheTtl = config('CACHE_TTL', 3600); // With default fallback
```

### Check Environment:
```php
require_once __DIR__ . '/includes/config_loader.php';
if (isLocalEnvironment()) {
    echo "Running on localhost";
} else {
    echo "Running on production";
}
```

---

## ⚠️ Important Notes for InfinityFree

1. **Database Host Changes**: InfinityFree uses `sqlXXX.infinityfree.com`, not `localhost`
2. **No WebSockets**: Remove/disable WebSocket features (use polling instead)
3. **File Permissions**: InfinityFree automatically sets correct permissions
4. **PHP Version**: InfinityFree uses PHP 8.x (your code is compatible)
5. **Session Path**: InfinityFree handles sessions automatically
6. **Upload Limits**: Max 10MB file uploads, 256MB disk space

---

## 📝 Checklist Before Going Live

- [ ] Upload all files to `/htdocs/`
- [ ] Create `/config/` folder outside htdocs
- [ ] Upload `config.php` to `/config/`
- [ ] Update database credentials in `config.php`
- [ ] Update `includes/db.php` to use config_loader
- [ ] Update `classes/ExternalAPIService.php` to use config_loader
- [ ] Import database SQL file via phpMyAdmin
- [ ] Test database connection
- [ ] Test login functionality
- [ ] Test job import (if APIs work on InfinityFree)

---

## 🆘 Troubleshooting

**"Configuration file not found"**
- Make sure `/config/config.php` exists outside htdocs
- Check file permissions (should be 644)

**"Database connection failed"**
- Verify database credentials in `config.php`
- Check InfinityFree control panel for correct DB host/username

**"Call to undefined function config()"**
- Make sure you've included `config_loader.php` before using config()

---

## 📞 Support

If you encounter issues during deployment:
1. Check InfinityFree forums
2. Verify file paths are correct
3. Check PHP error logs in InfinityFree control panel
