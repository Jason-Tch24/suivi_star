<?php
/**
 * Email Service for STAR Volunteer Management System
 * Handles all email communications for the application
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../vendor/autoload.php';

class EmailService
{
    private $mailer;
    private $appConfig;

    public function __construct()
    {
        $this->appConfig = require __DIR__ . '/../../config/app.php';
        $this->setupMailer();
    }

    private function setupMailer()
    {
        $this->mailer = new PHPMailer(true);

        try {
            // Configuration SMTP (à adapter selon votre serveur mail)
            $this->mailer->isSMTP();
            $this->mailer->Host = $_ENV['MAIL_HOST'] ?? 'smtp.gmail.com'; // ou votre serveur SMTP
            $this->mailer->SMTPAuth = true;
            $this->mailer->Username = $_ENV['MAIL_USERNAME'] ?? ''; // votre email
            $this->mailer->Password = $_ENV['MAIL_PASSWORD'] ?? ''; // votre mot de passe
            $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mailer->Port = $_ENV['MAIL_PORT'] ?? 587;

            // Configuration par défaut pour les emails
            $this->mailer->setFrom($_ENV['MAIL_FROM'] ?? 'noreply@star-system.com', $this->appConfig['name']);
            $this->mailer->CharSet = 'UTF-8';
            $this->mailer->isHTML(true);

        } catch (Exception $e) {
            error_log('Email setup failed: ' . $e->getMessage());
        }
    }

    /**
     * Envoie un email de bienvenue à un nouvel aspirant
     */
    public function sendWelcomeEmail($userData, $aspirantData)
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($userData['email'], $userData['first_name'] . ' ' . $userData['last_name']);

            $this->mailer->Subject = '🌟 Bienvenue dans le programme STAR !';
            
            $this->mailer->Body = $this->getWelcomeEmailTemplate($userData, $aspirantData);
            $this->mailer->AltBody = $this->getWelcomeEmailTextVersion($userData);

            $result = $this->mailer->send();
            
            // Log du succès
            error_log("Email de bienvenue envoyé avec succès à: " . $userData['email']);
            
            return $result;

        } catch (Exception $e) {
            error_log('Erreur envoi email de bienvenue: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Envoie un email de notification de changement de statut
     */
    public function sendStatusChangeEmail($userData, $oldStatus, $newStatus)
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($userData['email'], $userData['first_name'] . ' ' . $userData['last_name']);

            $this->mailer->Subject = '📋 Mise à jour de votre statut STAR';
            
            $this->mailer->Body = $this->getStatusChangeEmailTemplate($userData, $oldStatus, $newStatus);
            $this->mailer->AltBody = $this->getStatusChangeEmailTextVersion($userData, $oldStatus, $newStatus);

            $result = $this->mailer->send();
            
            error_log("Email de changement de statut envoyé à: " . $userData['email']);
            
            return $result;

        } catch (Exception $e) {
            error_log('Erreur envoi email changement statut: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Envoie un email de notification de progression d'étape
     */
    public function sendStepProgressEmail($userData, $stepNumber, $stepName)
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($userData['email'], $userData['first_name'] . ' ' . $userData['last_name']);

            $this->mailer->Subject = '🎯 Nouvelle étape dans votre parcours STAR';
            
            $this->mailer->Body = $this->getStepProgressEmailTemplate($userData, $stepNumber, $stepName);
            $this->mailer->AltBody = $this->getStepProgressEmailTextVersion($userData, $stepNumber, $stepName);

            $result = $this->mailer->send();
            
            error_log("Email de progression envoyé à: " . $userData['email']);
            
            return $result;

        } catch (Exception $e) {
            error_log('Erreur envoi email progression: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Template HTML pour l'email de bienvenue
     */
    private function getWelcomeEmailTemplate($userData, $aspirantData)
    {
        $firstName = htmlspecialchars($userData['first_name']);
        $appName = htmlspecialchars($this->appConfig['name']);
        
        return "
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; margin: 0; padding: 20px; }
                .container { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 12px 12px 0 0; }
                .content { padding: 30px; }
                .footer { background: #f8f9fa; padding: 20px; text-align: center; border-radius: 0 0 12px 12px; }
                .btn { display: inline-block; padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 6px; margin: 10px 0; }
                .highlight { background: #e3f2fd; padding: 15px; border-left: 4px solid #2196f3; margin: 15px 0; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🌟 Bienvenue dans STAR !</h1>
                    <p>Votre parcours de service commence maintenant</p>
                </div>
                
                <div class='content'>
                    <h2>Bonjour {$firstName} !</h2>
                    
                    <p>Félicitations ! Votre candidature pour rejoindre le programme STAR a été soumise avec succès.</p>
                    
                    <div class='highlight'>
                        <strong>🎯 Prochaines étapes :</strong>
                        <ul>
                            <li>Connectez-vous à votre tableau de bord pour suivre votre progression</li>
                            <li>Complétez votre profil si nécessaire</li>
                            <li>Attendez la validation de votre candidature</li>
                        </ul>
                    </div>
                    
                    <p><strong>Vos préférences de ministère :</strong></p>
                    <ul>
                        <li>1ère préférence : Ministère sélectionné</li>
                    </ul>
                    
                    <p>Un mentor vous sera assigné prochainement pour vous accompagner dans ce parcours enrichissant.</p>
                    
                    <div style='text-align: center; margin: 30px 0;'>
                        <a href='http://localhost/suivie_star/login' class='btn'>Se connecter au tableau de bord</a>
                    </div>
                </div>
                
                <div class='footer'>
                    <p>Cet email a été envoyé par le système {$appName}.</p>
                    <p>Si vous avez des questions, n'hésitez pas à nous contacter.</p>
                </div>
            </div>
        </body>
        </html>";
    }

    /**
     * Version texte de l'email de bienvenue
     */
    private function getWelcomeEmailTextVersion($userData)
    {
        $firstName = $userData['first_name'];
        $appName = $this->appConfig['name'];
        
        return "
Bienvenue dans STAR !

Bonjour {$firstName},

Félicitations ! Votre candidature pour rejoindre le programme STAR a été soumise avec succès.

Prochaines étapes :
- Connectez-vous à votre tableau de bord pour suivre votre progression
- Complétez votre profil si nécessaire  
- Attendez la validation de votre candidature

Un mentor vous sera assigné prochainement pour vous accompagner dans ce parcours enrichissant.

Connexion : http://localhost/suivie_star/login

Cet email a été envoyé par le système {$appName}.
Si vous avez des questions, n'hésitez pas à nous contacter.
        ";
    }

    /**
     * Template pour email de changement de statut
     */
    private function getStatusChangeEmailTemplate($userData, $oldStatus, $newStatus)
    {
        $firstName = htmlspecialchars($userData['first_name']);
        $statusMessages = [
            'active' => 'Votre candidature est maintenant active ! 🎉',
            'completed' => 'Félicitations ! Vous avez terminé le programme STAR ! 🎊',
            'inactive' => 'Votre candidature a été mise en pause ⏸️',
            'pending' => 'Votre candidature est en attente de validation 📋'
        ];
        
        $message = $statusMessages[$newStatus] ?? 'Votre statut a été mis à jour';
        
        return "
        <html>
        <head><meta charset='UTF-8'><style>body{font-family:Arial,sans-serif;line-height:1.6;padding:20px;}</style></head>
        <body>
            <h2>📋 Mise à jour de votre statut STAR</h2>
            <p>Bonjour {$firstName},</p>
            <p>{$message}</p>
            <p><strong>Nouveau statut :</strong> " . ucfirst($newStatus) . "</p>
            <p>Connectez-vous à votre tableau de bord pour plus de détails.</p>
            <a href='http://localhost/suivie_star/login' style='display:inline-block;padding:10px 20px;background:#667eea;color:white;text-decoration:none;border-radius:5px;'>Voir le tableau de bord</a>
        </body>
        </html>";
    }

    private function getStatusChangeEmailTextVersion($userData, $oldStatus, $newStatus)
    {
        return "Mise à jour de votre statut STAR\n\nBonjour {$userData['first_name']},\n\nVotre statut a été mis à jour vers : " . ucfirst($newStatus) . "\n\nConnectez-vous pour plus de détails : http://localhost/suivie_star/login";
    }

    /**
     * Template pour email de progression d'étape
     */
    private function getStepProgressEmailTemplate($userData, $stepNumber, $stepName)
    {
        $firstName = htmlspecialchars($userData['first_name']);
        
        return "
        <html>
        <head><meta charset='UTF-8'><style>body{font-family:Arial,sans-serif;line-height:1.6;padding:20px;}</style></head>
        <body>
            <h2>🎯 Nouvelle étape dans votre parcours STAR</h2>
            <p>Bonjour {$firstName},</p>
            <p>Félicitations ! Vous avez progressé vers l'étape {$stepNumber} : <strong>{$stepName}</strong></p>
            <p>Continuez votre excellent travail dans le programme STAR !</p>
            <a href='http://localhost/suivie_star/login' style='display:inline-block;padding:10px 20px;background:#667eea;color:white;text-decoration:none;border-radius:5px;'>Voir votre progression</a>
        </body>
        </html>";
    }

    private function getStepProgressEmailTextVersion($userData, $stepNumber, $stepName)
    {
        return "Nouvelle étape STAR\n\nBonjour {$userData['first_name']},\n\nVous avez progressé vers l'étape {$stepNumber}: {$stepName}\n\nVoir votre progression : http://localhost/suivie_star/login";
    }
}
