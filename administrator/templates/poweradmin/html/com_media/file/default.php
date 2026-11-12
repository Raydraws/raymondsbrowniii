<?php
/**
 * @package     Poweradmin
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Uri\Uri;

$poweradminUiTabPath = JPATH_ADMINISTRATOR . '/templates/poweradmin/html/poweradminuitab.php';
if (file_exists($poweradminUiTabPath)) {
	require_once $poweradminUiTabPath;
}

// Add javascripts
HTMLHelper::_('behavior.core');
HTMLHelper::_('behavior.formvalidator');

/** @var Joomla\CMS\WebAsset\WebAssetManager $wa */
$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')
    ->useScript('form.validate')
    ->useStyle('com_media.mediamanager');

$script = $wa->getAsset('script', 'com_media.edit-images')->getUri(true);

$wa->usePreset('cropperjs');
$params = ComponentHelper::getParams('com_media');

/**
 * @var JForm $form
 */
$form = $this->form;

$tmpl = Factory::getApplication()->input->getCmd('tmpl');

// Load the toolbar when we are in an iframe
if ($tmpl == 'component')
{
	echo Toolbar::getInstance('toolbar')->render();
}

// Populate the media config
$config = [
	'apiBaseUrl'              => Uri::root() . 'administrator/index.php?option=com_media&format=json',
	'csrfToken'               => Session::getFormToken(),
	'uploadPath'              => $this->file->path,
	'editViewUrl'             => Uri::root() . 'administrator/index.php?option=com_media&view=file' . (!empty($tmpl) ? ('&tmpl=' . $tmpl) : ''),
	'allowedUploadExtensions' => $params->get('upload_extensions', ''),
	'maxUploadSizeMb'         => $params->get('upload_maxsize', 10),
	'contents'                => $this->file->content,
];

$this->document->addScriptOptions('com_media', $config);

$this->useCoreUI = true;
?>
<div class="row">
	<form action="#" method="post" name="adminForm" id="media-form" class="form-validate col-md-12">
	<?php $fieldSets = $form->getFieldsets(); ?>
	<?php if ($fieldSets) : ?>
		<?php echo JHtmlPoweradminUiTab::startTabSet('myTab', array('active' => 'attrib-' . reset($fieldSets)->name)); ?>
		<?php echo '<div id="media-manager-edit-container" class="media-manager-edit d-flex justify-content-around form-validate col-md-9 p-4"></div>'; ?>
		<?php echo LayoutHelper::render('joomla.edit.params', $this); ?>
		<?php echo JHtmlPoweradminUiTab::endTabSet(); ?>
	<?php endif; ?>
	</form>
</div>
<script type="module" src="<?php echo $script . '?' . $this->document->getMediaVersion(); ?>"></script>
