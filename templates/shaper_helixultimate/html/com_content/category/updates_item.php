<?php
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\Component\Content\Site\Helper\RouteHelper;

$item  = $this->item;
$link  = Route::_(RouteHelper::getArticleRoute($item->slug, $item->catid, $item->language));
$tags  = (isset($item->tags) && !empty($item->tags->itemTags)) ? $item->tags->itemTags : [];
$brand = null; $kind = 'Update';
foreach ($tags as $t) {
    if ($t->alias === 'site-work') { $kind = 'Site work'; continue; }
    if (!$brand) { $brand = $t; }
}
$images = json_decode($item->images ?? '{}');
$img    = !empty($images->image_intro) ? HTMLHelper::_('cleanImageURL', $images->image_intro) : null;
$date   = $item->publish_up ?: $item->created;
?>
<article class="rsb-update">
  <div class="rsb-update__meta">
    <time datetime="<?php echo HTMLHelper::_('date', $date, 'c'); ?>"><?php echo HTMLHelper::_('date', $date, 'M j, Y'); ?></time>
    <?php if ($brand) : ?><span class="rsb-brand rsb-brand--<?php echo $this->escape($brand->alias); ?>"><?php echo $this->escape($brand->title); ?></span><?php endif; ?>
    <span class="rsb-kind"><?php echo $kind; ?></span>
  </div>
  <div class="rsb-update__body">
    <h2 class="rsb-update__title"><a href="<?php echo $link; ?>"><?php echo $this->escape($item->title); ?></a></h2>
    <div class="rsb-update__text"><?php echo $item->introtext; ?></div>
    <?php if ($img) : ?><img class="rsb-update__img" src="<?php echo $this->escape($img->url); ?>" alt="<?php echo $this->escape($images->image_intro_alt ?? ''); ?>" loading="lazy"><?php endif; ?>
  </div>
</article>
