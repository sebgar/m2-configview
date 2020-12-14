<?php
namespace Sga\Configview\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Config extends AbstractHelper
{
    const XML_PATH_CONFIGVIEW_ENABLED_SYSTEM = 'configview/general/enabled_system';
    const XML_PATH_CONFIGVIEW_ENABLED_CATALOG_PRODUCT = 'configview/general/enabled_catalog_product';

    public function isEnabledSystem($store = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_CONFIGVIEW_ENABLED_SYSTEM,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    public function isEnabledCatalogProduct($store = null)
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_CONFIGVIEW_ENABLED_CATALOG_PRODUCT,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }
}
