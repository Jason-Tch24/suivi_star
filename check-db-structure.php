<?php
/**
 * Check Database Structure - STAR System
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

echo "<h1>🔍 Vérification de la Structure de la Base de Données</h1>";

try {
    $db = Database::getInstance();
    
    // Check users table structure
    echo "<h2>📋 Structure de la Table 'users'</h2>";
    $stmt = $db->query("DESCRIBE users");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<table style='width: 100%; border-collapse: collapse; margin: 20px 0;'>";
    echo "<tr style='background: #007bff; color: white;'>";
    echo "<th style='padding: 12px; border: 1px solid #ddd;'>Champ</th>";
    echo "<th style='padding: 12px; border: 1px solid #ddd;'>Type</th>";
    echo "<th style='padding: 12px; border: 1px solid #ddd;'>Null</th>";
    echo "<th style='padding: 12px; border: 1px solid #ddd;'>Clé</th>";
    echo "<th style='padding: 12px; border: 1px solid #ddd;'>Défaut</th>";
    echo "</tr>";
    
    $hasPasswordHash = false;
    $hasPassword = false;
    
    foreach ($columns as $column) {
        echo "<tr style='background: #f8f9fa;'>";
        echo "<td style='padding: 10px; border: 1px solid #ddd;'><strong>" . htmlspecialchars($column['Field']) . "</strong></td>";
        echo "<td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($column['Type']) . "</td>";
        echo "<td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($column['Null']) . "</td>";
        echo "<td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($column['Key']) . "</td>";
        echo "<td style='padding: 10px; border: 1px solid #ddd;'>" . htmlspecialchars($column['Default'] ?? 'NULL') . "</td>";
        echo "</tr>";
        
        if ($column['Field'] === 'password_hash') {
            $hasPasswordHash = true;
        }
        if ($column['Field'] === 'password') {
            $hasPassword = true;
        }
    }
    echo "</table>";
    
    // Check password field issue
    echo "<h2>🔐 Diagnostic du Problème de Mot de Passe</h2>";
    
    if ($hasPasswordHash && !$hasPassword) {
        echo "<div style='background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "✅ <strong>Structure correcte :</strong> Le champ 'password_hash' existe.";
        echo "</div>";
    } elseif ($hasPassword && !$hasPasswordHash) {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "❌ <strong>PROBLÈME TROUVÉ :</strong> Le champ s'appelle 'password' au lieu de 'password_hash' !";
        echo "<br>Le code Auth.php cherche 'password_hash' mais la base de données utilise 'password'.";
        echo "</div>";
    } elseif ($hasPassword && $hasPasswordHash) {
        echo "<div style='background: #fff3cd; color: #856404; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "⚠️ <strong>Attention :</strong> Les deux champs 'password' et 'password_hash' existent.";
        echo "</div>";
    } else {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "❌ <strong>ERREUR :</strong> Aucun champ de mot de passe trouvé !";
        echo "</div>";
    }
    
    // Test a specific user's password
    echo "<h2>🧪 Test du Mot de Passe Admin</h2>";
    $stmt = $db->prepare("SELECT email, password, password_hash FROM users WHERE email = 'admin@star-church.org' LIMIT 1");
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($admin) {
        echo "<div style='background: #e9ecef; padding: 15px; border-radius: 5px; font-family: monospace;'>";
        echo "<strong>Email :</strong> " . htmlspecialchars($admin['email']) . "<br>";
        
        if (isset($admin['password'])) {
            echo "<strong>Champ 'password' :</strong> " . substr($admin['password'], 0, 30) . "...<br>";
            $passwordWorks = password_verify('password123', $admin['password']);
            echo "<strong>Test password123 avec 'password' :</strong> " . ($passwordWorks ? "✅ OK" : "❌ Échec") . "<br>";
        }
        
        if (isset($admin['password_hash'])) {
            echo "<strong>Champ 'password_hash' :</strong> " . substr($admin['password_hash'], 0, 30) . "...<br>";
            $passwordHashWorks = password_verify('password123', $admin['password_hash']);
            echo "<strong>Test password123 avec 'password_hash' :</strong> " . ($passwordHashWorks ? "✅ OK" : "❌ Échec") . "<br>";
        }
        echo "</div>";
        
        // Provide solution
        if (isset($admin['password']) && password_verify('password123', $admin['password'])) {
            echo "<div style='background: #d1ecf1; color: #0c5460; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
            echo "<h3>💡 Solution Trouvée !</h3>";
            echo "<p>Le mot de passe fonctionne avec le champ 'password', mais le code cherche 'password_hash'.</p>";
            echo "<p><strong>Correction nécessaire :</strong> Modifier le fichier <code>src/models/User.php</code> ligne 51</p>";
            echo "<p><strong>Changer :</strong> <code>\$user['password_hash']</code></p>";
            echo "<p><strong>En :</strong> <code>\$user['password']</code></p>";
            echo "</div>";
        }
    } else {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "❌ Utilisateur admin non trouvé !";
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; color: #721c24; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<h2>❌ Erreur</h2>";
    echo "<p>Erreur : " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "</div>";
}

echo "<hr>";
echo "<div style='text-align: center; margin: 30px 0;'>";
echo "<a href='index.php' style='background: #6c757d; color: white; padding: 12px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>← Accueil</a>";
echo "<a href='check-users.php' style='background: #007bff; color: white; padding: 12px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>👥 Vérifier Utilisateurs</a>";
echo "<a href='debug-login.php' style='background: #ffc107; color: #212529; padding: 12px 20px; text-decoration: none; border-radius: 5px; margin: 5px;'>🔍 Debug Connexion</a>";
echo "</div>";
?>
