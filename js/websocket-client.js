/**
 * WebSocket Client for Career Hub
 * Handles real-time notifications and updates
 */

class CareerHubWebSocket {
    constructor(url = 'ws://localhost:8080') {
        this.url = url;
        this.socket = null;
        this.reconnectAttempts = 0;
        this.maxReconnectAttempts = 5;
        this.reconnectDelay = 3000;
        this.isConnected = false;
        this.listeners = {};
        this.subscriptions = new Set();
        this.authToken = this.getAuthToken();
    }

    /**
     * Connect to WebSocket server
     */
    connect() {
        try {
            const fullUrl = this.appendToken(this.url, this.authToken);
            this.socket = new WebSocket(fullUrl);
            
            this.socket.onopen = (event) => {
                console.log('WebSocket connected');
                this.isConnected = true;
                this.reconnectAttempts = 0;
                this.trigger('connected', event);
                
                // Register user if logged in (fallback after token handshake)
                const userId = this.getUserId();
                if (userId) {
                    this.register(userId);
                }
                // Resubscribe channels after reconnect
                this.subscriptions.forEach(ch => this.subscribe(ch));
            };
            
            this.socket.onmessage = (event) => {
                try {
                    const data = JSON.parse(event.data);
                    console.log('WebSocket message:', data);
                    
                    // Handle different message types
                    switch (data.type) {
                        case 'connection':
                            this.trigger('connection', data);
                            break;
                        case 'job_notification':
                            this.handleJobNotification(data);
                            break;
                        case 'application_update':
                            this.handleApplicationUpdate(data);
                            break;
                        case 'pong':
                            // Response to ping
                            break;
                        default:
                            this.trigger('message', data);
                            break;
                    }
                } catch (e) {
                    console.error('Error parsing WebSocket message:', e);
                }
            };
            
            this.socket.onerror = (error) => {
                console.error('WebSocket error:', error);
                this.trigger('error', error);
            };
            
            this.socket.onclose = (event) => {
                console.log('WebSocket disconnected');
                this.isConnected = false;
                this.trigger('disconnected', event);
                this.attemptReconnect();
            };
            
        } catch (error) {
            console.error('Failed to create WebSocket connection:', error);
            this.attemptReconnect();
        }
    }

    /**
     * Attempt to reconnect
     */
    attemptReconnect() {
        if (this.reconnectAttempts < this.maxReconnectAttempts) {
            this.reconnectAttempts++;
            console.log(`Attempting to reconnect... (${this.reconnectAttempts}/${this.maxReconnectAttempts})`);
            
            setTimeout(() => {
                this.connect();
            }, this.reconnectDelay);
        } else {
            console.error('Max reconnection attempts reached');
            this.trigger('maxReconnectAttemptsReached');
        }
    }

    /**
     * Register user with WebSocket server
     */
    register(userId) {
        this.send({
            type: 'register',
            userId: userId
        });
    }

    subscribe(channel) {
        if (!channel) return;
        this.subscriptions.add(channel);
        this.send({ type: 'subscribe', channel });
    }

    unsubscribe(channel) {
        if (!channel) return;
        this.subscriptions.delete(channel);
        this.send({ type: 'unsubscribe', channel });
    }

    /**
     * Send message to WebSocket server
     */
    send(data) {
        if (this.isConnected && this.socket.readyState === WebSocket.OPEN) {
            this.socket.send(JSON.stringify(data));
        } else {
            console.warn('WebSocket is not connected');
        }
    }

    appendToken(baseUrl, token) {
        if (!token) return baseUrl;
        try {
            const u = new URL(baseUrl);
            u.searchParams.set('token', token);
            return u.toString();
        } catch (e) {
            // Fallback simple append
            return baseUrl + (baseUrl.includes('?') ? '&' : '?') + 'token=' + encodeURIComponent(token);
        }
    }

    getAuthToken() {
        // Prefer in-memory global provided by server-side template injection
        if (window.WS_AUTH_TOKEN) return window.WS_AUTH_TOKEN;
        // Fallback to localStorage
        return localStorage.getItem('wsAuthToken') || null;
    }

    /**
     * Handle job notification
     */
    handleJobNotification(data) {
        this.trigger('jobNotification', data);
        
        // Show browser notification
        this.showNotification(
            'New Job Posted!',
            `${data.title} at ${data.company}`,
            '/pages/jobs.php?id=' + data.jobId
        );
    }

    /**
     * Handle application status update
     */
    handleApplicationUpdate(data) {
        this.trigger('applicationUpdate', data);
        
        // Show browser notification
        this.showNotification(
            'Application Update',
            `Your application status: ${data.status}`,
            '/pages/applications.php'
        );
    }

    /**
     * Show browser notification
     */
    showNotification(title, body, url = null) {
        if ('Notification' in window && Notification.permission === 'granted') {
            const notification = new Notification(title, {
                body: body,
                icon: '/assets/logo.png',
                badge: '/assets/badge.png'
            });
            
            if (url) {
                notification.onclick = function() {
                    window.open(url, '_blank');
                    notification.close();
                };
            }
        }
    }

    /**
     * Request notification permission
     */
    requestNotificationPermission() {
        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission().then(permission => {
                console.log('Notification permission:', permission);
            });
        }
    }

    /**
     * Get user ID from session/localStorage
     */
    getUserId() {
        // Try to get from localStorage or a global variable
        return localStorage.getItem('userId') || window.userId || null;
    }

    /**
     * Add event listener
     */
    on(event, callback) {
        if (!this.listeners[event]) {
            this.listeners[event] = [];
        }
        this.listeners[event].push(callback);
    }

    /**
     * Trigger event
     */
    trigger(event, data) {
        if (this.listeners[event]) {
            this.listeners[event].forEach(callback => callback(data));
        }
    }

    /**
     * Send ping to keep connection alive
     */
    ping() {
        this.send({ type: 'ping' });
    }

    /**
     * Start keep-alive ping
     */
    startKeepAlive(interval = 30000) {
        setInterval(() => {
            if (this.isConnected) {
                this.ping();
            }
        }, interval);
    }

    /**
     * Disconnect from WebSocket server
     */
    disconnect() {
        if (this.socket) {
            this.socket.close();
        }
    }
}

// Initialize WebSocket connection when page loads
let wsClient = null;

document.addEventListener('DOMContentLoaded', function() {
    // Initialize WebSocket client
    wsClient = new CareerHubWebSocket('ws://localhost:8080');
    wsClient.connect();
    wsClient.startKeepAlive();
    wsClient.requestNotificationPermission();

    // Example dynamic subscriptions (uncomment as needed)
    // wsClient.subscribe('jobs');
    // if (wsClient.getUserId()) wsClient.subscribe('notifications:user:' + wsClient.getUserId());
    
    // Listen for job notifications
    wsClient.on('jobNotification', function(data) {
        console.log('New job notification:', data);
        
        // Update UI if on jobs page
        if (window.location.pathname.includes('jobs.php')) {
            // Refresh job listings or add new job card
            location.reload();
        }
    });
    
    // Listen for application updates
    wsClient.on('applicationUpdate', function(data) {
        console.log('Application update:', data);
        
        // Update UI if on applications page
        if (window.location.pathname.includes('applications.php')) {
            location.reload();
        }
    });
    
    // Handle connection status
    wsClient.on('connected', function() {
        console.log('Connected to real-time notifications');
        showConnectionStatus('connected');
    });
    
    wsClient.on('disconnected', function() {
        console.log('Disconnected from real-time notifications');
        showConnectionStatus('disconnected');
    });
});

/**
 * Show connection status indicator
 */
function showConnectionStatus(status) {
    let indicator = document.getElementById('ws-status-indicator');
    
    if (!indicator) {
        indicator = document.createElement('div');
        indicator.id = 'ws-status-indicator';
        indicator.style.cssText = `
            position: fixed;
            top: 10px;
            right: 10px;
            padding: 8px 12px;
            border-radius: 4px;
            font-size: 12px;
            z-index: 10000;
            transition: opacity 0.3s;
        `;
        document.body.appendChild(indicator);
    }
    
    if (status === 'connected') {
        indicator.textContent = '● Connected';
        indicator.style.backgroundColor = '#4caf50';
        indicator.style.color = 'white';
        
        // Hide after 3 seconds
        setTimeout(() => {
            indicator.style.opacity = '0';
        }, 3000);
    } else {
        indicator.textContent = '● Disconnected';
        indicator.style.backgroundColor = '#f44336';
        indicator.style.color = 'white';
        indicator.style.opacity = '1';
    }
}

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = CareerHubWebSocket;
}
