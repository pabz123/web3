<?php
/**
 * WebSocket Server Runner
 * 
 * To run this server, execute in terminal:
 * php websocket_server.php
 * 
 * The server will listen on port 8080 for WebSocket connections
 */

require_once __DIR__ . '/classes/autoload.php';

echo "===========================================\n";
echo "Career Hub WebSocket Server\n";
echo "===========================================\n\n";

try {
    $server = new WebSocketServer('0.0.0.0', 8080);
    $server->start();
} catch (Exception $e) {
    echo "Error starting WebSocket server: " . $e->getMessage() . "\n";
    exit(1);
}
