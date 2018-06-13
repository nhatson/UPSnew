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
namespace Bss\Ups\Model\ResourceModel\UpsData;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * FieldName
     *
     * @var string
     */
    protected $_idFieldName = 'id';

    /**
     * Collection initialisation
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Bss\Ups\Model\UpsData',
            'Bss\Ups\Model\ResourceModel\UpsData'
        );
    }
}
