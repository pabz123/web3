<?php
/**
 * Secure Configuration File
 * 
 * IMPORTANT: On InfinityFree, move this file OUTSIDE the public htdocs directory
 * Recommended location: /config/config.php (parent of htdocs)
 * 
 * Directory structure on InfinityFree:
 * /home/username/
 *   ├── htdocs/              (public web root - your website files go here)
 *   └── config/              (private - create this folder)
 *       └── config.php       (this file - NOT accessible from web)
 */

return [
    // ==========================================
    // DATABASE CONFIGURATION
    // ==========================================
    
    // Local Development
    'DB_HOST_LOCAL' => 'localhost',
    'DB_USER_LOCAL' => 'root',
    'DB_PASS_LOCAL' => '',
    'DB_NAME_LOCAL' => 'uniconnect_db',
    
    // InfinityFree Production
    'DB_HOST' => 'sql113.infinityfree.com',
    'DB_USER' => 'if0_40185804',
    'DB_PASS' => 'careerhub12',  // Change this to your actual InfinityFree database password
    'DB_NAME' => 'if0_40185804_uniconnect_db',
    
    // ==========================================
    // EXTERNAL API KEYS
    // ==========================================
    
    // Adzuna Job Search API
    'ADZUNA_APP_ID' => 'a306aa16',
    'ADZUNA_APP_KEY' => '918d215fb40ce6f6d9c506be26c5dafd',
    
    // RapidAPI (JSearch)
    'RAPIDAPI_KEY' => '03dabda16dmsh846042236d69e8fp102ba6jsn2f1f9c31e700',
    
    // ==========================================
    // APPLICATION SETTINGS
    // ==========================================
    
    // Cache TTL in seconds (default: 1 hour)
    'CACHE_TTL' => 3600,
    
    // Base URL (automatically detect environment)
    'BASE_URL' => ($_SERVER['HTTP_HOST'] ?? '') === 'localhost' 
        ? '/career_hub' 
        : '',  // Empty for production (InfinityFree uses root)
];
?>
