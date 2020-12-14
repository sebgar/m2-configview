<?php
namespace Sga\Configview\Block\Adminhtml\Catalog\Product;

class Popin extends \Magento\Backend\Block\Template
{
    protected $_jsonSerializer;
    protected $_urlBuilder;
    protected $_scopeConfig;
    protected $_configStructure;
    protected $_readerConfigFile;
    protected $_helper;
    protected $_helperConfig;

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Serialize\Serializer\Json $jsonSerializer,
        \Magento\Backend\Model\UrlInterface $urlBuilder,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Config\Model\Config\Structure $configStructure,
        \Magento\Framework\App\Config\Initial $reader,
        \Sga\Configview\Helper\Data $helper,
        \Sga\Configview\Helper\Config $helperConfig,
        array $data = []
    ) {
        $this->_jsonSerializer = $jsonSerializer;
        $this->_urlBuilder = $urlBuilder;
        $this->_scopeConfig = $scopeConfig;
        $this->_configStructure = $configStructure;
        $this->_readerConfigFile = $reader;
        $this->_helper = $helper;
        $this->_helperConfig = $helperConfig;

        parent::__construct($context, $data);
    }

    public function getJsonSerializer()
    {
        return $this->_jsonSerializer;
    }

    public function getUrlBuilder()
    {
        return $this->_urlBuilder;
    }

    public function getStoreManager()
    {
        return $this->_storeManager;
    }

    public function isEnabled()
    {
        return $this->_helperConfig->isEnabledCatalogProduct();
    }

    public function getCurrentProductId()
    {
        return (int)$this->_request->getParam('id');
    }

    public function getAttributeCode()
    {
        return $this->_request->getParam('attribute');
    }

    public function getDefaultValue()
    {
        return $this->_helper->getProductAttributeScopeValue($this->getCurrentProductId(), $this->getAttributeCode());
    }

    public function getStoreValue($storeId)
    {
        return $this->_helper->getProductAttributeScopeValue($this->getCurrentProductId(), $this->getAttributeCode(), $storeId);
    }
}