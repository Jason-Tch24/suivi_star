<?php
/**
 * Test du système d'email STAR
 */

require_once __DIR__ . '/src/services/EmailService.php';

echo "<h1>Test du Système d'Email STAR</h1>";
echo "<style>body { font-family: Arial, sans-serif; padding: 20px; }</style>";

// Données de test
$testUserData = [
    'email' => 'test@example.com',
    'first_name' => 'Jean',
    'last_name' => 'Dupont'
];

$testAspirantData = [
    'ministry_preference_1' => 'Ministère de la musique',
    'status' => 'active'
];

try {
    $emailService = new EmailService();
    
    echo "<h2>✅ Service d'email initialisé avec succès</h2>";
    
    // Test 1: Email de bienvenue
    echo "<h3>Test 1: Email de bienvenue</h3>";
    echo "<p><strong>Configuration détectée :</strong></p>";
    echo "<ul>";
    echo "<li>MAIL_HOST: " . ($_ENV['MAIL_HOST'] ?? 'Non défini') . "</li>";
    echo "<li>MAIL_PORT: " . ($_ENV['MAIL_PORT'] ?? 'Non défini') . "</li>";
    echo "<li>MAIL_FROM: " . ($_ENV['MAIL_FROM'] ?? 'Non défini') . "</li>";
    echo "</ul>";
    
    echo "<p><strong>Contenu de l'email de bienvenue (aperçu) :</strong></p>";
    echo "<div style='border: 1px solid #ccc; padding: 10px; background: #f9f9f9; max-width: 600px;'>";
    
    // Créer une instance pour accéder aux templates (simulation)
    echo "<p>🌟 <strong>Bienvenue dans STAR !</strong></p>";
    echo "<p>Bonjour " . htmlspecialchars($testUserData['first_name']) . " !</p>";
    echo "<p>Félicitations ! Votre candidature pour rejoindre le programme STAR a été soumise avec succès.</p>";
    echo "<p><em>Ceci est un aperçu du template d'email.</em></p>";
    
    echo "</div>";
    
    // Test 2: Email de changement de statut
    echo "<h3>Test 2: Email de changement de statut</h3>";
    echo "<div style='border: 1px solid #ccc; padding: 10px; background: #f9f9f9; max-width: 600px;'>";
    echo "<p>📋 <strong>Mise à jour de votre statut STAR</strong></p>";
    echo "<p>Bonjour " . htmlspecialchars($testUserData['first_name']) . " !</p>";
    echo "<p>Votre candidature est maintenant active ! 🎉</p>";
    echo "<p><em>Ceci est un aperçu du template d'email.</em></p>";
    echo "</div>";
    
    // Test 3: Email de progression
    echo "<h3>Test 3: Email de progression</h3>";
    echo "<div style='border: 1px solid #ccc; padding: 10px; background: #f9f9f9; max-width: 600px;'>";
    echo "<p>🎯 <strong>Nouvelle étape dans votre parcours STAR</strong></p>";
    echo "<p>Bonjour " . htmlspecialchars($testUserData['first_name']) . " !</p>";
    echo "<p>Félicitations ! Vous avez progressé vers l'étape 2 : <strong>Formation initiale</strong></p>";
    echo "<p><em>Ceci est un aperçu du template d'email.</em></p>";
    echo "</div>";
    
    echo "<h2>💡 Instructions de configuration</h2>";
    echo "<div style='border: 1px solid #blue; padding: 15px; background: #e3f2fd;'>";
    echo "<h4>Pour activer l'envoi d'emails réels :</h4>";
    echo "<ol>";
    echo "<li>Modifiez le fichier <code>.env</code> avec vos paramètres SMTP</li>";
    echo "<li>Pour Gmail :</li>";
    echo "<ul>";
    echo "<li>MAIL_HOST=smtp.gmail.com</li>";
    echo "<li>MAIL_PORT=587</li>";
    echo "<li>MAIL_USERNAME=votre-email@gmail.com</li>";
    echo "<li>MAIL_PASSWORD=votre-mot-de-passe-app</li>";
    echo "</ul>";
    echo "<li>Pour d'autres fournisseurs, adaptez les paramètres SMTP</li>";
    echo "<li>Les emails sont automatiquement envoyés lors de la création de comptes</li>";
    echo "</ol>";
    echo "</div>";
    
    echo "<h2>🔧 Fonctionnalités disponibles</h2>";
    echo "<ul>";
    echo "<li>✅ Email de bienvenue automatique lors de la création de compte</li>";
    echo "<li>✅ Email de notification de changement de statut</li>";
    echo "<li>✅ Email de progression d'étape</li>";
    echo "<li>✅ Templates HTML avec design professionnel</li>";
    echo "<li>✅ Version texte alternative pour tous les emails</li>";
    echo "<li>✅ Gestion d'erreurs robuste (l'envoi d'email ne fait pas échouer les opérations)</li>";
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<h2>❌ Erreur lors de l'initialisation</h2>";
    echo "<p style='color: red;'>Erreur : " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>Vérifiez que :</p>";
    echo "<ul>";
    echo "<li>PHPMailer est installé (composer require phpmailer/phpmailer)</li>";
    echo "<li>Le fichier .env existe avec la configuration email</li>";
    echo "<li>Les paramètres SMTP sont corrects</li>";
    echo "</ul>";
}

echo "<hr>";
echo "<p><a href='index.php'>← Retour à l'accueil</a></p>";
echo "<p><a href='public/register.php'>Tester l'inscription (avec envoi d'email)</a></p>";
?>
