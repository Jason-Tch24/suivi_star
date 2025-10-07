<?php
/**
 * Fix Password Hashes - STAR System
 */

// Load environment variables
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

echo "<h1>🔧 Correction des Mots de Passe</h1>";

try {
    $db = Database::getInstance();
    
    // Check current password hashes
    echo "<h2>1. Vérification des Mots de Passe Actuels</h2>";
    $stmt = $db->query("SELECT id, email, password_hash FROM users ORDER BY id");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($users)) {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "❌ Aucun utilisateur trouvé !";
        echo "</div>";
        exit;
    }
    
    echo "<table style='width: 100%; border-collapse: collapse; margin: 20px 0;'>";
    echo "<tr style='background: #007bff; color: white;'>";
    echo "<th style='padding: 12px; border: 1px solid #ddd;'>ID</th>";
    echo "<th style='padding: 12px; border: 1px solid #ddd;'>Email</th>";
    echo "<th style='padding: 12px; border: 1px solid #ddd;'>Hash Actuel</th>";
    echo "<th style='padding: 12px; border: 1px solid #ddd;'>Test password123</th>";
    echo "</tr>";
    
    $needsFix = [];
    
    foreach ($users as $user) {
        $isValidHash = password_verify('password123', $user['password_hash']);
        $hashPreview = substr($user['password_hash'], 0, 30) . '...';
        
        echo "<tr style='background: #f8f9fa;'>";
        echo "<td style='padding: 10px; border: 1px solid #ddd;'>" . $user['id'] . "</td>";
        echo "<td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($user['email']) . "</td>";
        echo "<td style='padding: 10px; border: 1px solid #ddd; font-family: monospace; font-size: 12px;'>" . htmlspecialchars($hashPreview) . "</td>";
        echo "<td style='padding: 10px; border: 1px solid #ddd;'>" . ($isValidHash ? "✅ OK" : "❌ Échec") . "</td>";
        echo "</tr>";
        
        if (!$isValidHash) {
            $needsFix[] = $user;
        }
    }
    echo "</table>";
    
    // Fix passwords if needed
    if (!empty($needsFix)) {
        echo "<h2>2. Correction des Mots de Passe</h2>";
        echo "<div style='background: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "⚠️ " . count($needsFix) . " utilisateur(s) ont des mots de passe incorrects.";
        echo "</div>";
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fix_passwords'])) {
            $correctPassword = 'password123';
            $newHash = password_hash($correctPassword, PASSWORD_DEFAULT);
            
            echo "<h3>🔧 Application des Corrections...</h3>";
            
            foreach ($needsFix as $user) {
                try {
                    $stmt = $db->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
                    $stmt->execute([$newHash, $user['id']]);
                    
                    echo "<div style='background: #d4edda; color: #155724; padding: 10px; border-radius: 3px; margin: 5px 0;'>";
                    echo "✅ Mot de passe corrigé pour : " . htmlspecialchars($user['email']);
                    echo "</div>";
                    
                } catch (Exception $e) {
                    echo "<div style='background: #f8d7da; color: #721c24; padding: 10px; border-radius: 3px; margin: 5px 0;'>";
                    echo "❌ Erreur pour " . htmlspecialchars($user['email']) . " : " . $e->getMessage();
                    echo "</div>";
                }
            }
            
            echo "<div style='background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin: 20px 0;'>";
            echo "<h3>🎉 Correction Terminée !</h3>";
            echo "<p>Tous les mots de passe ont été mis à jour vers <strong>password123</strong></p>";
            echo "<p><a href='login.php' style='background: #007bff; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;'>🔐 Tester la Connexion</a></p>";
            echo "</div>";
            
        } else {
            echo "<form method='POST' style='margin: 20px 0;'>";
            echo "<input type='hidden' name='fix_passwords' value='1'>";
            echo "<div style='background: #e2e3e5; padding: 20px; border-radius: 5px;'>";
            echo "<h3>🔧 Correction Automatique</h3>";
            echo "<p>Cette action va :</p>";
            echo "<ul>";
            echo "<li>Mettre à jour tous les mots de passe vers <strong>password123</strong></li>";
            echo "<li>Utiliser un hachage sécurisé (bcrypt)</li>";
            echo "<li>Permettre la connexion immédiate</li>";
            echo "</ul>";
            echo "<button type='submit' style='background: #28a745; color: white; padding: 12px 24px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px;'>";
            echo "🔧 Corriger les Mots de Passe";
            echo "</button>";
            echo "</div>";
            echo "</form>";
        }
        
    } else {
        echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "<h2>✅ Tous les Mots de Passe sont Corrects !</h2>";
        echo "<p>Vous pouvez maintenant vous connecter avec :</p>";
        echo "<ul>";
        echo "<li><strong>Email :</strong> admin@star-church.org</li>";
        echo "<li><strong>Mot de passe :</strong> password123</li>";
        echo "</ul>";
        echo "<p><a href='login.php' style='background: #007bff; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;'>🔐 Aller à la Connexion</a></p>";
        echo "</div>";
    }
    
    // Test login function
    echo "<h2>3. Test de la Fonction de Connexion</h2>";
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['test_login'])) {
        require_once __DIR__ . '/src/middleware/Auth.php';
        
        $testEmail = 'admin@star-church.org';
        $testPassword = 'password123';
        
        echo "<h3>🧪 Test de Connexion...</h3>";
        echo "<p><strong>Email :</strong> $testEmail</p>";
        echo "<p><strong>Mot de passe :</strong> $testPassword</p>";
        
        try {
            if (Auth::login($testEmail, $testPassword)) {
                echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
                echo "<h3>🎉 CONNEXION RÉUSSIE !</h3>";
                echo "<p>La connexion fonctionne parfaitement.</p>";
                echo "<p><a href='dashboard.php' style='background: #007bff; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;'>📊 Aller au Dashboard</a></p>";
                echo "</div>";
                
                // Logout for next tests
                Auth::logout();
            } else {
                echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
                echo "<h3>❌ Échec de la Connexion</h3>";
                echo "<p>La fonction de connexion ne fonctionne toujours pas.</p>";
                echo "</div>";
            }
        } catch (Exception $e) {
            echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
            echo "<h3>❌ Erreur</h3>";
            echo "<p>Erreur : " . htmlspecialchars($e->getMessage()) . "</p>";
            echo "</div>";
        }
    } else {
        echo "<form method='POST' style='margin: 20px 0;'>";
        echo "<input type='hidden' name='test_login' value='1'>";
        echo "<button type='submit' style='background: #17a2b8; color: white; padding: 12px 24px; border: none; border-radius: 5px; cursor: pointer; font-size: 16px;'>";
        echo "🧪 Tester la Connexion Admin";
        echo "</button>";
        echo "</form>";
    }
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h2>❌ Erreur de Base de Données</h2>";
    echo "<p>Erreur : " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
}

echo "<hr>";
echo "<div style='text-align: center; margin: 30px 0;'>";
echo "<a href='index.php' style='background: #6c757d; color: white; padding: 12px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>← Accueil</a>";
echo "<a href='login.php' style='background: #007bff; color: white; padding: 12px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>🔐 Page de Connexion</a>";
echo "<a href='check-users.php' style='background: #28a745; color: white; padding: 12px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>👥 Vérifier Utilisateurs</a>";
echo "</div>";
?>
