<?php
namespace Raydraws\Module\RsbMemberCode\Site\Dispatcher;

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
        $data["code"] = $this->getHelperFactory()->getHelper('CodeHelper')->getCode($data['params']);
        return $data;
    }
}
