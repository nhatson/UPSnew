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
namespace Bss\Ups\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Sales\Model\Order\ShipmentRepository;
use Magento\Sales\Model\Order\Shipment\TrackFactory;
//use Bss\Ups\Api\UpsDataRepositoryInterface;

class UpsData extends AbstractDb
{
    /**
     * AppResource
     *
     * @var \Magento\Framework\Model\ResourceModel\Db\Context AppResource
     */
    protected $connection;

    protected $shipmentRepository;

    protected $trackFactory;

    //protected $upsDataRepository;

    /**
     * UpsData constructor.
     *
     * @param Context $context
     */
    public function __construct(
        ShipmentRepository $shipmentRepository,
        TrackFactory $trackFactory,
        Context $context,
        //UpsDataRepositoryInterface $upsDataRepository,
        $connectionName = null
    ) {
        $this->trackFactory = $trackFactory;
        $this->shipmentRepository = $shipmentRepository;
        $this->connection = $context->getResources();
        //$this->upsDataRepository = $upsDataRepository;
        parent::__construct($context, $connectionName);
    }

    /**
     * Resource initialisation
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('bss_ups_data', 'id');
    }

    /**
     * Check Unique
     *
     * @param string $result
     * @return int
     */
    public function checkExist($orderId, $retour, $b_ups_package_numbers = null)
    {
        $table = $this->getTable('bss_ups_data');
        if ($b_ups_package_numbers == null) {
            $select = $this->connection->getConnection()->select()
                        ->from(
                            ['ce' => $table],
                            ['*']
                        )
                        ->where('order_id = ?', $orderId)
                        ->where('retour = ?', $retour);
            $data = $this->connection->getConnection()->fetchAll($select);
        } else {
            $select = $this->connection->getConnection()->select()
                        ->from(
                            ['ce' => $table],
                            ['*']
                        )
                        ->where('order_id = ?', $orderId)
                        ->where('retour = ?', $retour)
                        ->where('paketnummer = ?', $b_ups_package_numbers);
            $data = $this->connection->getConnection()->fetchAll($select);        
        }
        return $data;
    }

    public function show_package_data($orderId, $sel)
    {
        $table = $this->getTable('bss_ups_data');
        $select = $this->connection->getConnection()->select()
                    ->from(
                        ['ce' => $table],
                        ['*']
                    )
                    ->where('order_id = ?', $orderId)
                    ->where('retour = ?', $sel);
        $if_data = $this->connection->getConnection()->fetchAll($select);      
        if (count($if_data)>0) {
            return $if_data;
        }
        else {
            return false;
        }
       
    }

    public function getSalesOrder($orderId)
    {
        $table = $this->getTable('sales_order');
        $select = $this->connection->getConnection()->select()
                    ->from(
                        ['ce' => $table],
                        ['entity_id','base_grand_total','shipping_address_id']
                    )
                    ->where('entity_id = ?', $orderId);
        $data = $this->connection->getConnection()->fetchAll($select);      
        return $data;
    }

    public function getSalesOrderAddress($addressId)
    {
        $table = $this->getTable('sales_order_address');
        $select = $this->connection->getConnection()->select()
                    ->from(
                        ['ce' => $table],
                        ['*']
                    )
                    ->where('entity_id = ?', $addressId);
        $data = $this->connection->getConnection()->fetchAll($select);
        return $data;
    }

    public function getSalesShipment($orderId)
    {
        $table = $this->getTable('sales_shipment');
        $select = $this->connection->getConnection()->select()
                    ->from(
                        ['ce' => $table],
                        ['*']
                    )
                    ->where('order_id = ?', $orderId);
        $data = $this->connection->getConnection()->fetchAll($select);
        return $data;
    }
    public function saveTrack($a_trackdata,$ship){
       $shipment = $this->shipmentRepository->get($ship);
        $data = array(
            'carrier_code' => $a_trackdata[0],
            'title' => $a_trackdata[2],
            'number' => $a_trackdata[1], // Replace with your tracking number
        );
        $track = $this->trackFactory->create()->addData($data);
        $shipment->addTrack($track)->save();      
   } 
   public function deleteTrack($orderId, $tracknumber)
   {
        $table = $this->getTable('sales_shipment_track');
        $this->connection->getConnection()->delete(
            $table,
            ['order_id = '.$orderId.' AND track_number = "'.$tracknumber.'"']
        );
    }
}
