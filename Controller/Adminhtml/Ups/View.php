<?php
/**
 * BSS Commerce Co.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://bsscommerce.com/Bss-Commerce-License.txt
 *
 * @category   BSS
 * @package    Bss_Ups
 * @author     Extension Team
 * @copyright  Copyright (c) 2017-2018 BSS Commerce Co. ( http://bsscommerce.com )
 * @license    http://bsscommerce.com/Bss-Commerce-License.txt
 */
namespace Bss\Ups\Controller\Adminhtml\Ups;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\InputException;
use Psr\Log\LoggerInterface;

class View extends \Bss\Ups\Controller\Adminhtml\Ups
{
    /**
     * Retrieve Module Enable
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        //die;
        //$resultPage = $this->resultPageFactory->create();
        if ($order = $this->_initOrder()) {   
            // Dient nur für das Template damit es weiss, dass es aus diesem Controller aufgerufen wird. Prüfung ob es auch anders möglich ist. Leider konnte ich mir das Object nicht ohne Umwege ausgeben.
            //$order->order_view = 1;
            $oid = $order->getId();
            $this->_coreRegistry->register('order_view'.$oid, 1);
            $this->_coreRegistry->register('egprint'.$oid, $this->getRequest()->getParam('egprint'));
            $this->ups();
            //var_dump(expression)
            //$url = $this->getUrl();
            //$this->_redirect(str_replace("ups/ups","sales/order",$url));
            $params = $this->getRequest()->getParams();
            $this->_redirect('sales/order/view',  $params);
            //return $resultPage;
        }
    }   
}
