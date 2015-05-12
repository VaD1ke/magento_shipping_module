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
 * Api model for shipping
 *
 * @category   Oggetto
 * @package    Oggetto_Shipping
 * @subpackage Test
 * @author     Vladislav Slesarenko <vslesarenko@oggettoweb.com>
 */
class Oggetto_Shipping_Test_Model_Api_Shipping extends EcomDev_PHPUnit_Test_Case
{

    /**
     * Return prices calculated by oggetto shipping API
     *
     * @param array  $orig         origin location
     * @param array  $dest         destination location
     * @param string $responseBody response body
     *
     * @return void
     *
     * @dataProvider dataProvider
     */
    public function testReturnsPricesCalculatedByOggettoShippingApi($orig, $dest, $responseBody)
    {
        $httpClientMock = $this->getMock('Varien_Http_Client', ['resetParameters', 'setParameterGet', 'request']);

        $httpResponseMock = $this->getMockBuilder('Zend_Http_Response')
            ->disableOriginalConstructor()
            ->setMethods(['getStatus', 'getBody'])
            ->getMock();

        $httpResponseMock->expects($this->once())
            ->method('getStatus')
            ->willReturn(200);

        $httpResponseMock->expects($this->once())
            ->method('getBody')
            ->willReturn($responseBody);

        $httpClientMock->expects($this->once())
            ->method('resetParameters')
            ->with(true);

        $httpClientMock->expects($this->once())
            ->method('setParameterGet')
            ->with($this->expected('get_parameter')->getData());

        $httpClientMock->expects($this->once())
            ->method('request')
            ->with('GET')
            ->willReturn($httpResponseMock);

        $apiMock = $this->getModelMock('oggetto_shipping/api_shipping', ['_getHttpClient']);

        $apiMock->expects($this->once())
            ->method('_getHttpClient')
            ->willReturn($httpClientMock);

        $this->replaceByMock('model', 'oggetto_shipping/api_shipping', $apiMock);

        $this->assertEquals(
            $this->expected()->getData()['calculated_prices'],
            $apiMock->calculatePrices($orig, $dest)
        );
    }
}
