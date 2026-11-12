<?php
/**
 * Poweradmin Form Field - Radio
 *
 * @copyright  (C) 2010 - 2026 JoomShaper. All rights reserved.
 * @license    http://www.gnu.org/licenses/gpl-2.0.html GPLv2 or later
 */

namespace Poweradmin\Admin\Form\Field;

\defined('_JEXEC') or die;

use Joomla\CMS\Form\Field\ListField;

/**
 * Form Field class for the Joomla Platform.
 * Provides radio button inputs with switcher layout as default
 *
 * @link   https://html.spec.whatwg.org/multipage/input.html#radio-button-state-(type=radio)
 * @since  1.7.0
 */
class RadioField extends ListField
{
    /**
     * The form field type.
     *
     * @var    string
     * @since  1.7.0
     */
    protected $type = 'Radio';

    /**
     * Name of the layout being used to render the field
     * Default changed to switcher layout for Poweradmin template
     *
     * @var    string
     * @since  3.5
     */
    protected $layout = 'joomla.form.field.radio.buttons';

    /**
     * Method to get the data to be passed to the layout for rendering.
     *
     * @return  array
     *
     * @since   3.5
     */
    protected function getLayoutData()
    {
        $data = parent::getLayoutData();

        $extraData = [
            'options' => $this->getOptions(),
            'value'   => (string) $this->value,
        ];

        return array_merge($data, $extraData);
    }
}

