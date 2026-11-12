<?php
/**
 * @package     Poweradmin
 * @subpackage  com_config
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;

defined('_JEXEC') or die;

HTMLHelper::_('form.csrf');
HTMLHelper::_('script', 'system/fields/joomla-field-send-test-mail.min.js', ['version' => 'auto', 'relative' => true]);

// Load JavaScript message titles
Text::script('ERROR');
Text::script('WARNING');
Text::script('NOTICE');
Text::script('MESSAGE');

// Add strings for JavaScript error translations.
Text::script('JLIB_JS_AJAX_ERROR_CONNECTION_ABORT');
Text::script('JLIB_JS_AJAX_ERROR_NO_CONTENT');
Text::script('JLIB_JS_AJAX_ERROR_OTHER');
Text::script('JLIB_JS_AJAX_ERROR_PARSE');
Text::script('JLIB_JS_AJAX_ERROR_TIMEOUT');

// Ajax request data.
$ajaxUri = Route::_('index.php?option=com_config&task=application.sendtestmail&format=json&' . Session::getFormToken() . '=1');

$this->name = Text::_('COM_CONFIG_MAIL_SETTINGS');
$this->fieldsname = 'mail';
$this->formclass = 'options-grid-form options-grid-form-half';

?>

<joomla-field-send-test-mail uri="<?php echo $ajaxUri; ?>">
	<?php echo LayoutHelper::render('joomla.content.options_default', $this); ?>

	<button class="btn btn-primary" type="button" id="sendtestmail">
		<span><?php echo Text::_('COM_CONFIG_SENDMAIL_ACTION_BUTTON'); ?></span>
	</button>
</joomla-field-send-test-mail>
