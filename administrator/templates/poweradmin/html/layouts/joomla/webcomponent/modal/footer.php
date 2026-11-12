<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 *
 * @since  4.0.0
 */

defined('JPATH_BASE') or die;

/**
 * Layout variable
 * ------------------
 * @param   array   $params    Modal parameters.
 *                  - footer   string   Optional markup for the modal footer
 */

extract($displayData);
?>
<footer>
	<?php echo $params['footer']; ?>
</footer>