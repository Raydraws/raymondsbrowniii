<?php
namespace Raydraws\Module\RsbShop\Site\Dispatcher;

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
        $data["products"] = $this->getHelperFactory()->getHelper('ShopHelper')->getProducts($data['params']);
        return $data;
    }
}
