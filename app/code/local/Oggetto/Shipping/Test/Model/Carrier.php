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
     * Return shipping rate result model
     *
     * @return void
     */
    public function testReturnsShippingRateResultModel()
    {
        $modelRateResultMock = $this->getModelMock('shipping/rate_result', []);
        $this->replaceByMock('model', 'shipping/rate_result', $modelRateResultMock);

        $this->assertEquals($modelRateResultMock,
            $this->_modelCarrier->collectRates(new Mage_Shipping_Model_Rate_Request));
    }

    /**
     * Return allowed methods array
     *
     * @return void
     */
    public function testReturnsAllowedMethodsArray()
    {
        $this->assertEquals(['standard' => 'Standard'], $this->_modelCarrier->getAllowedMethods());
    }
}
