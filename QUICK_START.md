# STAR System - Guide de Démarrage Rapide

## 🚀 Accès au Système

Le système STAR fonctionne maintenant **sans mod_rewrite** pour éviter les erreurs de configuration Apache.

### 📍 **URLs Principales**

- **Page d'accueil** : `http://localhost:8888/suivie_star/index.php`
- **Connexion** : `http://localhost:8888/suivie_star/login.php`
- **Inscription** : `http://localhost:8888/suivie_star/register.php`
- **Tableau de bord** : `http://localhost:8888/suivie_star/dashboard.php`
- **Configuration** : `http://localhost:8888/suivie_star/setup.php`

### 🔑 **Comptes de Démonstration**

| Rôle | Email | Mot de passe |
|------|-------|--------------|
| **Administrateur** | admin@star-church.org | password123 |
| **Pasteur** | pastor@star-church.org | password123 |
| **MDS** | mds@star-church.org | password123 |
| **Mentor** | mentor1@star-church.org | password123 |
| **Aspirant** | aspirant1@example.com | password123 |

## ✅ **Étapes de Configuration**

### 1. Vérifier la Base de Données
- La base de données `star_volunteer_system` doit être créée
- Si elle n'existe pas, visitez : `http://localhost:8888/suivie_star/setup.php`
- Cliquez sur "Setup Database"

### 2. Tester la Connexion
1. Allez sur : `http://localhost:8888/suivie_star/login.php`
2. Utilisez un compte de démonstration
3. Vous serez redirigé vers le tableau de bord approprié

### 3. Explorer les Fonctionnalités
- **Aspirant** : Voir le parcours personnel, suivre les progrès
- **Administrateur** : Gérer les utilisateurs, valider les étapes
- **Pasteur** : Voir les analyses et métriques du programme
- **Mentor** : Gérer les aspirants assignés
- **MDS** : Gérer les entretiens et validations

## 🔧 **Résolution du Problème mod_rewrite**

Le système a été adapté pour fonctionner **sans URL rewriting** car mod_rewrite n'était pas activé dans MAMP.

### Option A : Utiliser le système actuel (Recommandé)
- Fonctionne immédiatement
- URLs avec `.php` (ex: `login.php`, `dashboard.php`)
- Aucune configuration supplémentaire requise

### Option B : Activer mod_rewrite dans MAMP
1. Ouvrir MAMP
2. Aller dans **Preferences** → **Web Server** → **Apache**
3. Modifier `/Applications/MAMP/conf/apache/httpd.conf`
4. Décommenter : `LoadModule rewrite_module modules/mod_rewrite.so`
5. Redémarrer MAMP
6. Restaurer le fichier `.htaccess` original

## 📱 **Navigation du Système**

### Pour les Aspirants
1. **S'inscrire** : `register.php` → Remplir le formulaire
2. **Se connecter** : `login.php` → Voir le tableau de bord
3. **Suivre les progrès** : Timeline visuelle des 6 étapes

### Pour les Administrateurs
1. **Se connecter** : `login.php` avec compte admin
2. **Gérer les aspirants** : Voir tous les candidats
3. **Valider les étapes** : Approuver/rejeter les progressions
4. **Gérer les utilisateurs** : Ajouter mentors, MDS, etc.

### Pour les Pasteurs
1. **Se connecter** : `login.php` avec compte pasteur
2. **Voir les analyses** : Graphiques et métriques
3. **Superviser le programme** : Vue d'ensemble complète

## 🎯 **Parcours STAR (6 Étapes)**

1. **Candidature** (7 jours) → Aspirant STAR
2. **Formation PCNC** (6 mois) → Formation pastorale
3. **Entretien MDS** (14 jours) → Validation ministérielle
4. **Formation Ministère** (30 jours) → Formation avec mentor
5. **Rapport Mentor** (7 jours) → Évaluation finale
6. **Confirmation** (7 jours) → Bénévole actif STAR

## 🆘 **Support**

### Problèmes Courants
- **Erreur 500** : Vérifier que mod_rewrite est désactivé ou utiliser les URLs `.php`
- **Base de données** : Exécuter `setup.php` pour créer les tables
- **Connexion** : Utiliser les comptes de démonstration fournis

### Fichiers de Test
- `debug.php` : Informations système et diagnostic
- `test.php` : Test de connexion base de données
- `simple_test.php` : Test PHP basique

## 🎉 **Prêt à Utiliser !**

Le système STAR est maintenant opérationnel et prêt pour la gestion des bénévoles de votre église. Commencez par vous connecter avec un compte de démonstration et explorez les différentes fonctionnalités !
