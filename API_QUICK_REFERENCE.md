# Career Hub API - Quick Reference Card

## 🔑 Authentication
All API requests require a token. Include it as:
- Query param: `?api_token=YOUR_TOKEN`
- OR Header: `X-API-TOKEN: YOUR_TOKEN`

---

## 📤 Export Jobs
```
GET /api/v1/export_jobs.php
```

**Parameters:**
- `api_token` (required)
- `format` - json, csv, xml (default: json)
- `limit` - number (default: 100)
- `offset` - number (default: 0)
- `type` - full-time, part-time, internship
- `company` - filter by company name

**Example:**
```bash
curl "http://yoursite.com/api/v1/export_jobs.php?api_token=TOKEN&format=json&limit=20"
```

---

## 📤 Export Applications
```
GET /api/v1/export_applications.php
```

**Parameters:**
- `api_token` (required)
- `user_id` - filter by user
- `job_id` - filter by job
- `status` - pending, reviewed, accepted, rejected

**Example:**
```bash
curl "http://yoursite.com/api/v1/export_applications.php?api_token=TOKEN&status=pending"
```

---

## 📥 Import Jobs
```
POST /api/v1/import_jobs.php
```

**Headers:**
- `Content-Type: application/json`
- `X-API-TOKEN: YOUR_TOKEN`

**Body (single job):**
```json
{
  "title": "Software Engineer",
  "company": "Tech Corp",
  "description": "Job description",
  "location": "Remote",
  "type": "full-time"
}
```

**Body (multiple jobs):**
```json
{
  "jobs": [
    {"title": "Job 1", "company": "Co 1", "description": "..."},
    {"title": "Job 2", "company": "Co 2", "description": "..."}
  ]
}
```

**Example:**
```bash
curl -X POST http://yoursite.com/api/v1/import_jobs.php \
  -H "Content-Type: application/json" \
  -H "X-API-TOKEN: TOKEN" \
  -d '{"title":"Dev","company":"ABC","description":"..."}'
```

---

## 📊 Statistics
```
GET /api/v1/stats.php
```

**Parameters:**
- `api_token` (required)

**Response:**
```json
{
  "success": true,
  "data": {
    "jobs": {
      "total": 150,
      "full_time": 80,
      "internships": 45
    },
    "applications": {
      "total": 500,
      "pending": 200
    },
    "users": {
      "students": 300,
      "employers": 75
    }
  }
}
```

**Example:**
```bash
curl "http://yoursite.com/api/v1/stats.php?api_token=TOKEN"
```

---

## 🌐 Fetch External Jobs
```
GET /api/v1/fetch_external_jobs.php
```

**Requires:** Admin session login

**Parameters:**
- `query` - search term (default: "software developer")
- `source` - adzuna or jsearch (default: adzuna)
- `import` - true/false (import to database)

**Example:**
```bash
# Fetch only (no import)
curl "http://yoursite.com/api/v1/fetch_external_jobs.php?query=developer&source=adzuna"

# Fetch and import
curl "http://yoursite.com/api/v1/fetch_external_jobs.php?query=designer&import=true"
```

---

## 🔔 Send Notification
```
POST /api/v1/notify.php
```

**Requires:** User session

**Body:**
```json
{
  "type": "job_notification",
  "userId": 5,
  "data": {
    "jobId": 10,
    "title": "New Job",
    "company": "Tech Corp"
  }
}
```

---

## 🌐 WebSocket Connection

**URL:** `ws://localhost:8080`

### Connect
```javascript
const ws = new WebSocket('ws://localhost:8080');
```

### Register User
```javascript
ws.send(JSON.stringify({
  type: 'register',
  userId: 123
}));
```

### Receive Job Notification
```javascript
ws.onmessage = function(event) {
  const data = JSON.parse(event.data);
  if (data.type === 'job_notification') {
    console.log('New job:', data.title);
  }
};
```

---

## 📱 Using the WebSocket Client

Already integrated! Just use the global `wsClient` object:

```javascript
// Listen for job notifications
wsClient.on('jobNotification', function(data) {
  console.log('New job:', data);
});

// Listen for application updates
wsClient.on('applicationUpdate', function(data) {
  console.log('Status:', data.status);
});

// Check connection status
wsClient.on('connected', function() {
  console.log('WebSocket connected!');
});
```

---

## 🔧 OOP Classes Quick Reference

### Job Model
```php
require_once 'classes/autoload.php';

$jobModel = new Job();

// Get all jobs
$jobs = $jobModel->all(50, 0);

// Search
$results = $jobModel->search('developer');

// Find by ID
$job = $jobModel->find(5);

// Create
$id = $jobModel->create([
  'title' => 'Dev',
  'company' => 'ABC',
  'description' => '...',
  'location' => 'Remote',
  'type' => 'full-time',
  'created_at' => date('Y-m-d H:i:s')
]);

// Update
$jobModel->update(5, ['title' => 'Senior Dev']);

// Delete
$jobModel->delete(5);

// Statistics
$stats = $jobModel->getStatistics();
```

### User Model
```php
$userModel = new User();

// Find by email
$user = $userModel->findByEmail('test@example.com');

// Authenticate
$user = $userModel->authenticate('email', 'password');

// Register
$userId = $userModel->register([
  'name' => 'John',
  'email' => 'john@example.com',
  'password' => 'secure123',
  'role' => 'student'
]);

// Get by role
$students = $userModel->getByRole('student');
```

### Application Model
```php
$appModel = new Application();

// Get user's applications
$apps = $appModel->getByUser(5);

// Get job applications
$apps = $appModel->getByJob(10);

// Check if applied
$hasApplied = $appModel->hasApplied(5, 10);

// Create application
$appId = $appModel->create([
  'user_id' => 5,
  'job_id' => 10,
  'status' => 'pending',
  'created_at' => date('Y-m-d H:i:s')
]);

// Update status
$appModel->updateStatus($appId, 'accepted');
```

---

## 🚨 Common Errors

### 401 Unauthorized
- Check API token is correct
- Ensure token is properly included
- Verify token in API file matches

### 500 Internal Server Error
- Check PHP error logs
- Verify database connection
- Ensure all required fields are provided

### WebSocket Connection Failed
- Ensure server is running: `php websocket_server.php`
- Check port 8080 is available
- Try `ws://127.0.0.1:8080` instead of localhost

---

## 📋 Testing Checklist

- [ ] Test database connection
- [ ] Test export jobs API
- [ ] Test import jobs API
- [ ] Test statistics API
- [ ] Start WebSocket server
- [ ] Test WebSocket connection
- [ ] Test browser notifications
- [ ] Configure external API keys
- [ ] Test external API fetch
- [ ] Verify all OOP models work

---

## 🔗 Quick Links

- **Full API Docs:** `API_DOCUMENTATION.md`
- **OOP Guide:** `README_OOP.md`
- **Setup Guide:** `SETUP_GUIDE.md`
- **Test Page:** `http://yoursite.com/test_setup.php`

---

## 💡 Pro Tips

1. **Cache API responses** - Already implemented (1 hour cache)
2. **Use pagination** - Large datasets can be slow
3. **Validate input** - Always validate before importing
4. **Monitor WebSocket** - Keep WebSocket server running
5. **Secure tokens** - Use long, random tokens (32+ chars)
6. **Check logs** - Enable error logging for debugging
7. **Test locally first** - Always test before production
8. **Use HTTPS** - In production, use `wss://` for WebSocket

---

## 📞 Support

**Test your setup:**
```
http://yoursite.com/test_setup.php
```

**Documentation files:**
- API_DOCUMENTATION.md - Complete API reference
- README_OOP.md - OOP architecture guide
- SETUP_GUIDE.md - Detailed setup instructions
- IMPLEMENTATION_SUMMARY.md - What's been done

---

**Quick Start Command:**
```bash
# 1. Test setup
# Visit: http://yoursite.com/test_setup.php

# 2. Start WebSocket server
php websocket_server.php

# 3. Test API
curl "http://yoursite.com/api/v1/stats.php?api_token=YOUR_TOKEN"
```

---

**All set! Happy coding! 🚀**
