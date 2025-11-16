<?php
/**
 * WebSocket Server Class
 * Handles real-time communication for job notifications
 * 
 * To run this server, execute: php websocket_server.php
 */
class WebSocketServer {
    private $address;
    private $port;
    private $socket;
    private $clients = [];
    private $userConnections = [];
    private $channels = [];
    private $clientMeta = [];
    private $rateLimits = [];
    private $notificationQueue;
    private $lastQueueCheck = 0;
    private $queueCheckIntervalMs = 500; // check every 0.5s
    
    public function __construct($address = '0.0.0.0', $port = 8080) {
        $this->address = $address;
        $this->port = $port;
        $this->notificationQueue = __DIR__ . '/../cache/notifications.json';
    }
    
    /**
     * Start the WebSocket server
     */
    public function start() {
        // Create socket
        $this->socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        
        if ($this->socket === false) {
            die("socket_create() failed: " . socket_strerror(socket_last_error()) . "\n");
        }
        
        // Set socket options
        socket_set_option($this->socket, SOL_SOCKET, SO_REUSEADDR, 1);
        
        // Bind socket
        if (socket_bind($this->socket, $this->address, $this->port) === false) {
            die("socket_bind() failed: " . socket_strerror(socket_last_error($this->socket)) . "\n");
        }
        
        // Listen for connections
        if (socket_listen($this->socket, 5) === false) {
            die("socket_listen() failed: " . socket_strerror(socket_last_error($this->socket)) . "\n");
        }
        
        echo "WebSocket server started on {$this->address}:{$this->port}\n";
        
        // Main server loop
        while (true) {
            $read = array_merge([$this->socket], $this->clients);
            $write = null;
            $except = null;
            
            if (socket_select($read, $write, $except, 0, 100000) === false) { // 100ms
                break;
            }
            
            // Handle new connections
            if (in_array($this->socket, $read)) {
                $newClient = socket_accept($this->socket);
                $this->clients[] = $newClient;
                
                $header = socket_read($newClient, 2048);
                if (!$this->performHandshake($header, $newClient)) {
                    // Handshake failed or unauthorized
                    @socket_close($newClient);
                    $key = array_search($newClient, $this->clients);
                    if ($key !== false) unset($this->clients[$key]);
                    continue;
                }
                
                $this->sendMessage($newClient, json_encode([
                    'type' => 'connection',
                    'message' => 'Connected to Career Hub WebSocket'
                ]));
                
                $key = array_search($this->socket, $read);
                unset($read[$key]);
            }
            
            // Handle client messages
            foreach ($read as $client) {
                $data = @socket_read($client, 1024, PHP_BINARY_READ);
                
                if ($data === false || $data === '') {
                    // Client disconnected
                    $this->disconnect($client);
                    continue;
                }
                
                $message = $this->unmask($data);
                if ($this->checkRateLimit($client) === false) {
                    $this->sendMessage($client, json_encode(['type' => 'error', 'message' => 'Rate limit exceeded']));
                    $this->disconnect($client);
                    continue;
                }
                $this->handleMessage($client, $message);
            }

            // Periodically process queued notifications
            $now = (int)(microtime(true) * 1000);
            if ($now - $this->lastQueueCheck >= $this->queueCheckIntervalMs) {
                $this->processNotificationQueue();
                $this->lastQueueCheck = $now;
            }
        }
    }
    
    /**
     * Perform WebSocket handshake
     */
    private function performHandshake($rawHeaders, $client) {
        $lines = preg_split("/\r\n/", $rawHeaders);
        $headers = [];
        $path = '/';
        
        foreach ($lines as $line) {
            $line = rtrim($line);
            if (preg_match('/^GET\s+(\S+)\s+HTTP\//i', $line, $m)) {
                $path = $m[1];
            } elseif (preg_match('/\A(\S+): (.*)\z/', $line, $matches)) {
                $headers[$matches[1]] = $matches[2];
            }
        }
        
        if (!isset($headers['Sec-WebSocket-Key'])) {
            return false;
        }
        
        // Optional auth via query string token: ws://host:port/?token=...
        $authorized = true;
        $userId = null;
        $urlParts = parse_url($path);
        if (!empty($urlParts['query'])) {
            parse_str($urlParts['query'], $qs);
            if (!empty($qs['token'])) {
                $meta = $this->validateWsToken($qs['token']);
                if ($meta) {
                    $userId = (int)$meta['userId'];
                    $this->userConnections[$userId] = $client;
                    $this->clientMeta[(int)$client] = ['userId' => $userId];
                } else {
                    $authorized = false;
                }
            }
        }

        if ($authorized === false) {
            // Reject handshake
            $response = "HTTP/1.1 401 Unauthorized\r\nConnection: close\r\n\r\n";
            @socket_write($client, $response, strlen($response));
            return false;
        }

        $secKey = $headers['Sec-WebSocket-Key'];
        $secAccept = base64_encode(pack('H*', sha1($secKey . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));
        
        $response = "HTTP/1.1 101 Switching Protocols\r\n";
        $response .= "Upgrade: websocket\r\n";
        $response .= "Connection: Upgrade\r\n";
        $response .= "Sec-WebSocket-Accept: $secAccept\r\n\r\n";
        
        socket_write($client, $response, strlen($response));
        return true;
    }
    
    /**
     * Handle incoming message from client
     */
    private function handleMessage($client, $message) {
        $data = json_decode($message, true);
        
        if (!$data) {
            return;
        }
        
        switch ($data['type'] ?? '') {
            case 'register':
                // Register user with their connection
                $userId = $data['userId'] ?? null;
                if ($userId) {
                    $this->userConnections[$userId] = $client;
                    $this->clientMeta[(int)$client]['userId'] = (int)$userId;
                }
                break;
            case 'subscribe':
                $channel = $data['channel'] ?? null;
                if ($channel) {
                    $this->subscribe($client, $channel);
                }
                break;
            case 'unsubscribe':
                $channel = $data['channel'] ?? null;
                if ($channel) {
                    $this->unsubscribe($client, $channel);
                }
                break;
                
            case 'ping':
                $this->sendMessage($client, json_encode(['type' => 'pong']));
                break;
                
            default:
                // Optional channel broadcast
                if (!empty($data['channel'])) {
                    $this->broadcastToChannel($data['channel'], json_encode($data));
                } else {
                    $this->broadcast($message);
                }
                break;
        }
    }
    
    /**
     * Send message to specific client
     */
    private function sendMessage($client, $message) {
        $message = $this->mask($message);
        @socket_write($client, $message, strlen($message));
    }
    
    /**
     * Broadcast message to all clients
     */
    public function broadcast($message) {
        $message = $this->mask($message);
        foreach ($this->clients as $client) {
            @socket_write($client, $message, strlen($message));
        }
    }

    public function broadcastToChannel($channel, $message) {
        $message = $this->mask($message);
        if (!isset($this->channels[$channel])) return;
        foreach ($this->channels[$channel] as $client) {
            @socket_write($client, $message, strlen($message));
        }
    }
    
    /**
     * Send message to specific user
     */
    public function sendToUser($userId, $message) {
        if (isset($this->userConnections[$userId])) {
            $this->sendMessage($this->userConnections[$userId], $message);
        }
    }
    
    /**
     * Disconnect client
     */
    private function disconnect($client) {
        $key = array_search($client, $this->clients);
        if ($key !== false) {
            unset($this->clients[$key]);
        }
        
        // Remove from user connections
        foreach ($this->userConnections as $userId => $conn) {
            if ($conn === $client) {
                unset($this->userConnections[$userId]);
                break;
            }
        }
        // Remove from channels
        foreach ($this->channels as $channel => $subs) {
            $idx = array_search($client, $subs, true);
            if ($idx !== false) {
                unset($this->channels[$channel][$idx]);
            }
        }
        unset($this->clientMeta[(int)$client]);
        
        @socket_close($client);
    }

    private function subscribe($client, $channel) {
        if (!isset($this->channels[$channel])) {
            $this->channels[$channel] = [];
        }
        if (!in_array($client, $this->channels[$channel], true)) {
            $this->channels[$channel][] = $client;
        }
    }

    private function unsubscribe($client, $channel) {
        if (!isset($this->channels[$channel])) return;
        $idx = array_search($client, $this->channels[$channel], true);
        if ($idx !== false) {
            unset($this->channels[$channel][$idx]);
        }
    }

    private function validateWsToken($token) {
        $dir = __DIR__ . '/../cache/ws_tokens';
        $file = $dir . '/' . basename($token) . '.json';
        if (!file_exists($file)) return null;
        $meta = json_decode(@file_get_contents($file), true);
        if (!$meta) return null;
        if (empty($meta['expiresAt']) || time() > (int)$meta['expiresAt']) return null;
        return $meta;
    }

    private function processNotificationQueue() {
        if (!file_exists($this->notificationQueue)) return;
        $json = @file_get_contents($this->notificationQueue);
        if ($json === false) return;
        $items = json_decode($json, true);
        if (!is_array($items) || empty($items)) return;

        $remaining = [];
        foreach ($items as $item) {
            if (!empty($item['sent'])) { continue; }
            $payloadArr = [
                'type' => $item['type'] ?? 'message',
                'data' => $item['data'] ?? []
            ];
            $payload = json_encode($payloadArr);
            if (!empty($item['userId'])) {
                $this->sendToUser((int)$item['userId'], $payload);
            } elseif (!empty($item['channel'])) {
                $this->broadcastToChannel($item['channel'], json_encode($payloadArr));
            } else {
                $this->broadcast($payload);
            }
            // mark sent (skip requeue)
        }
        @file_put_contents($this->notificationQueue, json_encode($remaining, JSON_PRETTY_PRINT));
    }

    private function checkRateLimit($client) {
        $key = (int)$client;
        $now = microtime(true);
        $window = 10.0; // seconds
        $limit = 30; // max messages per window
        if (!isset($this->rateLimits[$key])) {
            $this->rateLimits[$key] = ['start' => $now, 'count' => 0];
        }
        $entry = &$this->rateLimits[$key];
        if ($now - $entry['start'] > $window) {
            $entry['start'] = $now;
            $entry['count'] = 0;
        }
        $entry['count']++;
        return $entry['count'] <= $limit;
    }
    
    /**
     * Mask message for WebSocket protocol
     */
    private function mask($text) {
        $b1 = 0x80 | (0x1 & 0x0f);
        $length = strlen($text);
        
        if ($length <= 125) {
            $header = pack('CC', $b1, $length);
        } elseif ($length > 125 && $length < 65536) {
            $header = pack('CCn', $b1, 126, $length);
        } else {
            $header = pack('CCNN', $b1, 127, $length);
        }
        
        return $header . $text;
    }
    
    /**
     * Unmask message from client
     */
    private function unmask($text) {
        $length = ord($text[1]) & 127;
        
        if ($length == 126) {
            $masks = substr($text, 4, 4);
            $data = substr($text, 8);
        } elseif ($length == 127) {
            $masks = substr($text, 10, 4);
            $data = substr($text, 14);
        } else {
            $masks = substr($text, 2, 4);
            $data = substr($text, 6);
        }
        
        $text = "";
        for ($i = 0; $i < strlen($data); ++$i) {
            $text .= $data[$i] ^ $masks[$i % 4];
        }
        
        return $text;
    }
}
