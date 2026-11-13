<?php
namespace Raydraws\Module\RsbShop\Site\Helper;

\defined('_JEXEC') or die;

use Joomla\CMS\Http\HttpFactory;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;

/**
 * Reads the store's public collection feed (/collections/{handle}/products.json),
 * caches it on disk, and returns products with live name, price and link.
 */
class ShopHelper
{
    public function getProducts(Registry $params): array
    {
        $store = rtrim((string) $params->get('store_url', 'https://shop.raydraws.com'), '/');
        $coll  = preg_replace('/[^a-z0-9\-]/', '', strtolower((string) $params->get('collection', 'backpacks-bags')));
        $url   = $store . '/collections/' . $coll . '/products.json?limit=250';
        $dir   = JPATH_CACHE . '/mod_rsb_shop';
        $file  = $dir . '/' . md5($url) . '.json';
        $ttl   = max(5, (int) $params->get('cache_minutes', 60)) * 60;
        $json  = is_file($file) ? (string) file_get_contents($file) : '';

        if ($json === '' || (time() - filemtime($file)) > $ttl) {
            try {
                $res  = HttpFactory::getHttp()->get($url, ['Accept' => 'application/json'], 6);
                $body = (string) $res->getBody();
                if ($res->getStatusCode() === 200 && isset(json_decode($body, true)['products'])) {
                    $json = $body;
                    if (!is_dir($dir)) { mkdir($dir, 0755, true); }
                    file_put_contents($file, $json);
                } elseif (is_file($file)) {
                    touch($file, time() - $ttl + 600);
                }
            } catch (\Throwable $e) {
                // keep the cached copy
            }
        }

        $images = [];
        foreach (preg_split('/\r?\n/', (string) $params->get('images', '')) as $line) {
            if (strpos($line, '=') !== false) {
                [$h, $img] = array_map('trim', explode('=', $line, 2));
                if ($h !== '' && $img !== '') { $images[$h] = $img; }
            }
        }

        $out  = [];
        $data = $json !== '' ? (json_decode($json, true)['products'] ?? []) : [];
        foreach ($data as $p) {
            if ($images && !isset($images[$p['handle']])) { continue; }
            $variant = $p['variants'][0] ?? null;
            if (!$variant || empty($variant['available'])) { continue; }
            $img = $images[$p['handle']] ?? ($p['images'][0]['src'] ?? '');
            $out[] = [
                'name'  => html_entity_decode(strip_tags($p['title']), ENT_QUOTES, 'UTF-8'),
                'price' => '$' . rtrim(rtrim(number_format((float) $variant['price'], 2, '.', ''), '0'), '.'),
                'url'   => $store . '/products/' . $p['handle'],
                'img'   => preg_match('#^https?://#', $img) ? $img : Uri::root(true) . '/' . ltrim($img, '/'),
                'own'   => isset($images[$p['handle']]),
            ];
        }

        return $out;
    }
}
