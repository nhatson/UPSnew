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

class Upsps extends \Bss\Ups\Controller\Adminhtml\Ups
{
    /**
     * Retrieve Module Enable
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        if ($order = $this->_initOrder()) {   
            $this->ups();
            $this->_redirect('sales/order/view', array('order_id' => $order->getId()));
        }
    }     
}
