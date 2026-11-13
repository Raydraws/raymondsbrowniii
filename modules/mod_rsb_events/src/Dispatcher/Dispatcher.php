<?php
namespace Raydraws\Module\RsbEvents\Site\Dispatcher;

\defined('_JEXEC') or die;

use Joomla\CMS\Dispatcher\AbstractModuleDispatcher;
use Joomla\CMS\Helper\HelperFactoryAwareInterface;
use Joomla\CMS\Helper\HelperFactoryAwareTrait;

class Dispatcher extends AbstractModuleDispatcher implements HelperFactoryAwareInterface
{
    use HelperFactoryAwareTrait;

    protected function getLayoutData(): array
    {
        $data           = parent::getLayoutData();
        $data['events'] = $this->getHelperFactory()->getHelper('EventsHelper')->getEvents($data['params']);
        return $data;
    }
}
