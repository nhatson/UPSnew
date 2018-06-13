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
use Bss\Ups\Api\UpsDataRepositoryInterface;
use Bss\Ups\Api\Data\UpsDataInterface;
use Bss\Ups\Api\Data\UpsDataInterfaceFactory;
use Bss\Ups\Model\ResourceModel\UpsData as ResourceUpsData;
use Bss\Ups\Model\ResourceModel\UpsData\CollectionFactory as UpsDataCollectionFactory;

class UpsDataRepository implements UpsDataRepositoryInterface
{
    /**
     * Instances
     *
     * @var array
     */
    protected $instances = [];

    /**
     * ResourceUpsData
     *
     * @var ResourceUpsData
     */
    protected $resource;

    /**
     * UpsDataCollectionFactory
     *
     * @var UpsDataCollectionFactory
     */
    protected $upsDataCollectionFactory;

    /**
     * UpsDataInterfaceFactory
     *
     * @var UpsDataInterfaceFactory
     */
    protected $upsDataInterfaceFactory;

    /**
     * DataObjectHelper
     *
     * @var DataObjectHelper
     */
    protected $dataObjectHelper;

    /**
     * UpsDataRepository constructor.
     *
     * @param ResourceUpsData $resource
     * @param UpsDataCollectionFactory $upsDataCollectionFactory
     * @param UpsDataInterfaceFactory $upsDataInterfaceFactory
     * @param DataObjectHelper $dataObjectHelper
     */
    public function __construct(
        ResourceUpsData $resource,
        UpsDataCollectionFactory $upsDataCollectionFactory,
        UpsDataInterfaceFactory $upsDataInterfaceFactory,
        DataObjectHelper $dataObjectHelper
    ) {
        $this->resource = $resource;
        $this->UpsDataCollectionFactory = $upsDataCollectionFactory;
        $this->UpsDataInterfaceFactory = $upsDataInterfaceFactory;
        $this->dataObjectHelper = $dataObjectHelper;
    }

    /**
     * Save UpsData Data
     *
     * @param UpsDataInterface $upsData
     * @return UpsDataInterface
     * @throws CouldNotSaveException
     */
    public function save(UpsDataInterface $upsData)
    {
        try {
            $this->resource->save($upsData);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the UpsData: %1',
                $exception->getMessage()
            ));
        }
        return $upsData;
    }

    /**
     * Get UpsData Data
     *
     * @param int $upsDataId
     * @return UpsDataInterface
     * @throws NoSuchEntityException
     */
    public function getById($upsDataId)
    {
        if (!isset($this->instances[$upsDataId])) {
            $upsData = $this->UpsDataInterfaceFactory->create();
            $this->resource->load($upsData, $upsDataId);
            if (!$upsData->getId()) {
                throw new NoSuchEntityException(__('UpsData with id "%1" does not exist.', $upsDataId));
            }
            $this->instances[$upsDataId] = $upsData;
        }
        return $this->instances[$upsDataId];
    }

    /**
     * Delete UpsData
     *
     * @param UpsDataInterface $upsData
     * @return bool
     * @throws CouldNotSaveException
     * @throws StateException
     */
    public function delete(UpsDataInterface $upsData)
    {
        $id = $upsData->getId();
        try {
            unset($this->instances[$id]);
            $this->resource->delete($upsData);
        } catch (ValidatorException $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new StateException(
                __('Unable to remove UpsData with id "%1".', $id)
            );
        }
        unset($this->instances[$id]);
        return true;
    }

    /**
     * Delete UpsData by ID
     *
     * @param int $upsDataId
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteById($upsDataId)
    {
        $upsData = $this->getById($upsDataId);
        return $this->delete($upsData);
    }
}
