<?php
\defined('_JEXEC') or die;
if (empty($code) || $code->code === '') { return; }
$c     = htmlspecialchars($code->code, ENT_QUOTES, 'UTF-8');
$url   = rtrim($params->get('store_url', 'https://shop.raydraws.com'), '/') . '/discount/' . rawurlencode($code->code);
$fixed = !empty($code->fixed);
$terms = $fixed ? $params->get('fixed_terms', '15% off any order of $40 or more at shop.raydraws.com.') : $params->get('terms');
$short = $fixed ? $params->get('fixed_short_terms', '15% off orders $40+') : $params->get('short_terms');
$sub   = $fixed ? 'Applies at checkout' : 'Good through ' . $code->ends->format('M j') . ' · applies at checkout';
$until = $fixed ? '' : ' Good through ' . $code->ends->format('l, M j') . '.';
if ($params->get('style', 'card') === 'link') : ?>
<div class="rsb-links rsb-links__members">
  <a class="rsb-link" href="<?php echo $url; ?>"><span class="rsb-link__text"><span class="rsb-link__title"><?php echo $c; ?> · <?php echo htmlspecialchars($short, ENT_QUOTES, 'UTF-8'); ?></span><span class="rsb-link__sub"><?php echo $sub; ?></span></span><span class="rsb-link__badge">Members</span></a>
</div>
<?php else : ?>
<div class="rsb-card rsb-card--dark rsb-members"><p class="rsb-kicker"><?php echo $fixed ? 'MEMBERS · YOUR CODE' : 'MEMBERS · CODE OF THE MOMENT'; ?></p><h2><?php echo $c; ?></h2><p><?php echo htmlspecialchars($terms, ENT_QUOTES, 'UTF-8'); ?><?php echo $until; ?></p><div class="rsb-members__actions"><a class="rsb-btn rsb-btn--gold" href="<?php echo $url; ?>">Shop with the code</a><a class="rsb-btn rsb-btn--ghost" href="<?php echo \Joomla\CMS\Router\Route::_('index.php?Itemid=137'); ?>">More member links</a></div></div>
<?php endif;
