<?php
/** raymondsbrowniii.com homepage: NOW bar = title of the newest Updates article. */
defined('_JEXEC') or die;
if (!$list) { return; }
$items = is_array(reset($list)) ? reset($list) : $list;
$item  = reset($items);
?>
<a class="rsb-now" href="<?php echo $item->link; ?>">
  <span class="rsb-now__label">NOW</span>
  <span class="rsb-now__text"><?php echo htmlspecialchars($item->title, ENT_QUOTES, 'UTF-8'); ?></span>
</a>
