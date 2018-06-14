<?php
/*  UPS Block at sales/order/view 
 *  wrj
 *
*/
namespace Bss\Ups\Block\Adminhtml\Sales\Order\View;

use Magento\Backend\Block\Template\Context;
use Bss\Ups\Helper\Data;
use Magento\Framework\Session\SessionManagerInterface;

class Ups extends \Magento\Sales\Block\Adminhtml\Order\View\Info
{
	    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Admin helper
     *
     * @var \Magento\Sales\Helper\Admin
     */
    protected $_adminHelper;

    /**
     * Customer service
     *
     * @var \Magento\Customer\Api\CustomerMetadataInterface
     */
    protected $metadata;
    /**
     * Group service
     *
     * @var \Magento\Customer\Api\GroupRepositoryInterface
     */
    protected $groupRepository;
    /**
     * Metadata element factory
     *
     * @var \Magento\Customer\Model\Metadata\ElementFactory
     */
    protected $_metadataElementFactory;
    /**
     * @var Address\Renderer
     */
    protected $addressRenderer;

    /**
     * Sequence
     *
     * @var \Bss\Ups\Model\ResourceModel\UpsData
     */
    protected $upsData;
    protected $sessionManager;    

    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Sales\Helper\Admin $adminHelper
     * @param \Magento\Customer\Api\GroupRepositoryInterface $groupRepository
     * @param \Magento\Customer\Api\CustomerMetadataInterface $metadata
     * @param \Magento\Customer\Model\Metadata\ElementFactory $elementFactory
     * @param \Magento\Sales\Model\Order\Address\Renderer $addressRenderer
     * @param array $data
     */	
	public function __construct (
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Sales\Helper\Admin $adminHelper,
        \Magento\Customer\Api\GroupRepositoryInterface $groupRepository,
        \Magento\Customer\Api\CustomerMetadataInterface $metadata,
        \Magento\Customer\Model\Metadata\ElementFactory $elementFactory,
        \Magento\Sales\Model\Order\Address\Renderer $addressRenderer,
        \Bss\Ups\Model\ResourceModel\UpsData $upsData,
        SessionManagerInterface $sessionManager,
        array $data = []	      
    ) {
        parent::__construct($context, $registry, $adminHelper, $groupRepository, $metadata, $elementFactory, $addressRenderer, $data);
        $this->upsData = $upsData;
        $this->sessionManager = $sessionManager;
    }

    public function getUpsMessages($orderId)
    {
    	return $this->sessionManager->getData('ups_message_'.$orderId);
    }

    public function getEgprint($orderId)
    {
        return $this->sessionManager->getData('egprint'.$orderId);
    }

    public function getOrderView($orderId)
    {
        if($this->_coreRegistry->registry('order_view'.$orderId) !== null) {
            return $this->_coreRegistry->registry('order_view'.$orderId);
        } else {
            return '';
        }
    }

    public function getUpsTrackData($orderId)
    {
        $package_data = $this->upsData->show_package_data($orderId,"nein");
        if(isset($package_data) && $package_data != false){
        //shop ups tracking data
            $upsTrackData = array();
            foreach($package_data as $is){
                 $upsTrackData['paketnummer'] = $is['paketnummer'];
                 $upsTrackData['paketart'] = $is['paketart'];
                 $upsTrackData['nach'] = $is['nach'];
                 $upsTrackData['paketanzahl'] = $is['paketanzahl'];
                 $upsTrackData['paketkilo'] = $is['paketkilo'];
                 $upsTrackData['kosten'] = $is['kosten'];
                 $upsTrackData['nachnahme'] = $is['nachnahme'];
            }
            return $upsTrackData;
        } else {
            return null;
        }
    }

    public function getUpsTrackDataRetour($orderId)
    {
        $retour_data = $this->upsData->show_package_data($orderId,"ja");
        if(isset($retour_data) && $retour_data != false){
           //shop ups tracking data
             $upsTrackDataRetour = array();
             foreach($retour_data as $ir){
                 $upsTrackDataRetour['paketnummer'] = $ir['paketnummer'];
                 $upsTrackDataRetour['paketart'] = $ir['paketart'];
                 $upsTrackDataRetour['nach'] = $ir['nach'];
                 $upsTrackDataRetour['paketanzahl'] = $ir['paketanzahl'];
                 $upsTrackDataRetour['paketkilo'] = $ir['paketkilo'];
                 $upsTrackDataRetour['kosten'] = $ir['kosten'];
                 $upsTrackDataRetour['nachnahme'] = $ir['nachnahme'];
             }
             return $upsTrackDataRetour;
        } else {
            return null;
        }       
    }

    public function fetchupsdata($orderId)
    {
        return $this->upsData->checkExist($orderId,'nein');
    }

    public function removeEgprint($orderId)
    {
        return $this->sessionManager->unsetData('egprint'.$orderId);
    }

}