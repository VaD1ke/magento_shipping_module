<?php
/**
 * Oggetto Shipping extension for Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade
 * the Oggetto Shipping module to newer versions in the future.
 * If you wish to customize the Oggetto Shipping module for your needs
 * please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Oggetto
 * @package    Oggetto_Shipping
 * @copyright  Copyright (C) 2015 Oggetto Web (http://oggettoweb.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Helper data test class
 *
 * @category   Oggetto
 * @package    Oggetto_Shipping
 * @subpackage Helper
 * @author     Vladislav Slesarenko <vslesarenko@oggettoweb.com>
 */
class Oggetto_Shipping_Test_Helper_Data extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Return shipping origin country id from store config
     *
     * @return void
     *
     * @loadFixture
     */
    public function testShippingOriginCountryIdFromStoreConfig()
    {
        $shippingCountry = Mage::helper('oggetto_shipping')->getShippingOriginCountryId();
        $this->assertEquals('test country', $shippingCountry);
    }

    /**
     * Return shipping origin region id from store config
     *
     * @return void
     *
     * @loadFixture
     */
    public function testShippingOriginRegionIdFromStoreConfig()
    {
        $shippingRegion = Mage::helper('oggetto_shipping')->getShippingOriginRegionId();
        $this->assertEquals('test region', $shippingRegion);
    }

    /**
     * Return shipping origin city from store config
     *
     * @return void
     *
     * @loadFixture
     */
    public function testShippingOriginCityFromStoreConfig()
    {
        $shippingCity = Mage::helper('oggetto_shipping')->getShippingOriginCity();
        $this->assertEquals('test city', $shippingCity);
    }
}