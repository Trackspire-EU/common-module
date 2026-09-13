<?php

declare(strict_types=1);

namespace Trackspire\CommonModule\Service;

use DateTimeImmutable;
use Resend;
use Resend\Client as ResendClient;
use Trackspire\CommonModule\Repository\EnvironmentRepository;
use Trackspire\CommonModule\Value\User\UserEmail;

class MailService
{
    /** Resend dashboard template ID for the password reset email */
    private const string TEMPLATE_PASSWORD_RESET = '0556400b-9812-4eb2-94c6-287550783bd7';

    /** Resend dashboard template ID for the organization invitation email */
    private const string TEMPLATE_ORG_INVITATION = '51ca5d73-5515-4465-ad0b-a84da700ca9c';

    /** Resend dashboard template ID for the early access invitation email */
    private const string TEMPLATE_EARLY_ACCESS_INVITATION = 'd3a2195-64d4-4d43-9a42-25fc46cdf0bb';

    /** Resend dashboard template ID for the weekly report email */
    private const string TEMPLATE_WEEKLY_REPORT = 'bb90f527-f132-4d53-a230-fe973f97d940';

    /** Resend dashboard template ID for the new-login-location alert email */
    private const string TEMPLATE_NEW_LOGIN = '036db034-507b-46f3-9bc1-929b29f56d91';

    private readonly ResendClient $resend;

    public function __construct(
        private readonly EnvironmentRepository $envRepo,
    ) {
        $this->resend = Resend::client($this->envRepo->get('RESEND_API_KEY'));
    }

    public function sendPasswordResetEmail(string $toEmail, string $resetToken): void
    {
        $appUrl = $this->envRepo->get('APP_URL', 'http://localhost:5173');
        $resetUrl = $appUrl . '/reset-password?token=' . urlencode($resetToken);

        $this->sendTemplate(
            $toEmail,
            'Reset your Trackspire password',
            self::TEMPLATE_PASSWORD_RESET,
            [
                'RESET_URL' => $resetUrl,
                'EXPIRES_IN' => '1 hour',
            ],
        );
    }

    public function sendInvitationEmail(UserEmail $toEmail, string $token): void
    {
        $appUrl = $this->envRepo->get('APP_URL', 'http://localhost:5173');
        $acceptUrl = $appUrl . '/accept-invite?token=' . urlencode($token);

        $this->sendTemplate(
            $toEmail->asString(),
            'You have been invited to Trackspire',
            self::TEMPLATE_ORG_INVITATION,
            [
                'ACCEPT_URL' => $acceptUrl,
                'EXPIRES_IN' => '7 days',
            ],
        );
    }

    public function sendEarlyAccessInvitationEmail(UserEmail $toEmail, string $formattedCode): void
    {
        $appUrl = $this->envRepo->get('APP_URL', 'http://localhost:5173');
        $registerUrl = $appUrl . '/register?code=' . urlencode($formattedCode) . '&email=' . urlencode($toEmail->asString());

        $this->sendTemplate(
            $toEmail->asString(),
            'Your Trackspire early access invitation',
            self::TEMPLATE_EARLY_ACCESS_INVITATION,
            [
                'REGISTER_URL' => $registerUrl,
                'INVITE_CODE' => $formattedCode,
            ],
        );
    }

    public function sendWeeklyReport(string $toEmail, string $siteName, string $siteDomain, array $data): void
    {
        $appUrl     = $this->envRepo->get('APP_URL', 'http://localhost:5173');
        $period     = $data['period'];
        $kpis       = $data['kpis'];
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

        $this->sendTemplate(
            $toEmail,
            "Weekly report: $siteName",
            self::TEMPLATE_WEEKLY_REPORT,
            [
                'SITE_NAME' => $siteName,
                'SITE_DOMAIN' => $siteDomain,
                'PERIOD_FROM' => $period['from'],
                'PERIOD_TO' => $period['to'],
                'PAGEVIEWS' => $kpis['pageviews'],
                'VISITORS' => $kpis['visitors'],
                'SESSIONS' => $kpis['sessions'],
                'BOUNCE_RATE' => $bounceRate,
                'TOP_PAGES_HTML' => $topPagesRows,
                'TOP_SOURCES_HTML' => $topSourcesRows,
                'DASHBOARD_URL' => $appUrl,
            ],
        );
    }

    public function sendNewLoginEmail(
        string $toEmail,
        string $ipAddress,
        ?string $city,
        ?string $countryCode,
        DateTimeImmutable $loginAt,
    ): void {
        $location = $city !== null && $countryCode !== null
            ? "$city, $countryCode"
            : ($countryCode ?? 'Unknown location');

        $this->sendTemplate(
            $toEmail,
            'New login to your Trackspire account',
            self::TEMPLATE_NEW_LOGIN,
            [
                'IP_ADDRESS' => $ipAddress,
                'LOCATION' => $location,
                'LOGIN_TIME' => $loginAt->format(DATE_ATOM),
            ],
        );
    }

    private function sendTemplate(string $toEmail, string $subject, string $templateId, array $variables): void
    {
        $fromAddress = $this->envRepo->get('MAIL_FROM_ADDRESS', 'noreply@trackspire.eu');
        $fromName = $this->envRepo->get('MAIL_FROM_NAME', 'Trackspire');

        $this->resend->emails->send([
            'from' => "$fromName <$fromAddress>",
            'to' => [$toEmail],
            'subject' => $subject,
            'template' => [
                'id' => $templateId,
                'variables' => $variables,
            ],
        ]);
    }
}