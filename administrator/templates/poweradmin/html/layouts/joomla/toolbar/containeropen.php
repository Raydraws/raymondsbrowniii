<?php
/**
 * @package     Joomla.Site
 * @subpackage  Layout
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('JPATH_BASE') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

HTMLHelper::_('script', 'legacy/toolbar.min.js', array('version' => 'auto', 'relative' => true));
?>
<nav aria-label="<?php echo Text::_('JTOOLBAR'); ?>">
<div class="btn-toolbar d-flex" role="toolbar" id="<?php echo $displayData['id']; ?>">