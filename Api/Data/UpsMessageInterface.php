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
 * UpsMessage Interface.
 * @api
 */
interface UpsMessageInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    const ID            = 'id';
    const ORDER_ID      = 'order_id';
    const ERROR         = 'error';
    const MESSAGE       = 'message';
    const READ          = 'read';
    const CREATE        = 'create';

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
     * Get Error
     *
     * @return int
     */
    public function getError();

    /**
     * Get Message
     *
     * @return string
     */
    public function getMessage();

    /**
     * Get Read
     *
     * @return string
     */
    public function getRead();

    /**
     * Get Create
     *
     * @return int
     */
    public function getCreate();

    /**
     * Set ID
     *
     * @param int $id
     * @return UpsMessageInterface
     */
    public function setId($id);
    
    /**
     * Set OrderId
     *
     * @param string $orderId
     * @return UpsMessageInterface
     */
    public function setOrderId($orderId);

    /**
     * Set Error
     *
     * @param string $error
     * @return UpsMessageInterface
     */
    public function setError($error);

    /**
     * Set Message
     *
     * @param string $message
     * @return UpsMessageInterface
     */
    public function setMessage($message);

    /**
     * Set Read
     *
     * @param string $read
     * @return UpsMessageInterface
     */
    public function setRead($read);

    /**
     * Set Create
     *
     * @param string $create
     * @return UpsMessageInterface
     */
    public function setCreate($create);
}
