<?php
/**
 * raymondsbrowniii.com: "Shows & exhibitions" list for mod_feed.
 * Reads the Shows feed published by artbyraybrown.com, where shows are added and edited:
 *   https://artbyraybrown.com/index.php?option=com_ajax&plugin=abrfeed&group=ajax&format=raw&feed=shows
 * Pick this layout in the module's Advanced tab. Upcoming shows come first, then past ones.
 * Renders nothing when the feed can't be reached, so the page never shows an error.
 */
defined('_JEXEC') or die;

if (empty($rssurl) || empty($feed) || is_string($feed) || $feed === false) {
    return;
}

$esc  = static fn ($s) => htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
$fix  = static fn ($url) => preg_replace('#^(https?://[^/]+)(/[^/]+)\2/#', '$1$2/', $url); // local subfolder installs
$part = static function ($html, $name) {
    return preg_match('/<p data-abr="' . $name . '">(.*?)<\/p>/is', $html, $m) ? trim(html_entity_decode(strip_tags($m[1]), ENT_QUOTES, 'UTF-8')) : '';
};

$max   = min(count($feed), (int) $params->get('rssitems', 30));
$rows  = [];
$first = null; // first show that has a flyer: featured beside the list

for ($i = 0; $i < $max; $i++) {
    $it   = $feed[$i];
    $html = (string) $it->content;
    preg_match('/<img[^>]+src="([^"]+)"/i', $html, $m);
    $cats = [];

    foreach ((array) $it->categories as $k => $v) {
        $cats[] = is_string($k) ? $k : (string) $v;
    }

    $row = [
        'title'    => trim((string) $it->title),
        'uri'      => trim((string) ($it->uri ?: $it->guid)),
        'img'      => !empty($m[1]) ? $fix(html_entity_decode($m[1])) : '',
        'when'     => $part($html, 'when'),
        'where'    => $part($html, 'where'),
        'role'     => $part($html, 'role'),
        'upcoming' => in_array('Upcoming', $cats, true),
    ];

    if ($first === null && $row['img'] !== '') {
        $first = $row;
    }

    $rows[] = $row;
}

if (!$rows) {
    return;
}
?>
<div class="rsb-showgrid">
  <div class="rsb-showgrid__list">
    <ul class="rsb-showlist">
    <?php foreach ($rows as $r) : ?>
      <li<?php echo $r['upcoming'] ? ' class="is-upcoming"' : ''; ?>>
        <span class="when"><?php echo $esc($r['when']); ?></span>
        <div class="what">
          <a class="rsb-showlink" href="<?php echo $esc($r['uri']); ?>"><?php echo $esc($r['title']); ?></a>
          <?php if ($r['upcoming']) : ?><span class="rsb-solo">UPCOMING</span><?php endif; ?>
          <?php if ($r['role'] !== '' && stripos($r['role'], 'solo') !== false) : ?><span class="rsb-solo">SOLO</span><?php endif; ?>
          <?php $sub = implode(' · ', array_filter([$r['role'] !== '' && stripos($r['role'], 'solo') === false ? $r['role'] : '', $r['where']])); ?>
          <?php if ($sub !== '') : ?><span><?php echo $esc($sub); ?></span><?php endif; ?>
        </div>
      </li>
    <?php endforeach; ?>
    </ul>
  </div>
  <?php if ($first) : ?>
  <figure class="rsb-showgrid__feature" style="margin:0">
    <a href="<?php echo $esc($first['uri']); ?>"><img src="<?php echo $esc($first['img']); ?>" alt="Flyer for <?php echo $esc($first['title']); ?>" loading="lazy"></a>
    <p><?php echo $esc($first['title']); ?><?php echo $first['when'] !== '' ? ' · ' . $esc($first['when']) : ''; ?></p>
  </figure>
  <?php endif; ?>
</div>
