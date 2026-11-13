<?php
namespace Raydraws\Module\RsbEvents\Site\Helper;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Http\HttpFactory;
use Joomla\Registry\Registry;

/**
 * Reads a public iCal feed, caches it on disk, and returns upcoming events.
 * If the feed can't be reached the last good copy is used (stale but safe).
 */
class EventsHelper
{
    public function getEvents(Registry $params): array
    {
        $url = trim((string) $params->get('ics_url', ''));
        if ($url === '' || stripos($url, 'private-') !== false) {
            return []; // never use a secret address
        }

        $dir  = JPATH_CACHE . '/mod_rsb_events';
        $file = $dir . '/' . md5($url) . '.ics';
        $ttl  = max(5, (int) $params->get('cache_minutes', 60)) * 60;
        $ics  = is_file($file) ? (string) file_get_contents($file) : '';

        if ($ics === '' || (time() - filemtime($file)) > $ttl) {
            try {
                $res = HttpFactory::getHttp()->get($url, [], 6);
                if ($res->getStatusCode() === 200 && strpos((string) $res->getBody(), 'BEGIN:VCALENDAR') !== false) {
                    $ics = (string) $res->getBody();
                    if (!is_dir($dir)) { mkdir($dir, 0755, true); }
                    file_put_contents($file, $ics);
                } elseif (is_file($file)) {
                    touch($file, time() - $ttl + 600); // retry in 10 minutes
                }
            } catch (\Throwable $e) {
                // keep the cached copy
            }
        }

        return $ics === '' ? [] : $this->upcoming($ics, (int) $params->get('count', 3));
    }

    private function upcoming(string $ics, int $count): array
    {
        $ics    = preg_replace("/\r?\n[ \t]/", '', $ics); // unfold lines
        $tz     = new \DateTimeZone(Factory::getApplication()->get('offset', 'UTC'));
        $now    = new \DateTimeImmutable('now', $tz);
        $events = [];

        if (!preg_match_all('/BEGIN:VEVENT(.*?)END:VEVENT/s', $ics, $blocks)) {
            return [];
        }

        foreach ($blocks[1] as $b) {
            $start = $this->date($b, 'DTSTART', $tz, $allDay);
            if (!$start) { continue; }
            $end = $this->date($b, 'DTEND', $tz, $x) ?: $start;
            if ($end < $now) { continue; }
            if (preg_match('/^STATUS:CANCELLED/m', $b)) { continue; }
            $events[] = (object) [
                'title'    => $this->text($b, 'SUMMARY'),
                'location' => $this->text($b, 'LOCATION'),
                'url'      => $this->text($b, 'URL'),
                'start'    => $start,
                'allDay'   => $allDay,
            ];
        }

        usort($events, fn ($a, $b) => $a->start <=> $b->start);
        return array_slice($events, 0, max(1, $count));
    }

    private function date(string $b, string $key, \DateTimeZone $tz, &$allDay): ?\DateTimeImmutable
    {
        $allDay = false;
        if (!preg_match('/^' . $key . '([^:\r\n]*):([0-9TZ]+)/m', $b, $m)) { return null; }
        $v = $m[2];
        if (strlen($v) === 8) {
            $allDay = true;
            return \DateTimeImmutable::createFromFormat('!Ymd', $v, $tz) ?: null;
        }
        $zone = $tz;
        if (substr($v, -1) === 'Z') { $zone = new \DateTimeZone('UTC'); $v = substr($v, 0, -1); }
        elseif (preg_match('/TZID=([^;:]+)/', $m[1], $z)) { try { $zone = new \DateTimeZone($z[1]); } catch (\Throwable $e) {} }
        $d = \DateTimeImmutable::createFromFormat('Ymd\THis', $v, $zone);
        return $d ? $d->setTimezone($tz) : null;
    }

    private function text(string $b, string $key): string
    {
        if (!preg_match('/^' . $key . '[^:\r\n]*:(.*)$/m', $b, $m)) { return ''; }
        return trim(str_replace(['\\n', '\\,', '\\;', '\\\\'], ["\n", ',', ';', '\\'], $m[1]));
    }
}
