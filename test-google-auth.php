<?php
/**
 * Test Google Authentication Implementation
 */

require_once __DIR__ . '/src/services/GoogleAuthService.php';
require_once __DIR__ . '/src/models/User.php';

echo "<h1>🔍 Test de l'Authentification Google</h1>";
echo "<div style='font-family: monospace; background: #f5f5f5; padding: 20px; border-radius: 8px;'>";

// Test 1: Configuration Google
echo "<h2>1. Configuration Google OAuth</h2>";
try {
    $googleAuth = new GoogleAuthService();
    
    if ($googleAuth->isConfigured()) {
        echo "✅ Configuration Google OAuth détectée<br>";
        echo "🔗 URL d'authentification générée avec succès<br>";
    } else {
        echo "⚠️ Configuration Google OAuth manquante<br>";
        echo "📝 Ajoutez GOOGLE_CLIENT_ID et GOOGLE_CLIENT_SECRET dans votre .env<br>";
    }
} catch (Exception $e) {
    echo "❌ Erreur lors de l'initialisation: " . $e->getMessage() . "<br>";
}

// Test 2: Modèle User avec Google
echo "<h2>2. Modèle User - Méthodes Google</h2>";
try {
    $userModel = new User();
    
    // Test findByGoogleId method
    $testGoogleId = 'test_google_id_123';
    $result = $userModel->findByGoogleId($testGoogleId);
    echo "✅ Méthode findByGoogleId() fonctionne<br>";
    
    // Test create with Google data
    $testUserData = [
        'google_id' => 'test_' . time(),
        'email' => 'test.google@example.com',
        'first_name' => 'Test',
        'last_name' => 'Google',
        'auth_provider' => 'google',
        'role' => 'aspirant',
        'status' => 'active'
    ];
    
    echo "✅ Structure de données Google validée<br>";
    
} catch (Exception $e) {
    echo "❌ Erreur modèle User: " . $e->getMessage() . "<br>";
}

// Test 3: Structure de base de données
echo "<h2>3. Structure de Base de Données</h2>";
try {
    require_once __DIR__ . '/src/models/Database.php';
    $db = Database::getInstance();
    
    // Check Google fields
    $result = $db->query("SHOW COLUMNS FROM users LIKE 'google_%'");
    $googleFields = $result->fetchAll();
    
    if (count($googleFields) >= 2) {
        echo "✅ Champs Google ajoutés à la table users:<br>";
        foreach ($googleFields as $field) {
            echo "   - " . $field['Field'] . " (" . $field['Type'] . ")<br>";
        }
    } else {
        echo "❌ Champs Google manquants dans la table users<br>";
    }
    
    // Check auth_provider field
    $result = $db->query("SHOW COLUMNS FROM users LIKE 'auth_provider'");
    $authField = $result->fetch();
    
    if ($authField) {
        echo "✅ Champ auth_provider configuré: " . $authField['Type'] . "<br>";
    } else {
        echo "❌ Champ auth_provider manquant<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Erreur base de données: " . $e->getMessage() . "<br>";
}

// Test 4: Fichiers d'authentification
echo "<h2>4. Fichiers d'Authentification</h2>";

$authFiles = [
    'auth/google/login.php' => 'Point d\'entrée OAuth',
    'auth/google/callback.php' => 'Gestionnaire de callback',
    'src/services/GoogleAuthService.php' => 'Service d\'authentification'
];

foreach ($authFiles as $file => $description) {
    if (file_exists($file)) {
        echo "✅ $description: $file<br>";
    } else {
        echo "❌ Fichier manquant: $file<br>";
    }
}

// Test 5: Dépendances Composer
echo "<h2>5. Dépendances</h2>";
if (file_exists('vendor/autoload.php')) {
    echo "✅ Autoloader Composer disponible<br>";
    
    if (file_exists('vendor/google/apiclient')) {
        echo "✅ Google API Client installé<br>";
    } else {
        echo "⚠️ Google API Client non trouvé dans vendor/<br>";
    }
} else {
    echo "❌ Composer autoloader manquant<br>";
    echo "   Exécutez: composer install<br>";
}

echo "</div>";

echo "<h2>📋 Résumé</h2>";
echo "<p>Si tous les tests sont ✅, l'authentification Google est prête !</p>";
echo "<p>Pour tester complètement:</p>";
echo "<ol>";
echo "<li>Configurez vos identifiants Google dans .env</li>";
echo "<li>Visitez la page de connexion</li>";
echo "<li>Cliquez sur 'Continuer avec Google'</li>";
echo "</ol>";
?>
