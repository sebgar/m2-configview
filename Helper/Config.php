<?php
namespace Sga\Configview\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper
{
    const XML_PATH_CONFIGVIEW_ENABLED_SYSTEM = 'admin/configview/enabled_system';

    public function isEnabledSystem($store = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_CONFIGVIEW_ENABLED_SYSTEM,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }
}
