<?php
defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Router\Route;
use Joomla\Component\Content\Site\Helper\RouteHelper;

$item     = $this->item;
$featured = !empty($this->rsbFeatured);
$canView  = $item->params->get('access-view');
$link     = $canView
    ? Route::_(RouteHelper::getArticleRoute($item->slug, $item->catid, $item->language))
    : Route::_('index.php?option=com_users&view=login');
$images   = json_decode($item->images ?? '{}');
$src      = $featured ? ($images->image_fulltext ?? '') : '';
$src      = $src ?: ($images->image_intro ?? '');
$img      = $src ? HTMLHelper::_('cleanImageURL', $src) : null;
$date     = $item->publish_up ?: $item->created;
$words    = str_word_count(strip_tags($item->introtext . ($item->fulltext ?? '')));
$mins     = max(1, (int) round($words / 220));
$excerpt  = trim(strip_tags($item->introtext));
if (mb_strlen($excerpt) > 180) { $excerpt = mb_substr($excerpt, 0, 177) . '…'; }
$cls      = $featured ? 'rsb-post rsb-post--featured' : 'rsb-post';
?>
<a class="<?php echo $cls; ?>" href="<?php echo $link; ?>" data-cat="<?php echo $this->escape($item->category_alias); ?>">
  <span class="rsb-post__media">
    <?php if ($img) : ?><img src="<?php echo $this->escape($img->url); ?>" alt="" loading="<?php echo $featured ? 'eager' : 'lazy'; ?>"><?php endif; ?>
    <?php if (!$canView) : ?><span class="rsb-post__lock">Members</span><?php endif; ?>
  </span>
  <span class="rsb-post__body">
    <span class="rsb-post__meta"><?php echo $featured ? 'Featured · ' : ''; ?><?php echo $this->escape($item->category_title); ?> · <?php echo HTMLHelper::_('date', $date, 'M j, Y'); ?></span>
    <span class="rsb-post__title"><?php echo $this->escape($item->title); ?></span>
    <span class="rsb-post__excerpt"><?php echo $this->escape($excerpt); ?></span>
    <span class="rsb-post__more"><?php echo $canView ? $mins . ' min read →' : 'Sign in to read →'; ?></span>
  </span>
</a>
