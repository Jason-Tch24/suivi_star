<?php
/**
 * Diagnostic des Profils - STAR System
 */

session_start();

// Load dependencies
require_once __DIR__ . '/src/models/Database.php';
require_once __DIR__ . '/src/middleware/Auth.php';

echo "<h1>👥 Diagnostic des Profils Utilisateurs</h1>";

try {
    $db = Database::getInstance();
    
    // Test 1: Check all users in database
    echo "<h2>1. 📊 Tous les Utilisateurs dans la Base de Données</h2>";
    
    $stmt = $db->query("SELECT id, email, first_name, last_name, role, status, password_hash FROM users ORDER BY role, id");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($users)) {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "❌ Aucun utilisateur trouvé ! <a href='setup.php'>Créer des utilisateurs</a>";
        echo "</div>";
        exit;
    }
    
    echo "<table style='width: 100%; border-collapse: collapse; margin: 20px 0;'>";
    echo "<tr style='background: #007bff; color: white;'>";
    echo "<th style='padding: 12px; border: 1px solid #ddd;'>ID</th>";
    echo "<th style='padding: 12px; border: 1px solid #ddd;'>Email</th>";
    echo "<th style='padding: 12px; border: 1px solid #ddd;'>Nom</th>";
    echo "<th style='padding: 12px; border: 1px solid #ddd;'>Rôle</th>";
    echo "<th style='padding: 12px; border: 1px solid #ddd;'>Statut</th>";
    echo "<th style='padding: 12px; border: 1px solid #ddd;'>Mot de Passe</th>";
    echo "<th style='padding: 12px; border: 1px solid #ddd;'>Test</th>";
    echo "</tr>";
    
    $roleColors = [
        'administrator' => '#dc3545',
        'pastor' => '#28a745',
        'mds' => '#007bff',
        'mentor' => '#ffc107',
        'aspirant' => '#6f42c1'
    ];
    
    foreach ($users as $user) {
        $roleColor = $roleColors[$user['role']] ?? '#6c757d';
        $statusColor = $user['status'] === 'active' ? '#28a745' : '#dc3545';
        
        // Test password
        $passwordWorks = password_verify('password123', $user['password_hash']);
        
        echo "<tr style='background: #f8f9fa;'>";
        echo "<td style='padding: 10px; border: 1px solid #ddd;'>" . $user['id'] . "</td>";
        echo "<td style='padding: 10px; border: 1px solid #ddd;'><strong>" . htmlspecialchars($user['email']) . "</strong></td>";
        echo "<td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) . "</td>";
        echo "<td style='padding: 10px; border: 1px solid #ddd; color: $roleColor; font-weight: bold;'>" . htmlspecialchars($user['role']) . "</td>";
        echo "<td style='padding: 10px; border: 1px solid #ddd; color: $statusColor; font-weight: bold;'>" . htmlspecialchars($user['status']) . "</td>";
        echo "<td style='padding: 10px; border: 1px solid #ddd;'>" . ($passwordWorks ? "✅ OK" : "❌ Échec") . "</td>";
        echo "<td style='padding: 10px; border: 1px solid #ddd;'>";
        echo "<button onclick='testLogin(\"" . htmlspecialchars($user['email']) . "\")' style='background: #007bff; color: white; padding: 5px 10px; border: none; border-radius: 3px; cursor: pointer; font-size: 12px;'>Test</button>";
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Test 2: Fix passwords for all users
    echo "<h2>2. 🔧 Correction des Mots de Passe</h2>";
    
    $needsFix = [];
    foreach ($users as $user) {
        if (!password_verify('password123', $user['password_hash'])) {
            $needsFix[] = $user;
        }
    }
    
    if (!empty($needsFix)) {
        echo "<div style='background: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "⚠️ " . count($needsFix) . " utilisateur(s) ont des mots de passe incorrects.";
        echo "</div>";
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fix_all_passwords'])) {
            $correctHash = password_hash('password123', PASSWORD_DEFAULT);
            
            foreach ($needsFix as $user) {
                $stmt = $db->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
                $stmt->execute([$correctHash, $user['id']]);
                
                echo "<div style='background: #d4edda; color: #155724; padding: 5px 10px; border-radius: 3px; margin: 2px 0; font-size: 14px;'>";
                echo "✅ Mot de passe corrigé pour : " . htmlspecialchars($user['email']);
                echo "</div>";
            }
            
            echo "<div style='background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
            echo "<h3>🎉 Tous les mots de passe ont été corrigés !</h3>";
            echo "<p>Tous les utilisateurs peuvent maintenant se connecter avec le mot de passe : <strong>password123</strong></p>";
            echo "</div>";
            
            // Refresh page to show updated data
            echo "<script>setTimeout(function(){ window.location.reload(); }, 2000);</script>";
        } else {
            echo "<form method='POST' style='margin: 10px 0;'>";
            echo "<input type='hidden' name='fix_all_passwords' value='1'>";
            echo "<button type='submit' style='background: #dc3545; color: white; padding: 12px 24px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px;'>";
            echo "🔧 Corriger Tous les Mots de Passe";
            echo "</button>";
            echo "</form>";
        }
    } else {
        echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "✅ Tous les mots de passe sont corrects !";
        echo "</div>";
    }
    
    // Test 3: Create missing users
    echo "<h2>3. 👤 Création des Utilisateurs Manquants</h2>";
    
    $requiredUsers = [
        ['email' => 'admin@star-church.org', 'first_name' => 'System', 'last_name' => 'Administrator', 'role' => 'administrator'],
        ['email' => 'pastor@star-church.org', 'first_name' => 'John', 'last_name' => 'Pastor', 'role' => 'pastor'],
        ['email' => 'mds@star-church.org', 'first_name' => 'Mary', 'last_name' => 'MDS', 'role' => 'mds'],
        ['email' => 'mentor1@star-church.org', 'first_name' => 'David', 'last_name' => 'Mentor', 'role' => 'mentor'],
        ['email' => 'aspirant1@example.com', 'first_name' => 'Sarah', 'last_name' => 'Aspirant', 'role' => 'aspirant']
    ];
    
    $existingEmails = array_column($users, 'email');
    $missingUsers = [];
    
    foreach ($requiredUsers as $requiredUser) {
        if (!in_array($requiredUser['email'], $existingEmails)) {
            $missingUsers[] = $requiredUser;
        }
    }
    
    if (!empty($missingUsers)) {
        echo "<div style='background: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "⚠️ " . count($missingUsers) . " utilisateur(s) manquant(s) :";
        foreach ($missingUsers as $user) {
            echo "<br>- " . $user['email'] . " (" . $user['role'] . ")";
        }
        echo "</div>";
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_missing_users'])) {
            foreach ($missingUsers as $userData) {
                $stmt = $db->prepare("INSERT INTO users (email, password_hash, first_name, last_name, role, status) VALUES (?, ?, ?, ?, ?, 'active')");
                $stmt->execute([
                    $userData['email'],
                    password_hash('password123', PASSWORD_DEFAULT),
                    $userData['first_name'],
                    $userData['last_name'],
                    $userData['role']
                ]);
                
                echo "<div style='background: #d4edda; color: #155724; padding: 5px 10px; border-radius: 3px; margin: 2px 0; font-size: 14px;'>";
                echo "✅ Utilisateur créé : " . htmlspecialchars($userData['email']) . " (" . $userData['role'] . ")";
                echo "</div>";
            }
            
            echo "<div style='background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
            echo "<h3>🎉 Tous les utilisateurs ont été créés !</h3>";
            echo "</div>";
            
            // Refresh page
            echo "<script>setTimeout(function(){ window.location.reload(); }, 2000);</script>";
        } else {
            echo "<form method='POST' style='margin: 10px 0;'>";
            echo "<input type='hidden' name='create_missing_users' value='1'>";
            echo "<button type='submit' style='background: #28a745; color: white; padding: 12px 24px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px;'>";
            echo "👤 Créer les Utilisateurs Manquants";
            echo "</button>";
            echo "</form>";
        }
    } else {
        echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "✅ Tous les utilisateurs requis existent !";
        echo "</div>";
    }
    
    // Test 4: Test login for each user
    echo "<h2>4. 🔐 Test de Connexion par Profil</h2>";
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_user_login'])) {
        $testEmail = $_POST['test_email'];
        $testPassword = 'password123';
        
        echo "<h3>🧪 Test de connexion pour : " . htmlspecialchars($testEmail) . "</h3>";
        
        try {
            if (Auth::login($testEmail, $testPassword)) {
                $user = Auth::user();
                echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
                echo "<strong>🎉 CONNEXION RÉUSSIE !</strong><br>";
                echo "Utilisateur : " . $user['first_name'] . " " . $user['last_name'] . "<br>";
                echo "Rôle : " . $user['role'] . "<br>";
                echo "Dashboard URL : " . Auth::getDashboardUrl() . "<br>";
                echo "<a href='" . Auth::getDashboardUrl() . "' style='background: #007bff; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px; margin-top: 10px; display: inline-block;'>📊 Aller au Dashboard</a>";
                echo "</div>";
                
                Auth::logout(); // Clean up for next tests
            } else {
                echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
                echo "❌ Échec de la connexion pour " . htmlspecialchars($testEmail);
                echo "</div>";
            }
        } catch (Exception $e) {
            echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
            echo "❌ Erreur : " . $e->getMessage();
            echo "</div>";
        }
    }
    
    // Quick test buttons for each role
    echo "<div style='display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; margin: 20px 0;'>";
    
    $testUsers = [
        ['email' => 'admin@star-church.org', 'name' => 'Administrator', 'color' => '#dc3545'],
        ['email' => 'pastor@star-church.org', 'name' => 'Pastor', 'color' => '#28a745'],
        ['email' => 'mds@star-church.org', 'name' => 'MDS', 'color' => '#007bff'],
        ['email' => 'mentor1@star-church.org', 'name' => 'Mentor', 'color' => '#ffc107'],
        ['email' => 'aspirant1@example.com', 'name' => 'Aspirant', 'color' => '#6f42c1']
    ];
    
    foreach ($testUsers as $testUser) {
        echo "<form method='POST' style='background: white; padding: 15px; border-radius: 8px; border: 2px solid " . $testUser['color'] . ";'>";
        echo "<input type='hidden' name='test_user_login' value='1'>";
        echo "<input type='hidden' name='test_email' value='" . $testUser['email'] . "'>";
        echo "<h4 style='color: " . $testUser['color'] . "; margin-top: 0;'>👤 " . $testUser['name'] . "</h4>";
        echo "<p style='font-size: 14px; margin: 5px 0;'><strong>Email:</strong> " . $testUser['email'] . "</p>";
        echo "<p style='font-size: 14px; margin: 5px 0;'><strong>Mot de passe:</strong> password123</p>";
        echo "<button type='submit' style='background: " . $testUser['color'] . "; color: white; padding: 10px 15px; border: none; border-radius: 5px; cursor: pointer; width: 100%; margin-top: 10px;'>";
        echo "🔐 Tester la Connexion";
        echo "</button>";
        echo "</form>";
    }
    
    echo "</div>";
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h2>❌ Erreur</h2>";
    echo "<p>Erreur : " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
}

// Navigation
echo "<hr>";
echo "<div style='text-align: center; margin: 30px 0;'>";
echo "<a href='index.php' style='background: #6c757d; color: white; padding: 12px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>← Accueil</a>";
echo "<a href='login.php' style='background: #007bff; color: white; padding: 12px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>🔐 Page de Connexion</a>";
echo "<a href='setup.php' style='background: #28a745; color: white; padding: 12px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>🔧 Configuration</a>";
echo "</div>";

?>

<script>
function testLogin(email) {
    // Create a form and submit it
    const form = document.createElement('form');
    form.method = 'POST';
    form.innerHTML = `
        <input type="hidden" name="test_user_login" value="1">
        <input type="hidden" name="test_email" value="${email}">
    `;
    document.body.appendChild(form);
    form.submit();
}
</script>
