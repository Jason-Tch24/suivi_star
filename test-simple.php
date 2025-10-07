<?php
/**
 * Test Simple - STAR System
 */

echo "<h1>🔧 Test Simple de Connexion</h1>";

// Test 1: Basic PHP
echo "<h2>1. ✅ PHP fonctionne</h2>";
echo "<p>Version PHP: " . PHP_VERSION . "</p>";

// Test 2: Session
session_start();
echo "<h2>2. ✅ Sessions fonctionnent</h2>";
echo "<p>Session ID: " . session_id() . "</p>";

// Test 3: Database connection with direct PDO
echo "<h2>3. 🗄️ Test de Base de Données Direct</h2>";

try {
    // Direct PDO connection
    $host = 'localhost';
    $port = '8889';
    $dbname = 'star_volunteer_system';
    $username = 'root';
    $password = 'root';
    
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    
    echo "✅ Connexion PDO directe réussie<br>";
    
    // Test query
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch();
    echo "✅ " . $result['count'] . " utilisateur(s) dans la base<br>";
    
    // Get admin user
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute(['admin@star-church.org']);
    $admin = $stmt->fetch();
    
    if ($admin) {
        echo "✅ Utilisateur admin trouvé<br>";
        echo "<div style='background: #e9ecef; padding: 10px; border-radius: 5px; font-family: monospace; margin: 10px 0;'>";
        echo "ID: " . $admin['id'] . "<br>";
        echo "Email: " . $admin['email'] . "<br>";
        echo "Nom: " . $admin['first_name'] . " " . $admin['last_name'] . "<br>";
        echo "Rôle: " . $admin['role'] . "<br>";
        echo "Statut: " . $admin['status'] . "<br>";
        echo "</div>";
        
        // Test password
        $testPassword = 'password123';
        $passwordWorks = password_verify($testPassword, $admin['password_hash']);
        echo "<p>🔐 Test mot de passe 'password123': " . ($passwordWorks ? "✅ OK" : "❌ ÉCHEC") . "</p>";
        
        if (!$passwordWorks) {
            echo "<div style='background: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
            echo "<strong>⚠️ Le mot de passe ne fonctionne pas !</strong><br>";
            echo "Je vais le corriger maintenant...";
            echo "</div>";
            
            // Fix password
            $newHash = password_hash($testPassword, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE email = ?");
            $stmt->execute([$newHash, 'admin@star-church.org']);
            
            echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
            echo "✅ <strong>Mot de passe corrigé !</strong><br>";
            echo "Vous pouvez maintenant vous connecter avec:<br>";
            echo "Email: admin@star-church.org<br>";
            echo "Mot de passe: password123";
            echo "</div>";
        }
        
    } else {
        echo "❌ Utilisateur admin non trouvé !<br>";
        
        // Create admin user
        echo "<div style='background: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "<strong>⚠️ Création de l'utilisateur admin...</strong>";
        echo "</div>";
        
        $adminData = [
            'email' => 'admin@star-church.org',
            'password_hash' => password_hash('password123', PASSWORD_DEFAULT),
            'first_name' => 'System',
            'last_name' => 'Administrator',
            'role' => 'administrator',
            'status' => 'active'
        ];
        
        $stmt = $pdo->prepare("INSERT INTO users (email, password_hash, first_name, last_name, role, status) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $adminData['email'],
            $adminData['password_hash'],
            $adminData['first_name'],
            $adminData['last_name'],
            $adminData['role'],
            $adminData['status']
        ]);
        
        echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "✅ <strong>Utilisateur admin créé !</strong><br>";
        echo "Email: admin@star-church.org<br>";
        echo "Mot de passe: password123";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "❌ Erreur de base de données: " . $e->getMessage() . "<br>";
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<strong>PROBLÈME:</strong> Impossible de se connecter à la base de données.<br>";
    echo "<strong>Solutions possibles:</strong><br>";
    echo "1. Vérifiez que MAMP est démarré<br>";
    echo "2. Vérifiez que MySQL fonctionne sur le port 8889<br>";
    echo "3. Vérifiez que la base de données 'star_volunteer_system' existe<br>";
    echo "4. Allez sur <a href='setup.php'>setup.php</a> pour créer la base de données";
    echo "</div>";
}

// Test 4: Test de connexion simple
echo "<h2>4. 🔐 Test de Connexion Simple</h2>";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['simple_login'])) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    echo "<h3>🔍 Test avec:</h3>";
    echo "<p>Email: " . htmlspecialchars($email) . "</p>";
    echo "<p>Mot de passe: " . str_repeat('*', strlen($password)) . "</p>";
    
    try {
        // Direct authentication without classes
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND status = 'active'");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password_hash'])) {
            // Set session manually
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
            
            echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
            echo "<strong>🎉 CONNEXION RÉUSSIE !</strong><br>";
            echo "Session créée manuellement.<br>";
            echo "Utilisateur: " . $_SESSION['user_name'] . "<br>";
            echo "Rôle: " . $_SESSION['user_role'] . "<br>";
            echo "<a href='dashboard.php' style='background: #007bff; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;'>📊 Aller au Dashboard</a>";
            echo "</div>";
            
        } else {
            echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
            echo "❌ <strong>Échec de la connexion</strong><br>";
            if (!$user) {
                echo "Utilisateur non trouvé ou inactif.";
            } else {
                echo "Mot de passe incorrect.";
            }
            echo "</div>";
        }
        
    } catch (Exception $e) {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "❌ Erreur: " . $e->getMessage();
        echo "</div>";
    }
}

// Login form
echo "<form method='POST' style='background: #f8f9fa; padding: 20px; border-radius: 8px; max-width: 400px; margin: 20px 0;'>";
echo "<input type='hidden' name='simple_login' value='1'>";
echo "<h3>🔐 Test de Connexion Direct</h3>";
echo "<div style='margin-bottom: 15px;'>";
echo "<label style='display: block; margin-bottom: 5px; font-weight: bold;'>Email:</label>";
echo "<input type='email' name='email' value='admin@star-church.org' style='width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;' required>";
echo "</div>";
echo "<div style='margin-bottom: 15px;'>";
echo "<label style='display: block; margin-bottom: 5px; font-weight: bold;'>Mot de passe:</label>";
echo "<input type='password' name='password' value='password123' style='width: 100%; padding: 8px; border: 1px solid #ccc; border-radius: 4px;' required>";
echo "</div>";
echo "<button type='submit' style='background: #28a745; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; width: 100%;'>";
echo "🔐 Test Connexion Direct";
echo "</button>";
echo "</form>";

// Show current session
echo "<h2>5. 📊 Session Actuelle</h2>";
echo "<div style='background: #e9ecef; padding: 15px; border-radius: 5px; font-family: monospace;'>";
if (empty($_SESSION)) {
    echo "Aucune session active";
} else {
    echo "Session active:<br>";
    foreach ($_SESSION as $key => $value) {
        echo "$key: " . htmlspecialchars($value) . "<br>";
    }
}
echo "</div>";

// Navigation
echo "<hr>";
echo "<div style='text-align: center; margin: 30px 0;'>";
echo "<a href='index.php' style='background: #6c757d; color: white; padding: 12px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>← Accueil</a>";
echo "<a href='login.php' style='background: #007bff; color: white; padding: 12px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>🔐 Page de Connexion</a>";
echo "<a href='dashboard.php' style='background: #28a745; color: white; padding: 12px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>📊 Dashboard</a>";
echo "</div>";
?>
