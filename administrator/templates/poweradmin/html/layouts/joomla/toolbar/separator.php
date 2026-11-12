<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('JPATH_BASE') or die;

/**
 * @var  bool    $is_child
 * @var  string  $id
 * @var  string  $doTask
 * @var  string  $class
 * @var  string  $text
 * @var  string  $btnClass
 * @var  string  $tagName
 * @var  string  $htmlAttributes
 */
extract($displayData, EXTR_OVERWRITE);
?>

<?php if ($is_child): ?>
	<?php if (!empty($text)): ?>
		<h6 class="dropdown-header">
			<?php echo $text; ?>
		</h6>
	<?php else: ?>
		<div class="dropdown-divider"></div>
	<?php endif; ?>
<?php else: ?>
	<!-- Separator in main toolbar (invisible spacing) -->
<?php endif; ?>
