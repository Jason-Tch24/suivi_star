<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test des Styles - STAR System</title>
    <link rel="stylesheet" href="public/css/style.css">
    <link rel="stylesheet" href="public/css/dashboard.css">
</head>
<body>
    <div class="container" style="padding: 2rem 0;">
        <h1 class="text-center mb-5">Test des Styles STAR</h1>
        
        <!-- Test Hero Section -->
        <section class="hero-section mb-5">
            <div class="container">
                <div class="hero-content">
                    <h1>Système STAR</h1>
                    <h2>Gestion des Bénévoles</h2>
                    <p class="hero-description">
                        Bienvenue dans le système de gestion des bénévoles STAR. 
                        Suivez votre parcours de formation et rejoignez notre communauté.
                    </p>
                    <div class="hero-actions">
                        <a href="login.php" class="btn btn-primary">Se Connecter</a>
                        <a href="register.php" class="btn btn-secondary">S'inscrire</a>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Test Statistics -->
        <section class="section-card mb-5">
            <h2>Statistiques</h2>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number">127</div>
                    <div class="stat-label">Total Aspirants</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">45</div>
                    <div class="stat-label">Actifs</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number">82</div>
                    <div class="stat-label">Complétés</div>
                </div>
            </div>
        </section>
        
        <!-- Test Buttons -->
        <section class="section-card mb-5">
            <h2>Boutons</h2>
            <div style="display: flex; gap: 1rem; flex-wrap: wrap; margin-bottom: 1rem;">
                <button class="btn btn-primary">Primaire</button>
                <button class="btn btn-secondary">Secondaire</button>
                <button class="btn btn-success">Succès</button>
                <button class="btn btn-warning">Attention</button>
                <button class="btn btn-danger">Danger</button>
            </div>
        </section>
        
        <!-- Test Alerts -->
        <section class="section-card mb-5">
            <h2>Alertes</h2>
            <div class="alert alert-success mb-3">
                <strong>Succès !</strong> Votre candidature a été soumise avec succès.
            </div>
            <div class="alert alert-info mb-3">
                <strong>Information :</strong> Votre prochaine session de formation est prévue demain.
            </div>
            <div class="alert alert-warning mb-3">
                <strong>Attention :</strong> La date limite de votre formation PCNC approche.
            </div>
            <div class="alert alert-error">
                <strong>Erreur :</strong> Il y a eu un problème lors du traitement de votre demande.
            </div>
        </section>
        
        <!-- Test Timeline -->
        <section class="section-card mb-5">
            <h2>Timeline du Parcours</h2>
            <div class="journey-timeline">
                <div class="timeline">
                    <div class="timeline-item completed">
                        <div class="timeline-marker">1</div>
                        <div class="timeline-content">
                            <h3>Candidature Complétée</h3>
                            <p>Candidature soumise avec succès et devenu Aspirant STAR</p>
                        </div>
                    </div>
                    
                    <div class="timeline-item in_progress">
                        <div class="timeline-marker">2</div>
                        <div class="timeline-content">
                            <h3>Formation PCNC</h3>
                            <p>Formation de 6 mois en Soins Pastoraux et Accompagnement</p>
                        </div>
                    </div>
                    
                    <div class="timeline-item pending">
                        <div class="timeline-marker">3</div>
                        <div class="timeline-content">
                            <h3>Entretien MDS</h3>
                            <p>En attente de programmation d'entretien avec l'équipe MDS</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        
        <!-- Test Status Card -->
        <div class="status-card mb-5">
            <div class="status-info">
                <div class="current-step">
                    <span class="step-number">2</span>
                    <div class="step-details">
                        <h3>Formation PCNC</h3>
                        <p>Complétez le cours de 6 mois en Soins Pastoraux et Accompagnement</p>
                        <span class="status-badge">En Cours</span>
                    </div>
                </div>
            </div>
            <div class="status-actions">
                <p class="status-message">Continuez votre excellent travail ! Vous progressez bien.</p>
            </div>
        </div>
        
        <div class="text-center">
            <a href="index.php" class="btn btn-primary">← Retour à l'Accueil</a>
            <a href="ui-demo.php" class="btn btn-secondary">Voir la Démo UI</a>
        </div>
    </div>
</body>
</html>
