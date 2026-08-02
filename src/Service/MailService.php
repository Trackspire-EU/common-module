<?php

declare(strict_types=1);

namespace Trackspire\CommonModule\Service;

use PHPMailer\PHPMailer\PHPMailer;
use Trackspire\CommonModule\Repository\EnvironmentRepository;
use Trackspire\CommonModule\Value\User\UserEmail;

class MailService
{
    public function __construct(
        private readonly EnvironmentRepository $envRepo,
    ) {
    }

    public function sendPasswordResetEmail(string $toEmail, string $resetToken): void
    {
        $appUrl = $this->envRepo->get('APP_URL', 'http://localhost:5173');
        $resetUrl = $appUrl . '/reset-password?token=' . urlencode($resetToken);

        $body = <<<HTML
        <p>You requested a password reset for your Trackspire account.</p>
        <p>Click the link below to set a new password. The link expires in 1 hour.</p>
        <p><a href="{$resetUrl}">{$resetUrl}</a></p>
        <p>If you did not request this, ignore this email.</p>
        HTML;

        $this->send($toEmail, 'Reset your Trackspire password', $body);
    }

    public function sendInvitationEmail(UserEmail $toEmail, string $token): void
    {
        $appUrl = $this->envRepo->get('APP_URL', 'http://localhost:5173');
        $acceptUrl = $appUrl . '/accept-invite?token=' . urlencode($token);

        $body = <<<HTML
        <p>You have been invited to join an organization on Trackspire.</p>
        <p>Click the link below to accept the invitation. It expires in 7 days.</p>
        <p><a href="{$acceptUrl}">{$acceptUrl}</a></p>
        <p>If you do not have an account yet, please register first, then use this link.</p>
        HTML;

        $this->send($toEmail->asString(), 'You have been invited to Trackspire', $body);
    }

    public function sendEarlyAccessInvitationEmail(UserEmail $toEmail, string $formattedCode): void
    {
        $appUrl = $this->envRepo->get('APP_URL', 'http://localhost:5173');
        $registerUrl = $appUrl . '/register?code=' . urlencode($formattedCode) . '&email=' . urlencode($toEmail->asString());

        $body = <<<HTML
        <p>You have been invited to join Trackspire early access.</p>
        <p>Click the link below to register. Your invite code is: <strong>{$formattedCode}</strong></p>
        <p><a href="{$registerUrl}">{$registerUrl}</a></p>
        <p>If you did not expect this invitation, you can ignore this email.</p>
        HTML;

        $this->send($toEmail->asString(), 'Your Trackspire early access invitation', $body);
    }

    private function send(string $toEmail, string $subject, string $htmlBody): void
    {
        $mailer = new PHPMailer(true);
        $mailer->isSMTP();
        $mailer->Host = $this->envRepo->get('SMTP_HOST', 'localhost');
        $mailer->Port = (int) $this->envRepo->get('SMTP_PORT', '1025');

        $smtpUser = $this->envRepo->get('SMTP_USER', '', true);
        if (!empty($smtpUser)) {
            $mailer->SMTPAuth = true;
            $mailer->Username = $smtpUser;
            $mailer->Password = $this->envRepo->get('SMTP_PASSWORD', '');
            $mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        }

        $fromAddress = $this->envRepo->get('SMTP_FROM_ADDRESS', 'noreply@trackspire.app');
        $fromName = $this->envRepo->get('SMTP_FROM_NAME', 'Trackspire');

        $mailer->setFrom($fromAddress, $fromName);
        $mailer->addAddress($toEmail);
        $mailer->isHTML(true);
        $mailer->Subject = $subject;
        $mailer->Body = $htmlBody;
        $mailer->AltBody = strip_tags($htmlBody);

        $mailer->send();
    }
}