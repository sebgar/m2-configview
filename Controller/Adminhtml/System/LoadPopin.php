<?php
namespace Sga\Configview\Controller\Adminhtml\System;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;

class LoadPopin extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Sga_Configview::config_system';

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
                $section = (string)$this->getRequest()->getParam('section');
                $group = (string)$this->getRequest()->getParam('group');
                $field = (string)$this->getRequest()->getParam('field');

                if ($section !== '' && $group !== '' && $field !== '') {
                    $this->_loadLayout('configview_system_loadpopin');
                    $data['html'] = $this->_view->getLayout()->getOutput();
                } else {
                    $data['message'] = __('Some parameters are not set : section / group / field');
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
