<?php
/**
 * raymondsbrowniii.com: Updates timeline (alternative category layout).
 * Pick it on the menu item: Menu item > Advanced/Options > Layout = "updates".
 */
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\CMS\Router\Route;

$app    = Factory::getApplication();
$db     = Factory::getContainer()->get('DatabaseDriver');
$catId  = (int) $this->category->id;
$active = (int) $app->getInput()->getInt('filter_tag', 0);
$base   = Route::_('index.php?option=com_content&view=category&layout=blog&id=' . $catId);
$sep    = strpos($base, '?') === false ? '?' : '&';

// Brand tags (published, top level) for the filter chips
$tags = $db->setQuery(
    $db->getQuery(true)
        ->select($db->quoteName(['id', 'title', 'alias']))
        ->from($db->quoteName('#__tags'))
        ->where($db->quoteName('published') . ' = 1')
        ->where($db->quoteName('parent_id') . ' = 1')
        ->order($db->quoteName('lft'))
)->loadObjectList();

// Live stat: updates published this month in this category
$monthStart = Factory::getDate('first day of this month 00:00:00')->toSql();
$thisMonth  = (int) $db->setQuery(
    $db->getQuery(true)
        ->select('COUNT(*)')
        ->from($db->quoteName('#__content'))
        ->where($db->quoteName('catid') . ' = ' . $catId)
        ->where($db->quoteName('state') . ' = 1')
        ->where($db->quoteName('publish_up') . ' >= ' . $db->quote($monthStart))
)->loadResult();
$brands = count(array_filter($tags, fn($t) => !in_array($t->alias, ['site-work', 'hub'], true)));

// RSS follows the current filter (brand feed when a chip is on, everything otherwise)
$feed = Route::_('index.php?option=com_content&view=category&id=' . $catId . '&format=feed&type=rss&filter_tag=' . $active);
?>
<div class="rsb-updates">
  <header class="rsb-pagehead">
    <p class="rsb-kicker">Updates · RSS · Email digest</p>
    <h1 class="rsb-h1"><?php echo $this->escape($this->params->get('page_heading', 'What’s new')); ?></h1>
    <?php if ($this->category->description) : ?>
      <div class="rsb-lead"><?php echo $this->category->description; ?></div>
    <?php else : ?>
      <p class="rsb-lead">Everything I ship across the studio, logged as it happens. Follow by brand, grab the RSS, or get the weekly digest.</p>
    <?php endif; ?>
  </header>

  <nav class="rsb-chips" aria-label="Filter updates by brand">
    <a class="rsb-chip<?php echo $active ? '' : ' is-on'; ?>" href="<?php echo $base . $sep; ?>filter_tag=0"<?php echo $active ? '' : ' aria-current="true"'; ?>>All</a>
    <?php foreach ($tags as $t) : $on = $active === (int) $t->id; ?>
      <a class="rsb-chip<?php echo $on ? ' is-on' : ''; ?>" href="<?php echo $base . $sep . 'filter_tag=' . (int) $t->id; ?>"<?php echo $on ? ' aria-current="true"' : ''; ?>><?php echo $this->escape($t->alias === 'site-work' ? 'Site work' : $t->title); ?></a>
    <?php endforeach; ?>
    <a class="rsb-chip rsb-chip--rss" href="<?php echo $feed; ?>"><?php echo $active ? 'RSS for this brand' : 'RSS'; ?></a>
  </nav>

  <div class="rsb-split">
    <div class="rsb-split__main">
      <?php if (empty($this->items)) : ?>
        <p class="rsb-empty">No updates here yet.</p>
      <?php endif; ?>
      <?php foreach ($this->items as $item) : $this->item = $item; echo $this->loadTemplate('item'); endforeach; ?>

      <?php if ($this->pagination->pagesTotal > 1) : ?>
        <div class="rsb-pager"><?php echo $this->pagination->getPagesLinks(); ?></div>
      <?php endif; ?>
    </div>

    <aside class="rsb-split__side">
      <div class="rsb-stats">
        <div class="rsb-card rsb-stat"><p class="rsb-stat__n"><?php echo $thisMonth; ?></p><p class="rsb-stat__l">updates this month</p></div>
        <div class="rsb-card rsb-stat"><p class="rsb-stat__n"><?php echo $brands; ?></p><p class="rsb-stat__l">brands in the family</p></div>
      </div>
      <?php foreach (ModuleHelper::getModules('updates-sidebar') as $m) : ?>
        <?php echo ModuleHelper::renderModule($m, ['style' => 'none']); ?>
      <?php endforeach; ?>
    </aside>
  </div>
</div>
