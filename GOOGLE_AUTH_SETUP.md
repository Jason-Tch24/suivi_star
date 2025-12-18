# Configuration de l'Authentification Google pour STAR

Ce guide vous explique comment configurer l'authentification Google pour permettre aux utilisateurs de créer des comptes et se connecter avec leur compte Google.

## Prérequis

- Composer installé sur votre système
- Accès à la Google Cloud Console
- Serveur web configuré (MAMP, XAMPP, etc.)

## Installation

### 1. Installation des dépendances

Exécutez le script d'installation automatique :

```bash
php setup-google-auth.php
```

Ou installez manuellement :

```bash
composer install
```

### 2. Configuration Google Cloud Console

1. **Accédez à la Google Cloud Console**
   - Allez sur https://console.cloud.google.com/
   - Connectez-vous avec votre compte Google

2. **Créez ou sélectionnez un projet**
   - Cliquez sur le sélecteur de projet en haut
   - Créez un nouveau projet ou sélectionnez un existant

3. **Activez l'API Google+**
   - Dans le menu de navigation, allez à "APIs & Services" > "Library"
   - Recherchez "Google+ API" et activez-la

4. **Créez les identifiants OAuth 2.0**
   - Allez à "APIs & Services" > "Credentials"
   - Cliquez sur "Create Credentials" > "OAuth 2.0 Client IDs"
   - Sélectionnez "Web application"
   - Configurez les URLs autorisées :
     - **Origines JavaScript autorisées** : `http://localhost` (ou votre domaine)
     - **URIs de redirection autorisées** : `http://localhost/suivie_star/auth/google/callback.php`

5. **Récupérez vos identifiants**
   - Copiez le "Client ID" et le "Client Secret"

### 3. Configuration de l'application

1. **Créez le fichier .env**
   ```bash
   cp .env.example .env
   ```

2. **Ajoutez vos identifiants Google dans .env**
   ```env
   GOOGLE_CLIENT_ID=votre_client_id_google
   GOOGLE_CLIENT_SECRET=votre_client_secret_google
   ```

3. **Mettez à jour la base de données**
   ```bash
   php setup-google-auth.php
   ```

## Fonctionnalités

### Pour les utilisateurs

- **Connexion rapide** : Les utilisateurs peuvent se connecter avec leur compte Google
- **Inscription simplifiée** : Création de compte automatique avec les informations Google
- **Sécurité renforcée** : Pas besoin de gérer des mots de passe

### Pour les administrateurs

- **Gestion unifiée** : Les comptes Google et locaux sont gérés dans le même système
- **Traçabilité** : Distinction entre les comptes créés localement et via Google
- **Flexibilité** : Les utilisateurs peuvent avoir les deux types d'authentification

## Structure des fichiers ajoutés

```
├── composer.json                          # Dépendances PHP
├── src/services/GoogleAuthService.php     # Service d'authentification Google
├── auth/google/login.php                  # Point d'entrée OAuth
├── auth/google/callback.php               # Gestionnaire de callback
├── database/migrations/002_add_google_auth_fields.sql  # Migration DB
└── setup-google-auth.php                  # Script d'installation
```

## Modifications apportées

### Base de données
- Ajout du champ `google_id` pour lier les comptes Google
- Ajout du champ `auth_provider` (local/google)
- Ajout du champ `google_avatar_url` pour les photos de profil
- Le champ `password_hash` est maintenant optionnel

### Interface utilisateur
- Bouton "Continuer avec Google" sur les pages de connexion et d'inscription
- Styles CSS pour les boutons Google
- Séparateurs visuels entre les méthodes d'authentification

### Sécurité
- Validation des tokens OAuth
- Gestion des erreurs d'authentification
- Protection contre les attaques CSRF

## Dépannage

### Erreur "Google OAuth is not configured"
- Vérifiez que `GOOGLE_CLIENT_ID` et `GOOGLE_CLIENT_SECRET` sont définis dans .env
- Assurez-vous que le fichier .env est chargé correctement

### Erreur de redirection
- Vérifiez que l'URL de callback est correctement configurée dans Google Cloud Console
- L'URL doit correspondre exactement à celle configurée

### Erreur "Invalid client"
- Vérifiez que le Client ID est correct
- Assurez-vous que l'API Google+ est activée

## Support

Pour toute question ou problème, consultez :
- La documentation Google OAuth 2.0
- Les logs d'erreur du serveur
- Le fichier `storage/logs/star.log`
