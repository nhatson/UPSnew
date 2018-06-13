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
namespace Bss\Ups\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Sales\Api\OrderManagementInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order\ShipmentFactory;
use Magento\Sales\Model\Order\ShipmentRepository;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\InputException;
use Psr\Log\LoggerInterface;
use Magento\Framework\Api\DataObjectHelper;
use Bss\Ups\Api\Data\UpsDataInterface;
use Bss\Ups\Api\Data\UpsDataInterfaceFactory;
use Bss\Ups\Api\UpsDataRepositoryInterface;
use Bss\Ups\Api\Data\UpsMessageInterface;
use Bss\Ups\Api\Data\UpsMessageInterfaceFactory;
use Bss\Ups\Api\UpsMessageRepositoryInterface;
use Bss\Ups\Model\Xml;
use Magento\Sales\Model\Convert\Order;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\Stdlib\DateTime\DateTime;

class Ups extends \Magento\Sales\Controller\Adminhtml\Order
{
    protected $shipmentFactory;
    protected $shipmentRepository;
    protected $convertOrder;
    protected $ups;
    private $selection;
    protected $xmlClass;
    /**
     * DataObjectHelper
     *
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * UpsDataRepository
     *
     * @var UpsDataRepositoryInterface
     */
    protected $upsDataRepository;

    /**
     * UpsDataFactory
     *
     * @var UpsDataInterfaceFactory
     */
    protected $upsDataFactory;

     /**
     * UpsMessageRepository
     *
     * @var UpsMessageRepositoryInterface
     */
    protected $upsMessageRepository;

    /**
     * UpsMessageFactory
     *
     * @var UpsMessageInterfaceFactory
     */
    protected $upsMessageFactory;

    /**
     * Sequence
     *
     * @var \Bss\Ups\Model\ResourceModel\UpsData
     */
    protected $upsData;
    protected $sessionManager;
    protected $datetime;

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
        ShipmentFactory $shipmentFactory,
        ShipmentRepository $shipmentRepository,
        Order $convertOrder,
        LoggerInterface $logger,
        \Bss\Ups\Model\ResourceModel\UpsData $upsData,
        \Bss\Ups\Model\Ups $ups,
        DataObjectHelper $dataObjectHelper,
        UpsDataInterfaceFactory $upsDataFactory,
        UpsDataRepositoryInterface $upsDataRepository,
        UpsMessageInterfaceFactory $upsMessageFactory,
        UpsMessageRepositoryInterface $upsMessageRepository,
        Xml $xmlClass,
        SessionManagerInterface $sessionManager,
        DateTime $datetime
    ) {
        parent::__construct($context, $coreRegistry, $fileFactory, $translateInline, $resultPageFactory, $resultJsonFactory, $resultLayoutFactory, $resultRawFactory, $orderManagement, $orderRepository, $logger);
        $this->ups = $ups;
        $this->shipmentFactory = $shipmentFactory;
        $this->shipmentRepository = $shipmentRepository;
        $this->convertOrder = $convertOrder;
        $this->upsData = $upsData;
        $this->dataObjectHelper  = $dataObjectHelper;
        $this->upsDataFactory = $upsDataFactory;
        $this->upsDataRepository   = $upsDataRepository;
        $this->upsMessageFactory = $upsMessageFactory;
        $this->upsMessageRepository   = $upsMessageRepository;       
        $this->xmlClass = $xmlClass;
        $this->sessionManager = $sessionManager;
        $this->datetime  = $datetime;
    }

    public function execute()
    {
    }

    public function ups() 
    {
        $this->initselection();
        if($this->getRequest()->getParam('shipment_id') != "" && $this->getRequest()->getParam('order_id') == ""){
            $oid = $this->ups->get_orderId($this->getRequest()->getParam('shipment_id'));
        } else {
            $oid =$this->getRequest()->getParam('order_id');
        }
        $this->ups->b_order_id =  $oid;
        $order = $this->orderRepository->get($oid);
        $this->ups->increment_id = $order->getIncrementId();
        foreach ($order->getInvoiceCollection() as $invoce) {
            $this->ups->invoice_increment_id = $invoce->getIncrementId();
            break;
        }
        if(isset($this->selection) && $this->selection !="") {
            switch($this->selection){
                case "rate":
                {
                    $solo_weight = $this->getRequest()->getParam('weight_total');
                    $this->ups->set_params($this->getRequest()->getParam('zipcode'),$this->getRequest()->getParam('country_id'),ceil($solo_weight));
                    $this->ups->ups_xml_init('rate');
                    $this->ups->get_price_single();
                    // wrj Preisanfrage 
                    //print_r($ups->request_data);
                    
                    $this->ups->send_ups_xml();                    
                    global $xml;

                    $p_xml_single = $this->xmlClass;
                    $p_xml_single->__xml($this->ups->response_data,'xml');
                    // wrj Preisantwort Single Ship
                    //print_r($ups->response_data);
                    //echo '--------------------------------------------------------------------------------------------------------------------------------------<br>';
                    //echo '<pre>';print_r($xml);die;
                    $amount = $xml['RatingServiceSelectionResponse_RatedShipment_NegotiatedRates_NetSummaryCharges_GrandTotal'][0]->MonetaryValue[3];
                    $amount = $amount * $this->getRequest()->getParam('count');
                    $ups_message = "<b>Preis Einzelpaketesendung:</b> ".number_format(round(($amount*1.19),2), 2, ',', ' ')." &euro; (Netto: ".number_format(($amount), 2, ',', ' ').")<br/>";            
                    
                    $this->ups->request_data = "";
                    $this->ups->ups_xml_init('rate');
                    $this->ups->get_price_multi($this->getRequest()->getParam('count'));
                    
                    // wrj Preisanfrage 
                    //print_r($ups->request_data);
                    
                    $this->ups->send_ups_xml();

                    global $xml;

                    $p_xml_multi = $this->xmlClass;
                    $p_xml_multi->__xml($this->ups->response_data,'xml');
                        
                     // wrj Preisantwort Multi Ship
                    // print_r($ups->response_data);
                    //echo '<pre>';print_r($xml);die;
                    $amount ='';
                    $amount = $xml['RatingServiceSelectionResponse_RatedShipment_NegotiatedRates_NetSummaryCharges_GrandTotal'][0]->MonetaryValue[7];
                    //echo '--- '.$amount.'-----';
                    $ups_message .= "<b>Preis Mehrpaketesendung:</b> ".number_format(round((($amount*1.19)),2), 2, ',', ' ')." &euro; (Netto: ".number_format(($amount), 2, ',', ' ').")";
                    $this->sessionManager->setData('ups_message_'.$oid, $ups_message);     
                    break;
                }
                case "ship":
                case "ship_retour":
                {
                    //get $_GET url parameter
                    $this->ups->a_params['order_id'] = $oid;
                    $this->ups->a_params['paketanzahl'] = $this->getRequest()->getParam('paketanzahl');
                    $this->ups->a_params['paketkilo'] = $this->getRequest()->getParam('paketkilo');
                    $this->ups->a_params['nach'] = ($this->selection == 'ship_retour')? 'nein' : $this->getRequest()->getParam('nach');
                    $this->ups->a_params['paketart'] = $this->getRequest()->getParam('paketart');

                    $ups_status = "kein Status";
                    $void = 0;
                    //already exist ups_data?                  
                    $if_ups = $this->upsData->checkExist($oid,'nein');
                    $if_upsretour = "";
                    if($this->selection=="ship_retour"){
                        $if_upsretour = $this->upsData->checkExist($oid,'ja');
                    }
                    if((count($if_ups)<1 && $this->selection == 'ship') || (count($if_upsretour)<1 && $this->selection == 'ship_retour')) {
                        $a_ups_data = $this->upsData->getSalesOrder($this->ups->a_params['order_id']);
                        foreach($a_ups_data as $upsd){
                            $this->ups->a_params['wert'] = $upsd['base_grand_total'];
                            $this->ups->a_params['shipping_address_id'] = $upsd['shipping_address_id'];
                        }

                        $a_shipping_address = $this->upsData->getSalesOrderAddress($this->ups->a_params['shipping_address_id']);
                        foreach($a_shipping_address as $shipping_adr){
                            $this->ups->ShipTo = $shipping_adr;
                        }
                        
                        switch($this->selection){
                            case "ship":{
                                //from eurogreens
                                $this->ups->ShipFrom = array('CompanyName'=>$this->ups->shipper_name,'PhoneNumber'=>$this->ups->shipper_from_phone,'AddressLine1'=>$this->ups->shipper_ResidentialAddress,'City'=>$this->ups->shipper_from_city,'PostalCode'=>$this->ups->shipper_from_zip,'CountryCode'=>$this->ups->shipper_from_country,'Prefix'=>'','firstname'=>'','lastname'=>'');
                                break;
                            }
                            case "ship_retour":{
                                //from eurogreens
                                $this->ups->ShipFrom = array('CompanyName'=>$this->ups->ShipTo['company'],'PhoneNumber'=>$this->ups->ShipTo['telephone'],'AddressLine1'=>$this->ups->ShipTo['street'],'City'=>$this->ups->ShipTo['city'],'PostalCode'=>$this->ups->ShipTo['postcode'],'CountryCode'=>$this->ups->ShipTo['country_id'],'Prefix'=>$this->ups->ShipTo['prefix'],'firstname'=>$this->ups->ShipTo["firstname"],'lastname'=>$this->ups->ShipTo["lastname"]);
                                $this->ups->ShipTo['company']=$this->ups->shipper_name;
                                $this->ups->ShipTo['telephone']=$this->ups->shipper_from_phone;
                                $this->ups->ShipTo['street']=$this->ups->shipper_ResidentialAddress;
                                $this->ups->ShipTo['city']=$this->ups->shipper_from_city;
                                $this->ups->ShipTo['postcode']=$this->ups->shipper_from_zip;
                                $this->ups->ShipTo['Prefix']="";
                                $this->ups->ShipTo['firstname']="";
                                $this->ups->ShipTo['lastname']="";
                                break;
                            }

                        }
                        switch($this->ups->a_params['paketart']){
                            case "einzel":{

                                $this->ups->package_numbers="";

                                for($d=1;$d<($this->ups->a_params['paketanzahl']+1);$d++){

                                       $this->ups->request_data = "";     

                                       $this->ups->upsShipmentConfirmRequestSingle($d,$this->selection);
                                       // wrj
                                       // echo 'Labelanfrage anfrage';
                                       // print_r($this->ups->request_data);
                                       // exit;
                                       $this->ups->upsShipmentConfirmResponseSingle($d);
                                       // wrj
                                       // echo 'Label Response Single Ship';
                                       //print_r($this->ups->response_data);
                                       // exit;
                                       $this->ups->upsShipmentAcceptRequestSingle();
                                       $this->ups->upsShipmentAcceptResponseSingle($d);                                                                             
                                }                                
                                break;
                            }case "mehr":{                                
                                $this->ups->package_numbers="";                              
                                $this->ups->upsShipmentConfirmRequest($this->selection);
                                $this->ups->upsShipmentConfirmResponseSingle(1);
                                $this->ups->upsShipmentAcceptRequestSingle();
                                $this->ups->upsShipmentAcceptResponse();
                                break;
                            }
                        }
                        //save package numbers
                        $b_package_numbers = substr($this->ups->package_numbers,1);
                        if($this->selection =='ship'){
                            $sql_retour = 'nein';
                            //print package data
                            $upsTrackData = array();
                            $upsTrackData['paketnummer'] = $b_package_numbers;
                            $upsTrackData['paketart'] = $this->ups->a_params['paketart'];
                            $upsTrackData['nach'] = $this->ups->a_params['nach'];
                            $upsTrackData['paketanzahl'] = $this->ups->a_params['paketanzahl'];
                            $upsTrackData['paketkilo'] = $this->ups->a_params['paketkilo'];
                            $upsTrackData['kosten'] = $this->ups->a_params['cost'];         
                            $order->upsTrackData = $upsTrackData;
                        }
                        else{
                            $sql_retour = 'ja';
                        }
                        $data['order_id'] = $this->ups->a_params['order_id'];
                        $data['paketnummer'] = $b_package_numbers;
                        $data['paketanzahl'] = $this->ups->a_params['paketanzahl'];
                        $data['paketkilo'] = $this->ups->a_params['paketkilo'];
                        $data['nach'] = $this->ups->a_params['nach'];
                        $data['paketart'] = $this->ups->a_params['paketart'];
                        $data['kosten'] = $this->ups->a_params['cost'];
                        $data['retour'] = $sql_retour;
                        $model = $this->upsDataFactory->create();
                        try {
                            $this->dataObjectHelper->populateWithArray($model, $data, UpsDataInterface::class);
                            $this->upsDataRepository->save($model);
                        } catch (\Exception $e) {
                        };                                                                                     
                        //if shipment_id?
                        //save trackingnumbers
                        $shipment = $this->upsData->getSalesShipment($order->getId());
                        foreach($shipment as $sc){
                            $shipmentId = $sc['entity_id'];
                        }
                        if(!isset($shipmentId)){
                            if($order->canShip()){
                                 $_itemQty              = $order->getItemsCollection()->count();
                                $shipment = $this->convertOrder->toShipment($order);
                                 foreach ($order->getAllItems() AS $orderItem) {
                                    // Check if order item has qty to ship or is virtual
                                    if (! $orderItem->getQtyToShip() || $orderItem->getIsVirtual()) {
                                        continue;
                                    }
                                    $qtyShipped = $orderItem->getQtyToShip();
                                    // Create shipment item with qty
                                    $shipmentItem = $this->convertOrder->itemToShipmentItem($orderItem)->setQty($qtyShipped);
                                    // Add shipment item to shipment
                                    $shipment->addItem($shipmentItem);
                                }
                                // Register shipment
                                $shipment->register();
                                $shipment->getOrder()->setIsInProcess(true);
                                try {
                                    // Save created shipment and order
                                    $shipment->save();
                                    $shipment->getOrder()->save();
                                    // Send email
                                } catch (\Exception $e) {
                                   echo "Shipment Not Created". $e->getMessage(); exit;
                                }
                            }
                            $shipment = $this->upsData->getSalesShipment($order->getId()); 
                            foreach($shipment as $sc){
                                $shipmentId = $sc['entity_id'];
                            }
                        }
                        //speichern mit bestehender ID       -> testen
                        $a_tracking_numbers = explode(";", $b_package_numbers);
                        foreach($a_tracking_numbers as $atn){
                            $this->upsData->saveTrack(array("ups",$atn,"UPS"),$shipmentId);
                        }
                    }
                    break;                    
                }
                case "storno":
                case "storno_retour":
                {   
                    $sql_param = ($this->selection == 'storno')? 'nein' : 'ja';

                    $b_package_data = $this->upsData->show_package_data($oid,$sql_param);                
                    foreach($b_package_data as $bpd){
                       $b_ups_package_numbers = $bpd['paketnummer'];
                   }
                   $ups_tracking_numbers = explode(";", $b_ups_package_numbers);
                   $this->sessionManager->setData('ups_message_'.$oid, '');
                   $shipment = $this->upsData->getSalesShipment($order->getId()); 
                   foreach($shipment as $sc){
                        $shipmentId = $sc['entity_id'];
                    }
                    foreach($ups_tracking_numbers as $utn){
                        $this->ups->ups_xml_init('void');
                        $this->ups->get_ups_void_data($utn);
                        $this->ups->send_ups_xml(); 
                        $this->ups->show_data('void');
                        $this->upsData->deleteTrack($oid, $utn);
                    }
                    $data = $this->upsData->checkExist($this->getRequest()->getParam('order_id'), $sql_param, $b_ups_package_numbers);
                    if (count($data) > 0 ){
                        foreach ($data as $ups) {
                            $upsId = $ups['id'];
                            $this->upsDataRepository->deleteById($upsId);
                        } 
                    }
                    break;
                }
            }
            //save ups messages
            if($this->ups->message_buffer != ""){
                $message['order_id'] = $oid;
                $message['error'] = $this->ups->ups_error;
                $message['message'] = $this->ups->message_buffer;
                $message['read'] = '';
                $message['create'] = $this->datetime->gmtDate();
                $modelMessage = $this->upsMessageFactory->create();
                try {
                    $this->dataObjectHelper->populateWithArray($modelMessage, $message, UpsMessageInterface::class);
                    $this->upsMessageRepository->save($modelMessage);
                } catch (\Exception $e) {
                };
            //show messages
                ($this->ups->ups_error==1)? $this->messageManager->addErrorMessage(__($this->ups->message_buffer)) : $this->messageManager->addSuccessMessage(__($this->ups->message_buffer));

            }
        }
    }

    private function initselection() {
        $this->selection = '';  
        //load package prices
        if($this->getRequest()->getParam('count')){
            $this->selection = 'rate';
        }
        //parcel
        if($this->getRequest()->getParam('paketanzahl') && !$this->getRequest()->getParam('retour') ){
            $this->selection = 'ship';
        }
        if($this->getRequest()->getParam('paketanzahl') && $this->getRequest()->getParam('retour') ){
            $this->selection = 'ship_retour';
        }
        //check ups storno
        if($this->getRequest()->getParam('storno') && $this->getRequest()->getParam('storno') == 'ups' ){
            $this->selection = 'storno'; 
        }
        if($this->getRequest()->getParam('storno') && $this->getRequest()->getParam('storno') == 'retour'){
            $this->selection = 'storno_retour';
        }
        return $this->selection;
    }      
}
