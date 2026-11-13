<?php
/** raymondsbrowniii.com homepage: "Latest" box = newest Updates articles. */
defined('_JEXEC') or die;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
if (!$list) { return; }
$items = is_array(reset($list)) ? reset($list) : $list;
?>
<aside class="rsb-latest" aria-labelledby="rsb-latest-h">
  <div class="rsb-latest__head"><h2 id="rsb-latest-h">Latest</h2><a href="<?php echo Route::_('index.php?Itemid=133'); ?>">All updates</a></div>
  <ol>
  <?php foreach ($items as $item) :
      $brand = '';
      if (!empty($item->tags->itemTags)) {
          foreach ($item->tags->itemTags as $t) { if ($t->alias !== 'site-work') { $brand = $t->title; break; } }
      }
      $date = $item->publish_up ?: $item->created;
  ?>
    <li>
      <span class="rsb-latest__meta"><time datetime="<?php echo HTMLHelper::_('date', $date, 'c'); ?>"><?php echo HTMLHelper::_('date', $date, 'M j'); ?></time><?php if ($brand) : ?><span class="rsb-brand rsb-brand--<?php echo htmlspecialchars($t->alias, ENT_QUOTES, 'UTF-8'); ?>"><?php echo htmlspecialchars($brand, ENT_QUOTES, 'UTF-8'); ?></span><?php endif; ?></span>
      <a href="<?php echo $item->link; ?>"><?php echo htmlspecialchars($item->title, ENT_QUOTES, 'UTF-8'); ?></a>
    </li>
  <?php endforeach; ?>
  </ol>
</aside>
