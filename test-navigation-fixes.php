<?php
/**
 * Test Navigation and Routing Fixes - STAR System
 * This file tests the navigation and routing fixes implemented
 */

echo "<h1>🧪 Navigation & Routing Test Results</h1>";

// Test 1: Check if routing configuration is correct
echo "<h2>1. Routing Configuration Test</h2>";

$indexContent = file_get_contents('index.php');

// Check if /aspirants route exists and points to correct file
if (strpos($indexContent, "case '/aspirants':") !== false && 
    strpos($indexContent, "include 'src/views/aspirants.php';") !== false) {
    echo "✅ /aspirants route correctly configured<br>";
} else {
    echo "❌ /aspirants route configuration issue<br>";
}

// Check if /ministries route exists
if (strpos($indexContent, "case '/ministries':") !== false && 
    strpos($indexContent, "include 'src/views/ministries.php';") !== false) {
    echo "✅ /ministries route correctly configured<br>";
} else {
    echo "❌ /ministries route configuration issue<br>";
}

// Test 2: Check if navigation links are updated
echo "<h2>2. Navigation Links Test</h2>";

$filesToCheck = [
    'src/views/aspirants.php',
    'src/views/ministries.php',
    'src/views/dashboard/admin.php',
    'src/views/dashboard/pastor.php',
    'src/views/dashboard/mds.php',
    'src/views/dashboard/mentor.php',
    'src/views/dashboard/aspirant.php'
];

foreach ($filesToCheck as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        
        // Check for clean URLs
        $hasCleanUrls = (strpos($content, 'href="/aspirants"') !== false || 
                        strpos($content, 'href="/ministries"') !== false ||
                        strpos($content, 'href="/dashboard"') !== false ||
                        strpos($content, 'href="/logout"') !== false);
        
        // Check for old .php links (should not exist)
        $hasOldLinks = (strpos($content, 'href="aspirants.php"') !== false || 
                       strpos($content, 'href="ministries.php"') !== false ||
                       strpos($content, 'href="dashboard.php"') !== false);
        
        if ($hasCleanUrls && !$hasOldLinks) {
            echo "✅ $file - Navigation links updated correctly<br>";
        } else {
            echo "❌ $file - Navigation links need attention<br>";
        }
    } else {
        echo "⚠️ $file - File not found<br>";
    }
}

// Test 3: Check CSS paths
echo "<h2>3. CSS Paths Test</h2>";

$cssFiles = ['src/views/aspirants.php', 'src/views/ministries.php'];

foreach ($cssFiles as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        
        // Check for correct relative CSS paths
        if (strpos($content, 'href="../../public/css/') !== false) {
            echo "✅ $file - CSS paths correctly configured<br>";
        } else {
            echo "❌ $file - CSS paths need fixing<br>";
        }
    }
}

// Test 4: Check 404 error page CSS
echo "<h2>4. 404 Error Page CSS Test</h2>";

if (file_exists('src/views/errors/404.php')) {
    $content = file_get_contents('src/views/errors/404.php');
    
    if (strpos($content, 'href="../../../public/css/style.css"') !== false) {
        echo "✅ 404 error page CSS path correctly configured<br>";
    } else {
        echo "❌ 404 error page CSS path needs fixing<br>";
    }
} else {
    echo "⚠️ 404 error page not found<br>";
}

// Test 5: File existence check
echo "<h2>5. Required Files Existence Test</h2>";

$requiredFiles = [
    'src/views/aspirants.php',
    'src/views/ministries.php',
    'public/css/modern-design-system.css',
    'public/css/dashboard-override.css'
];

foreach ($requiredFiles as $file) {
    if (file_exists($file)) {
        echo "✅ $file exists<br>";
    } else {
        echo "❌ $file missing<br>";
    }
}

echo "<h2>🎯 Summary</h2>";
echo "<p>If all tests show ✅, the navigation and routing fixes have been successfully implemented!</p>";
echo "<p>You can now test by clicking on the 'Aspirants' and 'Ministries' tabs in the navigation.</p>";

?>
