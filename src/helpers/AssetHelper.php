<?php
/**
 * Asset Helper for STAR Volunteer Management System
 * Helps manage CSS and JS file paths consistently across the application
 */

class AssetHelper {
    
    /**
     * Get the base URL for assets
     */
    public static function getBaseUrl() {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $path = dirname($_SERVER['SCRIPT_NAME']);
        
        // Remove /src/views/dashboard or similar paths to get to root
        $path = preg_replace('#/src/.*#', '', $path);
        
        return $protocol . '://' . $host . $path;
    }
    
    /**
     * Get CSS file path
     */
    public static function css($filename) {
        $baseUrl = self::getBaseUrl();
        return $baseUrl . '/public/css/' . $filename;
    }
    
    /**
     * Get JS file path
     */
    public static function js($filename) {
        $baseUrl = self::getBaseUrl();
        return $baseUrl . '/public/js/' . $filename;
    }
    
    /**
     * Get image file path
     */
    public static function img($filename) {
        $baseUrl = self::getBaseUrl();
        return $baseUrl . '/public/images/' . $filename;
    }
    
    /**
     * Generate CSS link tag
     */
    public static function cssLink($filename) {
        return '<link rel="stylesheet" href="' . self::css($filename) . '">';
    }
    
    /**
     * Generate JS script tag
     */
    public static function jsScript($filename) {
        return '<script src="' . self::js($filename) . '"></script>';
    }
    
    /**
     * Get relative path to root from current location
     */
    public static function getRelativeRoot() {
        $currentPath = $_SERVER['SCRIPT_NAME'];
        $depth = substr_count($currentPath, '/') - 1;
        
        if ($depth <= 1) {
            return './';
        }
        
        return str_repeat('../', $depth - 1);
    }
    
    /**
     * Get CSS path relative to current location
     */
    public static function relativeCss($filename) {
        return self::getRelativeRoot() . 'public/css/' . $filename;
    }
    
    /**
     * Get JS path relative to current location
     */
    public static function relativeJs($filename) {
        return self::getRelativeRoot() . 'public/js/' . $filename;
    }

    /**
     * Generate URL for application routes
     */
    public static function url($path = '/') {
        $baseUrl = self::getBaseUrl();

        // Clean the path
        $path = '/' . trim($path, '/');

        // For root path, just return base URL
        if ($path === '/') {
            return $baseUrl . '/';
        }

        // For other paths, use index.php with path parameter
        return $baseUrl . '/index.php?path=' . urlencode($path);
    }

    /**
     * Generate URL for direct PHP files (legacy support)
     */
    public static function directUrl($filename) {
        $baseUrl = self::getBaseUrl();
        return $baseUrl . '/' . ltrim($filename, '/');
    }

    /**
     * Get asset path (generic method for any asset)
     */
    public static function asset($path) {
        $baseUrl = self::getBaseUrl();
        return $baseUrl . '/public/' . ltrim($path, '/');
    }
}
?>
