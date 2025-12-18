<?php
/**
 * Google OAuth Authentication Service
 */

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../helpers/EnvLoader.php';

class GoogleAuthService {
    private $client;
    private $config;
    private $userModel;

    public function __construct() {
        // Load environment variables
        EnvLoader::load();

        $this->config = require __DIR__ . '/../../config/app.php';
        $this->userModel = new User();
        $this->initializeClient();
    }
    
    /**
     * Initialize Google Client
     */
    private function initializeClient() {
        $this->client = new Google_Client();
        $this->client->setClientId($this->config['google']['client_id']);
        $this->client->setClientSecret($this->config['google']['client_secret']);
        $this->client->setRedirectUri($this->config['google']['redirect_uri']);
        $this->client->setScopes($this->config['google']['scopes']);
        $this->client->setAccessType('offline');
        $this->client->setPrompt('select_account consent');
    }
    
    /**
     * Get Google OAuth URL
     */
    public function getAuthUrl() {
        return $this->client->createAuthUrl();
    }
    
    /**
     * Handle Google OAuth callback
     */
    public function handleCallback($code) {
        try {
            // Exchange code for access token
            $token = $this->client->fetchAccessTokenWithAuthCode($code);
            
            if (isset($token['error'])) {
                throw new Exception('Error fetching access token: ' . $token['error']);
            }
            
            $this->client->setAccessToken($token);
            
            // Get user info from Google
            $oauth2 = new Google_Service_Oauth2($this->client);
            $userInfo = $oauth2->userinfo->get();
            
            return $this->processGoogleUser($userInfo);
            
        } catch (Exception $e) {
            error_log('Google OAuth Error: ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Process Google user data
     */
    private function processGoogleUser($googleUser) {
        $googleId = $googleUser->getId();
        $email = $googleUser->getEmail();
        $firstName = $googleUser->getGivenName();
        $lastName = $googleUser->getFamilyName();
        $avatarUrl = $googleUser->getPicture();
        
        // Check if user already exists with this Google ID
        $existingUser = $this->userModel->findByGoogleId($googleId);
        
        if ($existingUser) {
            // Update user info and return
            $this->userModel->update($existingUser['id'], [
                'google_avatar_url' => $avatarUrl,
                'last_login' => date('Y-m-d H:i:s')
            ]);
            return $existingUser;
        }
        
        // Check if user exists with same email
        $existingEmailUser = $this->userModel->findByEmail($email);
        
        if ($existingEmailUser) {
            // Link Google account to existing user
            $this->userModel->update($existingEmailUser['id'], [
                'google_id' => $googleId,
                'auth_provider' => 'google',
                'google_avatar_url' => $avatarUrl,
                'last_login' => date('Y-m-d H:i:s')
            ]);
            return $existingEmailUser;
        }
        
        // Create new user
        return $this->createGoogleUser($googleId, $email, $firstName, $lastName, $avatarUrl);
    }
    
    /**
     * Create new user from Google data
     */
    private function createGoogleUser($googleId, $email, $firstName, $lastName, $avatarUrl) {
        $userData = [
            'google_id' => $googleId,
            'email' => $email,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'auth_provider' => 'google',
            'google_avatar_url' => $avatarUrl,
            'role' => 'aspirant', // Default role for new Google users
            'status' => 'active'
        ];
        
        $userId = $this->userModel->create($userData);
        
        if ($userId) {
            return $this->userModel->findById($userId);
        }
        
        return false;
    }
    
    /**
     * Check if Google OAuth is configured
     */
    public function isConfigured() {
        return !empty($this->config['google']['client_id']) && 
               !empty($this->config['google']['client_secret']);
    }
}
