<?php
/** raymondsbrowniii.com homepage: newest Journal posts as a compact list with covers. */
defined('_JEXEC') or die;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
if (!$list) { return; }
$items = is_array(reset($list)) ? reset($list) : $list;
?>
<div class="rsb-card rsb-homejournal">
  <div class="rsb-homecard__head"><span class="rsb-kicker">THE JOURNAL</span><a href="<?php echo Route::_('index.php?Itemid=134'); ?>">Read all</a></div>
  <?php foreach ($items as $item) :
      $images = json_decode($item->images ?? '{}');
      $src = $images->image_intro ?? '';
      $img = $src ? HTMLHelper::_('cleanImageURL', $src) : null;
      $words = str_word_count(strip_tags(($item->introtext ?? '') . ($item->fulltext ?? '')));
      $date = $item->publish_up ?: $item->created;
  ?>
    <a class="rsb-homejournal__item" href="<?php echo $item->link; ?>">
      <?php if ($img) : ?><img src="<?php echo htmlspecialchars($img->url, ENT_QUOTES, 'UTF-8'); ?>" alt="" loading="lazy"><?php endif; ?>
      <span><span class="rsb-homejournal__meta"><?php echo HTMLHelper::_('date', $date, 'M j, Y'); ?> · <?php echo max(1, (int) round($words / 220)); ?> min read</span>
      <span class="rsb-homejournal__title"><?php echo htmlspecialchars($item->title, ENT_QUOTES, 'UTF-8'); ?></span></span>
    </a>
  <?php endforeach; ?>
</div>
