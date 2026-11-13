<?php
/**
 * raymondsbrowniii.com: The Journal (alternative category layout).
 * Featured post on top, category chips (filter in the browser), card grid.
 * Members-only posts (access level "Members") show as teasers when the menu item has
 * "Show Unauthorised Links" on.
 */
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Router\Route;

$items = $this->items;
$user  = Factory::getApplication()->getIdentity();

// Featured = newest item flagged Featured, otherwise the newest item
$featured = null;
foreach ($items as $i => $it) { if ((int) $it->featured === 1) { $featured = $it; unset($items[$i]); break; } }
if (!$featured && $items) { $featured = array_shift($items); }

// Chips: categories that actually have posts, in the order they appear
$cats = [];
foreach (array_merge($featured ? [$featured] : [], $items) as $it) { $cats[$it->category_alias] = $it->category_title; }
unset($cats[$this->category->alias]);

$feed = Route::_('index.php?option=com_content&view=category&id=' . (int) $this->category->id . '&format=feed&type=rss');
?>
<div class="rsb-journal">
  <header class="rsb-pagehead">
    <p class="rsb-kicker">The Journal · <a href="<?php echo $feed; ?>">RSS</a></p>
    <h1 class="rsb-h1"><?php echo $this->escape($this->params->get('show_page_heading') ? $this->params->get('page_heading') : 'Notes from the studio'); ?></h1>
    <p class="rsb-lead">Shows, process, the stories behind the pieces, and what I’m learning running a one-person studio.</p>
  </header>

  <?php if ($featured) : $this->item = $featured; $this->rsbFeatured = true; echo $this->loadTemplate('item'); $this->rsbFeatured = false; endif; ?>

  <?php if (count($cats) > 1) : ?>
  <nav class="rsb-chips" aria-label="Filter posts by category" data-rsb-filter>
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
