<?php
/**
 * Test des Dashboards - STAR System
 */

session_start();

echo "<h1>📊 Test des Dashboards par Rôle</h1>";

// Test 1: Check dashboard files existence
echo "<h2>1. 📁 Vérification des Fichiers de Dashboard</h2>";

$dashboardFiles = [
    'administrator' => 'src/views/dashboard/admin.php',
    'pastor' => 'src/views/dashboard/pastor.php',
    'mds' => 'src/views/dashboard/mds.php',
    'mentor' => 'src/views/dashboard/mentor.php',
    'aspirant' => 'src/views/dashboard/aspirant.php'
];

foreach ($dashboardFiles as $role => $file) {
    $exists = file_exists(__DIR__ . '/' . $file);
    $status = $exists ? "✅" : "❌";
    $color = $exists ? "#28a745" : "#dc3545";
    
    echo "<div style='background: #f8f9fa; padding: 10px; margin: 5px 0; border-left: 4px solid $color; border-radius: 3px;'>";
    echo "$status <strong>$role</strong> → $file";
    if (!$exists) {
        echo " <span style='color: #dc3545;'>(MANQUANT)</span>";
    }
    echo "</div>";
}

// Test 2: Test dashboard access for each role
echo "<h2>2. 🔐 Test d'Accès aux Dashboards</h2>";

require_once __DIR__ . '/src/middleware/Auth.php';
require_once __DIR__ . '/src/models/Database.php';

try {
    $db = Database::getInstance();
    
    // Get all users
    $stmt = $db->query("SELECT id, email, first_name, last_name, role, status FROM users WHERE status = 'active' ORDER BY role");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($users)) {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "❌ Aucun utilisateur actif trouvé !";
        echo "</div>";
        exit;
    }
    
    // Test dashboard access for each user
    foreach ($users as $user) {
        echo "<h3>👤 Test pour : " . htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) . " (" . $user['role'] . ")</h3>";
        
        // Simulate login
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
        
        echo "<div style='background: #e9ecef; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "<strong>📧 Email:</strong> " . htmlspecialchars($user['email']) . "<br>";
        echo "<strong>👤 Rôle:</strong> " . htmlspecialchars($user['role']) . "<br>";
        echo "<strong>📊 Dashboard:</strong> ";
        
        // Test dashboard file
        $dashboardFile = $dashboardFiles[$user['role']] ?? null;
        if ($dashboardFile && file_exists(__DIR__ . '/' . $dashboardFile)) {
            echo "✅ Fichier existe<br>";
            
            // Test if dashboard loads without errors
            ob_start();
            $error = null;
            try {
                // Capture any errors
                set_error_handler(function($severity, $message, $file, $line) use (&$error) {
                    $error = "Erreur PHP: $message dans $file ligne $line";
                });
                
                include __DIR__ . '/' . $dashboardFile;
                
                restore_error_handler();
                $output = ob_get_contents();
                
                if ($error) {
                    echo "❌ Erreur de chargement: " . htmlspecialchars($error) . "<br>";
                } else {
                    echo "✅ Dashboard charge correctement<br>";
                }
                
            } catch (Exception $e) {
                echo "❌ Exception: " . htmlspecialchars($e->getMessage()) . "<br>";
            } finally {
                ob_end_clean();
                restore_error_handler();
            }
            
            // Direct access link
            echo "<strong>🔗 Lien direct:</strong> ";
            echo "<a href='dashboard.php' onclick='testDashboard(\"" . $user['email'] . "\")' style='background: #007bff; color: white; padding: 5px 10px; text-decoration: none; border-radius: 3px; font-size: 12px;'>Tester Dashboard</a>";
            
        } else {
            echo "❌ Fichier manquant: " . ($dashboardFile ?? 'Non défini') . "<br>";
        }
        
        echo "</div>";
        
        // Clear session for next test
        session_unset();
    }
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "❌ Erreur: " . htmlspecialchars($e->getMessage());
    echo "</div>";
}

// Test 3: Manual dashboard test
echo "<h2>3. 🧪 Test Manuel des Dashboards</h2>";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_dashboard'])) {
    $testEmail = $_POST['test_email'];
    $testRole = $_POST['test_role'];
    
    echo "<h3>🔍 Test du dashboard pour : " . htmlspecialchars($testEmail) . "</h3>";
    
    try {
        // Login user
        if (Auth::login($testEmail, 'password123')) {
            $user = Auth::user();
            
            echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
            echo "<strong>✅ Connexion réussie !</strong><br>";
            echo "Utilisateur : " . $user['first_name'] . " " . $user['last_name'] . "<br>";
            echo "Rôle : " . $user['role'] . "<br>";
            echo "<a href='dashboard.php' target='_blank' style='background: #007bff; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; margin-top: 10px; display: inline-block;'>📊 Ouvrir Dashboard</a>";
            echo "</div>";
            
            // Test dashboard loading
            echo "<h4>📊 Test de Chargement du Dashboard</h4>";
            $dashboardFile = $dashboardFiles[$user['role']] ?? null;
            
            if ($dashboardFile && file_exists(__DIR__ . '/' . $dashboardFile)) {
                ob_start();
                try {
                    include __DIR__ . '/' . $dashboardFile;
                    $output = ob_get_contents();
                    ob_end_clean();
                    
                    echo "<div style='background: #d4edda; color: #155724; padding: 10px; border-radius: 3px; margin: 5px 0;'>";
                    echo "✅ Dashboard chargé avec succès (" . strlen($output) . " caractères)";
                    echo "</div>";
                    
                } catch (Exception $e) {
                    ob_end_clean();
                    echo "<div style='background: #f8d7da; color: #721c24; padding: 10px; border-radius: 3px; margin: 5px 0;'>";
                    echo "❌ Erreur de chargement: " . htmlspecialchars($e->getMessage());
                    echo "</div>";
                }
            } else {
                echo "<div style='background: #f8d7da; color: #721c24; padding: 10px; border-radius: 3px; margin: 5px 0;'>";
                echo "❌ Fichier dashboard manquant: " . ($dashboardFile ?? 'Non défini');
                echo "</div>";
            }
            
        } else {
            echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
            echo "❌ Échec de la connexion pour " . htmlspecialchars($testEmail);
            echo "</div>";
        }
        
    } catch (Exception $e) {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "❌ Erreur: " . htmlspecialchars($e->getMessage());
        echo "</div>";
    }
}

// Test forms for each role
echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 15px; margin: 20px 0;'>";

$testUsers = [
    ['email' => 'admin@star-church.org', 'role' => 'administrator', 'name' => 'Administrator', 'color' => '#dc3545'],
    ['email' => 'pastor@star-church.org', 'role' => 'pastor', 'name' => 'Pastor', 'color' => '#28a745'],
    ['email' => 'mds@star-church.org', 'role' => 'mds', 'name' => 'MDS', 'color' => '#007bff'],
    ['email' => 'mentor1@star-church.org', 'role' => 'mentor', 'name' => 'Mentor', 'color' => '#ffc107'],
    ['email' => 'aspirant1@example.com', 'role' => 'aspirant', 'name' => 'Aspirant', 'color' => '#6f42c1']
];

foreach ($testUsers as $testUser) {
    echo "<form method='POST' style='background: white; padding: 15px; border-radius: 8px; border: 2px solid " . $testUser['color'] . ";'>";
    echo "<input type='hidden' name='test_dashboard' value='1'>";
    echo "<input type='hidden' name='test_email' value='" . $testUser['email'] . "'>";
    echo "<input type='hidden' name='test_role' value='" . $testUser['role'] . "'>";
    echo "<h4 style='color: " . $testUser['color'] . "; margin-top: 0;'>📊 " . $testUser['name'] . " Dashboard</h4>";
    echo "<p style='font-size: 14px; margin: 5px 0;'><strong>Email:</strong> " . $testUser['email'] . "</p>";
    echo "<p style='font-size: 14px; margin: 5px 0;'><strong>Rôle:</strong> " . $testUser['role'] . "</p>";
    echo "<button type='submit' style='background: " . $testUser['color'] . "; color: white; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer; width: 100%; margin-top: 10px;'>";
    echo "🔐 Tester Dashboard";
    echo "</button>";
    echo "</form>";
}

echo "</div>";

// Test 4: Quick login links
echo "<h2>4. 🚀 Liens de Connexion Rapide</h2>";
echo "<div style='background: #e2e3e5; padding: 20px; border-radius: 8px; margin: 20px 0;'>";
echo "<h3>🔗 Connexion Directe par Profil</h3>";
echo "<p>Cliquez sur un lien pour vous connecter directement avec ce profil :</p>";

foreach ($testUsers as $testUser) {
    echo "<div style='margin: 10px 0; padding: 10px; background: white; border-radius: 5px; border-left: 4px solid " . $testUser['color'] . ";'>";
    echo "<strong style='color: " . $testUser['color'] . ";'>👤 " . $testUser['name'] . "</strong><br>";
    echo "<small>Email: " . $testUser['email'] . " | Mot de passe: password123</small><br>";
    echo "<a href='login.php?auto_email=" . urlencode($testUser['email']) . "' style='background: " . $testUser['color'] . "; color: white; padding: 8px 12px; text-decoration: none; border-radius: 3px; font-size: 14px; margin-top: 5px; display: inline-block;'>🔐 Connexion " . $testUser['name'] . "</a>";
    echo "</div>";
}
echo "</div>";

// Navigation
echo "<hr>";
echo "<div style='text-align: center; margin: 30px 0;'>";
echo "<a href='index.php' style='background: #6c757d; color: white; padding: 12px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>← Accueil</a>";
echo "<a href='login.php' style='background: #007bff; color: white; padding: 12px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>🔐 Page de Connexion</a>";
echo "<a href='diagnostic-profils.php' style='background: #ffc107; color: #212529; padding: 12px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>👥 Diagnostic Profils</a>";
echo "</div>";
?>
