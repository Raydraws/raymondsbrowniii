<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('JPATH_BASE') or die;

$icon = empty($displayData['icon']) ? 'generic' : preg_replace('#\.[^ .]*$#', '', $displayData['icon']);
?>
<h1 class="page-title">
	<span class="icon-<?php echo $icon; ?>" aria-hidden="true"></span>
	<?php echo $displayData['title']; ?>
</h1>
