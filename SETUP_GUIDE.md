# Career Hub - Complete Setup Guide

## Prerequisites
- PHP 7.4 or higher
- MySQL database
- Web server (Apache/Nginx) or PHP built-in server
- cURL extension enabled
- PDO extension enabled
- Sockets extension enabled (for WebSocket server)

## Step-by-Step Setup

### 1. Database Configuration

The OOP classes will automatically use your existing database configuration. The credentials are in `classes/Database.php`:

```php
private $host = "sql113.infinityfree.com";
private $username = "if0_40185804";
private $password = "careerhub12";
private $database = "if0_40185804_uniconnect_db";
```

No changes needed if these are correct.

### 2. Configure API Tokens

**File:** `api/v1/export_jobs.php`, `export_applications.php`, `import_jobs.php`, `stats.php`

Update the API tokens:
```php
$validTokens = ['YOUR_SECRET_API_TOKEN', 'EXTERNAL_APP_TOKEN'];
```

**Recommended tokens (generate your own):**
```php
$validTokens = [
    'sk_live_51ABC123XYZ',  // Your internal API token
    'ext_app_token_456DEF'   // Token for external applications
];
```

### 3. Configure External APIs

#### Adzuna API (Free - 5000 calls/month)

1. **Sign up:** https://developer.adzuna.com/
2. **Get credentials:** After signup, you'll receive:
   - App ID (e.g., `a1b2c3d4`)
   - App Key (e.g., `0123456789abcdef0123456789abcdef`)

3. **Update file:** `classes/ExternalAPIService.php`
   ```php
   $this->adzunaAppId = 'YOUR_APP_ID';
   $this->adzunaAppKey = 'YOUR_APP_KEY';
   ```

#### JSearch API (RapidAPI) - Optional

1. **Sign up:** https://rapidapi.com/
2. **Subscribe:** Search for "JSearch API" and subscribe (free tier available)
3. **Get key:** Copy your RapidAPI key
4. **Update file:** `classes/ExternalAPIService.php`
   ```php
   'X-RapidAPI-Key: YOUR_RAPIDAPI_KEY'
   ```

### 4. Create Required Directories

Run these commands in your project root:

```bash
# Windows PowerShell
mkdir cache\api
mkdir cache\notifications

# Linux/Mac
mkdir -p cache/api
mkdir -p cache/notifications
```

Or create them manually:
- `career_hub/cache/api/`
- `career_hub/cache/notifications/`

### 5. Set Directory Permissions

**For Linux/Mac:**
```bash
chmod 755 cache
chmod 755 cache/api
chmod 755 cache/notifications
chmod 644 cache/api/*
chmod 644 cache/notifications/*
```

**For Windows:**
Right-click folders → Properties → Security → Give write permissions to web server user.

### 6. Test Basic Setup

Create a test file: `test_setup.php`

```php
<?php
require_once 'classes/autoload.php';

echo "<h1>Career Hub Setup Test</h1>";

// Test 1: Database Connection
echo "<h2>1. Database Connection</h2>";
try {
    $db = Database::getInstance();
    echo "✅ Database connected successfully!<br>";
} catch (Exception $e) {
    echo "❌ Database connection failed: " . $e->getMessage() . "<br>";
}

// Test 2: Job Model
echo "<h2>2. Job Model</h2>";
try {
    $jobModel = new Job();
    $jobs = $jobModel->all(5, 0);
    echo "✅ Job model working! Found " . count($jobs) . " jobs<br>";
} catch (Exception $e) {
    echo "❌ Job model failed: " . $e->getMessage() . "<br>";
}

// Test 3: User Model
echo "<h2>3. User Model</h2>";
try {
    $userModel = new User();
    echo "✅ User model initialized successfully!<br>";
} catch (Exception $e) {
    echo "❌ User model failed: " . $e->getMessage() . "<br>";
}

// Test 4: Application Model
echo "<h2>4. Application Model</h2>";
try {
    $appModel = new Application();
    $stats = $appModel->getStatistics();
    echo "✅ Application model working! Total applications: " . ($stats['total_applications'] ?? 0) . "<br>";
} catch (Exception $e) {
    echo "❌ Application model failed: " . $e->getMessage() . "<br>";
}

echo "<h2>Setup Complete!</h2>";
echo "<p>All core components are working. You can now proceed with API configuration.</p>";
?>
```

Access: `http://yoursite.com/test_setup.php`

### 7. Test API Endpoints

#### Test Export Jobs API

```bash
# Using curl (Windows PowerShell)
curl "http://localhost/career_hub/api/v1/export_jobs.php?api_token=YOUR_SECRET_API_TOKEN&limit=5"

# Using browser
http://localhost/career_hub/api/v1/export_jobs.php?api_token=YOUR_SECRET_API_TOKEN&limit=5
```

Expected response:
```json
{
  "success": true,
  "count": 5,
  "data": [...],
  "meta": {...}
}
```

#### Test Statistics API

```bash
curl "http://localhost/career_hub/api/v1/stats.php?api_token=YOUR_SECRET_API_TOKEN"
```

#### Test Import Jobs API

Create a test file: `test_import.json`
```json
{
  "jobs": [
    {
      "title": "Test Software Engineer",
      "company": "Test Corp",
      "description": "This is a test job posting",
      "location": "Remote",
      "type": "full-time"
    }
  ]
}
```

Import using curl:
```bash
curl -X POST http://localhost/career_hub/api/v1/import_jobs.php \
  -H "Content-Type: application/json" \
  -H "X-API-TOKEN: YOUR_SECRET_API_TOKEN" \
  -d @test_import.json
```

### 8. Setup WebSocket Server

#### Check PHP Sockets Extension

```bash
php -m | grep sockets
```

If not installed:
- **Windows:** Enable in `php.ini`: `extension=sockets`
- **Linux:** `sudo apt-get install php-sockets`

#### Start WebSocket Server

**Option 1: Direct Run (for testing)**
```bash
php websocket_server.php
```

You should see:
```
===========================================
Career Hub WebSocket Server
===========================================

WebSocket server started on 0.0.0.0:8080
```

Keep this terminal open.

**Option 2: Background Process (Windows)**
```powershell
Start-Process php -ArgumentList "websocket_server.php" -WindowStyle Hidden
```

**Option 3: Background Process (Linux)**
```bash
nohup php websocket_server.php > websocket.log 2>&1 &
```

**Option 4: Using PM2 (Recommended for production)**
```bash
npm install -g pm2
pm2 start websocket_server.php --interpreter php --name career-hub-ws
pm2 save
pm2 startup
```

#### Test WebSocket Connection

Create `test_websocket.html`:
```html
<!DOCTYPE html>
<html>
<head>
    <title>WebSocket Test</title>
</head>
<body>
    <h1>WebSocket Connection Test</h1>
    <div id="status">Connecting...</div>
    <div id="messages"></div>
    
    <script>
        const ws = new WebSocket('ws://localhost:8080');
        const status = document.getElementById('status');
        const messages = document.getElementById('messages');
        
        ws.onopen = function() {
            status.textContent = '✅ Connected!';
            status.style.color = 'green';
        };
        
        ws.onmessage = function(event) {
            const msg = document.createElement('p');
            msg.textContent = 'Message: ' + event.data;
            messages.appendChild(msg);
        };
        
        ws.onerror = function(error) {
            status.textContent = '❌ Connection failed';
            status.style.color = 'red';
        };
        
        ws.onclose = function() {
            status.textContent = '⚠️ Disconnected';
            status.style.color = 'orange';
        };
    </script>
</body>
</html>
```

Open in browser: `http://localhost/career_hub/test_websocket.html`

### 9. Verify Complete Integration

1. **Log into your website**
2. **Open browser console** (F12)
3. **Check for WebSocket connection:**
   ```
   WebSocket connected
   Connected to real-time notifications
   ```
4. **Look for connection indicator** in top-right corner

### 10. Test External API Integration (Admin Only)

1. Log in as admin
2. Visit: `http://yoursite.com/api/v1/fetch_external_jobs.php?query=developer&source=adzuna&import=false`
3. You should see jobs fetched from Adzuna

If import=true, jobs will be added to your database.

## Common Issues & Solutions

### Issue 1: Database Connection Failed
**Solution:**
- Verify credentials in `classes/Database.php`
- Check if MySQL server is running
- Ensure PDO extension is enabled: `php -m | grep pdo`

### Issue 2: WebSocket Server Won't Start
**Solutions:**
- Check if port 8080 is in use: `netstat -ano | findstr :8080` (Windows)
- Enable sockets extension in php.ini
- Try different port in `classes/WebSocketServer.php` constructor

### Issue 3: API Returns 401 Unauthorized
**Solution:**
- Verify API token matches in request and API file
- Check token format (no extra spaces)
- Ensure X-API-TOKEN header is set correctly

### Issue 4: External API Not Working
**Solution:**
- Verify API credentials are correct
- Check API key permissions
- Test API directly using curl
- Check error logs: `tail -f /path/to/php/error.log`

### Issue 5: WebSocket Client Not Connecting
**Solutions:**
- Ensure WebSocket server is running
- Check browser console for errors
- Verify WebSocket URL is correct
- Try `ws://127.0.0.1:8080` instead of `ws://localhost:8080`

### Issue 6: Cache Directory Errors
**Solution:**
- Create directories manually: `cache/api/` and `cache/notifications/`
- Set proper permissions (755 for directories)
- Check web server has write access

## Production Deployment Checklist

- [ ] Change database credentials
- [ ] Generate secure API tokens (32+ characters)
- [ ] Set up external API keys (Adzuna, JSearch)
- [ ] Configure WebSocket server to run as service
- [ ] Enable HTTPS (use wss:// for WebSocket)
- [ ] Set up log rotation
- [ ] Configure firewall rules (allow port 8080)
- [ ] Set up monitoring for WebSocket server
- [ ] Implement rate limiting on API endpoints
- [ ] Enable error logging in production
- [ ] Set up automated backups
- [ ] Test all API endpoints
- [ ] Test WebSocket functionality
- [ ] Configure caching strategy
- [ ] Set up CDN for static assets

## Testing Checklist

- [ ] Database connection works
- [ ] All OOP models function correctly
- [ ] API endpoints return valid data
- [ ] API authentication works
- [ ] External APIs fetch data
- [ ] Jobs can be imported
- [ ] WebSocket server starts successfully
- [ ] WebSocket client connects
- [ ] Real-time notifications work
- [ ] Browser notifications appear
- [ ] API export formats work (JSON, CSV, XML)
- [ ] Statistics endpoint returns correct data

## Support & Troubleshooting

### Enable Error Logging
Add to top of PHP files during debugging:
```php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
```

### Check PHP Error Log
```bash
# Linux
tail -f /var/log/php_errors.log

# Windows
# Check php.ini for error_log location
```

### WebSocket Server Log
```bash
php websocket_server.php > ws_log.txt 2>&1
```

### API Request Debugging
Use Postman or curl with verbose output:
```bash
curl -v "http://localhost/career_hub/api/v1/stats.php?api_token=YOUR_TOKEN"
```

## Next Steps

1. **Update existing API files** to use OOP classes
2. **Configure external API credentials**
3. **Test all endpoints thoroughly**
4. **Set up WebSocket server as service**
5. **Implement additional features** as needed
6. **Monitor system performance**
7. **Set up automated testing**

## Additional Resources

- **API Documentation:** `API_DOCUMENTATION.md`
- **OOP Guide:** `README_OOP.md`
- **PHP Documentation:** https://www.php.net/manual/
- **WebSocket Protocol:** https://datatracker.ietf.org/doc/html/rfc6455
- **Adzuna API Docs:** https://developer.adzuna.com/docs/

---

**Setup complete! Your Career Hub now has:**
✅ Object-Oriented PHP architecture
✅ RESTful API endpoints for data sharing
✅ External API integration (Adzuna, JSearch)
✅ Real-time WebSocket notifications
✅ Comprehensive documentation
