<?php
/**
 * Check Users in Database - STAR System
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

echo "<h1>👥 Vérification des Utilisateurs</h1>";

try {
    $db = Database::getInstance();
    
    // Check if users table exists
    $stmt = $db->query("SHOW TABLES LIKE 'users'");
    if ($stmt->rowCount() == 0) {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "<h2>❌ PROBLÈME MAJEUR: Table 'users' n'existe pas !</h2>";
        echo "<p>La base de données n'est pas configurée correctement.</p>";
        echo "<p><a href='setup.php' style='background: #dc3545; color: white; padding: 10px 15px; text-decoration: none; border-radius: 5px;'>🔧 Configurer la Base de Données</a></p>";
        echo "</div>";
        exit;
    }
    
    // Get all users
    $stmt = $db->query("SELECT * FROM users ORDER BY id");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($users)) {
        echo "<div style='background: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "<h2>⚠️ PROBLÈME: Aucun utilisateur dans la base de données</h2>";
        echo "<p>Vous devez créer des utilisateurs pour pouvoir vous connecter.</p>";
        echo "<p><a href='setup.php' style='background: #ffc107; color: #212529; padding: 10px 15px; text-decoration: none; border-radius: 5px;'>🔧 Créer des Utilisateurs</a></p>";
        echo "</div>";
    } else {
        echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "<h2>✅ " . count($users) . " utilisateur(s) trouvé(s)</h2>";
        echo "</div>";
        
        echo "<table style='width: 100%; border-collapse: collapse; margin: 20px 0;'>";
        echo "<tr style='background: #007bff; color: white;'>";
        echo "<th style='padding: 12px; border: 1px solid #ddd;'>ID</th>";
        echo "<th style='padding: 12px; border: 1px solid #ddd;'>Email</th>";
        echo "<th style='padding: 12px; border: 1px solid #ddd;'>Nom</th>";
        echo "<th style='padding: 12px; border: 1px solid #ddd;'>Rôle</th>";
        echo "<th style='padding: 12px; border: 1px solid #ddd;'>Statut</th>";
        echo "<th style='padding: 12px; border: 1px solid #ddd;'>Créé le</th>";
        echo "</tr>";
        
        foreach ($users as $user) {
            $statusColor = $user['status'] === 'active' ? '#28a745' : '#dc3545';
            $bgColor = $user['status'] === 'active' ? '#f8f9fa' : '#fff5f5';
            
            echo "<tr style='background: $bgColor;'>";
            echo "<td style='padding: 10px; border: 1px solid #ddd;'>" . $user['id'] . "</td>";
            echo "<td style='padding: 10px; border: 1px solid #ddd;'><strong>" . htmlspecialchars($user['email']) . "</strong></td>";
            echo "<td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) . "</td>";
            echo "<td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($user['role']) . "</td>";
            echo "<td style='padding: 10px; border: 1px solid #ddd; color: $statusColor; font-weight: bold;'>" . htmlspecialchars($user['status']) . "</td>";
            echo "<td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($user['created_at']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // Show login credentials
        echo "<div style='background: #e2e3e5; padding: 20px; border-radius: 5px; margin: 20px 0;'>";
        echo "<h2>🔑 Identifiants de Connexion Disponibles</h2>";
        echo "<p>Utilisez ces identifiants pour vous connecter :</p>";
        
        foreach ($users as $user) {
            if ($user['status'] === 'active') {
                echo "<div style='background: white; padding: 15px; margin: 10px 0; border-radius: 5px; border-left: 4px solid #28a745;'>";
                echo "<strong>👤 " . htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) . " (" . htmlspecialchars($user['role']) . ")</strong><br>";
                echo "📧 Email: <code>" . htmlspecialchars($user['email']) . "</code><br>";
                echo "🔑 Mot de passe: <code>password123</code><br>";
                echo "<a href='login.php' style='background: #007bff; color: white; padding: 8px 12px; text-decoration: none; border-radius: 3px; font-size: 14px; margin-top: 5px; display: inline-block;'>Se connecter avec ce compte</a>";
                echo "</div>";
            }
        }
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h2>❌ Erreur de Base de Données</h2>";
    echo "<p>Erreur: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>Vérifiez que MAMP est démarré et que la base de données existe.</p>";
    echo "</div>";
}

// Test password hashing
echo "<h2>🔐 Test de Hachage des Mots de Passe</h2>";
$testPassword = 'password123';
$hash = password_hash($testPassword, PASSWORD_DEFAULT);
echo "<p><strong>Mot de passe test:</strong> <code>$testPassword</code></p>";
echo "<p><strong>Hash généré:</strong> <code>" . substr($hash, 0, 50) . "...</code></p>";
echo "<p><strong>Vérification:</strong> " . (password_verify($testPassword, $hash) ? "✅ OK" : "❌ Échec") . "</p>";

echo "<hr>";
echo "<div style='text-align: center; margin: 30px 0;'>";
echo "<a href='index.php' style='background: #6c757d; color: white; padding: 12px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>← Accueil</a>";
echo "<a href='login.php' style='background: #007bff; color: white; padding: 12px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>🔐 Page de Connexion</a>";
echo "<a href='setup.php' style='background: #28a745; color: white; padding: 12px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>🔧 Configuration</a>";
echo "<a href='debug-login.php' style='background: #ffc107; color: #212529; padding: 12px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>🔍 Debug Connexion</a>";
echo "</div>";
?>
