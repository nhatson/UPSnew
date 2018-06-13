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
use Magento\Framework\Filesystem\DirectoryList;
use Magento\Framework\App\Filesystem\DirectoryList as Filesystem;

class Upslpr extends \Magento\Sales\Controller\Adminhtml\Order
{
    protected $directory;
    protected $pdfOrder;
    protected $datetime;
    protected $timezone;

    /**
     * Constructor.
     *
     * @param Context $context
     */
    public function __construct (
        Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Translate\InlineInterface $translateInline,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        OrderManagementInterface $orderManagement,
        OrderRepositoryInterface $orderRepository,
        LoggerInterface $logger,
        DirectoryList $directory,
        \Bss\Ups\Model\Order\Pdf\Order $pdfOrder
    ) {
        parent::__construct($context, $coreRegistry, $fileFactory, $translateInline, $resultPageFactory, $resultJsonFactory, $resultLayoutFactory, $resultRawFactory, $orderManagement, $orderRepository, $logger);
        $this->directory = $directory;
        $this->pdfOrder = $pdfOrder;
    }

    public function execute()
    {
        if ($order = $this->_initOrder()) {
            $order->setOrder($order);
            $pdf = $this->pdfOrder->getPdfRetour(array($order,$order->getId()));
            // $now = $this->datetime->gmtDate();
            // $date = $this->timezone->date($now)->format('Y-m-d_H-i-s');
            // return $this->_fileFactory->create('Retourscheine'.$date.'.pdf', $pdf->output('upslabel.pdf', 'S'), Filesystem::VAR_DIR);
            $this->_redirect('sales/order/view', array('order_id' => $order->getId()));
        }
    }  
}
