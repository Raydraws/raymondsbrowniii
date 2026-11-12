<?php

/**
 * @package     Poweradmin
 * @subpackage  System.Poweradmin
 *
 * Override of the core Modal Category field to use Poweradmin webcomponent modals.
 *
 * @copyright   (C) 2010 - 2026 JoomShaper.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Joomla\Component\Categories\Administrator\Field\Modal;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\LanguageHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Session\Session;
use Joomla\Database\ParameterType;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;

if (!class_exists('JHtmlPoweradmin')) {
	require_once JPATH_ADMINISTRATOR . '/templates/poweradmin/Service/HTML/Poweradmin.php';
}
// phpcs:enable PSR1.Files.SideEffects

/**
 * Poweradmin override: supports a modal category picker rendered with the template webcomponent.
 *
 * @since  1.0.0
 */
class CategoryField extends FormField
{
    /**
     * The form field type.
     *
     * @var     string
     */
    protected $type = 'Modal_Category';

    /**
     * Method to get the field input markup.
     *
     * @return  string
     */
    protected function getInput()
    {
        $extension = $this->element['extension']
            ? (string) $this->element['extension']
            : (string) Factory::getApplication()->getInput()->get('extension', 'com_content');

        $allowNew       = ((string) $this->element['new'] == 'true');
        $allowEdit      = ((string) $this->element['edit'] == 'true');
        $allowClear     = ((string) $this->element['clear'] != 'false');
        $allowSelect    = ((string) $this->element['select'] != 'false');
        $allowPropagate = ((string) $this->element['propagate'] == 'true');

        $languages = LanguageHelper::getContentLanguages([0, 1], false);

        Factory::getLanguage()->load('com_categories', JPATH_ADMINISTRATOR);

        $value   = (int) $this->value ?: '';
        $modalId = 'Category_' . $this->id;

        // Load the Poweradmin webcomponent helper if available.
        $webcomponentPath = JPATH_ADMINISTRATOR . '/templates/poweradmin/html/webcomponent.php';

        if (file_exists($webcomponentPath)) {
            require_once $webcomponentPath;
        }

        /** @var \Joomla\CMS\Application\CMSApplication $app */
        $app = Factory::getApplication();

        /** @var \Joomla\CMS\Document\HtmlDocument $document */
        $document = $app->getDocument();

        /** @var \Joomla\CMS\WebAsset\WebAssetManager $wa */
        $wa = $document->getWebAssetManager();
        $wa->useScript('field.modal-fields');

        if ($allowSelect) {
            static $scriptSelect = null;

            if (is_null($scriptSelect)) {
                $scriptSelect = [];
            }

            if (!isset($scriptSelect[$this->id])) {
                $wa->addInlineScript(
                    "
				window.jSelectCategory_" . $this->id . " = function (id, title, object) {
					window.processModalSelect('Category', '" . $this->id . "', id, title, '', object);
				}",
                    [],
                    ['type' => 'module']
                );

                Text::script('JGLOBAL_ASSOCIATIONS_PROPAGATE_FAILED');

                $scriptSelect[$this->id] = true;
            }
        }

        $linkCategories = 'index.php?option=com_categories&amp;view=categories&amp;layout=modal&amp;tmpl=component&amp;'
            . Session::getFormToken() . '=1&amp;extension=' . $extension;
        $linkCategory  = 'index.php?option=com_categories&amp;view=category&amp;layout=modal&amp;tmpl=component&amp;'
            . Session::getFormToken() . '=1&amp;extension=' . $extension;
        $modalTitle    = Text::_('COM_CATEGORIES_SELECT_A_CATEGORY');

        if (isset($this->element['language'])) {
            $linkCategories .= '&amp;forcedLanguage=' . $this->element['language'];
            $linkCategory   .= '&amp;forcedLanguage=' . $this->element['language'];
            $modalTitle     .= ' &#8212; ' . $this->element['label'];
        }

        $urlSelect = $linkCategories . '&amp;function=jSelectCategory_' . $this->id;
        $urlEdit   = $linkCategory . '&amp;task=category.edit&amp;id=\' + document.getElementById("'
            . $this->id . '_id").value + \'';
        $urlNew    = $linkCategory . '&amp;task=category.add';

        if ($value) {
            $db    = $this->getDatabase();
            $query = $db->getQuery(true)
                ->select($db->quoteName('title'))
                ->from($db->quoteName('#__categories'))
                ->where($db->quoteName('id') . ' = :value')
                ->bind(':value', $value, ParameterType::INTEGER);
            $db->setQuery($query);

            try {
                $title = $db->loadResult();
            } catch (\RuntimeException $e) {
                Factory::getApplication()->enqueueMessage($e->getMessage(), 'error');
            }
        }

        $title = empty($title) ? Text::_('COM_CATEGORIES_SELECT_A_CATEGORY') : htmlspecialchars($title, ENT_QUOTES, 'UTF-8');

        $html  = '';

        if ($allowSelect || $allowNew || $allowEdit || $allowClear) {
            $html .= '<span class="input-group">';
        }

        $html .= '<input class="form-control" id="' . $this->id . '_name" type="text" value="' . $title . '" readonly size="35">';

        if ($allowSelect) {
            $html .= '<button'
                . ' class="btn btn-primary' . ($value ? ' hidden' : '') . '"'
                . ' id="' . $this->id . '_select"'
                . ' type="button"'
                . ' onclick="document.getElementById(\'ModalSelect' . $modalId . '\').open();">'
                . '<span class="icon-file" aria-hidden="true"></span> ' . Text::_('JSELECT')
                . '</button>';
        }

        if ($allowNew) {
            $html .= '<button'
                . ' class="btn btn-secondary' . ($value ? ' hidden' : '') . '"'
                . ' id="' . $this->id . '_new"'
                . ' type="button"'
                . ' onclick="document.getElementById(\'ModalNew' . $modalId . '\').open();">'
                . \JHtmlPoweradmin::icon('plus', 14, 'currentColor', ['aria-hidden' => 'true']) . ' ' . Text::_('JACTION_CREATE')
                . '</button>';
        }

        if ($allowClear) {
            $html .= '<button'
                . ' class="btn btn-secondary' . ($value ? '' : ' hidden') . '"'
                . ' id="' . $this->id . '_clear"'
                . ' type="button"'
                . ' onclick="window.processModalParent(\'' . $this->id . '\'); return false;">'
                . '<span class="icon-times" aria-hidden="true"></span> ' . Text::_('JCLEAR')
                . '</button>';
        }

        if ($allowPropagate && \count($languages) > 2) {
            $tagLength            = (int) \strlen($this->element['language']);
            $callbackFunctionStem = substr('jSelectCategory_' . $this->id, 0, -$tagLength);

            $html .= '<button'
            . ' class="btn btn-primary' . ($value ? '' : ' hidden') . '"'
            . ' type="button"'
            . ' id="' . $this->id . '_propagate"'
            . ' title="' . Text::_('JGLOBAL_ASSOCIATIONS_PROPAGATE_TIP') . '"'
            . ' onclick="Joomla.propagateAssociation(\'' . $this->id . '\', \'' . $callbackFunctionStem . '\');">'
            . '<span class="icon-sync" aria-hidden="true"></span> ' . Text::_('JGLOBAL_ASSOCIATIONS_PROPAGATE_BUTTON')
            . '</button>';
        }

        if ($allowSelect || $allowNew || $allowEdit || $allowClear) {
            $html .= '</span>';
        }

        $modalDimensions = [
            'height'     => '75vh',
            'width'      => '90vw',
            'bodyHeight' => 80,
            'modalWidth' => 90,
        ];

        if ($allowSelect) {
            $modalConfig = array_merge([
                'title'  => $modalTitle,
                'url'    => $urlSelect,
                'footer' => '<button type="button" class="btn btn-secondary" onclick="document.getElementById(\'ModalSelect'
                    . $modalId . '\').close();">' . Text::_('JLIB_HTML_BEHAVIOR_CLOSE') . '</button>',
            ], $modalDimensions);

            $html .= $this->renderModal('ModalSelect' . $modalId, $modalConfig);
        }

        if ($allowNew) {
            $modalConfig = array_merge([
                'title'       => Text::_('COM_CATEGORIES_NEW_CATEGORY'),
                'backdrop'    => 'static',
                'keyboard'    => false,
                'closeButton' => false,
                'url'         => $urlNew,
                'footer'      => '<button type="button" class="btn btn-secondary"'
                        . ' onclick="window.processModalEdit(this, \'' . $this->id . '\', \'add\', \'category\', \'cancel\', \'item-form\'); return false;">'
                        . Text::_('JLIB_HTML_BEHAVIOR_CLOSE') . '</button>'
                        . '<button type="button" class="btn btn-primary"'
                        . ' onclick="window.processModalEdit(this, \'' . $this->id . '\', \'add\', \'category\', \'save\', \'item-form\'); return false;">'
                        . Text::_('JSAVE') . '</button>'
                        . '<button type="button" class="btn btn-success"'
                        . ' onclick="window.processModalEdit(this, \'' . $this->id . '\', \'add\', \'category\', \'apply\', \'item-form\'); return false;">'
                        . Text::_('JAPPLY') . '</button>',
            ], $modalDimensions);

            $html .= $this->renderModal('ModalNew' . $modalId, $modalConfig);
        }

        if ($allowEdit) {
            $modalConfig = array_merge([
                'title'       => Text::_('COM_CATEGORIES_EDIT_CATEGORY'),
                'backdrop'    => 'static',
                'keyboard'    => false,
                'closeButton' => false,
                'url'         => $urlEdit,
                'footer'      => '<button type="button" class="btn btn-secondary"'
                        . ' onclick="window.processModalEdit(this, \'' . $this->id . '\', \'edit\', \'category\', \'cancel\', \'item-form\'); return false;">'
                        . Text::_('JLIB_HTML_BEHAVIOR_CLOSE') . '</button>'
                        . '<button type="button" class="btn btn-primary"'
                        . ' onclick="window.processModalEdit(this, \'' . $this->id . '\', \'edit\', \'category\', \'save\', \'item-form\'); return false;">'
                        . Text::_('JSAVE') . '</button>'
                        . '<button type="button" class="btn btn-success"'
                        . ' onclick="window.processModalEdit(this, \'' . $this->id . '\', \'edit\', \'category\', \'apply\', \'item-form\'); return false;">'
                        . Text::_('JAPPLY') . '</button>',
            ], $modalDimensions);

            $html .= $this->renderModal('ModalEdit' . $modalId, $modalConfig);
        }

        $class = $this->required ? ' class="required modal-value"' : '';

        $html .= '<input type="hidden" id="' . $this->id . '_id"' . $class . ' data-required="' . (int) $this->required . '" name="'
            . $this->name . '" data-text="' . htmlspecialchars(Text::_('COM_CATEGORIES_SELECT_A_CATEGORY', true), ENT_COMPAT, 'UTF-8')
            . '" value="' . $value . '">';

        return $html;
    }

    /**
     * Render a modal using the Poweradmin webcomponent when available, with Bootstrap as a fallback.
     *
     * @param   string  $selector  Modal selector/id.
     * @param   array   $options   Modal options.
     *
     * @return  string
     */
    private function renderModal(string $selector, array $options): string
    {
        if (class_exists('JHtmlWebcomponent')) {
            return \JHtmlWebcomponent::renderModal($selector, $options);
        }

        return HTMLHelper::_('bootstrap.renderModal', $selector, $options);
    }

    /**
     * Method to get the field label markup.
     *
     * @return  string
     */
    protected function getLabel()
    {
        return str_replace($this->id, $this->id . '_name', parent::getLabel());
    }
}


