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
namespace Bss\Ups\Model;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Exception\LocalizedException;
use Bss\Ups\Api\Data\UpsDataInterface;

class UpsData extends AbstractModel implements UpsDataInterface
{
    /**
     * UpsData Cache tag
     */
    const CACHE_TAG = 'bss_ups_data';

    /**
     * Constructor
     *
     * @param Context $context
     * @param Registry $registry
     * @param UploaderPool $uploaderPool
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialise resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Bss\Ups\Model\ResourceModel\UpsData');
    }

    /**
     * Get cache identities
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get Order Id
     *
     * @return int
     */
    public function getOrderId()
    {
        return $this->getData(UpsDataInterface::ORDER_ID);
    }

    /**
     * Get Versanddatum
     *
     * @return string
     */
    public function getVersanddatum()
    {
        return $this->getData(UpsDataInterface::VERSANDDATUM);
    }

    /**
     * Get Zustelldatum
     *
     * @return string
     */
    public function getZustelldatum()
    {
        return $this->getData(UpsDataInterface::ZUSTELLDATUM);
    }

    /**
     * Get Paketnummer
     *
     * @return string
     */
    public function getPaketnummer()
    {
        return $this->getData(UpsDataInterface::PAKETNUMMER);
    }

    /**
     * Get Paketanzahl
     *
     * @return int
     */
    public function getPaketanzahl()
    {
        return $this->getData(UpsDataInterface::PAKETANZAHL);
    }

    /**
     * Get Paketkilo
     *
     * @return string
     */
    public function getPaketkilo()
    {
        return $this->getData(UpsDataInterface::PAKETKILO);
    }

    /**
     * Get Nach
     *
     * @return int
     */
    public function getNach()
    {
        return $this->getData(UpsDataInterface::NACH);
    }

    /**
     * Get Paketart
     *
     * @return int
     */
    public function getPaketart()
    {
        return $this->getData(UpsDataInterface::PAKETART);
    }

    /**
     * Get Kosten
     *
     * @return string
     */
    public function getKosten()
    {
        return $this->getData(UpsDataInterface::KOSTEN);
    }

    /**
     * Get Nachnahme
     *
     * @return string
     */
    public function getNachnahme()
    {
        return $this->getData(UpsDataInterface::NACHNAHME);
    }

    /**
     * Get Retour
     *
     * @return int
     */
    public function getRetour()
    {
        return $this->getData(UpsDataInterface::RETOUR);
    }

    /**
     * Set Order Id
     *
     * @param string $orderId
     * @return UpsDataInterface
     */
    public function setOrderId($orderId)
    {
        return $this->setData(UpsDataInterface::ORDER_ID, $orderId);
    }

    /**
     * Set Versanddatum
     *
     * @param string $versanddatum
     * @return UpsDataInterface
     */
    public function setVersanddatum($versanddatum)
    {
        return $this->setData(UpsDataInterface::VERSANDDATUM, $versanddatum);
    }

    /**
     * Set Zustelldatum
     *
     * @param string $zustelldatum
     * @return UpsDataInterface
     */
    public function setZustelldatum($zustelldatum)
    {
        return $this->setData(UpsDataInterface::ZUSTELLDATUM, $zustelldatum);
    }

    /**
     * Set Paketnummer
     *
     * @param string $paketnummer
     * @return UpsDataInterface
     */
    public function setPaketnummer($paketnummer)
    {
        return $this->setData(UpsDataInterface::PAKETNUMMER, $paketnummer);
    }

    /**
     * Set Paketanzahl
     *
     * @param string $paketanzahl
     * @return UpsDataInterface
     */
    public function setPaketanzahl($paketanzahl)
    {
        return $this->setData(UpsDataInterface::PAKETANZAHL, $paketanzahl);
    }

    /**
     * Set Paketkilo
     *
     * @param string $paketkilo
     * @return UpsDataInterface
     */
    public function setPaketkilo($paketkilo)
    {
        return $this->setData(UpsDataInterface::PAKETKILO, $paketkilo);
    }

    /**
     * Set Nach
     *
     * @param int $nach
     * @return UpsDataInterface
     */
    public function setNach($nach)
    {
        return $this->setData(UpsDataInterface::NACH, $nach);
    }

    /**
     * Set Paketart
     *
     * @param int $paketart
     * @return UpsDataInterface
     */
    public function setPaketart($paketart)
    {
        return $this->setData(UpsDataInterface::PAKETART, $paketart);
    }

    /**
     * Set Kosten
     *
     * @param string $kosten
     * @return UpsDataInterface
     */
    public function setKosten($kosten)
    {
        return $this->setData(UpsDataInterface::KOSTEN, $kosten);
    }

    /**
     * Set Nachnahme
     *
     * @param string $nachnahme
     * @return UpsDataInterface
     */
    public function setNachnahme($nachnahme)
    {
        return $this->setData(UpsDataInterface::NACHNAHME, $nachnahme);
    }

    /**
     * Set Retour
     *
     * @param int $retour
     * @return UpsDataInterface
     */
    public function setRetour($retour)
    {
        return $this->setData(UpsDataInterface::RETOUR, $retour);
    }
}
