<?php
/**
 * Diagnostic Complet - STAR System
 */

// Start session
session_start();

echo "<h1>🔍 Diagnostic Complet du Problème de Connexion</h1>";

// Test 1: Environment and basic setup
echo "<h2>1. 🌍 Environnement et Configuration</h2>";
echo "<div style='background: #f8f9fa; padding: 15px; border-radius: 5px; font-family: monospace;'>";
echo "PHP Version: " . PHP_VERSION . "<br>";
echo "Server: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "<br>";
echo "Document Root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'Unknown') . "<br>";
echo "Current URL: " . ($_SERVER['HTTP_HOST'] ?? 'localhost') . $_SERVER['REQUEST_URI'] . "<br>";
echo "Session Status: " . (session_status() === PHP_SESSION_ACTIVE ? 'Active' : 'Inactive') . "<br>";
echo "Session ID: " . session_id() . "<br>";
echo "</div>";

// Test 2: File existence
echo "<h2>2. 📁 Vérification des Fichiers</h2>";
$requiredFiles = [
    'src/models/Database.php',
    'src/models/User.php',
    'src/middleware/Auth.php',
    'login.php',
    '.env'
];

foreach ($requiredFiles as $file) {
    $exists = file_exists(__DIR__ . '/' . $file);
    $status = $exists ? "✅" : "❌";
    echo "$status $file<br>";
}

// Test 3: Database connection
echo "<h2>3. 🗄️ Connexion à la Base de Données</h2>";
try {
    // Load environment
    if (file_exists('.env')) {
        $lines = file('.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
                list($key, $value) = explode('=', $line, 2);
                $_ENV[trim($key)] = trim($value);
            }
        }
    }
    
    require_once __DIR__ . '/src/models/Database.php';
    $db = Database::getInstance();
    echo "✅ Connexion à la base de données réussie<br>";
    
    // Check database name
    $stmt = $db->query("SELECT DATABASE() as db_name");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "📊 Base de données: " . $result['db_name'] . "<br>";
    
} catch (Exception $e) {
    echo "❌ Erreur de connexion: " . $e->getMessage() . "<br>";
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<strong>PROBLÈME MAJEUR:</strong> Impossible de se connecter à la base de données.<br>";
    echo "Vérifiez que MAMP est démarré et que la base de données existe.";
    echo "</div>";
    exit;
}

// Test 4: Users table and data
echo "<h2>4. 👥 Vérification des Utilisateurs</h2>";
try {
    // Check if users table exists
    $stmt = $db->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() == 0) {
        echo "❌ Table 'users' n'existe pas !<br>";
        echo "<a href='setup.php' style='background: #dc3545; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;'>🔧 Créer la Base de Données</a><br>";
        exit;
    }
    
    // Get users
    $stmt = $db->query("SELECT id, email, password_hash, first_name, last_name, role, status FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($users)) {
        echo "❌ Aucun utilisateur dans la base de données !<br>";
        echo "<a href='setup.php' style='background: #ffc107; color: #212529; padding: 10px 15px; text-decoration: none; border-radius: 5px;'>🔧 Créer des Utilisateurs</a><br>";
        exit;
    }
    
    echo "✅ " . count($users) . " utilisateur(s) trouvé(s)<br>";
    
    // Show admin user specifically
    $admin = null;
    foreach ($users as $user) {
        if ($user['email'] === 'admin@star-church.org') {
            $admin = $user;
            break;
        }
    }
    
    if ($admin) {
        echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "<strong>👑 Utilisateur Admin Trouvé:</strong><br>";
        echo "ID: " . $admin['id'] . "<br>";
        echo "Email: " . $admin['email'] . "<br>";
        echo "Nom: " . $admin['first_name'] . " " . $admin['last_name'] . "<br>";
        echo "Rôle: " . $admin['role'] . "<br>";
        echo "Statut: " . $admin['status'] . "<br>";
        echo "Hash: " . substr($admin['password_hash'], 0, 20) . "...<br>";
        echo "</div>";
    } else {
        echo "❌ Utilisateur admin@star-church.org non trouvé !<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "<br>";
}

// Test 5: Password verification
echo "<h2>5. 🔐 Test de Vérification du Mot de Passe</h2>";
if ($admin) {
    $testPassword = 'password123';
    $isValid = password_verify($testPassword, $admin['password_hash']);
    
    echo "<div style='background: #e9ecef; padding: 15px; border-radius: 5px; font-family: monospace;'>";
    echo "Mot de passe testé: '$testPassword'<br>";
    echo "Hash dans la DB: " . substr($admin['password_hash'], 0, 50) . "...<br>";
    echo "Résultat: " . ($isValid ? "✅ VALIDE" : "❌ INVALIDE") . "<br>";
    echo "</div>";
    
    if (!$isValid) {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "<strong>PROBLÈME TROUVÉ:</strong> Le mot de passe 'password123' ne correspond pas au hash stocké !<br>";
        echo "Solution: Réinitialiser le mot de passe.";
        echo "</div>";
        
        // Fix password button
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fix_admin_password'])) {
            $newHash = password_hash('password123', PASSWORD_DEFAULT);
            $stmt = $db->prepare("UPDATE users SET password_hash = ? WHERE email = 'admin@star-church.org'");
            $stmt->execute([$newHash]);
            
            echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
            echo "✅ Mot de passe admin réinitialisé avec succès !<br>";
            echo "Nouveau hash: " . substr($newHash, 0, 50) . "...<br>";
            echo "</div>";
            
            // Update admin data for next tests
            $admin['password_hash'] = $newHash;
        } else {
            echo "<form method='POST' style='margin: 10px 0;'>";
            echo "<input type='hidden' name='fix_admin_password' value='1'>";
            echo "<button type='submit' style='background: #dc3545; color: white; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer;'>";
            echo "🔧 Réinitialiser le Mot de Passe Admin";
            echo "</button>";
            echo "</form>";
        }
    }
}

// Test 6: Auth class functionality
echo "<h2>6. 🔒 Test de la Classe Auth</h2>";
try {
    require_once __DIR__ . '/src/middleware/Auth.php';
    require_once __DIR__ . '/src/models/User.php';
    
    echo "✅ Classes Auth et User chargées<br>";
    
    // Test User model
    $userModel = new User();
    $testUser = $userModel->findByEmail('admin@star-church.org');
    
    if ($testUser) {
        echo "✅ User::findByEmail() fonctionne<br>";
        
        // Test authenticate method
        $authResult = $userModel->authenticate('admin@star-church.org', 'password123');
        echo "🔐 User::authenticate() résultat: " . ($authResult ? "✅ SUCCÈS" : "❌ ÉCHEC") . "<br>";
        
        if ($authResult) {
            // Test Auth::login
            $loginResult = Auth::login('admin@star-church.org', 'password123');
            echo "🔑 Auth::login() résultat: " . ($loginResult ? "✅ SUCCÈS" : "❌ ÉCHEC") . "<br>";
            
            if ($loginResult) {
                echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
                echo "<strong>🎉 CONNEXION RÉUSSIE !</strong><br>";
                echo "L'utilisateur admin peut maintenant se connecter.<br>";
                echo "<a href='login.php' style='background: #007bff; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;'>🔐 Aller à la Page de Connexion</a>";
                echo "</div>";
                
                // Show session info
                echo "<h3>📊 Informations de Session:</h3>";
                echo "<div style='background: #e9ecef; padding: 15px; border-radius: 5px; font-family: monospace;'>";
                echo "Session Data:<br>";
                print_r($_SESSION);
                echo "</div>";
                
                Auth::logout(); // Clean up for next tests
            }
        }
    } else {
        echo "❌ User::findByEmail() a échoué<br>";
    }
    
} catch (Exception $e) {
    echo "❌ Erreur dans les classes: " . $e->getMessage() . "<br>";
}

// Test 7: Manual login form test
echo "<h2>7. 🧪 Test Manuel de Connexion</h2>";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['manual_login'])) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    echo "<h3>🔍 Tentative de connexion:</h3>";
    echo "<div style='background: #e9ecef; padding: 15px; border-radius: 5px;'>";
    echo "Email saisi: '" . htmlspecialchars($email) . "'<br>";
    echo "Mot de passe saisi: '" . str_repeat('*', strlen($password)) . "'<br>";
    echo "</div>";
    
    try {
        if (Auth::login($email, $password)) {
            echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
            echo "<strong>🎉 CONNEXION MANUELLE RÉUSSIE !</strong><br>";
            echo "Redirection vers le dashboard...<br>";
            echo "<script>setTimeout(function(){ window.location.href = 'dashboard.php'; }, 2000);</script>";
            echo "</div>";
        } else {
            echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
            echo "❌ Échec de la connexion manuelle<br>";
            echo "Vérifiez les identifiants ou réinitialisez le mot de passe ci-dessus.";
            echo "</div>";
        }
    } catch (Exception $e) {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "❌ Erreur: " . $e->getMessage();
        echo "</div>";
    }
}

echo "<form method='POST' style='background: #f8f9fa; padding: 20px; border-radius: 8px; max-width: 400px; margin: 20px 0;'>";
echo "<input type='hidden' name='manual_login' value='1'>";
echo "<h3>🔐 Test de Connexion Manuel</h3>";
echo "<div style='margin-bottom: 15px;'>";
echo "<label style='display: block; margin-bottom: 5px; font-weight: bold;'>Email:</label>";
echo "<input type='email' name='email' value='admin@star-church.org' style='width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;' required>";
echo "</div>";
echo "<div style='margin-bottom: 15px;'>";
echo "<label style='display: block; margin-bottom: 5px; font-weight: bold;'>Mot de passe:</label>";
echo "<input type='password' name='password' value='password123' style='width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;' required>";
echo "</div>";
echo "<button type='submit' style='background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; width: 100%;'>";
echo "🔐 Tester la Connexion";
echo "</button>";
echo "</form>";

// Navigation
echo "<hr>";
echo "<div style='text-align: center; margin: 30px 0;'>";
echo "<a href='index.php' style='background: #6c757d; color: white; padding: 12px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>← Accueil</a>";
echo "<a href='login.php' style='background: #007bff; color: white; padding: 12px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>🔐 Page de Connexion</a>";
echo "<a href='setup.php' style='background: #28a745; color: white; padding: 12px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>🔧 Configuration</a>";
echo "</div>";
?>
