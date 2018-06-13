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

use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\StateException;
use Magento\Framework\Exception\ValidatorException;
use Magento\Framework\Exception\NoSuchEntityException;
use Bss\Ups\Api\UpsMessageRepositoryInterface;
use Bss\Ups\Api\Data\UpsMessageInterface;
use Bss\Ups\Api\Data\UpsMessageInterfaceFactory;
use Bss\Ups\Model\ResourceModel\UpsMessage as ResourceUpsMessage;
use Bss\Ups\Model\ResourceModel\UpsMessage\CollectionFactory as UpsMessageCollectionFactory;

class UpsMessageRepository implements UpsMessageRepositoryInterface
{
    /**
     * Instances
     *
     * @var array
     */
    protected $instances = [];

    /**
     * ResourceUpsMessage
     *
     * @var ResourceUpsMessage
     */
    protected $resource;

    /**
     * UpsMessageCollectionFactory
     *
     * @var UpsMessageCollectionFactory
     */
    protected $upsMessageCollectionFactory;

    /**
     * UpsMessageInterfaceFactory
     *
     * @var UpsMessageInterfaceFactory
     */
    protected $upsMessageInterfaceFactory;

    /**
     * DataObjectHelper
     *
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * UpsMessageRepository constructor.
     *
     * @param ResourceUpsMessage $resource
     * @param UpsMessageCollectionFactory $upsMessageCollectionFactory
     * @param UpsMessageInterfaceFactory $upsMessageInterfaceFactory
     * @param DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        ResourceUpsMessage $resource,
        UpsMessageCollectionFactory $upsMessageCollectionFactory,
        UpsMessageInterfaceFactory $upsMessageInterfaceFactory,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->resource = $resource;
        $this->upsMessageCollectionFactory = $upsMessageCollectionFactory;
        $this->upsMessageInterfaceFactory = $upsMessageInterfaceFactory;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * Save UpsMessage Data
     *
     * @param UpsMessageInterface $upsMessage
     * @return UpsMessageInterface
     * @throws CouldNotSaveException
     */
    public function save(UpsMessageInterface $upsMessage)
    {
        try {
            $this->resource->save($upsMessage);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the UpsMessage: %1',
                $exception->getMessage()
            ));
        }
        return $upsMessage;
    }

    /**
     * Get UpsMessage Data
     *
     * @param int $upsMessageId
     * @return UpsMessageInterface
     * @throws NoSuchEntityException
     */
    public function getById($upsMessageId)
    {
        if (!isset($this->instances[$upsMessageId])) {
            $upsMessage = $this->upsMessageCollectionFactory->create();
            $this->resource->load($upsMessage, $upsMessageId);
            if (!$upsMessage->getId()) {
                throw new NoSuchEntityException(__('UpsMessage with id "%1" does not exist.', $upsMessageId));
            }
            $this->instances[$upsMessageId] = $upsMessage;
        }
        return $this->instances[$upsMessageId];
    }

    /**
     * Delete UpsMessage
     *
     * @param UpsMessageInterface $upsMessage
     * @return bool
     * @throws CouldNotSaveException
     * @throws StateException
     */
    public function delete(UpsMessageInterface $upsMessage)
    {
        $id = $upsMessage->getId();
        try {
            unset($this->instances[$id]);
            $this->resource->delete($upsMessage);
        } catch (ValidatorException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new StateException(
                __('Unable to remove UpsMessage with id "%1".', $id)
            );
        }
        unset($this->instances[$id]);
        return true;
    }

    /**
     * Delete UpsMessage by ID
     *
     * @param int $upsMessageId
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($upsMessageId)
    {
        $upsMessage = $this->getById($upsMessageId);
        return $this->delete($upsMessage);
    }
}
