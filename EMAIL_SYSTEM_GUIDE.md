# Guide du Système de Messagerie STAR

## Vue d'ensemble

Le système STAR intègre maintenant un système de messagerie automatique qui envoie des emails lors d'événements importants du parcours des aspirants.

## Fonctionnalités

### 1. Email de Bienvenue
- **Déclencheur** : Création d'un nouveau compte aspirant
- **Destinataire** : L'aspirant nouvellement inscrit
- **Contenu** : Message de bienvenue avec informations sur les prochaines étapes
- **Template** : HTML avec design professionnel + version texte

### 2. Email de Changement de Statut
- **Déclencheur** : Modification du statut d'un aspirant
- **Destinataire** : L'aspirant concerné
- **Contenu** : Information sur le nouveau statut et sa signification
- **Statuts supportés** :
  - `active` : Candidature active
  - `completed` : Programme terminé
  - `inactive` : Candidature en pause
  - `pending` : En attente de validation

### 3. Email de Progression d'Étape
- **Déclencheur** : Avancement d'un aspirant à une nouvelle étape
- **Destinataire** : L'aspirant concerné
- **Contenu** : Félicitations et informations sur la nouvelle étape

## Configuration

### Fichier .env

Ajoutez ces variables dans votre fichier `.env` :

```bash
# Configuration Email
MAIL_HOST=smtp.gmail.com        # Serveur SMTP
MAIL_PORT=587                   # Port SMTP
MAIL_USERNAME=votre-email@gmail.com
MAIL_PASSWORD=votre-mot-de-passe-app
MAIL_ENCRYPTION=tls
MAIL_FROM=noreply@star-system.com
MAIL_FROM_NAME="STAR System"
```

### Configuration Gmail

1. Activez l'authentification en 2 étapes sur votre compte Gmail
2. Générez un mot de passe d'application : [Guide Google](https://support.google.com/accounts/answer/185833)
3. Utilisez ce mot de passe d'application dans `MAIL_PASSWORD`

### Autres Fournisseurs

Adaptez les paramètres SMTP selon votre fournisseur :
- **Outlook/Hotmail** : smtp.live.com, port 587
- **Yahoo** : smtp.mail.yahoo.com, port 587
- **Serveur personnalisé** : Consultez la documentation de votre hébergeur

## Utilisation dans le Code

### Service EmailService

```php
// Initialisation
require_once 'src/services/EmailService.php';
$emailService = new EmailService();

// Email de bienvenue
$emailService->sendWelcomeEmail($userData, $aspirantData);

// Email de changement de statut  
$emailService->sendStatusChangeEmail($userData, $oldStatus, $newStatus);

// Email de progression
$emailService->sendStepProgressEmail($userData, $stepNumber, $stepName);
```

### Modèle Aspirant avec Emails

```php
// Mise à jour de statut avec email automatique
$aspirantModel->updateStatusWithEmail($id, 'completed');

// Avancement d'étape avec email automatique
$aspirantModel->advanceStepWithEmail($aspirantId);
```

## Intégration Automatique

### Création de Compte
L'email de bienvenue est automatiquement envoyé lors de l'inscription via `public/register.php`.

### API Aspirants
L'API (`api/aspirants.php`) envoie automatiquement des emails lors des changements de statut.

### Gestion d'Erreurs
- Les erreurs d'email sont loggées mais n'interrompent pas les opérations
- Le système fonctionne même si la configuration email n'est pas présente

## Templates d'Email

### Structure HTML
- Design moderne et responsive
- Couleurs de la marque STAR
- Header avec logo et gradient
- Contenu structuré avec call-to-action
- Footer avec informations de contact

### Version Texte
- Alternative texte pour tous les emails
- Compatible avec tous les clients email
- Contenu identique sans formatage HTML

## Tests et Débogage

### Page de Test
Accédez à `http://localhost/suivie_star/test-email-system.php` pour :
- Vérifier la configuration
- Voir des aperçus des templates
- Tester la connexion SMTP

### Logs d'Erreur
Les erreurs d'email sont enregistrées dans les logs PHP :
```php
error_log('Email error: ' . $e->getMessage());
```

### Mode Debug
Pour tester sans envoyer de vrais emails :
1. Utilisez un serveur de test comme [MailHog](https://github.com/mailhog/MailHog)
2. Configurez `MAIL_HOST=localhost` et `MAIL_PORT=1025`

## Types d'Emails Futurs (Extensions Possibles)

1. **Rappels de Documents** - Relancer pour documents manquants
2. **Invitations d'Événements** - Sessions, réunions, formations
3. **Messages de Félicitations** - Étapes importantes franchies
4. **Affectations de Ministère** - Notification d'assignation
5. **Notifications d'Inactivité** - Relance des aspirants inactifs
6. **Messages Personnalisés** - Communications spécifiques des mentors

## Sécurité

- Les mots de passe SMTP ne sont jamais exposés dans le code
- Utilisation de mots de passe d'application pour Gmail
- Échappement HTML de toutes les données utilisateur
- Validation des adresses email avant envoi

## Support

Pour toute question ou problème avec le système d'email :
1. Vérifiez la configuration dans `.env`
2. Consultez les logs PHP pour les erreurs
3. Testez avec la page de diagnostic
4. Vérifiez les paramètres de votre fournisseur email
