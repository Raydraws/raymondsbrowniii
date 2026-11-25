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
// Series: walk entries (alias from-the-walk-*) vs Notes from the Studio
$walk     = (bool) preg_match('/^from-the-walk-[0-9]/', $item->alias);
if ($walk) { $cls .= ' rsb-post--walk'; }
$series   = $walk ? 'from-the-walk' : 'notes-from-the-studio';
if ($walk && $featured && $img && strpos($img->url, '-square.') !== false) { $img->url = str_replace('-square.', '-wide.', $img->url); }
$metaline = $walk ? 'From the Walk' : 'Notes from the Studio' . ($item->category_alias !== 'journal' ? ' · ' . $item->category_title : '');
$walkicon = '<svg aria-hidden="true" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="13" cy="4" r="2"></circle><path d="M10 21l2-6-3-3 1-5 4 3 3 1"></path><path d="M10 7l-3 2-1 3"></path><path d="M12 15l3 2 1 4"></path></svg>';
?>
<a class="<?php echo $cls; ?>" href="<?php echo $link; ?>" data-cat="<?php echo $series; ?>">
  <span class="rsb-post__media">
    <?php if ($img) : ?><img src="<?php echo $this->escape($img->url); ?>" alt="" loading="<?php echo $featured ? 'eager' : 'lazy'; ?>"><?php endif; ?>
    <?php if (!$canView) : ?><span class="rsb-post__lock">Members</span><?php endif; ?>
    <?php if ($walk) : ?><span class="rsb-walkbadge"><?php echo $walkicon; ?>MORNING WALK</span><?php endif; ?>
  </span>
  <span class="rsb-post__body">
    <span class="rsb-post__meta"><?php echo $featured ? 'Featured · ' : ''; ?><?php echo $this->escape($metaline); ?> · <?php echo HTMLHelper::_('date', $date, 'M j, Y'); ?></span>
    <span class="rsb-post__title"><?php echo $this->escape($item->title); ?></span>
    <span class="rsb-post__excerpt"><?php echo $this->escape($excerpt); ?></span>
    <span class="rsb-post__more"><?php echo $canView ? $mins . ' min read →' : 'Sign in to read →'; ?></span>
  </span>
</a>
