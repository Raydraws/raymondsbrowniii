<?php
namespace Raydraws\Module\RsbMemberCode\Site\Helper;

\defined('_JEXEC') or die;

use Joomla\Registry\Registry;

/**
 * Works out the members code for the current period.
 * Period n starts at anchor + n * period_days (America/New_York).
 * Code = "RSB3" + first 6 hex chars (upper case) of HMAC-SHA256(secret, "YYYY-MM-DD" of the period start).
 * The morning check uses the same formula to create each code in Shopify a day early.
 */
class CodeHelper
{
    public static function codeFor(string $secret, \DateTimeImmutable $start): string
    {
        return 'RSB3' . strtoupper(substr(hash_hmac('sha256', $start->format('Y-m-d'), $secret), 0, 6));
    }

    public function getCode(Registry $params): ?object
    {
        $tz     = new \DateTimeZone('America/New_York');
        $now    = new \DateTimeImmutable('now', $tz);
        $anchor = new \DateTimeImmutable((string) $params->get('anchor', '2026-11-17 19:30'), $tz);
        $days   = max(1, (int) $params->get('period_days', 5));
        $secret = (string) $params->get('secret', '');

        if ($now < $anchor || $secret === '') {
            return (object) ['code' => (string) $params->get('first_code', ''), 'ends' => $anchor];
        }

        $n     = intdiv($now->getTimestamp() - $anchor->getTimestamp(), $days * 86400);
        $start = $anchor->modify('+' . ($n * $days) . ' days');

        return (object) ['code' => self::codeFor($secret, $start), 'ends' => $start->modify('+' . $days . ' days')];
    }
}
