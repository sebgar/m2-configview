<?php
namespace Sga\Configview\Block\Adminhtml\System;

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
        return $this->_helperConfig->isEnabledSystem();
    }

    public function getSection()
    {
        return $this->_request->getParam('section');
    }

    public function getGroup()
    {
        return $this->_request->getParam('group');
    }

    public function getField()
    {
        return $this->_request->getParam('field');
    }

    public function getKey()
    {
        return $this->getSection().'/'.$this->getGroup().'/'.$this->getField();
    }

    public function getInfoXml()
    {
        return $this->_configStructure->getElement($this->getKey());
    }

    public function getXmlValue()
    {
        $data = $this->_readerConfigFile->getData('default');
        if (isset($data[$this->getSection()][$this->getGroup()][$this->getField()])) {
            return $data[$this->getSection()][$this->getGroup()][$this->getField()];
        }

        return null;
    }

    public function isShowInDefault()
    {
        $info = $this->getInfoXml();
        return (bool)$info->showInDefault();
    }

    public function getDefaultValue()
    {
        return $this->_helper->getConfigScopeDefaultValue($this->getSection(), $this->getGroup(), $this->getField());
    }

    public function isShowInWebsite()
    {
        $info = $this->getInfoXml();
        return (bool)$info->showInWebsite();
    }

    public function getWebsiteValue($scopeId)
    {
        return $this->_helper->getConfigScopeWebsiteValue($this->getSection(), $this->getGroup(), $this->getField(), $scopeId);
    }

    public function isShowInStore()
    {
        $info = $this->getInfoXml();
        return (bool)$info->showInStore();
    }

    public function getStoreValue($scopeId)
    {
        return $this->_helper->getConfigScopeStoreValue($this->getSection(), $this->getGroup(), $this->getField(), $scopeId);
    }
}