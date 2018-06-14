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
use Magento\Framework\App\Filesystem\DirectoryList;

class Upslpn extends \Magento\Sales\Controller\Adminhtml\Order
{
    protected $pdfOrder;

    protected $upsData;

    protected $storeManager;

    /**
     * File check
     *
     * @var \Magento\Framework\Filesystem\Io\File
     */
    protected $ioFile;

    /**
     * File system
     *
     * @var \Magento\Framework\Filesystem
     */
    protected $filesystem;

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
        StoreManagerInterface $storeManager,
        \Magento\Framework\Filesystem\Io\File $ioFile,
        \Magento\Framework\Filesystem $filesystem
    ) {
        parent::__construct($context, $coreRegistry, $fileFactory, $translateInline, $resultPageFactory, $resultJsonFactory, $resultLayoutFactory, $resultRawFactory, $orderManagement, $orderRepository, $logger);
        $this->pdfOrder = $pdfOrder;
        $this->upsData = $upsData;
        $this->storeManager = $storeManager;
        $this->ioFile = $ioFile;
        $this->filesystem = $filesystem;
    }

    public function execute()
    {
        if ($order = $this->_initOrder()) {

            // Standartparams festlegen
            $label_path = $this->pdfOrder->label_path;
            $jpg_label_path = $this->pdfOrder->jpg_label_path;
            $cod_label_path = $this->pdfOrder->jpg_label_path;

            // Nachnahme
            $b_cod ="nein";
            $html = '                   
                    <style type="text/css">
                        @page { 
                            size:21.0cm 29.7cm;
                        }
                    
                        div.a4pages { 
                            page-break-after:always;
                        }
                    </style>
                    
                    <style type="text/css" media="all">
                    
                        div.instructions-div { 
                            display:none;
                        }
                    
                    </style>                    
                    
                    ';
            
            // UPS-Daten holen
            // $connection = Mage::getSingleton('core/resource')->getConnection('core_read');
            // $select_ups = $connection->select("SELECT *")->from('ups_data')->where("`order_id`='".$order->getId()."' AND `retour`='nein'")->order("");
            // $if_ups_data = $connection->fetchAll($select_ups);
            $if_ups_data = $this->upsData->checkExist($order->getId(), 'nein');

            $base_url = $this->storeManager->getStore()->getBaseUrl();
            
            // UPS-Daten durchlaufen
            foreach($if_ups_data as $ud){
            
                $b_cod = $ud['nach'];
            
                // Packetnummern ermitteln
                $package = array_reverse(explode(";", $ud['paketnummer']));
                $counter = 1;
                
                $package_count = sizeof($package);
            
                // Pakete durchlaufen
                foreach($package as $p){
            
                    // Filename zusammensetzen
                    $imageFile = $label_path. '/'.$p.'.gif';

                    // Images vorhanden?
                    if (file_exists($imageFile)) {
                        $path = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath('bss/ups/');
                        $this->ioFile->checkAndCreateFolder($path);
                        $newfile = $path.$p.'.gif';
                        $this->ioFile->cp($imageFile, $newfile);
                        // Label laden
                        $html .= '<div class="a4pages">'.str_replace(
                                './label'.$p.'.gif',
                                $base_url. '/media/bss/ups/'.$p.'.gif',
                                implode(" ",file($label_path. '/'.$p.'.html'))).'</div>';
                        //Wenn Nachnahme und letztes Label
                        if($b_cod =='ja' AND $package_count == $counter){
                            // Html String aufbereiten und Nachnahmeschein laden
                            $html .= '<div class="a4pages">'.str_replace(
                                    array(
                                            '<!DOCTYPE HTML PUBLIC \"-//IETF//DTD HTML 3.2//EN\">',
                                            '<th',
                                            '</th>',
                                            '<td colspan="3"><b> &nbsp;</b></td>'
                                    ),                                      
                                    array(
                                            '',
                                            '<tr><th',
                                            '</th></tr>',
                                            '<tr><td colspan="3"><b> &nbsp;</b></td></tr>',
                                            ''
                                    ),implode(" ",file($label_path. '/'.$p.'_COD.html'))).'</div>';
                                
                        }       
                    }       
                    $counter++;
                }       
            }   

//          $html .= '
//                      <script type="text/javascript">
//                          javascript:window.print(); 
//                          window.parent.Windows.close(\'browser_window\');
//                      </script>';
            echo $html;
            // history anpassen
            // Versendemail schicken            
            $this->pdffinish($order->getId());  
            
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
