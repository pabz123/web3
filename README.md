# Career Connect Hub - Project Architecture

## 📋 Table of Contents
1. [Project Overview](#project-overview)
2. [Core Concepts Explained](#core-concepts-explained)
   - [What are APIs?](#what-are-apis)
   - [What are WebSockets?](#what-are-websockets)
3. [Directory Structure](#directory-structure)
4. [System Architecture Diagram](#system-architecture-diagram)
5. [Data Flow Diagrams](#data-flow-diagrams)
6. [Authentication Flow](#authentication-flow)
7. [WebSocket Real-Time Communication](#websocket-real-time-communication)
8. [Database Schema Relationships](#database-schema-relationships)
9. [File Relationships](#file-relationships)
10. [Detailed User Flow & Technical Implementation](#detailed-user-flow--technical-implementation)

---

## 📖 Project Overview

Career Connect Hub is a modular, full-stack web application designed to connect students and recent graduates with employers and recruiters. It combines traditional HTTP REST APIs for CRUD operations with a persistent WebSocket layer for instant, low-latency notifications. The codebase follows a clear separation of concerns: frontend presentation (PHP + HTML + JS), application logic (PHP APIs, OOP classes), and the data layer (MySQL).

Key capabilities:
- Role-based authentication and session management (Students, Employers, Admins)
- Job posting, search and filtering (responsive UI)
- Secure application submission with CV upload and server-side validation
- Real-time notifications and live dashboard metrics via a custom PHP WebSocket server
- Internal REST APIs for frontend and third-party APIs for importing external job listings
- Object-oriented PHP models and a Database abstraction using mysqli (OOP style)

Primary design goals:
- Clear separation between synchronous requests (HTTP APIs) and asynchronous events (WebSockets)
- Security-first approach: prepared statements, bcrypt password hashing, CSRF tokens, and session hardening
- Responsive frontend UI (mobile-first CSS and progressive enhancement)
- Maintainability: small, single-responsibility PHP classes in classes/ and reusable includes/ middleware

Technology highlights (what you should know):
- WebSockets: Custom PHP socket server (see classes/WebSocketServer.php and websocket_server.php) listening on port 8080. Implements handshake, masking/unmasking, channel pub/sub, targeted user routing, rate limiting and a persistent notification queue for offline delivery.
- Database connectivity: Uses mysqli in object-oriented style via a Database wrapper class (see classes/Database.php). All queries use prepared statements; the models (User, Job, Application) encapsulate data access.
- APIs: Internal RESTful endpoints live under api/ (e.g. get_jobs.php, applications.php). These endpoints accept JSON or form data, validate and sanitize inputs, interact with model classes and return JSON responses.
- External APIs: import_external_jobs.php and classes/ExternalAPIService.php consume third-party job providers via cURL with API keys, parse and persist results into local tables.
- Frontend responsiveness: CSS in css/ follows a mobile-first approach with responsive breakpoints; JavaScript modules debounce expensive actions (search), lazy-load images and use progressive enhancement for older browsers.

---

## 🔄 Activity Diagram (High level — Student apply flow)


Start -> Open Job Listing Page
Open Job Listing Page -> Click Apply Button
Click Apply Button -> Load Apply Form (auth_check)
Load Apply Form -> Attach CV + Enter Cover Letter
Attach CV + Enter Cover Letter -> Submit Form (AJAX multipart/form-data)
Submit Form -> API applications.php validates and stores file
API applications.php -> Insert application record in MySQL
Insert application record -> Queue WebSocket notification for employer
Queue WebSocket notification -> WebSocketServer reads queue and delivers
WebSocketServer -> Employer receives push (if online)
Employer -> Reviews application -> Updates status via API
Update status -> WebSocket push to student
End


Notes:
- Each arrow corresponds to an HTTP request or an internal server action. File storage and DB operations are done server-side. The queued notification ensures offline employers still get the event when they reconnect.

---

## 🔁 Collaboration / Sequence Diagram (Simplified ASCII)

Scenario: Student applies → Employer notified → Employer updates status → Student notified


Student Browser      API Server        MySQL DB        WebSocket Server      Employer Browser
     |                  |                |                  |                    |
     | ---POST apply--->|                |                  |                    |
     |                  |--validate----->|                  |                    |
     |                  |--store file--->|                  |                    |
     |                  |--INSERT appl-->|                  |                    |
     |                  |<--app_id-------|                  |                    |
     |<--200 {ok}-------|                |                  |                    |
     |                  |--queue notif-->|                  |                    |
     |                  |                |--queue file----->|                    |
     |                  |                |                  |--read queue------->|
     |                  |                |                  |--deliver to emp--->|
     |                  |                |                  |                    |
Employer Browser     (when employer acts)                (server-side)
     |                  |                |                  |                    |
     | ---POST status-> |                |                  |                    |
     |                  |--UPDATE appl-->|                  |                    |
     |                  |--push notif--->|                  |                    |
     |                  |                |                  |--deliver to stud--->|
     |                  |                |                  |                    |


This sequence illustrates the key collaborators and the order of operations, showing how the API server, database, and WebSocket server coordinate to deliver real-time updates.

---

## Implementation details to emphasise

- WebSocket server is implemented as a long-running PHP CLI script (websocket_server.php) that uses low-level socket functions (socket_create, socket_bind, socket_listen, socket_select, socket_read, socket_write). The server performs the WebSocket handshake, keeps a map of userId→connection (for targeted pushes), supports channel subscriptions for pub/sub, and periodically reads a JSON file queue for offline notifications.

- Database access is centralized: classes/Database.php exposes an OOP mysqli wrapper. Typical usage in models:

php
$db = Database::getInstance();
$conn = $db->getConnection(); // returns mysqli object
$stmt = $conn->prepare('SELECT id FROM users WHERE email=?');
$stmt->bind_param('s', $email);
$stmt->execute();


All queries use prepared statements and models return arrays or objects for the API layer to encode as JSON.

- Internal APIs follow a thin-controller approach: validate input → call model → return JSON. APIs set proper HTTP status codes and consistent response shapes (success, error, data).

- External APIs are consumed by a service class ExternalAPIService that handles authentication (API keys from config), rate limiting (backoff/retry), and normalizes remote job payloads into the local jobs schema before insert.

- Responsiveness and frontend UX:
  - CSS: mobile-first with breakpoints at 480px, 768px, 1024px
  - JS: debounce for search (300ms), lazy-loading for images, progressive enhancement for critical functionality (forms work without JS, but enhanced with AJAX when available)
  - Accessibility: semantic HTML, labels for inputs, ARIA where necessary

---

If you'd like, I can now:
1. Convert these ASCII diagrams into downloadable slides (markdown slides or a PPTX) highlighting features.  
2. Insert activity & collaboration diagrams in a PlantUML block (if you plan to render them with PlantUML).  
3. Expand the Database/mysqli OOP examples with exact snippets from classes/Database.php and Job.php.

Tell me which next step you prefer and I will continue.

---

## 🎓 Core Concepts Explained

### What are APIs?

*API* stands for *Application Programming Interface. Think of it as a **waiter in a restaurant*:


You (Frontend) → Waiter (API) → Kitchen (Backend/Database)
                    ↓
You (Frontend) ← Waiter (API) ← Kitchen (Backend/Database)


#### *Real-World Analogy:*
- *You* (the customer) don't go into the kitchen to cook your own food
- *The waiter* takes your order, brings it to the kitchen, and returns with your meal
- *The kitchen* prepares the food but never interacts directly with customers
- *The menu* is like API documentation - tells you what you can order

#### *In Career Connect Hub:*

*Example 1: Searching for Jobs*

javascript
// Frontend JavaScript (You = the customer)
fetch('/api/search_jobs.php', {
    method: 'POST',
    body: JSON.stringify({
        query: 'Software Engineer',
        location: 'Remote'
    })
})

.then(response => response.json())  // Waiter brings back your order
.then(data => {
    console.log(data.jobs);  // You receive the food (data)
});


*What happens behind the scenes:*


1. Browser sends HTTP POST request to /api/search_jobs.php
   ↓
2. API endpoint (search_jobs.php) receives the request
   - Validates the input (is 'query' provided?)
   - Authenticates user (is user logged in?)
   ↓
3. API talks to Database (the kitchen)
   - SELECT * FROM jobs WHERE title LIKE '%Software Engineer%'
   ↓
4. Database returns results (50 job listings)
   ↓
5. API formats the data as JSON
   {
     "success": true,
     "jobs": [
       {"id": 1, "title": "Software Engineer", "company": "TechCorp"},
       {"id": 2, "title": "Senior Software Engineer", "company": "StartupXYZ"},
       ...
     ],
     "total": 50
   }
   ↓
6. Browser receives JSON response and displays jobs


#### *Types of APIs:*

*1. RESTful API* (What Career Connect Hub uses)
- *REST* = Representational State Transfer
- Uses standard HTTP methods:
  - *GET*: Retrieve data (e.g., get list of jobs)
  - *POST*: Create new data (e.g., submit job application)
  - *PUT*: Update existing data (e.g., update profile)
  - *DELETE*: Remove data (e.g., delete job posting)

*Example API Endpoints in This Project:*

| Endpoint | Method | Purpose | Example Request |
|----------|--------|---------|-----------------|
| /api/get_jobs.php | GET | Fetch all jobs | GET /api/get_jobs.php?limit=20 |
| /api/applications.php | POST | Submit application | POST /api/applications.php with form data |
| /api/update_application_status.php | POST | Update app status | POST with {application_id: 123, status: 'reviewed'} |
| /api/admin_users.php | GET | Get all users (admin) | GET /api/admin_users.php |

*2. External APIs* (Third-party services)
- Career Connect Hub also *consumes* external APIs
- Example: import_external_jobs.php fetches jobs from remote APIs like:
  - https://api.remotejobs.io/v1/jobs
  - Sends request with API key for authentication
  - Receives job listings from external companies
  - Saves them to local database

#### *Why Use APIs?*

✅ *Separation of Concerns*: Frontend doesn't need to know database structure  
✅ *Security*: Database credentials never exposed to browser  
✅ *Reusability*: Same API can serve web, mobile app, desktop app  
✅ *Scalability*: Can add caching, load balancing at API layer  
✅ *Flexibility*: Can change database without changing frontend  

#### *API Request/Response Cycle:*


┌─────────────┐
│   Browser   │ (Frontend - JavaScript)
└──────┬──────┘
       │ 1. HTTP Request
       │    POST /api/applications.php
       │    Body: {job_id: 42, cover_letter: "..."}
       ↓
┌─────────────┐
│  API Server │ (Backend - PHP)
│             │ 2. Validate input
│             │ 3. Check authentication
│             │ 4. Process business logic
└──────┬──────┘
       │ 5. Query Database
       ↓
┌─────────────┐
│  Database   │ (MySQL)
│             │ 6. Execute SQL
│             │ 7. Return results
└──────┬──────┘
       │ 8. Format response
       ↓
┌─────────────┐
│  API Server │ 9. Send JSON response
└──────┬──────┘
       │ 10. HTTP Response
       │     {success: true, application_id: 123}
       ↓
┌─────────────┐
│   Browser   │ 11. Update UI
└─────────────┘


#### *API Response Formats:*

*Success Response:*
json
{
  "success": true,
  "message": "Application submitted successfully",
  "data": {
    "application_id": 123,
    "status": "pending"
  }
}


*Error Response:*
json
{
  "success": false,
  "error": "Invalid job ID",
  "code": 400
}


#### *API Security in This Project:*

1. *Session-Based Authentication*: 
   - User must be logged in (session cookie checked)
   - auth_check.php verifies session before API executes

2. *CSRF Protection*:
   - Token included in forms prevents cross-site attacks
   - API validates token matches session

3. *Input Validation*:
   - Email format validation
   - SQL injection prevention (prepared statements)
   - File upload validation (size, type)

4. *Rate Limiting*:
   - Prevents spam/abuse
   - Max 30 requests per 10 seconds per user

---

### What are WebSockets?

*WebSocket* is a *two-way communication channel* between browser and server. Unlike APIs where the browser must *ask* for data, WebSockets allow the server to *push* data to the browser instantly.

#### *Restaurant Analogy Revisited:*

*Traditional API (HTTP Request/Response):*

You: "Waiter, is my food ready?"
Waiter: "No, not yet."
(5 seconds later)
You: "Waiter, is my food ready?"
Waiter: "No, still cooking."
(5 seconds later)
You: "Waiter, is my food ready?"
Waiter: "Yes! Here it is."

☹ *Problem*: You keep asking (polling) - inefficient and annoying

*WebSocket (Persistent Connection):*

You: "Waiter, tell me when my food is ready."
Waiter: "Sure, I'll let you know."
(You wait patiently)
(5 minutes later)
Waiter: "Your food is ready!" (comes to you)

😊 *Solution*: Server notifies you when event happens - efficient and instant

#### *Technical Comparison:*

| Feature | HTTP/AJAX (API) | WebSocket |
|---------|-----------------|-----------|
| *Connection* | New connection per request | Single persistent connection |
| *Direction* | One-way: Client → Server → Client | Two-way: Client ↔ Server |
| *Latency* | Higher (multiple round-trips) | Lower (instant push) |
| *Overhead* | HTTP headers sent each time (~500 bytes) | Minimal framing (~2-6 bytes) |
| *Use Case* | Fetch data on demand | Real-time updates, notifications |
| *Example* | Load job listings | New job notification appears |

#### *How WebSockets Work in Career Connect Hub:*

*Step 1: Initial Handshake (HTTP → WebSocket Upgrade)*


Browser → Server: HTTP GET /websocket
Upgrade: websocket
Connection: Upgrade
Sec-WebSocket-Key: dGhlIHNhbXBsZSBub25jZQ==
                    ↓
Server → Browser: HTTP 101 Switching Protocols
Upgrade: websocket
Connection: Upgrade
Sec-WebSocket-Accept: s3pPLMBiTxaQ9kYGzzhZRbK+xOo=
                    ↓
         Connection Upgraded!
    Now using WebSocket protocol


*Step 2: Persistent Connection*


┌──────────────────────────────────────────────────┐
│         Browser (websocket-client.js)            │
│                                                  │
│  const ws = new WebSocket('ws://localhost:8080') │
└───────────────────┬──────────────────────────────┘
                    │
                    │ Persistent TCP Connection
                    │ (stays open for hours/days)
                    │
┌───────────────────┴──────────────────────────────┐
│         Server (WebSocketServer.php)             │
│                                                  │
│  While(true) { socket_select($clients) }         │
└──────────────────────────────────────────────────┘


*Step 3: Real-Time Communication*


Scenario: Employer reviews student's application

┌─────────────┐                    ┌─────────────┐
│  Employer   │                    │   Student   │
│   Browser   │                    │   Browser   │
└──────┬──────┘                    └──────┬──────┘
       │                                  │
       │ 1. Click "Reviewed" button      │
       │                                  │
       │ 2. AJAX POST to API              │
       │    /api/update_application.php   │
       │         ↓                        │
       │    ┌─────────┐                  │
       │    │ API     │ 3. Update DB     │
       │    │ Server  │    status='reviewed'
       │    └────┬────┘                  │
       │         │                        │
       │         │ 4. Send to WebSocket   │
       │         │    Server              │
       │         ↓                        │
       │    ┌──────────────┐             │
       │    │  WebSocket   │ 5. Find     │
       │    │   Server     │    student's│
       │    │              │    connection
       │    └──────┬───────┘             │
       │           │                      │
       │           │ 6. PUSH notification │
       │           └──────────────────────→
       │                                  │
       │                   7. Browser shows notification
       │                      "Application Reviewed!"
       │                                  │


*Instant Delivery*: Student sees update in ~50ms without refreshing page!

#### *WebSocket Message Types in Career Connect Hub:*

*1. Registration* (Associate connection with user)
javascript
// Browser sends:
{
  "type": "register",
  "userId": 15
}

// Server stores: userConnections[15] = client_socket
// Now server knows which socket belongs to user #15


*2. Channel Subscription* (Pub/Sub pattern)
javascript
// Browser sends:
{
  "type": "subscribe",
  "channel": "job_updates"
}

// Server adds client to channel
// channels['job_updates'] = [client1, client2, client3, ...]


*3. Broadcast to Channel*
javascript
// Server sends to all subscribers of 'job_updates':
{
  "type": "job_notification",
  "title": "Senior Developer",
  "company": "TechCorp",
  "jobId": 42
}

// All students subscribed to 'job_updates' receive instantly


*4. Targeted Message* (Send to specific user)
javascript
// Server sends to user #15 only:
{
  "type": "application_update",
  "status": "reviewed",
  "jobTitle": "Software Engineer"
}

// Only user #15's browser receives this message


*5. Ping/Pong* (Keep connection alive)
javascript
// Browser sends every 30 seconds:
{"type": "ping"}

// Server responds:
{"type": "pong"}

// Prevents connection timeout


#### *WebSocket Frame Structure (Binary Protocol):*

Unlike HTTP (text-based), WebSocket uses binary frames:


Client → Server (Masked Frame):
┌────┬──────┬──────┬────────┬─────────┬─────────┐
│FIN │Opcode│Mask  │Length  │Mask Key │ Payload │
│ 1  │ 0x1  │  1   │  123   │4 bytes  │123 bytes│
└────┴──────┴──────┴────────┴─────────┴─────────┘

Server → Client (Unmasked Frame):
┌────┬──────┬──────┬────────┬─────────┐
│FIN │Opcode│Mask  │Length  │ Payload │
│ 1  │ 0x1  │  0   │  150   │150 bytes│
└────┴──────┴──────┴────────┴─────────┘


*Fields Explained:*
- *FIN*: 1 = final frame (message complete)
- *Opcode*: 0x1 = text frame, 0x2 = binary, 0x8 = close
- *Mask*: 1 = payload is masked (required client → server)
- *Length*: Payload size (7 bits, 16 bits, or 64 bits)
- *Mask Key*: 4-byte random key for XOR masking
- *Payload*: Actual message (JSON, text, binary)

*Why Masking?*
- Security: Prevents cache poisoning attacks
- Required by spec for client → server messages
- Server → client messages are NOT masked

#### *WebSocket Use Cases in Career Connect Hub:*

| Feature | Without WebSocket | With WebSocket |
|---------|-------------------|----------------|
| *New Job Posted* | Refresh page to see | Notification appears instantly |
| *Application Status* | Check every 5 mins | Update pushed immediately |
| *Admin Dashboard* | Poll API every 3s | Real-time stats update |
| *Chat (future)* | Impossible efficiently | Natural chat experience |
| *Live Job Count* | Stale data | Always current |

#### *Advantages of WebSockets:*

✅ *Real-Time*: Updates appear instantly (50-100ms latency)  
✅ *Efficient*: Single connection vs. hundreds of HTTP requests  
✅ *Bidirectional*: Server can push without client asking  
✅ *Lower Bandwidth*: Minimal frame overhead (~2 bytes vs. ~500 bytes HTTP)  
✅ *Better UX*: Users see updates without manual refresh  

#### *Challenges & Solutions:*

| Challenge | Solution in This Project |
|-----------|--------------------------|
| *Connection drops* | Auto-reconnect with exponential backoff (3s, 6s, 12s) |
| *Scaling* | Can use Redis pub/sub for multi-server WebSocket |
| *Firewall issues* | Fallback to long-polling (not implemented yet) |
| *Rate limiting* | Max 30 messages per 10 seconds per client |
| *Offline users* | Queue notifications in cache/notifications.json |

#### *WebSocket vs. HTTP API Decision Matrix:*

*Use HTTP API when:*
- ❓ User explicitly requests data (search, load profile)
- 📊 Data is large (job listings, user profiles)
- 💾 Response can be cached
- 🔒 Need RESTful architecture for public API

*Use WebSocket when:*
- ⚡ Need instant updates (notifications, alerts)
- 💬 Bidirectional communication (chat, collaboration)
- 🔄 Frequent small updates (live counters, status)
- 📡 Server-initiated events (new job, app status change)

#### *Career Connect Hub Architecture:*


┌─────────────────────────────────────────────┐
│              Frontend (Browser)             │
│                                             │
│  ┌─────────────────┐  ┌──────────────────┐ │
│  │   HTTP AJAX     │  │   WebSocket      │ │
│  │   (API Calls)   │  │   (Real-Time)    │ │
│  └────────┬────────┘  └────────┬─────────┘ │
└───────────┼────────────────────┼───────────┘
            │                    │
            │                    │
    ┌───────▼─────────┐  ┌───────▼──────────┐
    │   PHP API       │  │  WebSocket       │
    │   Endpoints     │  │  Server          │
    │  (REST)         │  │  (PHP Sockets)   │
    │                 │  │                  │
    │ - get_jobs.php  │  │ Port: 8080       │
    │ - applications  │  │ Protocol: ws://  │
    │ - admin_users   │  │                  │
    └────────┬────────┘  └───────┬──────────┘
             │                    │
             │    Both access     │
             │                    │
             └──────────┬─────────┘
                        │
                ┌───────▼────────┐
                │   MySQL DB     │
                │   (Data Store) │
                └────────────────┘


*Data Flow Example:*

1. *Student searches jobs*: HTTP API (/api/search_jobs.php)
2. *Employer posts job*: HTTP API (/api/create_job.php)
3. *Server notifies students*: WebSocket broadcast (instant)
4. *Students see notification*: WebSocket receives push
5. *Student applies*: HTTP API (/api/applications.php)
6. *Employer gets alert*: WebSocket push to employer

#### *WebSocket Client Code (Simplified):*

javascript
// Create connection
const ws = new WebSocket('ws://localhost:8080');

// Connection opened
ws.onopen = () => {
    console.log('Connected to WebSocket');
    
    // Register user
    ws.send(JSON.stringify({
        type: 'register',
        userId: 15
    }));
    
    // Subscribe to job updates
    ws.send(JSON.stringify({
        type: 'subscribe',
        channel: 'job_updates'
    }));
};

// Receive messages
ws.onmessage = (event) => {
    const data = JSON.parse(event.data);
    
    if (data.type === 'job_notification') {
        // Show notification
        showNotification(`New Job: ${data.title} at ${data.company}`);
    }
    
    if (data.type === 'application_update') {
        // Update UI
        updateApplicationStatus(data.applicationId, data.status);
    }
};

// Connection closed
ws.onclose = () => {
    console.log('WebSocket disconnected');
    // Auto-reconnect after 3 seconds
    setTimeout(() => reconnect(), 3000);
};


#### *WebSocket Server Code (Simplified):*

php
// Main event loop
while (true) {
    // Wait for activity on any socket (new connection or message)
    socket_select($clients, $write, $except, 0, 100000);
    
    // Check for new connections
    if (has_new_connection()) {
        $newClient = socket_accept($socket);
        performHandshake($newClient);  // HTTP → WebSocket upgrade
        $clients[] = $newClient;
    }
    
    // Check for messages from connected clients
    foreach ($clients as $client) {
        $data = socket_read($client, 1024);
        $message = unmask($data);  // Decode WebSocket frame
        handleMessage($client, $message);
    }
}

// Handle incoming message
function handleMessage($client, $message) {
    $data = json_decode($message);
    
    switch ($data['type']) {
        case 'register':
            // Map userId to socket connection
            $userConnections[$data['userId']] = $client;
            break;
            
        case 'subscribe':
            // Add client to channel
            $channels[$data['channel']][] = $client;
            break;
    }
}

// Send message to specific user
function sendToUser($userId, $message) {
    $client = $userConnections[$userId];
    $masked = mask($message);  // Encode as WebSocket frame
    socket_write($client, $masked);
}

// Broadcast to all channel subscribers
function broadcastToChannel($channel, $message) {
    foreach ($channels[$channel] as $client) {
        socket_write($client, mask($message));
    }
}


---

## 🔑 Key Takeaways

### APIs (HTTP/REST):
- 🍽 *Like a restaurant waiter*: Takes your order, brings food
- 📞 *Request-Response*: You ask, server answers
- 🔄 *Stateless*: Each request is independent
- 📊 *Best for*: Fetching data, creating/updating records
- *Career Connect Hub Examples*: Search jobs, submit application, load profile

### WebSockets:
- 📡 *Like a phone call*: Two-way conversation stays open
- ⚡ *Push-Based*: Server sends updates without being asked
- 🔌 *Stateful*: Connection persists for session
- ⏱ *Best for*: Real-time updates, notifications, chat
- *Career Connect Hub Examples*: Job alerts, application status updates, admin live stats

### When to Use Each:

*Use API (HTTP/REST)* for:
- Loading pages
- Submitting forms
- Searching/filtering data
- CRUD operations (Create, Read, Update, Delete)

*Use WebSocket* for:
- Live notifications
- Real-time status updates
- Instant messaging
- Live dashboard metrics
- Collaborative features

---

## 📁 Directory Structure


career_hub/
│
├── pages/                      # Frontend pages (PHP + HTML)
│   ├── admin-login.php         # Admin authentication
│   ├── admin.php               # Admin dashboard
│   ├── login.php               # User login (Student/Employer)
│   ├── signup.php              # User registration
│   ├── student.php             # Student dashboard
│   ├── employer.php            # Employer dashboard
│   ├── jobs.php                # Job listings
│   ├── apply.php               # Job application form
│   ├── my-applications.php     # Student application tracking
│   └── ...                     # Other pages
│
├── api/                        # Backend API endpoints (RESTful)
│   ├── auth/                   # Authentication APIs
│   ├── user/                   # User management APIs
│   ├── applications.php        # Application submission
│   ├── get_jobs.php            # Fetch job listings
│   ├── admin_users.php         # Admin: manage users
│   ├── admin_jobs.php          # Admin: manage jobs
│   └── ...                     # Other API endpoints
│
├── includes/                   # Shared PHP utilities
│   ├── session.php             # Session management
│   ├── db.php                  # Database connection
│   ├── auth_check.php          # Authentication middleware
│   ├── navbar.php              # Navigation bar component
│   ├── footer.php              # Footer component
│   └── ...                     # Other utilities
│
├── classes/                    # PHP OOP classes
│   ├── Database.php            # Database abstraction layer
│   ├── User.php                # User model
│   ├── Job.php                 # Job model
│   ├── Application.php         # Application model
│   ├── WebSocketServer.php     # WebSocket server class
│   └── ExternalAPIService.php  # External job API integration
│
├── js/                         # Frontend JavaScript
│   ├── websocket-client.js     # WebSocket client library
│   ├── dashboard.js            # Dashboard interactions
│   ├── jobs.js                 # Job listing functionality
│   ├── apply.js                # Application form handling
│   ├── theme.js                # Dark/Light theme toggle
│   └── ...                     # Other JS modules
│
├── css/                        # Stylesheets
│   ├── global.css              # Global styles & CSS variables
│   ├── responsive.css          # Mobile-responsive styles
│   ├── dashboard.css           # Dashboard-specific styles
│   └── ...                     # Other stylesheets
│
├── uploads/                    # User-uploaded files
│   ├── profile/                # Profile images
│   └── cv/                     # Uploaded CVs/resumes
│
├── config/                     # Configuration files
│   └── config.php              # App configuration
│
├── websocket_server.php        # WebSocket server entry point
├── import_external_jobs.php    # Job import script
├── cleanup_jobs.php            # Job cleanup script
└── index.php                   # Landing page


---

## 🏗 System Architecture Diagram


┌─────────────────────────────────────────────────────────────────────────┐
│                          CLIENT LAYER (Browser)                          │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                           │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐  ┌─────────────┐│
│  │   HTML/CSS   │  │  JavaScript  │  │   Themes     │  │  WebSocket  ││
│  │   Pages      │  │   Modules    │  │  (Dark/Light)│  │   Client    ││
│  └──────────────┘  └──────────────┘  └──────────────┘  └─────────────┘│
│         │                  │                  │                 │        │
└─────────┼──────────────────┼──────────────────┼─────────────────┼────────┘
          │                  │                  │                 │
          ▼                  ▼                  ▼                 ▼
┌─────────────────────────────────────────────────────────────────────────┐
│                      APPLICATION LAYER (PHP Server)                      │
├─────────────────────────────────────────────────────────────────────────┤
│                                                                           │
│  ┌───────────────────────────────────────────────────────────────────┐  │
│  │                      PAGES (Frontend Views)                        │  │
│  │  • login.php  • signup.php  • student.php  • employer.php         │  │
│  │  • admin.php  • jobs.php    • apply.php    • my-applications.php  │  │
│  └───────────────────────────────────────────────────────────────────┘  │
│                                   │                                      │
│                                   ▼                                      │
│  ┌───────────────────────────────────────────────────────────────────┐  │
│  │                      INCLUDES (Middleware)                         │  │
│  │  • session.php (Session Mgmt)  • auth_check.php (Auth Middleware) │  │
│  │  • navbar.php (UI Components)  • db.php (Database Connection)     │  │
│  └───────────────────────────────────────────────────────────────────┘  │
│                                   │                                      │
│         ┌─────────────────────────┼─────────────────────────┐           │
│         ▼                         ▼                         ▼           │
│  ┌─────────────┐          ┌─────────────┐          ┌─────────────┐     │
│  │  API Layer  │          │   Classes   │          │  WebSocket  │     │
│  │  (RESTful)  │          │   (Models)  │          │   Server    │     │
│  ├─────────────┤          ├─────────────┤          ├─────────────┤     │
│  │• auth/      │          │• User.php   │          │• Real-time  │     │
│  │• user/      │◄────────►│• Job.php    │◄────────►│  Notifications│   │
│  │• jobs.php   │          │• Application│          │• Live Updates│     │
│  │• applications│         │• Database   │          │• Port 8080  │     │
│  └─────────────┘          └─────────────┘          └─────────────┘     │
│         │                         │                         │           │
└─────────┼─────────────────────────┼─────────────────────────┼───────────┘
          │                         │                         │
          ▼                         ▼                         ▼
┌─────────────────────────────────────────────────────────────────────────┐
│                         DATA LAYER (MySQL Database)                      │
├─────────────────────────────────────────────────────────────────────────┤
│  ┌─────────┐  ┌─────────┐  ┌─────────┐  ┌──────────────┐  ┌─────────┐ │
│  │  users  │  │ students│  │employers│  │ applications │  │  jobs   │ │
│  │  table  │  │  table  │  │  table  │  │    table     │  │  table  │ │
│  └─────────┘  └─────────┘  └─────────┘  └──────────────┘  └─────────┘ │
│       │            │             │               │               │       │
│       └────────────┴─────────────┴───────────────┴───────────────┘       │
│                              Foreign Keys                                │
└─────────────────────────────────────────────────────────────────────────┘
                                   │
                                   ▼
┌─────────────────────────────────────────────────────────────────────────┐
│                      EXTERNAL SERVICES                                   │
├─────────────────────────────────────────────────────────────────────────┤
│  • External Job APIs (Adzuna, JSearch, etc.)                            │
│  • File Storage (uploads/ directory)                                     │
└─────────────────────────────────────────────────────────────────────────┘


---

## 🔄 Data Flow Diagrams

### 1. User Authentication Flow


┌────────┐        ┌──────────┐        ┌──────────┐        ┌──────────┐
│ User   │        │ login.php│        │ session. │        │ Database │
│ Browser│        │ (Page)   │        │ php      │        │ (MySQL)  │
└───┬────┘        └────┬─────┘        └────┬─────┘        └────┬─────┘
    │                  │                   │                   │
    │ 1. Submit Login  │                   │                   │
    ├─────────────────►│                   │                   │
    │   (email, pwd,   │                   │                   │
    │    role)         │                   │                   │
    │                  │                   │                   │
    │                  │ 2. Query User     │                   │
    │                  ├──────────────────────────────────────►│
    │                  │   (SELECT * FROM users WHERE...)      │
    │                  │                   │                   │
    │                  │◄──────────────────────────────────────┤
    │                  │   3. Return User Data                 │
    │                  │                   │                   │
    │                  │ 4. Verify Password│                   │
    │                  ├──────────────────►│                   │
    │                  │   password_verify()                   │
    │                  │                   │                   │
    │                  │ 5. Create Session │                   │
    │                  │◄──────────────────┤                   │
    │                  │   $_SESSION['user']                   │
    │                  │                   │                   │
    │ 6. Redirect to   │                   │                   │
    │   Dashboard      │                   │                   │
    │◄─────────────────┤                   │                   │
    │ (student.php or  │                   │                   │
    │  employer.php)   │                   │                   │
    │                  │                   │                   │


### 2. Job Application Flow


┌────────┐   ┌──────────┐   ┌──────────────┐   ┌──────────┐   ┌──────────┐
│Student │   │ apply.php│   │ applications │   │ Database │   │WebSocket │
│ Browser│   │ (Page)   │   │ .php (API)   │   │ (MySQL)  │   │  Server  │
└───┬────┘   └────┬─────┘   └──────┬───────┘   └────┬─────┘   └────┬─────┘
    │             │                │                │              │
    │ 1. Fill Form│                │                │              │
    │   + Upload  │                │                │              │
    │   CV File   │                │                │              │
    ├────────────►│                │                │              │
    │             │                │                │              │
    │             │ 2. Submit Form │                │              │
    │             │   (AJAX POST)  │                │              │
    │             ├───────────────►│                │              │
    │             │   FormData     │                │              │
    │             │                │                │              │
    │             │                │ 3. Validate &  │              │
    │             │                │   Save CV File │              │
    │             │                │                │              │
    │             │                │ 4. INSERT INTO │              │
    │             │                │   applications │              │
    │             │                ├───────────────►│              │
    │             │                │                │              │
    │             │                │ 5. Notify      │              │
    │             │                │   Employer     │              │
    │             │                ├──────────────────────────────►│
    │             │                │   (WebSocket   │              │
    │             │                │    message)    │              │
    │             │                │                │              │
    │             │ 6. Return JSON │                │              │
    │             │   {success:true}                │              │
    │             │◄───────────────┤                │              │
    │             │                │                │              │
    │ 7. Show     │                │                │              │
    │   Success & │                │                │              │
    │   Redirect  │                │                │              │
    │◄────────────┤                │                │              │
    │             │                │                │              │


### 3. Admin Dashboard Data Flow


┌────────┐   ┌──────────┐   ┌──────────────┐   ┌──────────┐
│ Admin  │   │ admin.php│   │ admin_*.php  │   │ Database │
│ Browser│   │ (Page)   │   │ (APIs)       │   │ (MySQL)  │
└───┬────┘   └────┬─────┘   └──────┬───────┘   └────┬─────┘
    │             │                │                │
    │ 1. Load Page│                │                │
    ├────────────►│                │                │
    │             │                │                │
    │             │ 2. Fetch Stats │                │
    │             │   (PHP Queries)│                │
    │             ├───────────────────────────────►│
    │             │   COUNT users, jobs, etc       │
    │             │                │                │
    │             │ 3. Render Page │                │
    │             │   with Stats   │                │
    │◄────────────┤                │                │
    │ (HTML)      │                │                │
    │             │                │                │
    │ 4. JS: Load │                │                │
    │   Tab Data  │                │                │
    ├────────────►│                │                │
    │  (AJAX)     │                │                │
    │             │                │                │
    │             │ 5. Fetch Data  │                │
    │             ├───────────────►│                │
    │             │  (admin_users. │                │
    │             │   php API)     │                │
    │             │                │                │
    │             │                │ 6. SELECT *    │
    │             │                ├───────────────►│
    │             │                │   FROM users   │
    │             │                │                │
    │             │                │ 7. Return Rows │
    │             │                │◄───────────────┤
    │             │                │                │
    │             │ 8. Return JSON │                │
    │             │◄───────────────┤                │
    │  {users:[...]}               │                │
    │             │                │                │
    │ 9. Render   │                │                │
    │   Table     │                │                │
    │◄────────────┤                │                │
    │             │                │                │


---

## 🔐 Authentication Flow


┌─────────────────────────────────────────────────────────────┐
│                    Authentication System                     │
└─────────────────────────────────────────────────────────────┘

1. LOGIN REQUEST
   ┌──────────┐
   │ User     │
   │ submits  │
   │ form     │
   └────┬─────┘
        │
        ▼
   ┌──────────────────┐
   │ login.php        │
   │ • Sanitize input │
   │ • Check DB       │
   └────┬─────────────┘
        │
        ├─────► Admin login check (admins table)
        │       └─► If found: $_SESSION['user'] = [..., role='admin']
        │           └─► Redirect: admin.php
        │
        └─────► User login check (users table)
                └─► If found: $_SESSION['user'] = [..., role='student/employer']
                    └─► Redirect: student.php or employer.php

2. AUTHENTICATION MIDDLEWARE
   ┌──────────────────┐
   │ auth_check.php   │
   │ (included in     │
   │  protected pages)│
   └────┬─────────────┘
        │
        ▼
   Check: isset($_SESSION['user'])
        │
        ├─► YES: Allow access
        │
        └─► NO: Store current URL in $_SESSION['redirect_after_login']
                Redirect to login.php

3. SESSION MANAGEMENT
   ┌──────────────────┐
   │ session.php      │
   │ • Start session  │
   │ • Set timeout    │
   │ • Security flags │
   └──────────────────┘
        │
        ▼
   • session_start()
   • Check last activity (30min timeout)
   • If expired: destroy session → redirect to login.php?timeout=1

4. ROLE-BASED ACCESS
   ┌──────────────────────────────────────┐
   │ Page checks: $_SESSION['user']['role']│
   └──────────────────────────────────────┘
        │
        ├─► 'admin'    → Access: admin.php, admin_*.php APIs
        ├─► 'student'  → Access: student.php, apply.php, my-applications.php
        └─► 'employer' → Access: employer.php, employer-applicants.php


---

## 🔌 WebSocket Real-Time Communication

### Architecture


┌─────────────────────────────────────────────────────────────────────┐
│                      WebSocket System Architecture                   │
└─────────────────────────────────────────────────────────────────────┘

SERVER SIDE (PHP)                          CLIENT SIDE (JavaScript)
┌───────────────────────┐                 ┌───────────────────────┐
│ websocket_server.php  │                 │ websocket-client.js   │
│ (Port 8080)           │                 │ (Browser)             │
├───────────────────────┤                 ├───────────────────────┤
│                       │                 │                       │
│ 1. Start Server       │                 │ 1. Create WebSocket   │
│    php websocket_     │                 │    const ws = new     │
│    server.php         │                 │    WebSocket(...)     │
│                       │                 │                       │
│ 2. Listen on :8080    │◄───────────────►│ 2. Connect            │
│                       │   WebSocket     │    ws.connect()       │
│ 3. Accept Connections │   Connection    │                       │
│                       │                 │                       │
│ 4. Handle Messages:   │                 │ 3. Send Messages:     │
│    • subscribe        │                 │    ws.send({...})     │
│    • unsubscribe      │                 │                       │
│    • notification     │                 │ 4. Receive Messages:  │
│                       │                 │    ws.on('message')   │
│ 5. Broadcast to       │                 │                       │
│    Subscribers        │─────────────────►│ 5. Handle Events:     │
│                       │   Push Notif.   │    • connected        │
│                       │                 │    • disconnected     │
│                       │                 │    • notification     │
└───────────────────────┘                 └───────────────────────┘

NOTIFICATION CHANNELS:
├─► admin_notifications  (Admin-specific alerts)
├─► new_users           (New user registrations)
├─► new_jobs            (New job postings)
├─► applications        (Job application updates)
├─► reports             (System reports)
└─► user_{id}           (User-specific notifications)


### Message Flow Example


SCENARIO: Student applies for a job

1. Student submits application
   ↓
2. applications.php API processes application
   ↓
3. API sends WebSocket message to server:
   {
     "type": "notification",
     "channel": "applications",
     "data": {
       "title": "New Application",
       "message": "John Doe applied for Software Engineer",
       "employer_id": 123
     }
   }
   ↓
4. WebSocket server broadcasts to:
   • Employer (channel: user_123)
   • Admin (channel: admin_notifications)
   ↓
5. Connected clients receive notification
   ↓
6. Browser displays toast notification
   ↓
7. Dashboard auto-refreshes relevant data


---

## 🗄 Database Schema Relationships


┌─────────────────────────────────────────────────────────────────────┐
│                        Database ERD (Entity Relationship Diagram)    │
└─────────────────────────────────────────────────────────────────────┘

┌──────────────┐
│    users     │
├──────────────┤
│ id (PK)      │───────────┐
│ email        │           │
│ password     │           │
│ role         │           │ ONE user
│ name         │           │ can have
│ profile_image│           │ multiple
│ createdAt    │           │ profiles
└──────────────┘           │
                           │
        ┌──────────────────┴──────────────────┐
        │                                     │
        ▼                                     ▼
┌──────────────┐                     ┌──────────────┐
│   students   │                     │  employers   │
├──────────────┤                     ├──────────────┤
│ id (PK)      │                     │ id (PK)      │
│ user_id (FK) │                     │ user_id (FK) │
│ university   │                     │ company_name │
│ skills       │                     │ company_desc │
│ bio          │                     │ industry     │
│ cv_path      │                     │ website      │
└──────┬───────┘                     └──────┬───────┘
       │                                    │
       │ ONE student                        │ ONE employer
       │ can submit                         │ can post
       │ many applications                  │ many jobs
       │                                    │
       ▼                                    ▼
┌──────────────┐                     ┌──────────────┐
│ applications │                     │    jobs      │
├──────────────┤                     ├──────────────┤
│ id (PK)      │◄────────────────────┤ id (PK)      │
│ student_id(FK)                     │ employer_id(FK)
│ job_id (FK)  │                     │ title        │
│ full_name    │  ONE job can        │ description  │
│ email        │  have many          │ location     │
│ phone        │  applications       │ type         │
│ cover_letter │                     │ requirements │
│ cv_file_path │                     │ salary       │
│ status       │                     │ deadline     │
│ applied_at   │                     │ createdAt    │
└──────────────┘                     └──────────────┘

┌──────────────┐
│   admins     │
├──────────────┤
│ id (PK)      │  (Separate table for admin accounts)
│ username     │
│ email        │
│ password     │
│ createdAt    │
└──────────────┘

LEGEND:
PK = Primary Key
FK = Foreign Key
───► = One-to-Many Relationship


---

## 📂 File Relationships Map

### Page → API → Database Flow


┌─────────────────────────────────────────────────────────────────────┐
│                     TYPICAL REQUEST FLOW                             │
└─────────────────────────────────────────────────────────────────────┘

EXAMPLE: Student Dashboard

pages/student.php
    │
    ├─► includes/auth_check.php (Check if user is logged in)
    │   └─► includes/session.php (Session management)
    │
    ├─► includes/navbar.php (Render navigation bar)
    │
    ├─► includes/db.php (Database connection)
    │
    ├─► Direct SQL queries (Fetch student stats)
    │   └─► Database: students, applications tables
    │
    └─► js/dashboard.js (Client-side JavaScript)
        │
        ├─► AJAX call to api/get_jobs.php
        │   └─► Database: jobs table
        │   └─► Returns: JSON job listings
        │
        └─► js/websocket-client.js
            └─► Connects to WebSocket server (port 8080)
            └─► Receives: Real-time notifications


### API Endpoint Structure


api/
├── auth/                       # Authentication endpoints
│   ├── login.php              → users/admins tables
│   ├── signup.php             → users, students/employers tables
│   └── logout.php             → session destruction
│
├── user/                      # User management
│   ├── profile.php            → students/employers tables
│   └── update.php             → students/employers tables
│
├── applications.php           → applications table
│   ├── POST: Submit application
│   └── GET: Fetch applications
│
├── get_jobs.php               → jobs table
│   └── GET: Fetch job listings (with filters)
│
├── admin_users.php            → users, students, employers tables
│   └── GET: Fetch all users (admin only)
│
├── admin_jobs.php             → jobs table
│   └── GET: Fetch all jobs (admin only)
│
├── admin_applications.php     → applications, users, jobs tables
│   └── GET: Fetch all applications (admin only)
│
└── import_jobs_api.php        → ExternalAPIService class
    └── Fetch jobs from external APIs → jobs table


### JavaScript Module Dependencies


js/
├── websocket-client.js        # Core WebSocket client (no dependencies)
│   └─► Used by: dashboard.js, admin.php, employer.php
│
├── theme.js                   # Theme switcher (standalone)
│   └─► Used by: All pages via localStorage
│
├── dashboard.js               # Dashboard interactions
│   ├─► Depends on: websocket-client.js
│   └─► Used by: student.php, employer.php
│
├── jobs.js                    # Job listing functionality
│   └─► AJAX calls to: api/get_jobs.php
│
├── apply.js                   # Application form handling
│   └─► AJAX calls to: api/applications.php
│
└── auth.js                    # Login/Signup handling
    └─► AJAX calls to: api/auth/*.php


---

## 🔧 Key Integration Points

### 1. Authentication Integration

login.php ──► session.php ──► $_SESSION['user']
                              │
                              ├─► auth_check.php (all protected pages)
                              └─► API auth checks


### 2. Real-Time Notifications

API Action (e.g., new application)
    │
    ├─► Trigger WebSocket message
    │   └─► websocket_server.php
    │       └─► Broadcast to subscribers
    │           └─► websocket-client.js (in browser)
    │               └─► Display toast notification
    │                   └─► Auto-refresh relevant data


### 3. External Job Import

import_external_jobs.php
    │
    ├─► classes/ExternalAPIService.php
    │   └─► Fetch from external APIs (Adzuna, JSearch, etc.)
    │       └─► Parse and normalize data
    │           └─► classes/Job.php
    │               └─► Save to jobs table


### 4. File Upload Flow

apply.php (form) ──► api/applications.php
                     │
                     ├─► Validate file (PDF/DOC, max 5MB)
                     ├─► Generate unique filename
                     ├─► Move to uploads/cv/
                     ├─► Save path in applications table
                     └─► Return success JSON


---

## 📝 Quick Reference: Common Use Cases

### Use Case 1: Student Applies for Job
1. *Student* visits pages/jobs.php → sees job listings
2. *Student* clicks "Apply" → redirects to pages/apply.php?jobId=X
3. *Student* fills form + uploads CV
4. *Form* submits to api/applications.php (AJAX)
5. *API* validates, saves to database, stores CV file
6. *API* sends WebSocket notification to employer
7. *Employer* sees real-time notification in dashboard
8. *Student* redirected to pages/my-applications.php

### Use Case 2: Employer Posts New Job
1. *Employer* visits pages/employer.php
2. *Employer* clicks "Post New Job" → opens form
3. *Form* submits to api/create_job.php
4. *API* saves job to database
5. *API* sends WebSocket notification to admin
6. *Job* appears in pages/jobs.php for students

### Use Case 3: Admin Monitors Platform
1. *Admin* logs in via pages/admin-login.php (with secret key)
2. *Admin* redirected to pages/admin.php
3. *Dashboard* loads stats from direct SQL queries
4. *Admin* switches tabs → AJAX calls to api/admin_*.php
5. *WebSocket* provides real-time updates (new users, jobs, applications)
6. *Admin* can delete users/jobs via action buttons

---

## 🎯 Summary

This project follows a *three-tier architecture*:

1. *Presentation Layer* (pages/, js/, css/)
   - User interface and client-side logic
   
2. *Application Layer* (api/, includes/, classes/)
   - Business logic, authentication, data processing
   
3. *Data Layer* (Database, uploads/)
   - Persistent storage and file management

*Key Technologies:*
- *Frontend*: PHP (templating), JavaScript (ES6), CSS3
- *Backend*: PHP 7.4+, MySQL 5.7+
- *Real-Time*: WebSocket (PHP Sockets)
- *Security*: Password hashing, prepared statements, CSRF protection, session management

*Communication Patterns:*
- *Synchronous*: PHP page rendering, AJAX API calls
- *Asynchronous*: WebSocket notifications, external API imports

---

## 🔄 Detailed User Flow & Technical Implementation

### 1. Student Registration Flow

*User Action*: Student clicks "Sign Up" → Fills form → Submits

*Under the Hood:*

#### *Step 1: Initial Page Load* (signup.php)

Browser → HTTP GET /pages/signup.php
         ↓
    session.php → session_start() → Check existing session
         ↓
    If logged in → Redirect to dashboard
    If not → Render signup form (HTML)
         ↓
Browser ← HTTP 200 OK (HTML form)


*Technical Details:*
- *Protocol*: HTTP/1.1 over TCP
- *Session*: PHP session cookie (PHPSESSID) created if not exists
- *Headers*: 
  - Content-Type: text/html; charset=UTF-8
  - Set-Cookie: PHPSESSID=abc123; Path=/; HttpOnly
- *Server Processing Time*: ~50-100ms (includes DB connection pool initialization)

#### *Step 2: Form Submission* (AJAX POST)

Browser → HTTP POST /api/auth/signup.php
         Content-Type: application/x-www-form-urlencoded
         Body: email=student@example.com&password=pass123&name=John&role=student
         ↓
    PHP Input Stream → $_POST array populated
         ↓
    Validation Layer:
         - Email format check (filter_var FILTER_VALIDATE_EMAIL)
         - Password strength (min 8 chars, complexity rules)
         - Required fields presence check
         - SQL injection prevention (prepared statements)
         ↓
    Database Layer (db.php):
         - mysqli_connect() → TCP connection to MySQL (port 3306)
         - Check if email exists: SELECT email FROM users WHERE email=?
         ↓
    If email exists → HTTP 400 JSON response {"success": false, "message": "Email already registered"}
         ↓
    If email available:
         - password_hash($password, PASSWORD_BCRYPT) → bcrypt hash with cost=10
         - INSERT INTO users (email, password, name, role, created_at) VALUES (?, ?, ?, ?, NOW())
         - mysqli_stmt_execute() → MySQL processes INSERT
         - $user_id = mysqli_insert_id() → Get auto-increment ID
         ↓
    Student-Specific Table:
         - INSERT INTO students (user_id, university, graduation_year) VALUES (?, ?, ?)
         ↓
    Session Creation:
         - $_SESSION['user_id'] = $user_id
         - $_SESSION['email'] = $email
         - $_SESSION['role'] = 'student'
         - $_SESSION['name'] = $name
         ↓
Browser ← HTTP 200 OK
         Content-Type: application/json
         Set-Cookie: PHPSESSID=xyz789; Path=/; HttpOnly; SameSite=Lax
         Body: {"success": true, "message": "Registration successful", "redirect": "/pages/student.php"}


*Technical Specifications:*
- *Encryption*: Password → bcrypt hash (60-char string, 2y algorithm identifier)
- *Database Transaction*: Auto-commit mode (each INSERT is atomic)
- *Session Storage*: Server-side file storage (/tmp/sess_xyz789)
- *Payload Size*: ~200-500 bytes (request), ~150 bytes (response)
- *Total Latency*: ~150-300ms (validation: 5ms, hashing: 100-200ms, DB: 20-50ms, session write: 10ms)

#### *Step 3: Dashboard Redirect*

Browser → JavaScript processes JSON response
         - Sees success: true
         - Executes: window.location.href = "/pages/student.php"
         ↓
Browser → HTTP GET /pages/student.php
         Cookie: PHPSESSID=xyz789
         ↓
    auth_check.php → session_start()
         - Read session from /tmp/sess_xyz789
         - Verify $_SESSION['user_id'] exists
         - Verify $_SESSION['role'] == 'student'
         ↓
    Database Query:
         - SELECT * FROM users u JOIN students s ON u.id=s.user_id WHERE u.id=?
         - Fetch user profile data
         ↓
    Render Dashboard:
         - PHP template engine processes HTML
         - Inject user data: <?php echo htmlspecialchars($name); ?>
         - Include navbar.php (session indicator, logout button)
         - Include footer.php
         ↓
Browser ← HTTP 200 OK (HTML dashboard)
         ↓
    DOM Ready → JavaScript initialization:
         - websocket-client.js → new CareerHubWebSocket('ws://localhost:8080')
         - Send: {type: 'register', userId: <?php echo $user_id; ?>}
         - WebSocket handshake (HTTP → WebSocket protocol upgrade)


---

### 2. Job Search & Application Flow

*User Action*: Student searches for jobs → Clicks "Apply" → Uploads CV → Submits

*Under the Hood:*

#### *Step 1: Job Search* (Real-time AJAX)

Browser → User types in search box: "Software Engineer"
         - JavaScript debounce (300ms delay)
         - Triggers: searchJobs("Software Engineer")
         ↓
Browser → HTTP POST /api/search_jobs.php
         Content-Type: application/json
         Body: {"query": "Software Engineer", "location": "Remote", "type": "Full-time"}
         ↓
    PHP JSON Decode: $data = json_decode(file_get_contents('php://input'), true)
         ↓
    SQL Query Building:
         - Prepared statement with LIKE wildcards
         - SELECT j.*, e.company_name, e.logo 
           FROM jobs j 
           JOIN employers e ON j.employer_id=e.user_id 
           WHERE j.title LIKE ? 
           AND j.location LIKE ? 
           AND j.job_type LIKE ?
           AND j.status='active'
           ORDER BY j.posted_date DESC
           LIMIT 50
         ↓
    MySQL Full-Table Scan:
         - Indexes used: idx_title, idx_location, idx_status
         - ~10-50ms query execution time
         - Returns result set (array of rows)
         ↓
    Data Transformation:
         - foreach loop → build JSON array
         - Format dates: date('M d, Y', strtotime($row['posted_date']))
         - Sanitize HTML: htmlspecialchars()
         ↓
Browser ← HTTP 200 OK
         Content-Type: application/json
         Body: {
           "success": true,
           "jobs": [
             {
               "id": 42,
               "title": "Software Engineer",
               "company": "TechCorp",
               "location": "Remote",
               "salary": "$80k-$120k",
               "posted_date": "Nov 10, 2025",
               "logo": "/uploads/company/techcorp.png"
             },
             ...
           ],
           "total": 15
         }
         ↓
Browser → JavaScript renders results:
         - Clear existing job cards
         - Loop through jobs array
         - Create DOM elements: <div class="job-card">
         - Attach click handlers for "Apply" buttons
         - Update UI (fade-in animation)


*Performance Optimizations:*
- *Debouncing*: Prevents excessive API calls during typing (300ms delay)
- *Database Indexing*: B-tree indexes on title, location, status columns
- *Result Limiting*: LIMIT 50 prevents memory overflow
- *Connection Pooling*: Persistent MySQL connections (mysqli_pconnect)

#### *Step 2: Job Application Submission*
```
Browser → User clicks "Apply" button on job #42
         ↓
Browser → HTTP GET /pages/apply.php?job_id=42
         Cookie: PHPSESSID=xyz789
         ↓
    auth_check.php → Verify student logged in
         ↓
    Job Data Fetch:
         - SELECT j.*, e.company_name 
           FROM jobs j 
           JOIN employers e ON j.employer_id=e.user_id 
           WHERE j.id=42
         ↓
    Check Existing Application:
         - SELECT id FROM applications WHERE job_id=42 AND student_id=?
         - If exists → Show "Already Applied" message
         ↓
Browser ← HTTP 200 OK (HTML form with job details, file upload input)
         ↓
    User fills form:
         - Cover letter (textarea)
         - CV upload (input type="file" accept=".pdf,.doc,.docx")
         ↓
    User clicks "Submit Application"
         ↓
Browser → JavaScript validation:
         - Check file size (max 5MB)
         - Check file type (PDF/DOC/DOCX)
         - Check cover letter length (min 50 chars)
         ↓
Browser → HTTP POST /api/applications.php (multipart/form-data)
         Content-Type: multipart/form-data; boundary=----WebKitFormBoundary
         Body: (binary data with file chunks)
         ------WebKitFormBoundary
         Content-Disposition: form-data; name="job_id"
         
         42
         ------WebKitFormBoundary
         Content-Disposition: form-data; name="cover_letter"
         
         I am excited to apply for...
         ------WebKitFormBoundary
         Content-Disposition: form-data; name="cv"; filename="resume.pdf"
         Content-Type: application/pdf
         
         %PDF-1.4... (binary data)
         ------WebKitFormBoundary--
         ↓
    PHP File Upload Processing: