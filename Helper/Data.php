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
namespace Bss\Ups\Helper;

use Magento\Framework\App\Helper\Context;
use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
    /**
     * Data constructor
     *
     * @param Context $context
     */
    public function __construct(
        Context $context
    ) {
        parent::__construct($context);
    }

    /**
     * Get Config
     *
     * @param string $key
     * @param bool $store
     * @return string
     */
    public function getZugangsdaten($key, $store = null)
    {
        return $this->scopeConfig->getValue(
            'upsconfig/zugangsdaten/' . $key,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get Config
     *
     * @param string $key
     * @param bool $store
     * @return string
     */
    public function getPfade($key, $store = null)
    {
        return $this->scopeConfig->getValue(
            'upsconfig/pfade/' . $key,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    /**
     * Get Config
     *
     * @param string $key
     * @param bool $store
     * @return string
     */
    public function getVersender($key, $store = null)
    {
        return $this->scopeConfig->getValue(
            'upsconfig/versender/' . $key,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }



    /**
     * Get Config
     *
     * @param string $key
     * @param bool $store
     * @return string
     */
    public function getVersandvon($key, $store = null)
    {
        return $this->scopeConfig->getValue(
            'upsconfig/versandvon/' . $key,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }               
}
