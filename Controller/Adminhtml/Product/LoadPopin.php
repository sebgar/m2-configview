<?php
namespace Sga\Configview\Controller\Adminhtml\Product;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;

class LoadPopin extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Sga_Configview::config_catalog_product';

    protected $_resultJsonFactory;
    protected $_helper;

    public function __construct(
        Context $context,
        JsonFactory $resultJsonFactory
    ) {
        $this->_resultJsonFactory = $resultJsonFactory;

        parent::__construct($context);
    }

    public function execute()
    {
        $data = [
            'message' => '',
            'html' => '',
        ];

        if ($this->getRequest()->isPost()) {
            try {
                $productId = (int)$this->getRequest()->getParam('id');
                $attribute = (string)$this->getRequest()->getParam('attribute');

                if ($productId > 0 && $attribute !== '') {
                    $this->_loadLayout('configview_catalog_product_loadpopin');
                    $data['html'] = $this->_view->getLayout()->getOutput();
                } else {
                    $data['message'] = __('Some parameters are not set : id / attribute');
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

    protected function _loadLayout($handles)
    {
        $this->_view->getLayout()->getUpdate()->addHandle($handles);
        $this->_view->generateLayoutBlocks();
    }
}
