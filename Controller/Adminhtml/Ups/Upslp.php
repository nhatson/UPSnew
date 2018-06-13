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
use Magento\Store\Model\StoreManagerInterface;

class Upslp extends \Magento\Sales\Controller\Adminhtml\Order
{
    protected $pdfOrder;
    protected $upsData;
    protected $storeManager;
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
        \Bss\Ups\Model\Order\Pdf\Order $pdfOrder,
        \Bss\Ups\Model\ResourceModel\UpsData $upsData,
        StoreManagerInterface $storeManager
    ) {
        parent::__construct($context, $coreRegistry, $fileFactory, $translateInline, $resultPageFactory, $resultJsonFactory, $resultLayoutFactory, $resultRawFactory, $orderManagement, $orderRepository, $logger);
        $this->pdfOrder = $pdfOrder;
        $this->upsData = $upsData;
        $this->storeManager = $storeManager;
    }

    public function execute()
    {
        //die;
        if ($order = $this->_initOrder()) {
            $this->pdffinish($order->getId());  
            $order->setOrder($order);
            //$pdf = Mage::getModel('EuroGreens_UpsLabelPrint/order_pdf_order')->getPdf(array($order,$order->getId()));
            $pdf = $this->pdfOrder->getPdff(array($order,$order->getId()));
            //return $this->_prepareDownloadResponse('Paketscheine'.Mage::getSingleton('core/date')->date('Y-m-d_H-i-s').'.pdf', $pdf->output(), 'application/pdf');
            $this->_redirect('sales/order/view', array('order_id' => $order->getId()));
        }    
    }

    protected function pdffinish($id) {
        
        $orderOBJ = $this->orderRepository->get($id);
        // Nur wenn der gewünschte Status nicht besteht.
        if ($orderOBJ->getStatus() != 'complete') {            
            //Shipping Daten holen
            $a_ups_data = $this->upsData->getSalesOrder($id);

            foreach($a_ups_data as $upsd){
                $a_params['shipping_address_id'] = $upsd['shipping_address_id'];
            }
                        
            $a_shipping_address = $this->upsData->getSalesOrderAddress($a_params['shipping_address_id']);
            foreach($a_shipping_address as $shipping_adr){
                $shipping_data = $shipping_adr;
            }
        
            // Bestellung Nr. ermitteln
            $bestellnummer = $orderOBJ->getIncrementId();
            $mailtext = '<html><p>Sehr geehrte(r) '.$shipping_data['firstname'].' '.$shipping_data['lastname'].',<br/><br/>
        
wir freuen uns, Sie mit dieser Mail &uuml;ber den Versand Ihrer Bestellung ('.$bestellnummer.') zu informieren. Ihre Ware wird derzeit verpackt und anschlie&szlig;end unserem Versender UPS &uuml;bergeben. Die Zustellung erfolgt innerhalb der n&auml;chsten 2 Werktage. Um zu sehen, wo sich Ihre Ware gerade befindet, loggen Sie sich bitte in Ihrem <a href="'.$this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB).'customer/account/">Kundenkonto</a> ein. Dort sind die Trackingdaten Ihrer Sendung hinterlegt.<br/>  
Bei Fragen zum Versand nutzen Sie bitte die UPS Servicenummer 01805-882663 (14Cent/Minute aus dem deutschen Festnetz, Mobilfunk ggf. Abweichend).<br/><br/>

Viel Freude mit Ihrer Lieferung und beste Gr&uuml;&szlig;e aus Bernau<br/><br/>

Ihr EuroGreens Service Team</p></html>';
        
//          // Mail
//          $config = array('auth' => 'login',
//                 'username' => 'mail@weihnachtsbaum-discount.de',
//                 'password' => 'qwertz789');
 
//          $transport = new Zend_Mail_Transport_Smtp('smtp.weihnachtsbaum-discount.de', $config);
 
//          $mail = new Zend_Mail('UTF-8');
//          $mail->setSubject('Versandinformation ('.$bestellnummer.')');
//          $mail->setBodyHtml($mailtext);
                
//          $mail->setFrom(Mage::getStoreConfig('general/imprint/email'),Mage::getStoreConfig('general/imprint/shop_name'));
//          $mail->addTo($shipping_data['email'], $shipping_data['firstname'].' '.$shipping_data['lastname']);
                
//          $mail->send($transport);
/*          // Vorschlag von Rene
            $from_email = Mage::getStoreConfig('general/imprint/email');
            $from_name = Mage::getStoreConfig('general/imprint/shop_name');
            $subject = 'Versandinformation ('.$bestellnummer.')';
            $to_email = $shipping_data['email'];
            $to_name = $shipping_data['firstname'].' '.$shipping_data['lastname'];
*/
/*          
            // Aufgrund von nicht ankommen der Mails in den Kostenfreien Accounts, exacte Absender Daten angegeben weil Renes Vorschlag bringt als Absender Email  service@m14s4-2-24db.ispgateway.de 
            $from_email = 'bestellung@weihnachtsbaum-discount.de';
            $from_name = 'Bestellung Weihnachtsbaum Discount';
            $subject = 'Versandinformation ('.$bestellnummer.')';
            $to_email = $shipping_data['email'];
            $to_name = $shipping_data['firstname'].' '.$shipping_data['lastname'];
            
            
            $mail = Mage::getModel('core/email')
                ->setFromEmail($from_email)
                ->setFromName($from_name)
                ->setSubject($subject)
                ->setToEmail($to_email)
                ->setToName($to_name)
                ->setType('html')
                ->setBody($mailtext)
                ->send();
                        
    
            
            // History Eintrag
            $orderOBJ->addStatusToHistory('ups','Versand-E-Mail Versendet',true);
*/          

            // Status ändern
            $orderOBJ->setStatus('complete');
            $orderOBJ->setState('complete');
            // History Eintrag
            $orderOBJ->addStatusToHistory('complete','Status geändert (UPS-Paketscheine)');
            // Speichern
            $orderOBJ->save();
        }       
    }        
}
