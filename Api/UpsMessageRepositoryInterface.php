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

use Bss\Ups\Api\Data\UpsMessageInterface;

/**
 * UpsMessage CRUD interface.
 * @api
 */
interface UpsMessageRepositoryInterface
{
    /**
     * Save UpsMessage.
     *
     * @param UpsMessageInterface $upsMessage
     * @return UpsMessageInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(UpsMessageInterface $upsMessage);

    /**
     * Retrieve UpsMessage.
     *
     * @param int $upsMessageId
     * @return UpsMessageInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($upsMessageId);

    /**
     * Delete UpsMessage.
     *
     * @param UpsMessageInterface $upsMessage
     * @return bool true on success
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(UpsMessageInterface $upsMessage);

    /**
     * Delete UpsMessage by ID.
     *
     * @param int $upsMessageId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($upsMessageId);
}
