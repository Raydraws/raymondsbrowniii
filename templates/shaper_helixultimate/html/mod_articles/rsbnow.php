<?php
/**
 * raymondsbrowniii.com homepage: "From the Walk" strip.
 * Shows the newest walk Journal entry (any published article whose alias starts with "from-the-walk-" plus its number, e.g. from-the-walk-001-...),
 * with Previous / Next to step through earlier ones. The short text is the article's meta description.
 * Falls back to the old NOW bar (title of the newest Updates article) when there are no walk entries.
 */
defined('_JEXEC') or die;

// From the Walk styles + script, version-stamped so browsers never use a stale cached copy
$rsbWa = \Joomla\CMS\Factory::getApplication()->getDocument()->getWebAssetManager();
if (!$rsbWa->assetExists('style', 'rsb.walk')) {
    $rsbWa->registerAndUseStyle('rsb.walk', 'templates/shaper_helixultimate/css/walk.css', ['version' => '20261123a']);
    $rsbWa->registerAndUseScript('rsb.walk', 'templates/shaper_helixultimate/js/walk.js', ['version' => '20261123a'], ['defer' => true]);
}

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\Component\Content\Site\Helper\RouteHelper;
use Joomla\Database\DatabaseInterface;

$walk = [];
try {
    $db  = Factory::getContainer()->get(DatabaseInterface::class);
    $now = Factory::getDate()->toSql();
    $q   = $db->getQuery(true)
        ->select($db->quoteName(['id', 'alias', 'catid', 'title', 'metadesc', 'introtext', 'publish_up', 'created', 'language']))
        ->from($db->quoteName('#__content'))
        ->where($db->quoteName('state') . ' = 1')
        ->where($db->quoteName('alias') . ' REGEXP ' . $db->quote('^from-the-walk-[0-9]+'))
        ->where('(' . $db->quoteName('publish_up') . ' IS NULL OR ' . $db->quoteName('publish_up') . ' <= ' . $db->quote($now) . ')')
        ->order($db->quoteName('publish_up') . ' DESC, ' . $db->quoteName('id') . ' DESC');
    $db->setQuery($q, 0, 60);
    $walk = $db->loadObjectList() ?: [];
} catch (\Throwable $e) {
    $walk = [];
}

if (!$walk) {
    if (!$list) { return; }
    $items = is_array(reset($list)) ? reset($list) : $list;
    $item  = reset($items);
    ?>
<a class="rsb-now" href="<?php echo $item->link; ?>">
  <span class="rsb-now__label">NOW</span>
  <span class="rsb-now__text"><?php echo htmlspecialchars($item->title, ENT_QUOTES, 'UTF-8'); ?></span>
</a>
    <?php
    return;
}

$entries = [];
foreach ($walk as $r) {
    $text = trim((string) $r->metadesc);
    if ($text === '') {
        $text = trim(preg_replace('/\s+/', ' ', strip_tags((string) $r->introtext)));
        if (mb_strlen($text) > 320) { $text = mb_substr($text, 0, 317) . '…'; }
    }
    $when = $r->publish_up ?: $r->created;
    $no   = preg_match('/^from-the-walk-(\d+)/', $r->alias, $m) ? ' · No. ' . (int) $m[1] : '';
    $entries[] = [
        'link' => Route::_(RouteHelper::getArticleRoute($r->id . ':' . $r->alias, $r->catid, $r->language)),
        'text' => $text,
        'date' => HTMLHelper::_('date', $when, 'D, M j, Y') . $no,
        'iso'  => HTMLHelper::_('date', $when, 'Y-m-d'),
    ];
}
$first = $entries[0];
$json  = json_encode($entries, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_UNESCAPED_UNICODE);
$more  = count($entries) > 1;
?>
<section class="rsb-walk" aria-label="From the Walk" data-rsb-walk="<?php echo htmlspecialchars($json, ENT_QUOTES, 'UTF-8'); ?>">
  <a class="rsb-walk__entry" href="<?php echo $first['link']; ?>">
    <span class="rsb-walk__label"><svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="13" cy="4" r="2"></circle><path d="M10 21l2-6-3-3 1-5 4 3 3 1"></path><path d="M10 7l-3 2-1 3"></path><path d="M12 15l3 2 1 4"></path></svg>FROM THE WALK</span>
    <span class="rsb-walk__text" aria-live="polite"><?php echo htmlspecialchars($first['text'], ENT_QUOTES, 'UTF-8'); ?></span>
  </a>
  <div class="rsb-walk__bar">
    <time class="rsb-walk__date" datetime="<?php echo $first['iso']; ?>"><?php echo htmlspecialchars($first['date'], ENT_QUOTES, 'UTF-8'); ?></time>
    <div class="rsb-walk__nav">
      <button type="button" class="rsb-walk__btn" data-dir="1"<?php echo $more ? '' : ' disabled'; ?>>‹ Previous</button>
      <button type="button" class="rsb-walk__btn" data-dir="-1" disabled>Next ›</button>
      <a class="rsb-walk__all" href="<?php echo Route::_('index.php?Itemid=134'); ?>">Read the journal</a>
    </div>
  </div>
</section>
