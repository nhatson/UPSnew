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
namespace Bss\Ups\Api;

use Bss\Ups\Api\Data\UpsDataInterface;

/**
 * UpsData CRUD interface.
 * @api
 */
interface UpsDataRepositoryInterface
{
    /**
     * Save UpsData.
     *
     * @param UpsDataInterface $upsData
     * @return UpsDataInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(UpsDataInterface $upsData);

    /**
     * Retrieve UpsData.
     *
     * @param int $upsDataId
     * @return UpsDataInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($upsDataId);

    /**
     * Delete UpsData.
     *
     * @param UpsDataInterface $upsData
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(UpsDataInterface $upsData);

    /**
     * Delete UpsData by ID.
     *
     * @param int $upsDataId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($upsDataId);
}
