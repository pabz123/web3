
# WebSocket Real-Time Notifications - Complete Integration Guide

## 🎯 What's Been Implemented

### Core Features
- ✅ Real-time WebSocket server with authentication
- ✅ Channel-based pub/sub system
- ✅ Rate limiting (10 messages/minute per client)
- ✅ Token-based authentication (15-minute expiry)
- ✅ Notification queue with automatic broadcasting
- ✅ Toast notifications with auto-hide
- ✅ Notification history panel with persistence
- ✅ Live connection status indicator

### Integrated Pages
1. **Student Dashboard** (`pages/student.php`)
   - Channels: jobs, applications, notifications
   - Real-time job updates
   - Application status changes
   - Toast notifications + history panel

2. **Employer Dashboard** (`pages/employer.php`)
   - Channels: applications, jobs, employer_notifications
   - New application alerts
   - Auto-refresh application count

3. **Admin Dashboard** (`pages/admin.php`)
   - Channels: admin_notifications, new_users, new_jobs, applications, reports
   - System-wide monitoring
   - Auto-refresh data tables on events

### Components Created
- `includes/notification_center.php` - Reusable notification history panel
- `test_websocket.html` - Advanced WebSocket testing console
- `test_notifications.html` - Simple notification sender
- `WEBSOCKET_TESTING.md` - Complete testing documentation

---

## 🚀 Quick Start (5 Minutes)

### 1. Start WebSocket Server
```powershell
cd C:\wamp64\www\career_hub
.\start_websocket.bat
```

Keep terminal open. Expected output:
```
===========================================
Career Hub WebSocket Server
===========================================

WebSocket Server started on 0.0.0.0:8080
Waiting for connections...
```

### 2. Test as Student
1. Log in as a student
2. Go to `pages/student.php`
3. Check bottom-right: status should show "Live" (green)
4. Click bell icon (🔔) in navbar to open notification history

### 3. Send Test Notification
Open in new tab: `test_notifications.html`
Click: **"Send: New Job Notification"**

**Watch student dashboard:**
- Toast slides in from top-right
- Bell icon shows badge (1)
- Notification saved to history panel

---

## 📝 How to Use in Your Code

### Send Notification from Backend

**Method 1: Via API (Recommended)**
```php
// From any PHP file
$data = [
    'channel' => 'jobs',
    'title' => 'New Job Posted',
    'message' => 'Check out: Software Engineer at TechCorp',
    'type' => 'info' // info|success|warning|error
];

// Queue it
$queueFile = __DIR__ . '/../cache/notifications.json';
$queue = file_exists($queueFile) ? json_decode(file_get_contents($queueFile), true) : [];
$queue[] = array_merge($data, ['timestamp' => time()]);
file_put_contents($queueFile, json_encode($queue));
```

**Method 2: Direct HTTP call**
```php
// Using cURL
$ch = curl_init('http://localhost/career_hub/api/v1/notify.php');
curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => http_build_query([
        'channel' => 'applications',
        'title' => 'New Application',
        'message' => 'John Doe applied for Software Engineer',
        'csrf_token' => $_SESSION['csrf_token'] ?? ''
    ]),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_COOKIE => session_name() . '=' . session_id()
]);
$response = curl_exec($ch);
curl_close($ch);
```

### Send to Specific User
```php
$queue[] = [
    'userId' => 123, // Target user ID
    'title' => 'Application Status Update',
    'message' => 'Your application was reviewed',
    'type' => 'success',
    'timestamp' => time()
];
```

### Frontend Integration

**Add to any page:**
```php
<!-- In <head> or before </body> -->
<?php include_once __DIR__ . '/../includes/notification_center.php'; ?>
<script src="../js/websocket-client.js"></script>

<script>
// Initialize WebSocket
const wsClient = new WebSocketClient('ws://localhost:8080');

wsClient.on('connected', () => {
    console.log('Connected');
    wsClient.subscribe('jobs'); // Subscribe to channels
});

wsClient.on('notification', (data) => {
    console.log('Notification:', data);
    
    // Add to history panel
    if (window.NotificationCenter) {
        window.NotificationCenter.add(data);
    }
    
    // Custom handling
    if (data.type === 'urgent') {
        alert(data.message);
    }
});

wsClient.connect();
</script>
```

---

## 🎨 Notification Types & Styling

### Available Types
- `info` - Blue gradient (default)
- `success` - Green gradient
- `warning` - Orange/yellow gradient
- `error` - Red gradient

### Custom Toast Colors
Toast notifications automatically match their type:
```javascript
showNotification({
    title: 'Success!',
    message: 'Job posted successfully',
    type: 'success' // Green toast
});
```

---

## 📊 Real-World Examples

### Example 1: New Job Posted (Employer → Students)
```php
// In api/create_job.php after job creation
$notification = [
    'channel' => 'jobs',
    'title' => 'New Job Posted',
    'message' => "$jobTitle at $company - Apply now!",
    'type' => 'info',
    'data' => [
        'job_id' => $newJobId,
        'url' => "/pages/jobs.php?id=$newJobId"
    ],
    'timestamp' => time()
];

$queueFile = __DIR__ . '/../cache/notifications.json';
$queue = file_exists($queueFile) ? json_decode(file_get_contents($queueFile), true) : [];
$queue[] = $notification;
file_put_contents($queueFile, json_encode($queue));
```

### Example 2: Application Status Change (Student notification)
```php
// In api/admin.php when changing application status
$notification = [
    'userId' => $studentId, // Send only to this student
    'title' => 'Application Update',
    'message' => "Your application status: $newStatus",
    'type' => 'success',
    'timestamp' => time()
];

// Queue it
$queueFile = __DIR__ . '/../cache/notifications.json';
$queue = file_exists($queueFile) ? json_decode(file_get_contents($queueFile), true) : [];
$queue[] = $notification;
file_put_contents($queueFile, json_encode($queue));
```

### Example 3: New Application (Employer notification)
```php
// In api/applications.php after student applies
$notification = [
    'userId' => $employerId, // Target employer
    'title' => 'New Application',
    'message' => "$studentName applied for $jobTitle",
    'type' => 'info',
    'data' => [
        'application_id' => $applicationId,
        'student_id' => $studentId,
        'job_id' => $jobId
    ],
    'timestamp' => time()
];

// Queue it
$queueFile = __DIR__ . '/../cache/notifications.json';
$queue = file_exists($queueFile) ? json_decode(file_get_contents($queueFile), true) : [];
$queue[] = $notification;
file_put_contents($queueFile, json_encode($queue));
```

---

## 🔧 Troubleshooting

### Connection Issues

**Problem:** Status shows "Offline"
- **Solution:** Ensure WebSocket server is running (`start_websocket.bat`)
- Check port 8080 not blocked by firewall
- Verify browser console for errors

**Problem:** "Failed to get auth token"
- **Solution:** User must be logged in
- Check `/api/auth/ws_token.php` is accessible
- Verify session is active

### Notification Issues

**Problem:** Notifications not appearing
- **Solution:** Check `cache/notifications.json` file exists and has content
- Ensure subscribed to correct channel
- Server processes queue every 5 seconds - wait briefly
- Check server console for "Broadcasting to channel" messages

**Problem:** Bell icon doesn't show count
- **Solution:** Notifications saved to `localStorage`
- Clear browser cache and test again
- Check browser console for JavaScript errors

### Rate Limiting

**Problem:** "Rate limit exceeded"
- **Solution:** Default: 10 messages per 60 seconds
- Wait 60 seconds
- Or restart WebSocket server to reset limits

---

## 🎯 Production Deployment

### Windows Service Setup
1. Install NSSM (Non-Sucking Service Manager)
2. Create service:
   ```powershell
   nssm install CareerHubWebSocket "C:\wamp64\bin\php\php8.x.x\php.exe"
   nssm set CareerHubWebSocket AppDirectory "C:\wamp64\www\career_hub"
   nssm set CareerHubWebSocket AppParameters "websocket_server.php"
   nssm start CareerHubWebSocket
   ```

### SSL/WSS (Secure WebSocket)
- Get SSL certificate for your domain
- Use reverse proxy (nginx/Apache) to forward wss:// to ws://localhost:8080
- Update client: `new WebSocketClient('wss://yourdomain.com/ws')`

### Firewall Configuration
- Allow inbound TCP port 8080
- For production, use standard ports (443 for wss://)

### Monitoring
- Log to file instead of console
- Set up process monitoring (restart on crash)
- Monitor `cache/notifications.json` size (clear old entries periodically)

---

## 📚 API Reference

### WebSocket Client Methods

```javascript
const ws = new WebSocketClient('ws://localhost:8080');

// Events
ws.on('connected', callback);
ws.on('disconnected', callback);
ws.on('error', callback);
ws.on('notification', callback);
ws.on('message', callback);

// Methods
ws.connect();
ws.disconnect();
ws.subscribe(channel);
ws.unsubscribe(channel);
ws.send(data);
```

### Notification Center API

```javascript
// Add notification to history
window.NotificationCenter.add({
    title: 'Title',
    message: 'Message',
    type: 'info' // info|success|warning|error
});

// Mark as read
window.NotificationCenter.markAsRead(notificationId);

// Mark all as read
window.NotificationCenter.markAllAsRead();

// Clear all
window.NotificationCenter.clear();
```

---

## 🎓 Next Steps

1. **Add more notification triggers:**
   - User registration
   - Profile updates
   - Deadline reminders
   - Interview scheduling

2. **Enhance UI:**
   - Sound alerts
   - Desktop notifications (Notification API)
   - Custom notification templates
   - Filter by type in history

3. **Advanced features:**
   - Read receipts
   - Notification preferences
   - Email fallback for offline users
   - Push notifications (PWA)

---

## 📞 Support

**Test Pages:**
- Advanced testing: `test_websocket.html`
- Simple sender: `test_notifications.html`

**Documentation:**
- `WEBSOCKET_TESTING.md` - Complete testing guide
- `API_DOCUMENTATION.md` - General API docs

**Files Modified:**
- `pages/student.php` - Student notifications
- `pages/employer.php` - Employer notifications
- `pages/admin.php` - Admin notifications
- `includes/notification_center.php` - History panel
- `classes/WebSocketServer.php` - Core server
- `js/websocket-client.js` - Client library

**Logs:**
- Server console (terminal running `start_websocket.bat`)
- Browser console (F12)
- `cache/notifications.json` - Queue file
- `cache/ws_tokens/` - Auth tokens

---

Made with ❤️ for Career Hub
