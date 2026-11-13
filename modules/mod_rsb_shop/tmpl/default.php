<?php
\defined('_JEXEC') or die;
use Joomla\CMS\Uri\Uri;
// Fallback when the store can't be reached and nothing is cached
if (empty($products)) {
    $products = [['name' => 'The King backpack', 'price' => '', 'url' => rtrim($params->get('store_url', 'https://shop.raydraws.com'), '/'), 'img' => Uri::root(true) . '/images/worlds/bp-king-red.jpg', 'own' => true]];
}
$first = $products[0];
$id    = 'rsb-shop-' . $module->id;
$store = rtrim($params->get('store_url', 'https://shop.raydraws.com'), '/');
?>
<a class="rsb-shop" id="<?php echo $id; ?>" href="<?php echo htmlspecialchars($first['url'], ENT_QUOTES, 'UTF-8'); ?>">
  <span class="rsb-shop__product<?php echo $first['own'] ? '' : ' rsb-shop__product--light'; ?>">
    <img src="<?php echo htmlspecialchars($first['img'], ENT_QUOTES, 'UTF-8'); ?>" alt="<?php echo htmlspecialchars($first['name'], ENT_QUOTES, 'UTF-8'); ?>">
    <span class="rsb-shop__label"><?php echo htmlspecialchars($first['name'] . ($first['price'] ? ' · ' . $first['price'] : ''), ENT_QUOTES, 'UTF-8'); ?></span>
  </span>
  <span class="rsb-shop__text">
    <span class="rsb-kicker"><?php echo htmlspecialchars($params->get('kicker', 'SHOP · shop.raydraws.com'), ENT_QUOTES, 'UTF-8'); ?></span>
    <h3><?php echo htmlspecialchars($params->get('headline', 'Prints, originals, apparel and beats in one cart'), ENT_QUOTES, 'UTF-8'); ?></h3>
    <strong>Shop all →</strong>
  </span>
</a>
<script>
(function () {
  var p = <?php echo json_encode($products, JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP); ?>;
  var x = p[Math.floor(Math.random() * p.length)], a = document.getElementById(<?php echo json_encode($id); ?>);
  if (!a || !x) return;
  a.href = x.url;
  var box = a.querySelector('.rsb-shop__product'), img = box.querySelector('img');
  img.src = x.img; img.alt = x.name;
  box.classList.toggle('rsb-shop__product--light', !x.own);
  box.querySelector('.rsb-shop__label').textContent = x.name + (x.price ? ' · ' + x.price : '');
})();
</script>
