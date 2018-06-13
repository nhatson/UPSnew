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
use Bss\Ups\Api\Data\UpsMessageInterface;

class UpsMessage extends AbstractModel implements UpsMessageInterface
{
    /**
     * UpsMessage Cache tag
     */
    const CACHE_TAG = 'bss_ups_message';

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
        $this->_init('Bss\Ups\Model\ResourceModel\UpsMessage');
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
        return $this->getData(UpsMessageInterface::ORDER_ID);
    }

    /**
     * Get Error
     *
     * @return int
     */
    public function getError()
    {
        return $this->getData(UpsMessageInterface::ERROR);
    }

    /**
     * Get Message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->getData(UpsMessageInterface::MESSAGE);
    }

    /**
     * Get Read
     *
     * @return string
     */
    public function getRead()
    {
        return $this->getData(UpsMessageInterface::READ);
    }

    /**
     * Get Create
     *
     * @return string
     */
    public function getCreate()
    {
        return $this->getData(UpsMessageInterface::CREATE);
    }

    /**
     * Set Order Id
     *
     * @param string $orderId
     * @return UpsMessageInterface
     */
    public function setOrderId($orderId)
    {
        return $this->setData(UpsMessageInterface::ORDER_ID, $orderId);
    }

    /**
     * Set Error
     *
     * @param string $error
     * @return UpsMessageInterface
     */
    public function setError($error)
    {
        return $this->setData(UpsMessageInterface::ERROR, $error);
    }

    /**
     * Set Message
     *
     * @param string $message
     * @return UpsMessageInterface
     */
    public function setMessage($message)
    {
        return $this->setData(UpsMessageInterface::MESSAGE, $message);
    }

    /**
     * Set Read
     *
     * @param string $read
     * @return UpsMessageInterface
     */
    public function setRead($read)
    {
        return $this->setData(UpsMessageInterface::READ, $read);
    }

    /**
     * Set Create
     *
     * @param string $create
     * @return UpsMessageInterface
     */
    public function setCreate($create)
    {
        return $this->setData(UpsMessageInterface::CREATE, $create);
    }
}
