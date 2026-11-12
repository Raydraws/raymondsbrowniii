<?php
/**
 * @package     Poweradmin
 * @subpackage  com_config
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;

defined('_JEXEC') or die;

$this->name = Text::_('COM_CONFIG_METADATA_SETTINGS');
$this->fieldsname = 'metadata';
$this->formclass = 'options-grid-form options-grid-form-half mb-4';

echo LayoutHelper::render('joomla.content.options_default', $this);
