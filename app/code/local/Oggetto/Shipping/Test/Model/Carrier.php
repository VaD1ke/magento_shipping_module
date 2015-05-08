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
 * Carrier model test class
 *
 * @category   Oggetto
 * @package    Oggetto_Shipping
 * @subpackage Test
 * @author     Vladislav Slesarenko <vslesarenko@oggettoweb.com>
 */
class Oggetto_Shipping_Test_Model_Carrier extends EcomDev_PHPUnit_Test_Case
{
    /**
     * Model questions
     *
     * @var Oggetto_Shipping_Model_Carrier
     */
    protected $_modelCarrier = null;

    /**
     * Set up initial variables
     *
     * @return void
     */
    protected function setUp()
    {
        parent::setUp();
        $this->_modelCarrier = Mage::getModel('oggetto_shipping/carrier');
    }

    /**
     * Return is active status from config
     *
     * @return void
     *
     * @loadFixture testReturnsIsActiveStatusFromConfig
     */
    public function testReturnsIsActiveStatusFromConfig()
    {
        $this->assertEquals(true, $this->_modelCarrier->isActive());
    }

    /**
     * Return is not active status from config
     *
     * @return void
     *
     * @loadFixture
     */
    public function testReturnsIsNotActiveStatusFromConfig()
    {
        $this->assertEquals(false, $this->_modelCarrier->isActive());
    }

    /**
     * Return shipping rate result model
     *
     * @return void
     */
    public function testReturnsShippingRateResultModel()
    {

    }

    /**
     * Return allowed methods array
     *
     * @return void
     */
    public function testReturnsAllowedMethodsArray()
    {
        $this->assertEquals($this->expected()->getData(), $this->_modelCarrier->getAllowedMethods());
    }
}
