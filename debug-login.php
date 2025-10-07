<?php
/**
 * Debug Login Issues - STAR System
 */

// Start session and load dependencies
session_start();
require_once __DIR__ . '/src/middleware/Auth.php';
require_once __DIR__ . '/src/models/User.php';

echo "<h1>🔍 Diagnostic des Problèmes de Connexion</h1>";

// Test 1: Check database connection
echo "<h2>1. Test de Connexion à la Base de Données</h2>";
try {
    $userModel = new User();
    echo "✅ Connexion à la base de données réussie<br>";
} catch (Exception $e) {
    echo "❌ Erreur de connexion à la base de données: " . $e->getMessage() . "<br>";
    exit;
}

// Test 2: Check available users
echo "<h2>2. Utilisateurs Disponibles dans la Base de Données</h2>";
try {
    $db = Database::getInstance();
    $stmt = $db->prepare("SELECT id, email, first_name, last_name, role, status FROM users ORDER BY id");
    $stmt->execute();
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($users)) {
        echo "⚠️ <strong>PROBLÈME TROUVÉ:</strong> Aucun utilisateur dans la base de données !<br>";
        echo "<p>Vous devez d'abord créer des utilisateurs. <a href='setup.php'>Cliquez ici pour configurer le système</a></p>";
    } else {
        echo "✅ " . count($users) . " utilisateur(s) trouvé(s):<br>";
        echo "<table border='1' style='border-collapse: collapse; margin: 10px 0;'>";
        echo "<tr style='background: #f0f0f0;'><th>ID</th><th>Email</th><th>Nom</th><th>Rôle</th><th>Statut</th></tr>";
        foreach ($users as $user) {
            $statusColor = $user['status'] === 'active' ? 'green' : 'red';
            echo "<tr>";
            echo "<td>" . $user['id'] . "</td>";
            echo "<td><strong>" . htmlspecialchars($user['email']) . "</strong></td>";
            echo "<td>" . htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) . "</td>";
            echo "<td>" . htmlspecialchars($user['role']) . "</td>";
            echo "<td style='color: $statusColor;'>" . htmlspecialchars($user['status']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    }
} catch (Exception $e) {
    echo "❌ Erreur lors de la récupération des utilisateurs: " . $e->getMessage() . "<br>";
}

// Test 3: Test login with demo credentials
echo "<h2>3. Test de Connexion avec Identifiants de Démonstration</h2>";

$testCredentials = [
    ['email' => 'admin@star-church.org', 'password' => 'password123', 'role' => 'Administrator'],
    ['email' => 'pastor@star-church.org', 'password' => 'password123', 'role' => 'Pastor'],
    ['email' => 'mds@star-church.org', 'password' => 'password123', 'role' => 'MDS'],
    ['email' => 'mentor1@star-church.org', 'password' => 'password123', 'role' => 'Mentor'],
    ['email' => 'aspirant1@example.com', 'password' => 'password123', 'role' => 'Aspirant']
];

foreach ($testCredentials as $cred) {
    echo "<h3>Test: " . $cred['email'] . " (" . $cred['role'] . ")</h3>";
    
    try {
        // Check if user exists
        $user = $userModel->findByEmail($cred['email']);
        if (!$user) {
            echo "❌ Utilisateur non trouvé dans la base de données<br>";
            continue;
        }
        
        echo "✅ Utilisateur trouvé: " . $user['first_name'] . " " . $user['last_name'] . "<br>";
        echo "📧 Email: " . $user['email'] . "<br>";
        echo "👤 Rôle: " . $user['role'] . "<br>";
        echo "📊 Statut: " . $user['status'] . "<br>";
        
        // Test password verification
        if (password_verify($cred['password'], $user['password'])) {
            echo "✅ Mot de passe correct<br>";
            
            // Test login function
            if (Auth::login($cred['email'], $cred['password'])) {
                echo "✅ <strong>Connexion réussie !</strong><br>";
                echo "<a href='dashboard.php' style='background: #28a745; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;'>Aller au Dashboard</a><br>";
                Auth::logout(); // Logout for next test
            } else {
                echo "❌ Échec de la fonction de connexion<br>";
            }
        } else {
            echo "❌ Mot de passe incorrect<br>";
            echo "🔍 Hash stocké: " . substr($user['password'], 0, 20) . "...<br>";
        }
        
    } catch (Exception $e) {
        echo "❌ Erreur lors du test: " . $e->getMessage() . "<br>";
    }
    
    echo "<hr>";
}

// Test 4: Manual login form
echo "<h2>4. Formulaire de Test de Connexion</h2>";

$loginError = '';
$loginSuccess = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_login'])) {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    echo "<h3>🔍 Tentative de connexion avec:</h3>";
    echo "📧 Email: " . htmlspecialchars($email) . "<br>";
    echo "🔑 Mot de passe: " . str_repeat('*', strlen($password)) . "<br>";
    
    if (empty($email) || empty($password)) {
        $loginError = 'Veuillez saisir l\'email et le mot de passe.';
    } else {
        try {
            // Check user exists
            $user = $userModel->findByEmail($email);
            if (!$user) {
                $loginError = 'Aucun utilisateur trouvé avec cet email.';
                echo "❌ Utilisateur non trouvé<br>";
            } else {
                echo "✅ Utilisateur trouvé: " . $user['first_name'] . " " . $user['last_name'] . "<br>";
                
                // Check password
                if (password_verify($password, $user['password'])) {
                    echo "✅ Mot de passe correct<br>";
                    
                    // Check status
                    if ($user['status'] !== 'active') {
                        $loginError = 'Votre compte n\'est pas actif.';
                        echo "❌ Compte non actif (statut: " . $user['status'] . ")<br>";
                    } else {
                        // Try login
                        if (Auth::login($email, $password)) {
                            $loginSuccess = 'Connexion réussie ! Redirection vers le dashboard...';
                            echo "✅ <strong>CONNEXION RÉUSSIE !</strong><br>";
                            echo "<script>setTimeout(function(){ window.location.href = 'dashboard.php'; }, 3000);</script>";
                        } else {
                            $loginError = 'Erreur lors de la connexion.';
                            echo "❌ Échec de la fonction Auth::login()<br>";
                        }
                    }
                } else {
                    $loginError = 'Mot de passe incorrect.';
                    echo "❌ Mot de passe incorrect<br>";
                }
            }
        } catch (Exception $e) {
            $loginError = 'Erreur système: ' . $e->getMessage();
            echo "❌ Erreur: " . $e->getMessage() . "<br>";
        }
    }
}

if ($loginError) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<strong>❌ Erreur:</strong> " . htmlspecialchars($loginError);
    echo "</div>";
}

if ($loginSuccess) {
    echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<strong>✅ Succès:</strong> " . htmlspecialchars($loginSuccess);
    echo "</div>";
}

?>

<form method="POST" style="background: #f8f9fa; padding: 20px; border-radius: 8px; max-width: 500px; margin: 20px 0;">
    <input type="hidden" name="test_login" value="1">
    
    <div style="margin-bottom: 15px;">
        <label style="display: block; margin-bottom: 5px; font-weight: bold;">Email:</label>
        <input type="email" name="email" value="<?php echo htmlspecialchars($_POST['email'] ?? 'admin@star-church.org'); ?>" 
               style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;" required>
    </div>
    
    <div style="margin-bottom: 15px;">
        <label style="display: block; margin-bottom: 5px; font-weight: bold;">Mot de passe:</label>
        <input type="password" name="password" value="password123" 
               style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;" required>
    </div>
    
    <button type="submit" style="background: #007bff; color: white; padding: 12px 24px; border: none; border-radius: 4px; cursor: pointer; font-size: 16px;">
        🔐 Tester la Connexion
    </button>
</form>

<?php
// Test 5: Session information
echo "<h2>5. Informations de Session</h2>";
echo "<div style='background: #e9ecef; padding: 15px; border-radius: 5px; font-family: monospace;'>";
echo "Session ID: " . session_id() . "<br>";
echo "Session Status: " . (session_status() === PHP_SESSION_ACTIVE ? 'Active' : 'Inactive') . "<br>";
echo "Session Data:<br>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";
echo "</div>";

// Test 6: Quick fixes
echo "<h2>6. Solutions Rapides</h2>";
echo "<div style='background: #fff3cd; padding: 15px; border-radius: 5px; border-left: 4px solid #ffc107;'>";
echo "<h3>🛠️ Si vous ne pouvez toujours pas vous connecter:</h3>";
echo "<ol>";
echo "<li><strong>Vérifiez vos identifiants:</strong> Utilisez exactement <code>admin@star-church.org</code> et <code>password123</code></li>";
echo "<li><strong>Réinitialisez la base de données:</strong> <a href='setup.php'>Cliquez ici pour reconfigurer le système</a></li>";
echo "<li><strong>Videz le cache:</strong> Actualisez la page avec Ctrl+F5 (ou Cmd+R sur Mac)</li>";
echo "<li><strong>Vérifiez les cookies:</strong> Supprimez les cookies du site dans votre navigateur</li>";
echo "<li><strong>Essayez un autre navigateur:</strong> Testez avec Chrome, Firefox ou Safari</li>";
echo "</ol>";
echo "</div>";

echo "<hr>";
echo "<p style='text-align: center;'>";
echo "<a href='index.php' style='background: #6c757d; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; margin: 5px;'>← Accueil</a>";
echo "<a href='login.php' style='background: #007bff; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; margin: 5px;'>Page de Connexion</a>";
echo "<a href='setup.php' style='background: #28a745; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; margin: 5px;'>Configuration</a>";
echo "</p>";
?>
