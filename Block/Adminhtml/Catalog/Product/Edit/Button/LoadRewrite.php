<?php
namespace Sga\Configview\Block\Adminhtml\Catalog\Product\Edit\Button;

use Magento\Framework\Registry;
use Magento\Framework\View\Element\UiComponent\Context;
use Magento\Catalog\Block\Adminhtml\Product\Edit\Button\Generic;
use Sga\Configview\Helper\Config as HelperConfig;

class LoadRewrite extends Generic
{
    protected $_helperConfig;

    public function __construct(
        Context $context,
        Registry $registry,
        HelperConfig $helperConfig
    ) {
        $this->_helperConfig = $helperConfig;

        parent::__construct($context, $registry);
    }

    /**
     * @return array
     */
    public function getButtonData()
    {
        if ($this->_helperConfig->isEnabledCatalogProduct()) {
            return [
                'label' => __('Load Rewrite'),
                'class' => 'action-secondary',
                'on_click' => '',
                'sort_order' => 20
            ];
        }
    }
}
