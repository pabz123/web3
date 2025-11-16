# MySQLi Implementation Notes

## ✅ All Classes Now Use MySQLi

Your Career Hub OOP classes have been implemented using **mysqli** (not PDO) to match your existing codebase.

---

## Updated Classes

### 1. Database.php
- Uses `mysqli` connection object
- Singleton pattern with mysqli
- Methods: `getConnection()`, `escape()`, `lastInsertId()`, `close()`

**Example:**
```php
$db = Database::getInstance();
$conn = $db->getConnection(); // Returns mysqli object
```

### 2. Model.php (Base Class)
- All prepared statements use `?` placeholders (mysqli style)
- Uses `bind_param()` with type strings ("i", "s", "d")
- Uses `get_result()` and `fetch_assoc()`
- All statements are properly closed

**Key Methods:**
- `find($id)` - Find by ID
- `all($limit, $offset)` - Get all records
- `create($data)` - Insert new record
- `update($id, $data)` - Update record
- `delete($id)` - Delete record
- `query($sql, $types, $params)` - Custom query
- `queryOne($sql, $types, $params)` - Custom query, single row

### 3. Job.php
- All methods use mysqli prepared statements
- Type binding: `bind_param("ssss", ...)` for search
- Proper statement closing

### 4. User.php
- Email/username lookup uses mysqli
- Password verification with mysqli
- Role-based queries

### 5. Application.php
- JOIN queries with mysqli
- Application tracking with prepared statements
- Statistics using mysqli

---

## MySQLi vs PDO Differences

### Before (PDO - NOT USED):
```php
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
$stmt->execute([':id' => $userId]);
$user = $stmt->fetch();
```

### After (MySQLi - IMPLEMENTED):
```php
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
```

---

## Type Strings for bind_param()

When using mysqli prepared statements, specify types:

| Type | Character | PHP Type |
|------|-----------|----------|
| Integer | `i` | `int` |
| Double | `d` | `float` |
| String | `s` | `string` |
| Blob | `b` | `blob` |

**Examples:**
```php
// One integer parameter
$stmt->bind_param("i", $userId);

// String parameter
$stmt->bind_param("s", $email);

// Multiple parameters: int, string, string
$stmt->bind_param("iss", $id, $name, $email);

// Four strings (search query)
$stmt->bind_param("ssss", $term, $term, $term, $term);
```

---

## Usage Examples

### Example 1: Find Job by ID
```php
require_once 'classes/autoload.php';

$jobModel = new Job();
$job = $jobModel->find(5); // Uses mysqli internally

print_r($job);
```

### Example 2: Search Jobs
```php
$jobModel = new Job();
$results = $jobModel->search('developer');

// Internally executes:
// $stmt = $conn->prepare("SELECT * FROM jobs WHERE title LIKE ? ...");
// $stmt->bind_param("ssss", $term, $term, $term, $term);
```

### Example 3: Create New User
```php
$userModel = new User();
$userId = $userModel->create([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'password' => password_hash('secret', PASSWORD_DEFAULT),
    'role' => 'student',
    'created_at' => date('Y-m-d H:i:s')
]);

// Internally uses mysqli prepared statement with bind_param()
```

### Example 4: Custom Query
```php
$jobModel = new Job();

// Using the protected query() method in your model
$sql = "SELECT * FROM jobs WHERE company = ? AND type = ?";
$results = $jobModel->query($sql, "ss", ['Tech Corp', 'full-time']);
```

---

## Key Features

### ✅ Security
- All queries use prepared statements
- Automatic type detection in Model::create()
- Protection against SQL injection

### ✅ Performance
- Connection pooling (Singleton)
- Proper statement closing
- Efficient parameter binding

### ✅ Compatibility
- Matches your existing mysqli code
- Works with your current database setup
- No PDO dependencies

### ✅ Error Handling
- mysqli_report for exceptions
- Try-catch blocks
- Error logging

---

## Migration from Existing Code

### Old Procedural mysqli Code:
```php
require_once 'includes/db.php';

$stmt = $conn->prepare("SELECT * FROM jobs WHERE id = ?");
$stmt->bind_param("i", $jobId);
$stmt->execute();
$result = $stmt->get_result();
$job = $result->fetch_assoc();
$stmt->close();
```

### New OOP mysqli Code:
```php
require_once 'classes/autoload.php';

$jobModel = new Job();
$job = $jobModel->find($jobId);
```

**Same security, cleaner code!**

---

## Compatibility Notes

### ✅ Compatible With:
- Your existing `includes/db.php` (mysqli)
- All mysqli-based code in `api/` folder
- Current database structure
- PHP 7.0+

### ⚠️ Not Compatible With:
- PDO-based code (not used in your project)
- Old mysql_* functions (deprecated)

---

## Testing

### Test Database Connection:
```php
require_once 'classes/autoload.php';

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    echo "✅ Connected: " . $conn->host_info . "\n";
    echo "✅ Database: " . $conn->get_charset() . " charset\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
```

### Test Job Model:
```php
require_once 'classes/autoload.php';

$jobModel = new Job();

// Test find
$job = $jobModel->find(1);
var_dump($job);

// Test search
$results = $jobModel->search('developer');
echo "Found " . count($results) . " jobs\n";

// Test create
$id = $jobModel->create([
    'title' => 'Test Job',
    'company' => 'Test Company',
    'description' => 'Test description',
    'location' => 'Remote',
    'type' => 'full-time',
    'created_at' => date('Y-m-d H:i:s')
]);
echo "Created job ID: $id\n";
```

---

## Common Patterns

### Pattern 1: Simple Select
```php
$stmt = $this->conn->prepare("SELECT * FROM table WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();
return $row;
```

### Pattern 2: Multiple Results
```php
$stmt = $this->conn->prepare("SELECT * FROM table WHERE type = ?");
$stmt->bind_param("s", $type);
$stmt->execute();
$result = $stmt->get_result();
$rows = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
return $rows;
```

### Pattern 3: Insert
```php
$stmt = $this->conn->prepare("INSERT INTO table (col1, col2) VALUES (?, ?)");
$stmt->bind_param("ss", $val1, $val2);
$stmt->execute();
$id = $this->conn->insert_id;
$stmt->close();
return $id;
```

### Pattern 4: Update
```php
$stmt = $this->conn->prepare("UPDATE table SET col = ? WHERE id = ?");
$stmt->bind_param("si", $value, $id);
$result = $stmt->execute();
$stmt->close();
return $result;
```

---

## Advantages of This Implementation

### 1. **Consistent with Your Codebase**
- Uses mysqli like your existing code
- No mixed PDO/mysqli confusion
- Easy integration

### 2. **Secure by Default**
- All queries are prepared statements
- Automatic parameter binding
- Type safety

### 3. **Clean & Maintainable**
- OOP structure
- Reusable code
- Easy to extend

### 4. **Performance**
- Singleton connection
- Statement reuse
- Efficient queries

### 5. **Developer Friendly**
- Simple API
- Clear method names
- Good documentation

---

## Troubleshooting

### Issue: "Call to undefined method mysqli_result::fetch_all()"
**Solution:** Requires mysqlnd driver. Check with:
```php
echo function_exists('mysqli_fetch_all') ? 'Yes' : 'No';
```

If missing, use loop instead:
```php
$rows = [];
while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
}
```

### Issue: "Commands out of sync"
**Solution:** Always close statements and fetch all results:
```php
$result = $stmt->get_result();
$rows = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close(); // Important!
```

### Issue: Prepared statement errors
**Solution:** Enable error reporting:
```php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
```

---

## Summary

✅ **All OOP classes use mysqli**
✅ **Prepared statements throughout**
✅ **Compatible with your existing code**
✅ **Secure and efficient**
✅ **Easy to use and maintain**

Your Career Hub now has professional OOP architecture using mysqli, matching your existing database layer perfectly!

---

## Quick Reference

```php
// Connect
$db = Database::getInstance();
$conn = $db->getConnection();

// Models
$jobModel = new Job();
$userModel = new User();
$appModel = new Application();

// CRUD Operations
$job = $jobModel->find($id);           // Read one
$jobs = $jobModel->all(50, 0);         // Read all
$id = $jobModel->create($data);        // Create
$jobModel->update($id, $data);         // Update
$jobModel->delete($id);                // Delete

// Custom methods
$jobs = $jobModel->search('developer');
$stats = $jobModel->getStatistics();
$user = $userModel->authenticate($email, $pass);
```

**Everything uses mysqli! 🎉**
