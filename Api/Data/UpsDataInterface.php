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
namespace Bss\Ups\Api\Data;

/**
 * UpsData Interface.
 * @api
 */
interface UpsDataInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ID            = 'id';
    const ORDER_ID      = 'order_id';
    const VERSANDDATUM  = 'versanddatum';
    const ZUSTELLDATUM  = 'zustelldatum';
    const PAKETNUMMER   = 'paketnummer';
    const PAKETANZAHL   = 'paketanzahl';
    const PAKETKILO     = 'paketkilo';
    const NACH          = 'nach';
    const PAKETART      = 'paketart';
    const KOSTEN        = 'kosten';
    const NACHNAHME     = 'nachnahme';
    const RETOUR        = 'retour';

    /**
     * Get ID
     *
     * @return int
     */
    public function getId();

    /**
     * Get Order Id
     *
     * @return int
     */
    public function getOrderId();

    /**
     * Get Versanddatum
     *
     * @return string
     */
    public function getVersanddatum();

    /**
     * Get Zustelldatum
     *
     * @return string
     */
    public function getZustelldatum();

    /**
     * Get Paketnummer
     *
     * @return string
     */
    public function getPaketnummer();

    /**
     * Get Paketanzahl
     *
     * @return int
     */
    public function getPaketanzahl();

    /**
     * Get Paketkilo
     *
     * @return string
     */
    public function getPaketkilo();

    /**
     * Get Nach
     *
     * @return bool|null
     */
    public function getNach();

    /**
     * Get Paketart
     *
     * @return string
     */
    public function getPaketart();

    /**
     * Get Kosten
     *
     * @return string
     */
    public function getKosten();

    /**
     * Get Nachnahme
     *
     * @return string
     */
    public function getNachnahme();

    /**
     * Get Retour
     *
     * @return int
     */
    public function getRetour();

    /**
     * Set ID
     *
     * @param int $id
     * @return UpsDataInterface
     */
    public function setId($id);

    /**
     * Set OrderId
     *
     * @param string $orderId
     * @return UpsDataInterface
     */
    public function setOrderId($orderId);

    /**
     * Set Versanddatum
     *
     * @param string $versanddatum
     * @return UpsDataInterface
     */
    public function setVersanddatum($versanddatum);

    /**
     * Set Zustelldatum
     *
     * @param string $zustelldatum
     * @return UpsDataInterface
     */
    public function setZustelldatum($zustelldatum);

    /**
     * Set Paketnummer
     *
     * @param string $paketnummer
     * @return UpsDataInterface
     */
    public function setPaketnummer($paketnummer);

    /**
     * Set Paketanzahl
     *
     * @param string $paketanzahl
     * @return UpsDataInterface
     */
    public function setPaketanzahl($paketanzahl);

    /**
     * Set Paketkilo
     *
     * @param string $paketkilo
     * @return UpsDataInterface
     */
    public function setPaketkilo($paketkilo);

    /**
     * Set Nach
     *
     * @param bool|int $nach
     * @return UpsDataInterface
     */
    public function setNach($nach);

    /**
     * Set Paketart
     *
     * @param int $paketart
     * @return UpsDataInterface
     */
    public function setPaketart($paketart);
    
    /**
     * Set Kosten
     *
     * @param string $kosten
     * @return UpsDataInterface
     */
    public function setKosten($kosten);

    /**
     * Set Nachnahme
     *
     * @param string $nachnahme
     * @return UpsDataInterface
     */
    public function setNachnahme($nachnahme);

    /**
     * Set Retour
     *
     * @param int $retour
     * @return UpsDataInterface
     */
    public function setRetour($retour);
}
