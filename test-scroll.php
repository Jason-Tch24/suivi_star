<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test de Défilement - STAR System</title>
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body class="auth-page">
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <h1>Test de Défilement des Formulaires</h1>
                <p>Vérification que les formulaires longs peuvent défiler correctement</p>
            </div>
            
            <div class="alert alert-info">
                <strong>Test de Défilement :</strong> Ce formulaire est intentionnellement long pour tester le défilement.
            </div>
            
            <form class="auth-form">
                <!-- Section 1 -->
                <h3 style="color: #2563eb; margin: 2rem 0 1rem 0;">Section 1 - Informations Personnelles</h3>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">Prénom *</label>
                        <input type="text" id="first_name" name="first_name" required placeholder="Votre prénom">
                    </div>
                    <div class="form-group">
                        <label for="last_name">Nom *</label>
                        <input type="text" id="last_name" name="last_name" required placeholder="Votre nom">
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" required placeholder="votre.email@exemple.com">
                </div>
                
                <div class="form-group">
                    <label for="phone">Téléphone</label>
                    <input type="tel" id="phone" name="phone" placeholder="Votre numéro de téléphone">
                </div>
                
                <div class="form-group">
                    <label for="address">Adresse</label>
                    <textarea id="address" name="address" rows="3" placeholder="Votre adresse complète"></textarea>
                </div>
                
                <!-- Section 2 -->
                <h3 style="color: #2563eb; margin: 2rem 0 1rem 0;">Section 2 - Informations Professionnelles</h3>
                
                <div class="form-group">
                    <label for="profession">Profession</label>
                    <input type="text" id="profession" name="profession" placeholder="Votre profession">
                </div>
                
                <div class="form-group">
                    <label for="experience">Expérience</label>
                    <select id="experience" name="experience">
                        <option value="">Sélectionnez votre niveau d'expérience</option>
                        <option value="debutant">Débutant (0-2 ans)</option>
                        <option value="intermediaire">Intermédiaire (3-5 ans)</option>
                        <option value="avance">Avancé (6-10 ans)</option>
                        <option value="expert">Expert (10+ ans)</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="skills">Compétences</label>
                    <textarea id="skills" name="skills" rows="4" placeholder="Décrivez vos compétences principales"></textarea>
                </div>
                
                <!-- Section 3 - Ministry Preferences -->
                <div class="ministry-preferences">
                    <h3>Section 3 - Préférences de Ministère</h3>
                    <p>Sélectionnez jusqu'à 3 ministères dans lesquels vous aimeriez servir :</p>
                    
                    <div class="form-group">
                        <label for="ministry1">Premier choix *</label>
                        <select id="ministry1" name="ministry1" required>
                            <option value="">Sélectionnez votre premier choix</option>
                            <option value="worship">Ministère de Louange</option>
                            <option value="children">Ministère des Enfants</option>
                            <option value="youth">Ministère des Jeunes</option>
                            <option value="hospitality">Ministère d'Accueil</option>
                            <option value="technical">Ministère Technique</option>
                            <option value="pastoral">Soins Pastoraux</option>
                            <option value="evangelism">Évangélisation</option>
                            <option value="administration">Administration</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="ministry2">Deuxième choix</label>
                        <select id="ministry2" name="ministry2">
                            <option value="">Sélectionnez votre deuxième choix</option>
                            <option value="worship">Ministère de Louange</option>
                            <option value="children">Ministère des Enfants</option>
                            <option value="youth">Ministère des Jeunes</option>
                            <option value="hospitality">Ministère d'Accueil</option>
                            <option value="technical">Ministère Technique</option>
                            <option value="pastoral">Soins Pastoraux</option>
                            <option value="evangelism">Évangélisation</option>
                            <option value="administration">Administration</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="ministry3">Troisième choix</label>
                        <select id="ministry3" name="ministry3">
                            <option value="">Sélectionnez votre troisième choix</option>
                            <option value="worship">Ministère de Louange</option>
                            <option value="children">Ministère des Enfants</option>
                            <option value="youth">Ministère des Jeunes</option>
                            <option value="hospitality">Ministère d'Accueil</option>
                            <option value="technical">Ministère Technique</option>
                            <option value="pastoral">Soins Pastoraux</option>
                            <option value="evangelism">Évangélisation</option>
                            <option value="administration">Administration</option>
                        </select>
                    </div>
                </div>
                
                <!-- Section 4 -->
                <h3 style="color: #2563eb; margin: 2rem 0 1rem 0;">Section 4 - Motivation et Disponibilité</h3>
                
                <div class="form-group">
                    <label for="motivation">Pourquoi voulez-vous devenir bénévole STAR ?</label>
                    <textarea id="motivation" name="motivation" rows="5" placeholder="Partagez votre motivation pour servir dans notre église..."></textarea>
                </div>
                
                <div class="form-group">
                    <label for="availability">Disponibilité</label>
                    <textarea id="availability" name="availability" rows="3" placeholder="Indiquez vos créneaux de disponibilité..."></textarea>
                </div>
                
                <div class="form-group">
                    <label for="previous_experience">Expérience précédente dans le bénévolat</label>
                    <textarea id="previous_experience" name="previous_experience" rows="4" placeholder="Décrivez votre expérience précédente en tant que bénévole..."></textarea>
                </div>
                
                <!-- Section 5 -->
                <h3 style="color: #2563eb; margin: 2rem 0 1rem 0;">Section 5 - Informations Supplémentaires</h3>
                
                <div class="form-group">
                    <label for="emergency_contact">Contact d'urgence</label>
                    <input type="text" id="emergency_contact" name="emergency_contact" placeholder="Nom et téléphone du contact d'urgence">
                </div>
                
                <div class="form-group">
                    <label for="medical_info">Informations médicales importantes</label>
                    <textarea id="medical_info" name="medical_info" rows="3" placeholder="Allergies, conditions médicales, etc. (optionnel)"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="additional_comments">Commentaires supplémentaires</label>
                    <textarea id="additional_comments" name="additional_comments" rows="4" placeholder="Toute information supplémentaire que vous souhaitez partager..."></textarea>
                </div>
                
                <button type="submit" class="btn btn-primary btn-full">Soumettre le Formulaire de Test</button>
            </form>
            
            <div class="auth-links">
                <p><a href="index.php">← Retour à l'Accueil</a></p>
                <p><a href="register.php">Formulaire d'Inscription Réel</a></p>
            </div>
            
            <div class="demo-credentials">
                <h4>Test de Défilement Réussi !</h4>
                <p class="demo-info">Si vous pouvez voir cette section, le défilement fonctionne correctement.</p>
                <div class="demo-grid">
                    <div class="demo-account">
                        <strong>Défilement Vertical</strong><br>
                        Le formulaire peut défiler<br>
                        de haut en bas
                    </div>
                    <div class="demo-account">
                        <strong>Responsive</strong><br>
                        Fonctionne sur mobile<br>
                        et desktop
                    </div>
                    <div class="demo-account">
                        <strong>Accessible</strong><br>
                        Tous les champs sont<br>
                        accessibles
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <style>
        /* Styles spécifiques pour le test de défilement */
        .auth-page {
            align-items: flex-start !important;
            overflow-y: auto !important;
            overflow-x: hidden !important;
            padding-top: 2rem !important;
            padding-bottom: 2rem !important;
        }
        
        .auth-container {
            margin: 1rem auto !important;
            max-width: 32rem !important;
        }
        
        .auth-card {
            overflow: visible !important;
            max-height: none !important;
        }
        
        .auth-form {
            max-height: none !important;
            overflow: visible !important;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .ministry-preferences {
            background: linear-gradient(135deg, #dbeafe 0%, white 100%);
            padding: 2rem;
            border-radius: 0.75rem;
            margin: 2rem 0;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }
        
        .ministry-preferences h3 {
            color: #0f172a;
            margin-bottom: 0.5rem;
            font-weight: 700;
            font-size: 1.25rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .ministry-preferences h3::before {
            content: '🏛️';
            font-size: 1.5rem;
        }
        
        .ministry-preferences p {
            color: #475569;
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
            line-height: 1.6;
        }
        
        .demo-credentials {
            background: linear-gradient(135deg, #f1f5f9 0%, white 100%);
            padding: 2rem;
            border-radius: 0.75rem;
            margin-top: 2rem;
            border: 1px solid #e2e8f0;
        }
        
        .demo-credentials h4 {
            color: #0f172a;
            margin-bottom: 1rem;
            font-weight: 600;
            font-size: 1.125rem;
            text-align: center;
        }
        
        .demo-info {
            color: #475569;
            text-align: center;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            line-height: 1.6;
        }
        
        .demo-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
        }
        
        .demo-account {
            background: white;
            padding: 1.5rem;
            border-radius: 0.5rem;
            text-align: center;
            font-size: 0.85rem;
            line-height: 1.5;
            border: 1px solid #e2e8f0;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease;
        }
        
        .demo-account:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        
        .demo-account strong {
            color: #2563eb;
            display: block;
            margin-bottom: 0.25rem;
            font-weight: 600;
        }
        
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .ministry-preferences {
                padding: 1.5rem;
                margin: 1.5rem 0;
            }
            
            .demo-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .demo-credentials {
                padding: 1.5rem;
            }
            
            .auth-container {
                max-width: 90% !important;
                margin: 1rem auto !important;
            }
        }
        
        @media (max-width: 480px) {
            .auth-page {
                padding: 1rem !important;
            }
            
            .ministry-preferences {
                padding: 1rem;
            }
            
            .demo-credentials {
                padding: 1rem;
            }
        }
    </style>
    
    <script>
        // Test de défilement automatique
        document.addEventListener('DOMContentLoaded', function() {
            // Ajouter un indicateur visuel que le JavaScript fonctionne
            const header = document.querySelector('.auth-header h1');
            if (header) {
                header.innerHTML += ' <span style="color: #10b981;">✓</span>';
            }
            
            // Test de défilement fluide
            const form = document.querySelector('.auth-form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    alert('Test de formulaire réussi ! Le défilement fonctionne correctement.');
                    
                    // Défiler vers le haut pour montrer que le défilement fonctionne
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth'
                    });
                });
            }
        });
    </script>
</body>
</html>
