<?php
/**
 * @package     Poweradmin
 * @subpackage  mod_multilangstatus
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;


// Load the webcomponent helper from template override
$webcomponentPath = JPATH_ADMINISTRATOR . '/templates/poweradmin/html/webcomponent.php';

if (file_exists($webcomponentPath)) {
	require_once $webcomponentPath;
}
?>

<div class="multilanguage">
	<a class="d-flex align-items-stretch header-item-link" data-href="#multiLangModal" href="#" title="<?php echo Text::_('MOD_MULTILANGSTATUS'); ?>" data-toggle="modal" role="button">
		<div class="d-none d-xl-block mr-2">
			<?php echo Text::_('MOD_MULTILANGSTATUS'); ?>
		</div>
		<div>
			<span class="icon-multilingual" aria-hidden="true"></span>
		</div>
	</a>

	<?php
	echo JHtmlWebcomponent::renderModal(
		'multiLangModal',
		array(
			'title'      => Text::_('MOD_MULTILANGSTATUS'),
			'url'        => Route::_('index.php?option=com_languages&view=multilangstatus&tmpl=component'),
			'height'     => '75vh',
			'width'      => '85vw',
			'bodyHeight' => 70,
			'modalWidth' => 80,
			'footer'     => '<button type="button" class="btn btn-secondary" data-dismiss="modal">' . Text::_('JTOOLBAR_CLOSE') . '</button>',
			'appendToBody' => true,
		)
	);
	?>
</div>
