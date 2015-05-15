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
     * Return prices/error calculated by oggetto shipping API
     *
     * @param string $status       status of calculation
     * @param array  $orig         origin location
     * @param array  $dest         destination location
     * @param string $responseBody response body with prices
     *
     * @return void
     *
     * @dataProvider dataProvider
     */
    public function testReturnsPricesCalculatedByOggettoShippingApi($status, $orig, $dest, $responseBody)
    {
        $httpResponseMock = $this->
                    _getHttpResponseMockWithGetStatusAndBodyMethodsAndDisabledConstructor(200, $responseBody);

        $httpClientMock = $this->
                    _getHttpClientMockWithResetAndSetParametersMethodsAndRequest($httpResponseMock);

        $apiMock = $this->
                    _getOggettoShippingApiMockWithProtectedGetHttpClientMethodAndReplaceIt($httpClientMock);

        $this->assertEquals(
            $this->expected($status)->getData()['result'],
            $apiMock->calculatePrices($orig, $dest)
        );
    }


    /**
     * Return empty array when response is not 200(OK) from calculating prices by oggetto shipping API
     *
     * @param array $orig origin location
     * @param array $dest destination location
     *
     * @return void
     *
     * @dataProvider dataProvider
     */
    public function testReturnsEmptyArrayFromCalculatingPricesByOggettoShippingApi($orig, $dest)
    {
        $httpResponseMock = $this->
                _getHttpResponseMockWithGetStatusAndBodyMethodsAndDisabledConstructor(777);

        $httpClientMock = $this->
                _getHttpClientMockWithResetAndSetParametersMethodsAndRequest($httpResponseMock);

        $apiMock = $this->_getOggettoShippingApiMockWithProtectedGetHttpClientMethodAndReplaceIt($httpClientMock);

        $this->assertEquals([], $apiMock->calculatePrices($orig, $dest));
    }

    /**
     * Get Http Response mock with get status and body methods and disabled constructor
     *
     * @param int    $status expected response status
     * @param string $body   expected response body
     *
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getHttpResponseMockWithGetStatusAndBodyMethodsAndDisabledConstructor($status, $body = null)
    {
        $httpResponseMock = $this->getMockBuilder('Zend_Http_Response')
            ->disableOriginalConstructor()
            ->setMethods(['getStatus', 'getBody'])
            ->getMock();

        $httpResponseMock->expects($this->once())
            ->method('getStatus')
            ->willReturn($status);

        if (is_null($body)) {
            $httpResponseMock->expects($this->never())
                ->method('getBody');
        } else {
            $httpResponseMock->expects($this->once())
                ->method('getBody')
                ->willReturn($body);
        }

        return $httpResponseMock;
    }

    /**
     * Get Http Client mock with reset and set parameters methods and request
     *
     * @param PHPUnit_Framework_MockObject_MockObject $httpResponseMock Http Response Mock
     *
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getHttpClientMockWithResetAndSetParametersMethodsAndRequest($httpResponseMock)
    {
        $httpClientMock = $this->getMock('Varien_Http_Client', ['resetParameters', 'setParameterGet', 'request']);

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

        return $httpClientMock;
    }

    /**
     * Get Oggetto Shipping Api mock with protected getHttpClient method and replace model by this mock
     *
     * @param PHPUnit_Framework_MockObject_MockObject $httpClientMock Http Client Mock
     *
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getOggettoShippingApiMockWithProtectedGetHttpClientMethodAndReplaceIt($httpClientMock)
    {
        $apiMock = $this->getModelMock('oggetto_shipping/api_shipping', ['_getHttpClient']);

        $apiMock->expects($this->once())
            ->method('_getHttpClient')
            ->willReturn($httpClientMock);

        $this->replaceByMock('model', 'oggetto_shipping/api_shipping', $apiMock);

        return $apiMock;
    }
}
