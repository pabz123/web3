# Career Hub - Object-Oriented PHP Architecture

## Overview
This document explains the new Object-Oriented PHP architecture implemented in Career Hub.

**⚡ Important: All classes use mysqli (not PDO) to match your existing codebase!**

See `MYSQLI_IMPLEMENTATION.md` for detailed mysqli information.

## Architecture Structure

```
career_hub/
├── classes/                    # OOP Classes
│   ├── autoload.php           # Class autoloader
│   ├── Database.php           # Singleton database connection
│   ├── Model.php              # Base model class
│   ├── Job.php                # Job model
│   ├── User.php               # User model
│   ├── Application.php        # Application model
│   ├── ExternalAPIService.php # External API integration
│   └── WebSocketServer.php    # WebSocket server
├── api/v1/                    # RESTful API endpoints
│   ├── export_jobs.php        # Export jobs data
│   ├── export_applications.php # Export applications
│   ├── import_jobs.php        # Import jobs
│   ├── stats.php              # Statistics endpoint
│   ├── fetch_external_jobs.php # Fetch from external APIs
│   └── notify.php             # WebSocket notifications
├── js/
│   └── websocket-client.js    # WebSocket client library
└── websocket_server.php       # WebSocket server runner
```

## Core Classes

### 1. Database Class (Singleton Pattern)
**File:** `classes/Database.php`

**Purpose:** Manages database connections using the Singleton pattern to ensure only one connection instance exists.

**Usage:**
```php
require_once 'classes/autoload.php';

$db = Database::getInstance();
$conn = $db->getConnection(); // Returns mysqli object
```

**Features:**
- Singleton pattern
- **mysqli** with prepared statements (compatible with your existing code)
- Error handling
- UTF-8 support
- Helper methods: `escape()`, `lastInsertId()`, `close()`

---

### 2. Model Class (Base Class)
**File:** `classes/Model.php`

**Purpose:** Abstract base class providing common CRUD operations for all models.

**Methods:**
- `find($id)` - Find record by ID
- `all($limit, $offset)` - Get all records with pagination
- `create($data)` - Create new record
- `update($id, $data)` - Update existing record
- `delete($id)` - Delete record
- `query($sql, $params)` - Execute custom query

**Usage:**
```php
// All models extend this class
class Job extends Model {
    protected $table = 'jobs';
}
```

---

### 3. Job Model
**File:** `classes/Job.php`

**Purpose:** Handles all job-related database operations.

**Methods:**
- `search($query)` - Search jobs by keyword
- `getByType($type)` - Get jobs by type (full-time, internship, etc.)
- `getByCompany($company)` - Get jobs by company
- `getRecent($limit)` - Get recent jobs
- `getStatistics()` - Get job statistics

**Usage Example:**
```php
require_once 'classes/autoload.php';

$jobModel = new Job();

// Get all jobs
$allJobs = $jobModel->all(50, 0);

// Search jobs
$results = $jobModel->search('software developer');

// Get job by ID
$job = $jobModel->find(5);

// Create new job
$jobId = $jobModel->create([
    'title' => 'Software Engineer',
    'company' => 'Tech Corp',
    'description' => 'Great opportunity...',
    'location' => 'Remote',
    'type' => 'full-time',
    'created_at' => date('Y-m-d H:i:s')
]);

// Update job
$jobModel->update(5, [
    'title' => 'Senior Software Engineer',
    'description' => 'Updated description...'
]);

// Delete job
$jobModel->delete(5);

// Get statistics
$stats = $jobModel->getStatistics();
```

---

### 4. User Model
**File:** `classes/User.php`

**Purpose:** Manages user authentication and user data.

**Methods:**
- `findByEmail($email)` - Find user by email
- `findByUsername($username)` - Find user by username
- `getByRole($role)` - Get users by role
- `authenticate($email, $password)` - Verify credentials
- `register($data)` - Register new user

**Usage Example:**
```php
$userModel = new User();

// Register new user
$userId = $userModel->register([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'password' => 'securepassword',
    'role' => 'student'
]);

// Authenticate user
$user = $userModel->authenticate('john@example.com', 'securepassword');

if ($user) {
    $_SESSION['user'] = $user;
    echo "Login successful!";
} else {
    echo "Invalid credentials";
}

// Find user by email
$user = $userModel->findByEmail('john@example.com');

// Get all students
$students = $userModel->getByRole('student');
```

---

### 5. Application Model
**File:** `classes/Application.php`

**Purpose:** Manages job applications.

**Methods:**
- `getByUser($userId)` - Get applications by user
- `getByJob($jobId)` - Get applications by job
- `hasApplied($userId, $jobId)` - Check if user applied
- `updateStatus($id, $status)` - Update application status
- `getStatistics()` - Get application statistics

**Usage Example:**
```php
$appModel = new Application();

// Create new application
$appId = $appModel->create([
    'user_id' => 5,
    'job_id' => 10,
    'status' => 'pending',
    'cover_letter' => 'I am interested...',
    'created_at' => date('Y-m-d H:i:s')
]);

// Get user's applications
$myApplications = $appModel->getByUser(5);

// Get applications for a job
$jobApplications = $appModel->getByJob(10);

// Check if already applied
if ($appModel->hasApplied(5, 10)) {
    echo "Already applied to this job";
}

// Update application status
$appModel->updateStatus($appId, 'reviewed');

// Get statistics
$stats = $appModel->getStatistics();
```

---

### 6. External API Service
**File:** `classes/ExternalAPIService.php`

**Purpose:** Integrates with external job APIs (Adzuna, JSearch).

**Methods:**
- `fetchAdzunaJobs($query, $location, $page)` - Fetch from Adzuna
- `parseAdzunaJobs($data)` - Parse Adzuna response
- `fetchJSearchJobs($query, $location)` - Fetch from JSearch
- `importJobs($jobs)` - Import jobs to database

**Usage Example:**
```php
$apiService = new ExternalAPIService();

// Fetch jobs from Adzuna
$adzunaData = $apiService->fetchAdzunaJobs('developer', 'us', 1);
$jobs = $apiService->parseAdzunaJobs($adzunaData);

// Import jobs
$imported = $apiService->importJobs($jobs);
echo "Imported $imported jobs";
```

---

### 7. WebSocket Server
**File:** `classes/WebSocketServer.php`

**Purpose:** Real-time communication server for notifications.

**Features:**
- WebSocket protocol implementation
- User registration
- Broadcasting
- Targeted user messages

**Usage:**
```bash
# Start the server
php websocket_server.php
```

---

## Migration Guide

### Converting Existing Procedural Code to OOP

#### Before (Procedural):
```php
require_once 'includes/db.php';

$stmt = $conn->prepare("SELECT * FROM jobs WHERE id = ?");
$stmt->bind_param("i", $jobId);
$stmt->execute();
$result = $stmt->get_result();
$job = $result->fetch_assoc();
```

#### After (OOP):
```php
require_once 'classes/autoload.php';

$jobModel = new Job();
$job = $jobModel->find($jobId);
```

### Updating Existing API Files

#### Example: Update `api/jobs.php`

**Before:**
```php
require_once '../includes/db.php';

$sql = "SELECT * FROM jobs";
$result = mysqli_query($conn, $sql);
$jobs = mysqli_fetch_all($result, MYSQLI_ASSOC);
```

**After:**
```php
require_once __DIR__ . '/../classes/autoload.php';

$jobModel = new Job();
$jobs = $jobModel->all();
```

---

## Best Practices

### 1. Use Autoloader
Always include the autoloader at the beginning of your files:
```php
require_once __DIR__ . '/classes/autoload.php';
```

### 2. Use Models Instead of Direct Queries
```php
// Good
$jobModel = new Job();
$jobs = $jobModel->search('developer');

// Avoid
$sql = "SELECT * FROM jobs WHERE title LIKE '%developer%'";
```

### 3. Handle Exceptions
```php
try {
    $jobModel = new Job();
    $jobId = $jobModel->create($data);
} catch (Exception $e) {
    error_log($e->getMessage());
    echo json_encode(['error' => 'Failed to create job']);
}
```

### 4. Validate Input
```php
$title = trim($_POST['title'] ?? '');
$company = trim($_POST['company'] ?? '');

if (empty($title) || empty($company)) {
    http_response_code(400);
    echo json_encode(['error' => 'Title and company are required']);
    exit;
}
```

---

## Testing

### Test Database Connection
```php
require_once 'classes/autoload.php';

try {
    $db = Database::getInstance();
    echo "Database connected successfully!";
} catch (Exception $e) {
    echo "Database connection failed: " . $e->getMessage();
}
```

### Test Job Model
```php
require_once 'classes/autoload.php';

$jobModel = new Job();

// Test find
$job = $jobModel->find(1);
print_r($job);

// Test search
$results = $jobModel->search('developer');
echo "Found " . count($results) . " jobs";

// Test statistics
$stats = $jobModel->getStatistics();
print_r($stats);
```

---

## Performance Considerations

### 1. Caching
External API responses are cached for 1 hour in `cache/api/` directory.

### 2. Prepared Statements
All queries use prepared statements to prevent SQL injection and improve performance.

### 3. Pagination
Use `limit` and `offset` parameters for large datasets:
```php
$jobs = $jobModel->all(50, 0); // First 50 jobs
$jobs = $jobModel->all(50, 50); // Next 50 jobs
```

---

## Security Features

1. **SQL Injection Prevention:** All queries use prepared statements
2. **Password Hashing:** Passwords are hashed using `password_hash()`
3. **Input Validation:** All user inputs are validated and sanitized
4. **API Authentication:** Token-based authentication for API endpoints
5. **Session Management:** Secure session handling

---

## Next Steps

1. **Update existing API files** to use OOP classes
2. **Configure external API credentials**
3. **Set up WebSocket server** for real-time features
4. **Test all endpoints** with the provided examples
5. **Implement additional features** as needed

---

## Troubleshooting

### Database Connection Issues
- Verify credentials in `classes/Database.php`
- Check database server status
- Ensure PDO extension is enabled

### WebSocket Server Won't Start
- Check if port 8080 is available
- Ensure PHP sockets extension is enabled
- Run: `php -m | grep sockets`

### API Returns 401 Unauthorized
- Verify API token is correct
- Check token configuration in API files
- Ensure token is passed correctly in request

---

## Contributing
When adding new features:
1. Create models in `classes/` directory
2. Follow existing naming conventions
3. Use prepared statements for database queries
4. Add proper error handling
5. Document new methods and classes

---

## Support
For questions or issues:
- Check this documentation
- Review API_DOCUMENTATION.md
- Test with provided examples
