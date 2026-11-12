<?php
/**
 * @package     Poweradmin
 * @subpackage  com_templates
 *
 * @copyright   Copyright (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license     http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

defined('_JEXEC') or die;
ksort($this->files, SORT_STRING);
?>

<ul class="directory-tree treeselect">
	<?php foreach($this->files as $key => $value) : ?>
		<?php if (is_array($value)) : ?>
			<li class="folder-select has-subtree">
				<a class="folder-url" data-id="<?php echo base64_encode($key); ?>" href="">
					<span class="icon-folder-2" aria-hidden="true"></span>
					<?php $explodeArray = explode('/', $key); echo $this->escape(end($explodeArray)); ?>
				</a>
				<?php echo $this->folderTree($value); ?>
			</li>
		<?php endif; ?>
	<?php endforeach; ?>
</ul>
