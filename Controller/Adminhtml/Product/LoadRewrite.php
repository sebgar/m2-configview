<?php
namespace Sga\Configview\Controller\Adminhtml\Product;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Sga\Configview\Helper\Data as HelperData;

class LoadRewrite extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Sga_Configview::config_catalog_product';

    protected $_resultJsonFactory;
    protected $_helperData;

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory,
        HelperData $helperData
    ) {
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_helperData = $helperData;

        parent::__construct($context);
    }

    public function execute()
    {
        $data = [
            'message' => '',
            'rewrites' => '',
        ];

        if ($this->getRequest()->isPost()) {
            try {
                $productId = (int)$this->getRequest()->getParam('id');
                $attributes = explode(',', (string)$this->getRequest()->getParam('attributes'));
                if (count($attributes) > 0) {
                    $data['rewrites'] = $this->_helperData->getProductAttributeNbRewrite($productId, $attributes);
                } else {
                    $data['message'] = __('No attributes parameter in request');
                }
            } catch (\Exception $e) {
                $data['message'] = $e->getMessage();
            }
        } else {
            $data['message'] = __('Not a POST request');
        }

        $result = $this->_resultJsonFactory->create();
        return $result->setData($data);
    }
}
