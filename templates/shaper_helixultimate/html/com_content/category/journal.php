<?php
/**
 * raymondsbrowniii.com: The Journal (alternative category layout).
 * Featured post on top, category chips (filter in the browser), card grid.
 * Members-only posts (access level "Members") show as teasers when the menu item has
 * "Show Unauthorised Links" on.
 */
defined('_JEXEC') or die;

// From the Walk styles + script, version-stamped so browsers never use a stale cached copy
$rsbWa = \Joomla\CMS\Factory::getApplication()->getDocument()->getWebAssetManager();
if (!$rsbWa->assetExists('style', 'rsb.walk')) {
    $rsbWa->registerAndUseStyle('rsb.walk', 'templates/shaper_helixultimate/css/walk.css', ['version' => '20261123a']);
    $rsbWa->registerAndUseScript('rsb.walk', 'templates/shaper_helixultimate/js/walk.js', ['version' => '20261123a'], ['defer' => true]);
}

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;

$items = $this->items;
$user  = Factory::getApplication()->getIdentity();

// Featured = newest item flagged Featured, otherwise the newest item
$featured = null;
foreach ($items as $i => $it) { if ((int) $it->featured === 1) { $featured = $it; unset($items[$i]); break; } }
if (!$featured && $items) { $featured = array_shift($items); }

// Chips: categories that actually have posts, in the order they appear
// Chips: the two series. Walk entries are articles whose alias starts with "from-the-walk-"; everything else is Notes from the Studio
$cats = [];
foreach (array_merge($featured ? [$featured] : [], $items) as $it) {
    if (preg_match('/^from-the-walk-[0-9]/', $it->alias)) { $cats['from-the-walk'] = 'From the Walk'; } else { $cats['notes-from-the-studio'] = 'Notes from the Studio'; }
}
ksort($cats);

$feed = Route::_('index.php?option=com_content&view=category&id=' . (int) $this->category->id . '&format=feed&type=rss');
?>
<div class="rsb-journal">
  <header class="rsb-pagehead">
    <p class="rsb-kicker">The Journal · <a href="<?php echo $feed; ?>">RSS</a></p>
    <h1 class="rsb-h1"><?php echo $this->escape($this->params->get('show_page_heading') ? $this->params->get('page_heading') : 'From the Mind of Ray'); ?></h1>
    <p class="rsb-lead">Morning reflections recorded on foot, notes from the studio, and the stories behind the work.</p>
  </header>

  <?php if ($featured) : $this->item = $featured; $this->rsbFeatured = true; echo $this->loadTemplate('item'); $this->rsbFeatured = false; endif; ?>

  <?php if (count($cats) > 1) : ?>
  <nav class="rsb-chips" aria-label="Filter posts by series" data-rsb-filter>
    <button type="button" class="rsb-chip is-on" data-cat="" aria-pressed="true">All</button>
    <?php foreach ($cats as $alias => $title) : ?>
      <button type="button" class="rsb-chip" data-cat="<?php echo $this->escape($alias); ?>" aria-pressed="false"><?php echo $this->escape($title); ?></button>
    <?php endforeach; ?>
  </nav>
  <?php endif; ?>

  <div class="rsb-journal__grid" data-rsb-grid>
    <?php foreach ($items as $it) : $this->item = $it; echo $this->loadTemplate('item'); endforeach; ?>
  </div>

  <?php if ($this->pagination->pagesTotal > 1) : ?>
    <div class="rsb-pager"><?php echo $this->pagination->getPagesLinks(); ?></div>
  <?php endif; ?>
</div>
<script>
document.querySelectorAll('[data-rsb-filter]').forEach(function (nav) {
  var grid = document.querySelector('[data-rsb-grid]');
  nav.addEventListener('click', function (e) {
    var b = e.target.closest('button[data-cat]'); if (!b) return;
    nav.querySelectorAll('button').forEach(function (x) { x.classList.toggle('is-on', x === b); x.setAttribute('aria-pressed', x === b ? 'true' : 'false'); });
    grid.querySelectorAll('[data-cat]').forEach(function (card) { card.hidden = b.dataset.cat !== '' && card.dataset.cat !== b.dataset.cat; });
  });
});
</script>
