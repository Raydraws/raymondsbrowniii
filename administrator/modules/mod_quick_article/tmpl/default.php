<?php
/**
 * @package     Poweradmin
 * @subpackage  mod_quick_article
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Uri\Uri;

HTMLHelper::_('behavior.core');
HTMLHelper::_('behavior.keepalive');

Factory::getDocument()->addScriptOptions('saveurl', Uri::root() . 'administrator/index.php?option=com_ajax&module=quick_article&method=saveQuickArticle&format=json');
Factory::getDocument()->addScriptOptions('editurl', Uri::root() . 'administrator/index.php?option=com_content&task=article.edit&id=');

Factory::getDocument()->getWebAssetManager()->useScript('choicesjs');
Factory::getDocument()->getWebAssetManager()->useStyle('choicesjs');
Factory::getDocument()->getWebAssetManager()->useScript('webcomponent.field-fancy-select');

HTMLHelper::_('stylesheet', 'vendor/choicesjs/css/choices.css', ['version' => 'auto', 'relative' => true]);
HTMLHelper::_('script', 'vendor/choicesjs/js/choices.js', ['version' => 'auto', 'relative' => true]);

?>

<form class="j-card-body" name="quickArticleForm" id="form-quick-article" method="post" action="#">
	<fieldset>
		<div class="row">
			<div class="col-12 col-xl-6">
				<div class="form-group">
					<label for="mod-quick-article-title" >
						<?php echo Text::_('JGLOBAL_TITLE'); ?>
					</label>
					<div class="input-group">
						<input
							type="text"
							id="mod-quick-article-title"
							name="title"
							class="form-control input-full"
							required="required"
						>
					</div>
				</div>
			</div>
			<div class="col-12 col-xl-6">
				<div class="form-group">
					<label for="mod-quick-article-category">
						<?php echo Text::_('JGLOBAL_CATEGORY_OPTIONS'); ?>
					</label>
					<div class="input-group">
						<?php if(!empty($categoryField)) : ?>
							<joomla-field-fancy-select search-placeholder="<?php echo Text::_('MOD_QUICK_ARTICLE_SEARCH_CATEGORY'); ?>">
								<?php echo $categoryField; ?>
							</joomla-field-fancy-select>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>

		<div class="form-group">
			<label for="mod-quick-article-description">
				<?php echo Text::_('JGLOBAL_FIELDSET_CONTENT'); ?>
			</label>
			<div class="input-group">
				<textarea
					name="description"
					id="mod-quick-article-description"
					cols="10"
					rows="5"
					class="form-control input-full"
					max="500"
				></textarea>
			</div>
		</div>
		
		<div class="form-group text-right mt-4">
			<button class="btn btn-secondary" type="button" role="button" id="mod-quick-article-clear">
				<?php echo Text::_('MOD_QUICK_ARTICLE_CLEAR'); ?>
			</button>
			<button class="btn btn-primary ml-3" type="submit" role="button" id="mod-quick-article-submit">
				<?php echo Text::_('MOD_QUICK_ARTICLE_SAVE_ARTICLE'); ?>
			</button>
		</div>
	</fieldset>
</form>
