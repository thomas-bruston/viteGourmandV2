<?php

declare(strict_types=1);

namespace Service;

use Core\Env;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


class MailService
{
    private string $host;
    private int    $port;
    private string $username;
    private string $password;
    private string $from;
    private string $fromName;
    private string $encryption;

    public function __construct()
    {
        $this->host       = Env::get('MAIL_HOST',       'smtp.example.com');
        $this->port       = (int) Env::get('MAIL_PORT', '587');
        $this->username   = Env::get('MAIL_USERNAME',   '');
        $this->password   = Env::get('MAIL_PASSWORD',   '');
        $this->from       = Env::get('MAIL_FROM',       'noreply@vitegourmand.fr');
        $this->fromName   = Env::get('MAIL_FROM_NAME',  'Vite & Gourmand');
        $this->encryption = Env::get('MAIL_ENCRYPTION', 'tls');
    }

    /* envoi mail */

    private function send(string $to, string $subject, string $htmlBody): bool
    {
        $mail = new PHPMailer(true);

        try {
            // Config SMTP
            $mail->isSMTP();
            $mail->Host       = $this->host;
            $mail->SMTPAuth   = true;
            $mail->Username   = $this->username;
            $mail->Password   = $this->password;
            $mail->SMTPSecure = $this->encryption === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = $this->port;
            $mail->CharSet    = 'UTF-8';

            
            $mail->setFrom($this->from, $this->fromName);
            $mail->addAddress($to);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $htmlBody;
            $mail->AltBody = strip_tags($htmlBody);

            $mail->send();
            return true;

        } catch (Exception $e) {
            error_log('[MailService::send] Erreur : ' . $mail->ErrorInfo);
            return false;
        }
    }
    /* Mail bienvenue */

    public function sendBienvenue(string $to, string $prenom): bool
    {
        $subject = 'Bienvenue chez Vite & Gourmand !';
        $body    = $this->layout("Bienvenue, {$prenom} !", "
            <p>Merci de vous être inscrit sur <strong>Vite & Gourmand</strong>.</p>
            <p>Vous pouvez dès maintenant consulter nos menus et passer votre première commande.</p>
            <a href='" . Env::get('APP_URL') . "/menus' class='btn'>Découvrir nos menus</a>
        ");

        return $this->send($to, $subject, $body);
    }

    /*Mail commande */

    public function sendConfirmationCommande(
        string $to,
        string $prenom,
        string $numeroCommande,
        string $menuTitre,
        string $datePrestation,
        float  $prixTotal
    ): bool {
        $subject = "Confirmation de votre commande {$numeroCommande}";
        $body    = $this->layout("Commande confirmée !", "
            <p>Bonjour {$prenom},</p>
            <p>Votre commande <strong>{$numeroCommande}</strong> a bien été enregistrée.</p>
            <table>
                <tr><td><strong>Menu</strong></td><td>{$menuTitre}</td></tr>
                <tr><td><strong>Date de livraison</strong></td><td>{$datePrestation}</td></tr>
                <tr><td><strong>Total</strong></td><td>" . number_format($prixTotal, 2) . " €</td></tr>
            </table>
            <p>Vous pouvez suivre votre commande depuis votre espace personnel.</p>
            <a href='" . Env::get('APP_URL') . "/mes-commandes' class='btn'>Suivre ma commande</a>
        ");

        return $this->send($to, $subject, $body);
    }

    /* Mail avis */

    public function sendInvitationAvis(
        string $to,
        string $prenom,
        string $numeroCommande,
        int    $commandeId
    ): bool {
        $subject = 'Votre avis nous intéresse !';
        $body    = $this->layout("Votre commande est terminée !", "
            <p>Bonjour {$prenom},</p>
            <p>Votre commande <strong>{$numeroCommande}</strong> est maintenant terminée.</p>
            <p>Nous espérons que vous vous êtes régalé(e) ! Partagez votre expérience en laissant un avis.</p>
            <a href='" . Env::get('APP_URL') . "/avis/nouveau?commande_id={$commandeId}' class='btn'>Laisser un avis</a>
        ");

        return $this->send($to, $subject, $body);
    }

    /* Mail retour matériel */

    public function sendRetourMateriel(
        string $to,
        string $prenom,
        string $numeroCommande
    ): bool {
        $subject = "Retour du matériel — Commande {$numeroCommande}";
        $body    = $this->layout("Retour du matériel", "
            <p>Bonjour {$prenom},</p>
            <p>Votre commande <strong>{$numeroCommande}</strong> a bien été livrée.</p>
            <p>Nous vous rappelons que le matériel mis à votre disposition doit être retourné 
               <strong>dans un délai de 10 jours ouvrés</strong>.</p>
            <p>Passé ce délai, des frais de <strong>600 € TTC</strong> seront facturés.</p>
            <p>Pour organiser la restitution, contactez-nous :</p>
            <a href='" . Env::get('APP_URL') . "/contact' class='btn'>Nous contacter</a>
        ");

        return $this->send($to, $subject, $body);
    }

    /* Mail reinit MDP */

    public function sendResetPassword(string $to, string $prenom, string $token): bool
    {
        $resetUrl = Env::get('APP_URL') . '/reinitialiser-mdp?token=' . $token;
        $subject  = 'Réinitialisation de votre mot de passe';
        $body     = $this->layout("Réinitialisation du mot de passe", "
            <p>Bonjour {$prenom},</p>
            <p>Vous avez demandé la réinitialisation de votre mot de passe.</p>
            <p>Cliquez sur le bouton ci-dessous pour choisir un nouveau mot de passe :</p>
            <a href='{$resetUrl}' class='btn'>Réinitialiser mon mot de passe</a>
            <p><small>Ce lien est valable <strong>1 heure</strong>. 
               Si vous n'êtes pas à l'origine de cette demande, ignorez ce mail.</small></p>
        ");

        return $this->send($to, $subject, $body);
    }

    /* Mail compte employé */
   
    public function sendCreationCompteEmploye(string $to, string $prenom): bool
    {
        $subject = 'Votre compte employé Vite & Gourmand';
        $body    = $this->layout("Bienvenue dans l'équipe !", "
            <p>Bonjour {$prenom},</p>
            <p>Un compte employé a été créé pour vous sur <strong>Vite & Gourmand</strong>.</p>
            <p>Vos identifiants de connexion vous ont été communiqués par votre administrateur.</p>
            <a href='" . Env::get('APP_URL') . "/connexion' class='btn'>Accéder à mon espace</a>
            <p><small>Si vous souhaitez modifier votre mot de passe, utilisez la fonction 
               \"Mot de passe oublié\" depuis la page de connexion.</small></p>
        ");

        return $this->send($to, $subject, $body);
    }

    // Template mail

    private function layout(string $title, string $content): string
    {
        return "
        <!DOCTYPE html>
        <html lang='fr'>
        <head>
            <meta charset='UTF-8'>
            <style>
                body        { font-family: Arial, sans-serif; background: #f5f5f5; margin: 0; padding: 20px; }
                .container  { max-width: 600px; margin: 0 auto; background: #fff; border-radius: 8px; overflow: hidden; }
                .header     { background: #2d5f3f; padding: 30px; text-align: center; }
                .header h1  { color: #fff; margin: 0; font-size: 24px; }
                .body       { padding: 30px; color: #333; line-height: 1.6; }
                .body h2    { color: #2d5f3f; }
                table       { width: 100%; border-collapse: collapse; margin: 15px 0; }
                td          { padding: 8px 12px; border-bottom: 1px solid #eee; }
                .btn        { display: inline-block; margin: 20px 0; padding: 12px 25px;
                              background: #2d5f3f; color: #fff !important; text-decoration: none;
                              border-radius: 5px; font-weight: bold; }
                .footer     { background: #f0f0f0; padding: 15px; text-align: center;
                              font-size: 12px; color: #888; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🍽️ Vite &amp; Gourmand</h1>
                </div>
                <div class='body'>
                    <h2>{$title}</h2>
                    {$content}
                </div>
                <div class='footer'>
                    Vite &amp; Gourmand — Traiteur bordelais<br>
                    25 rue Turenne, 33000 Bordeaux
                </div>
            </div>
        </body>
        </html>";
    }
}
