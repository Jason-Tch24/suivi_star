# 🔐 Guide de Résolution des Problèmes de Connexion - STAR System

## 🚨 **Problèmes de Connexion les Plus Courants**

### **1. Base de Données Non Configurée**
**Symptôme :** Message d'erreur ou page blanche
**Solution :** 
- Allez sur `http://localhost:8888/suivie_star/setup.php`
- Suivez les instructions de configuration
- Créez les utilisateurs de démonstration

### **2. Identifiants Incorrects**
**Symptôme :** Message "Email ou mot de passe invalide"
**Solution :** Utilisez exactement ces identifiants :

#### **👑 Administrateur**
- **Email :** `admin@star-church.org`
- **Mot de passe :** `password123`

#### **⛪ Pasteur**
- **Email :** `pastor@star-church.org`
- **Mot de passe :** `password123`

#### **👥 MDS (Ministry of STAR)**
- **Email :** `mds@star-church.org`
- **Mot de passe :** `password123`

#### **🤝 Mentor**
- **Email :** `mentor1@star-church.org`
- **Mot de passe :** `password123`

#### **🌟 Aspirant**
- **Email :** `aspirant1@example.com`
- **Mot de passe :** `password123`

### **3. MAMP Non Démarré**
**Symptôme :** Erreur de connexion à la base de données
**Solution :**
1. Ouvrez MAMP
2. Cliquez sur "Start Servers"
3. Vérifiez que Apache et MySQL sont verts
4. Réessayez la connexion

### **4. Cache du Navigateur**
**Symptôme :** Comportement étrange ou anciennes données
**Solution :**
- **Chrome/Edge :** Ctrl+Shift+R (Windows) ou Cmd+Shift+R (Mac)
- **Firefox :** Ctrl+F5 (Windows) ou Cmd+Shift+R (Mac)
- **Safari :** Cmd+Option+R

## 🔍 **Pages de Diagnostic**

### **Vérification Rapide**
1. **Vérifier les utilisateurs :** `http://localhost:8888/suivie_star/check-users.php`
2. **Debug de connexion :** `http://localhost:8888/suivie_star/debug-login.php`
3. **Configuration système :** `http://localhost:8888/suivie_star/setup.php`

## 🛠️ **Solutions Étape par Étape**

### **Étape 1 : Vérifier MAMP**
```
1. Ouvrir MAMP
2. Cliquer "Start Servers"
3. Vérifier que les ports sont :
   - Apache : 8888
   - MySQL : 8889
4. Aller sur http://localhost:8888/suivie_star/
```

### **Étape 2 : Vérifier la Base de Données**
```
1. Aller sur http://localhost:8888/suivie_star/check-users.php
2. Si "Table users n'existe pas" → Aller à l'étape 3
3. Si "Aucun utilisateur" → Aller à l'étape 3
4. Si utilisateurs présents → Aller à l'étape 4
```

### **Étape 3 : Configurer le Système**
```
1. Aller sur http://localhost:8888/suivie_star/setup.php
2. Cliquer "Initialize Database"
3. Cliquer "Create Demo Users"
4. Attendre la confirmation
5. Aller à l'étape 4
```

### **Étape 4 : Tester la Connexion**
```
1. Aller sur http://localhost:8888/suivie_star/login.php
2. Utiliser : admin@star-church.org / password123
3. Cliquer "Login"
4. Vous devriez être redirigé vers le dashboard
```

## ⚡ **Solutions Rapides**

### **Problème : "Invalid email or password"**
✅ **Solution :** Vérifiez que vous utilisez exactement `admin@star-church.org` et `password123`

### **Problème : Page blanche ou erreur 500**
✅ **Solution :** 
1. Vérifiez que MAMP est démarré
2. Allez sur `setup.php` pour configurer la base de données

### **Problème : "Database connection failed"**
✅ **Solution :**
1. Redémarrez MAMP
2. Vérifiez les ports (Apache: 8888, MySQL: 8889)
3. Vérifiez le fichier `.env`

### **Problème : Redirection infinie**
✅ **Solution :**
1. Supprimez les cookies du site
2. Videz le cache du navigateur
3. Essayez en navigation privée

### **Problème : "User not found"**
✅ **Solution :**
1. Allez sur `check-users.php` pour voir les utilisateurs
2. Si aucun utilisateur, allez sur `setup.php`
3. Créez les utilisateurs de démonstration

## 🔧 **Réinitialisation Complète**

Si rien ne fonctionne, suivez ces étapes :

### **1. Réinitialiser la Base de Données**
```
1. Aller sur http://localhost:8888/suivie_star/setup.php
2. Cliquer "Reset Database" (si disponible)
3. Cliquer "Initialize Database"
4. Cliquer "Create Demo Users"
```

### **2. Vider le Cache Navigateur**
```
1. Ouvrir les outils développeur (F12)
2. Clic droit sur le bouton actualiser
3. Choisir "Vider le cache et actualiser"
```

### **3. Redémarrer MAMP**
```
1. Arrêter les serveurs MAMP
2. Attendre 10 secondes
3. Redémarrer les serveurs
4. Réessayer la connexion
```

## 📞 **Support Supplémentaire**

### **Vérifications Avancées**
1. **Logs d'erreur PHP :** `/Applications/MAMP/logs/php_error.log`
2. **Logs Apache :** `/Applications/MAMP/logs/apache_error.log`
3. **Console navigateur :** F12 → Console (pour erreurs JavaScript)

### **Informations Système**
- **URL de base :** `http://localhost:8888/suivie_star/`
- **Port Apache :** 8888
- **Port MySQL :** 8889
- **Base de données :** `star_volunteer_system`

## ✅ **Test Final**

Après avoir suivi ces étapes :

1. **Aller sur :** `http://localhost:8888/suivie_star/login.php`
2. **Saisir :**
   - Email : `admin@star-church.org`
   - Mot de passe : `password123`
3. **Cliquer :** "Login"
4. **Résultat attendu :** Redirection vers le dashboard administrateur

## 🎯 **Identifiants de Test Garantis**

Ces identifiants fonctionnent après configuration :

| Rôle | Email | Mot de passe | Dashboard |
|------|-------|--------------|-----------|
| **Admin** | admin@star-church.org | password123 | Dashboard Admin |
| **Pastor** | pastor@star-church.org | password123 | Dashboard Pastor |
| **MDS** | mds@star-church.org | password123 | Dashboard MDS |
| **Mentor** | mentor1@star-church.org | password123 | Dashboard Mentor |
| **Aspirant** | aspirant1@example.com | password123 | Dashboard Aspirant |

---

**🚀 Si vous suivez ce guide, vous devriez pouvoir vous connecter sans problème !**

En cas de problème persistant, utilisez les pages de diagnostic :
- `check-users.php` - Vérifier les utilisateurs
- `debug-login.php` - Diagnostic complet de connexion
- `setup.php` - Configuration du système
