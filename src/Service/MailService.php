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

    public function sendWeeklyReport(string $toEmail, string $siteName, string $siteDomain, array $data): void
    {
        $appUrl    = $this->envRepo->get('APP_URL', 'http://localhost:5173');
        $period    = $data['period'];
        $kpis      = $data['kpis'];
        $bounceRate = $kpis['bounceRate'] !== null ? $kpis['bounceRate'] . '%' : '—';

        $topPagesRows = '';
        foreach ($data['topPages'] as $page) {
            $path = htmlspecialchars($page['pathname'], ENT_QUOTES);
            $pv   = (int) $page['pageviews'];
            $topPagesRows .= "<tr><td style='padding:6px 0;color:#374151;'>$path</td><td style='padding:6px 0;text-align:right;color:#6b7280;'>$pv</td></tr>";
        }

        $topSourcesRows = '';
        foreach ($data['topSources'] as $src) {
            $source = htmlspecialchars($src['source'], ENT_QUOTES);
            $pv     = (int) $src['pageviews'];
            $topSourcesRows .= "<tr><td style='padding:6px 0;color:#374151;'>$source</td><td style='padding:6px 0;text-align:right;color:#6b7280;'>$pv</td></tr>";
        }

        $body = <<<HTML
        <!DOCTYPE html>
        <html lang="en">
        <head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"></head>
        <body style="margin:0;padding:0;background:#f9fafb;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;">
          <table width="100%" cellpadding="0" cellspacing="0" style="background:#f9fafb;padding:32px 16px;">
            <tr><td align="center">
              <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:12px;overflow:hidden;border:1px solid #e5e7eb;max-width:600px;width:100%;">

                <!-- Header -->
                <tr><td style="background:#111827;padding:24px 32px;">
                  <p style="margin:0;color:#ffffff;font-size:20px;font-weight:700;">Trackspire</p>
                  <p style="margin:4px 0 0;color:#9ca3af;font-size:13px;">Weekly report for <strong style="color:#d1d5db;">$siteName</strong></p>
                </td></tr>

                <!-- Period -->
                <tr><td style="padding:24px 32px 0;">
                  <p style="margin:0;font-size:13px;color:#6b7280;">{$period['from']} – {$period['to']}</p>
                </td></tr>

                <!-- KPIs -->
                <tr><td style="padding:16px 32px 24px;">
                  <table width="100%" cellpadding="0" cellspacing="0">
                    <tr>
                      <td style="width:25%;text-align:center;padding:16px 8px;background:#f9fafb;border-radius:8px;border:1px solid #e5e7eb;">
                        <p style="margin:0;font-size:22px;font-weight:700;color:#111827;">{$kpis['pageviews']}</p>
                        <p style="margin:4px 0 0;font-size:12px;color:#6b7280;">Pageviews</p>
                      </td>
                      <td style="width:4%;"></td>
                      <td style="width:25%;text-align:center;padding:16px 8px;background:#f9fafb;border-radius:8px;border:1px solid #e5e7eb;">
                        <p style="margin:0;font-size:22px;font-weight:700;color:#111827;">{$kpis['visitors']}</p>
                        <p style="margin:4px 0 0;font-size:12px;color:#6b7280;">Visitors</p>
                      </td>
                      <td style="width:4%;"></td>
                      <td style="width:25%;text-align:center;padding:16px 8px;background:#f9fafb;border-radius:8px;border:1px solid #e5e7eb;">
                        <p style="margin:0;font-size:22px;font-weight:700;color:#111827;">{$kpis['sessions']}</p>
                        <p style="margin:4px 0 0;font-size:12px;color:#6b7280;">Sessions</p>
                      </td>
                      <td style="width:4%;"></td>
                      <td style="width:25%;text-align:center;padding:16px 8px;background:#f9fafb;border-radius:8px;border:1px solid #e5e7eb;">
                        <p style="margin:0;font-size:22px;font-weight:700;color:#111827;">$bounceRate</p>
                        <p style="margin:4px 0 0;font-size:12px;color:#6b7280;">Bounce Rate</p>
                      </td>
                    </tr>
                  </table>
                </td></tr>

                <!-- Top Pages -->
                <tr><td style="padding:0 32px 24px;">
                  <p style="margin:0 0 12px;font-size:14px;font-weight:600;color:#111827;">Top Pages</p>
                  <table width="100%" cellpadding="0" cellspacing="0" style="border-top:1px solid #e5e7eb;">
                    $topPagesRows
                  </table>
                </td></tr>

                <!-- Top Sources -->
                <tr><td style="padding:0 32px 24px;">
                  <p style="margin:0 0 12px;font-size:14px;font-weight:600;color:#111827;">Top Sources</p>
                  <table width="100%" cellpadding="0" cellspacing="0" style="border-top:1px solid #e5e7eb;">
                    $topSourcesRows
                  </table>
                </td></tr>

                <!-- CTA -->
                <tr><td style="padding:0 32px 32px;">
                  <a href="{$appUrl}" style="display:inline-block;background:#111827;color:#ffffff;text-decoration:none;padding:10px 20px;border-radius:8px;font-size:14px;font-weight:500;">Open Dashboard →</a>
                </td></tr>

                <!-- Footer -->
                <tr><td style="padding:16px 32px;border-top:1px solid #e5e7eb;background:#f9fafb;">
                  <p style="margin:0;font-size:12px;color:#9ca3af;">$siteDomain · You're receiving this because you enabled weekly reports in Trackspire site settings.</p>
                </td></tr>

              </table>
            </td></tr>
          </table>
        </body>
        </html>
        HTML;

        $this->send($toEmail, "Weekly report: $siteName", $body);
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