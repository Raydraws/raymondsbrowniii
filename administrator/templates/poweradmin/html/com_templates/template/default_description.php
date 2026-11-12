<?php
/**
 * @package     Poweradmin
 * @subpackage  com_templates
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Application\ApplicationHelper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\Component\Templates\Administrator\Helper\TemplatesHelper;

?>

<div class="clearfix">
	<div class="float-left mr-1 text-center">
		<?php if (!empty($this->template->thumbnail)) : ?>
		<?php echo HTMLHelper::_('templates.thumb', $this->template->element, $this->template->client_id); ?>
		<?php endif ?>
		<?php if (!empty($this->template->thumbnail)) : ?>
		<?php echo HTMLHelper::_('templates.thumbModal', $this->template->element, $this->template->client_id); ?>
		<?php endif ?>
	</div>
	<h2><?php echo ucfirst($this->template->element); ?></h2>
	<?php $client = ApplicationHelper::getClientInfo($this->template->client_id); ?>
	<p><?php $this->template->xmldata = TemplatesHelper::parseXMLTemplateFile($client->path, $this->template->element); ?></p>
	<p><?php echo Text::_($this->template->xmldata->description); ?></p>
</div>
