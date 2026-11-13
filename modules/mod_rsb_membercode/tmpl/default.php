<?php
\defined('_JEXEC') or die;
if (empty($code) || $code->code === '') { return; }
$c     = htmlspecialchars($code->code, ENT_QUOTES, 'UTF-8');
$url   = rtrim($params->get('store_url', 'https://shop.raydraws.com'), '/') . '/discount/' . rawurlencode($code->code);
$ends  = $code->ends->format('l, M j');
if ($params->get('style', 'card') === 'link') : ?>
<div class="rsb-links rsb-links__members">
  <a class="rsb-link" href="<?php echo $url; ?>"><span class="rsb-link__text"><span class="rsb-link__title"><?php echo $c; ?> · <?php echo htmlspecialchars($params->get('short_terms'), ENT_QUOTES, 'UTF-8'); ?></span><span class="rsb-link__sub">Good through <?php echo $code->ends->format('M j'); ?> · applies at checkout</span></span><span class="rsb-link__badge">Members</span></a>
</div>
<?php else : ?>
<div class="rsb-card rsb-card--dark rsb-members"><p class="rsb-kicker">MEMBERS · CODE OF THE MOMENT</p><h2><?php echo $c; ?></h2><p><?php echo htmlspecialchars($params->get('terms'), ENT_QUOTES, 'UTF-8'); ?> Good through <?php echo $ends; ?>.</p><div class="rsb-members__actions"><a class="rsb-btn rsb-btn--gold" href="<?php echo $url; ?>">Shop with the code</a><a class="rsb-btn rsb-btn--ghost" href="<?php echo \Joomla\CMS\Router\Route::_('index.php?Itemid=137'); ?>">More member links</a></div></div>
<?php endif;
