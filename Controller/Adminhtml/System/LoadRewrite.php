<?php
namespace Sga\Configview\Controller\Adminhtml\System;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Sga\Configview\Helper\Data as HelperData;

class LoadRewrite extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Sga_Configview::config_system';

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
                $keys = explode(',', (string)$this->getRequest()->getParam('keys'));
                if (count($keys) > 0) {
                    $data['rewrites'] = $this->_helperData->getConfigNbRewrite($keys);
                } else {
                    $data['message'] = __('No keys parameter in request');
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
