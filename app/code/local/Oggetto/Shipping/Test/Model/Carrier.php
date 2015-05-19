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
     * @param array $providerOrig original address
     * @param array $providerDest destination address
     *
     * @return void
     *
     * @dataProvider dataProvider
     */
    public function testReturnsShippingRateResultModelWithCalculatedPrices($providerOrig, $providerDest)
    {
        $originWithNames = [
            'country' => $this->expected('from_country')->getCountryName(),
            'region'  => $this->expected('from_country')->getRegionName(),
            'city'    => $providerOrig['city']
        ];

        $destinationWithNames = [
            'country' => $this->expected('to_country')->getCountryName(),
            'region'  => $this->expected('to_country')->getRegionName(),
            'city'    => $providerDest['city']
        ];

        $testCurrencyCode = $this->expected()->getCurrencyCode();

        $methodsQuantity = count($this->expected()->getData()['methods']);
        $methodsNames = array_values($this->expected()->getData()['methods']);

        $prices = $this->expected('prices')->getData();

        $rateResultMethod = new Mage_Shipping_Model_Rate_Result_Method;


        $request = $this->_getRequestWithEstablishedDestinationCountryRegionAndCity($providerDest);


        $modelCarrierMock = $this->getModelMock('oggetto_shipping/carrier', [
            'isActive',
            'getConfigData',
            'getRateMethod',
            'getOriginAddress',
            'getDestinationAddress',
            '_getCurrentCurrencyCode'
        ]);

        $modelCarrierMock->expects($this->once())
            ->method('isActive')
            ->willReturn(true);

        $modelCarrierMock->expects($this->once())
            ->method('getOriginAddress')
            ->willReturn($originWithNames);

        $modelCarrierMock->expects($this->once())
            ->method('getDestinationAddress')
            ->with($request)
            ->willReturn($destinationWithNames);

        $modelCarrierMock->expects($this->once())
            ->method('_getCurrentCurrencyCode')
            ->willReturn($testCurrencyCode);

        foreach ($methodsNames as $index => $methodName) {
            $modelCarrierMock->expects($this->at($index + 4))
                ->method('getRateMethod')
                ->with($methodName, $prices[$methodName])
                ->willReturn($rateResultMethod);
        }


        $this->_mockOggettoShippingApiModelForCalculatingPricesAndGetCurrency(
            $originWithNames, $destinationWithNames, $prices, $testCurrencyCode
        );

        $this->_mockDirectoryHelperForCurrencyConvertingCalculatedPrices($prices, $testCurrencyCode);


        $modelRateMock = $this->getModelMock('shipping/rate_result', ['append']);

        $modelRateMock->expects($this->exactly($methodsQuantity))
            ->method('append');


        $this->replaceByMock('model', 'oggetto_shipping/carrier', $modelCarrierMock);
        $this->replaceByMock('model', 'shipping/rate_result', $modelRateMock);


        $modelCarrierMock->collectRates($request);
    }

    /**
     * Return false in collectRates method with exception in calculated prices
     *
     * @param array $providerOrig original address
     * @param array $providerDest destination address
     *
     * @return void
     *
     * @dataProvider dataProvider
     */
    public function testReturnsFalseInCollectRatesMethodWithExceptionInCalculatedPrices($providerOrig, $providerDest)
    {
        $originWithNames = [
            'country' => $this->expected('from_country')->getCountryName(),
            'region'  => $this->expected('from_country')->getRegionName(),
            'city'    => $providerOrig['city']
        ];

        $destinationWithNames = [
            'country' => $this->expected('to_country')->getCountryName(),
            'region'  => $this->expected('to_country')->getRegionName(),
            'city'    => $providerDest['city']
        ];


        $request = $this->_getRequestWithEstablishedDestinationCountryRegionAndCity($providerDest);


        $modelCarrierMock = $this->getModelMock('oggetto_shipping/carrier', [
            'isActive',
            'getOriginAddress',
            'getDestinationAddress',
            '_getCurrentCurrencyCode'
        ]);

        $modelCarrierMock->expects($this->once())
            ->method('isActive')
            ->willReturn(true);

        $modelCarrierMock->expects($this->once())
            ->method('getOriginAddress')
            ->willReturn($originWithNames);

        $modelCarrierMock->expects($this->once())
            ->method('getDestinationAddress')
            ->with($request)
            ->willReturn($destinationWithNames);

        $modelCarrierMock->expects($this->never())
            ->method('_getCurrentCurrencyCode');


        $modelApiMock = $this->getModelMock('oggetto_shipping/api_shipping', ['calculatePrices']);

        $modelApiMock->expects($this->once())
            ->method('calculatePrices')
            ->with($originWithNames, $destinationWithNames)
            ->willThrowException(new Oggetto_Shipping_Model_Exceptions_CalculatePricesError);

        $this->replaceByMock('model', 'oggetto_shipping/carrier', $modelCarrierMock);
        $this->replaceByMock('model', 'oggetto_shipping/api_shipping', $modelApiMock);

        $this->assertFalse($modelCarrierMock->collectRates($request));
    }

    /**
     * Return false in collectRates method when Oggetto Shipping Method is not active
     *
     * @return void
     */
    public function testReturnsFalseInCollectRatesMethodWhenOggettoShippingMethodIsNotActive()
    {
        $modelCarrierMock = $this->getModelMock('oggetto_shipping/carrier', ['isActive']);

        $modelCarrierMock->expects($this->once())
            ->method('isActive');

        $this->replaceByMock('model', 'oggetto_shipping/carrier', $modelCarrierMock);

        $this->assertFalse($modelCarrierMock->collectRates(new Mage_Shipping_Model_Rate_Request));
    }

    /**
     * Return rate method with established params for collect rates method
     *
     * @return void
     */
    public function testReturnsRateMethodWithEstablishedParamsForCollectRatesMethod()
    {
        $carrier = $this->expected()->getCarrier();
        $configTitle = $this->expected()->getConfigTitle();
        $method = $this->expected()->getMethodName();
        $price = $this->expected()->getPrice();

        $modelCarrierMock = $this->getModelMock('oggetto_shipping/carrier', ['getConfigData']);

        $modelCarrierMock->expects($this->once())
            ->method('getConfigData')
            ->with('title')
            ->willReturn($configTitle);

        $this->replaceByMock('model', 'oggetto_shipping/carrier', $modelCarrierMock);


        $modelRateMethodMock = $this->getModelMock('shipping/rate_result_method', [
            'setCarrier', 'setCarrierTitle',
            'setMethod',  'setMethodTitle',
            'setPrice',   'setCost'
        ]);

        $modelRateMethodMock->expects($this->once())
            ->method('setCarrier')
            ->with($carrier);

        $modelRateMethodMock->expects($this->once())
            ->method('setCarrierTitle')
            ->with($configTitle);

        $modelRateMethodMock->expects($this->once())
            ->method('setMethod')
            ->with($method);

        $modelRateMethodMock->expects($this->once())
            ->method('setMethodTitle')
            ->with(ucfirst($method));

        $modelRateMethodMock->expects($this->once())
            ->method('setPrice')
            ->with($price);

        $modelRateMethodMock->expects($this->once())
            ->method('setCost')
            ->with($price);

        $this->replaceByMock('model', 'shipping/rate_result_method', $modelRateMethodMock);

        $this->assertEquals($modelRateMethodMock, $modelCarrierMock->getRateMethod($method, $price));
    }

    /**
     * Return origin address from config
     *
     * @param array $originAddress          origin address with country and region id
     * @param array $originAddressWithNames origin address with country and region names
     *
     * @return void
     *
     * @dataProvider dataProvider
     */
    public function testReturnsOriginAddressFromConfig($originAddress, $originAddressWithNames)
    {
        $this->_mockOggettoShippingHelperDataForGettingOriginAddressFromStoreConfig($originAddress);

        $modelCarrierMock = $this->
                _getOggettoShippingCarrierMockForGettingCountryAndRegionNames($originAddress, $originAddressWithNames);

        $this->assertEquals($originAddressWithNames, $modelCarrierMock->getOriginAddress($originAddress));
    }

    /**
     * Return destination address from request
     *
     * @param array $destAddress          destination address with country and region id
     * @param array $destAddressWithNames destination address with country and region names
     *
     * @return void
     *
     * @dataProvider dataProvider
     */
    public function testReturnsDestinationAddressFromRequest($destAddress, $destAddressWithNames)
    {
        $request = $this->_getRequestWithEstablishedDestinationCountryRegionAndCity($destAddress);

        $modelCarrierMock = $this->
                _getOggettoShippingCarrierMockForGettingCountryAndRegionNames($destAddress, $destAddressWithNames);

        $this->assertEquals($destAddressWithNames, $modelCarrierMock->getDestinationAddress($request));
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


    /**
     * Get request with established destination country, region and city
     *
     * @param array $destination destination address
     * @return Mage_Shipping_Model_Rate_Request
     */
    protected function _getRequestWithEstablishedDestinationCountryRegionAndCity($destination)
    {
        $request = new Mage_Shipping_Model_Rate_Request;

        $request->setDestCountryId($destination['country']);
        $request->setDestRegionId($destination['region']);
        $request->setDestCity($destination['city']);

        return $request;
    }

    /**
     * Mock Oggetto Shipping Carrier Model for getting country and region names by id
     *
     * @param array $addressWithIds   address with region and country ids
     * @param array $addressWithNames address with region and country names
     *
     * @return EcomDev_PHPUnit_Mock_Proxy
     *
     * @dataProvider dataProvider
     */
    protected function _getOggettoShippingCarrierMockForGettingCountryAndRegionNames($addressWithIds, $addressWithNames)
    {
        $modelCarrierMock = $this->getModelMock('oggetto_shipping/carrier', [
            '_getCountryNameById',
            '_getRegionNameById'
        ]);

        foreach (['country', 'region'] as $index => $location) {
            $modelCarrierMock->expects($this->at($index))
                ->method('_get' . ucfirst($location) . 'NameById')
                ->with($addressWithIds[$location])
                ->willReturn($addressWithNames[$location]);
        }

        $this->replaceByMock('model', 'oggetto_shipping/carrier', $modelCarrierMock);

        return $modelCarrierMock;
    }

    /**
     * Get Oggetto Shipping Helper Data Mock gor getting origin address from store config
     *
     * @param array $origin origin address
     * @return void
     */
    protected function _mockOggettoShippingHelperDataForGettingOriginAddressFromStoreConfig($origin)
    {
        $helperMock = $this->getHelperMock('oggetto_shipping', [
                'getShippingOriginCountryId',
                'getShippingOriginRegionId',
                'getShippingOriginCity']
        );

        $helperMock->expects($this->once())
            ->method('getShippingOriginCountryId')
            ->willReturn($origin['country']);

        $helperMock->expects($this->once())
            ->method('getShippingOriginRegionId')
            ->willReturn($origin['region']);

        $helperMock->expects($this->once())
            ->method('getShippingOriginCity')
            ->willReturn($origin['city']);

        $this->replaceByMock('helper', 'oggetto_shipping', $helperMock);
    }

    /**
     * Mock Oggetto Shipping Api Model for calculating prices and get currency
     *
     * @param array  $orig     original address
     * @param array  $dest     destination address
     * @param array  $prices   calculated shipping prices
     * @param string $currency currency code
     *
     * @return void
     */
    protected function _mockOggettoShippingApiModelForCalculatingPricesAndGetCurrency($orig, $dest, $prices, $currency)
    {
        $modelApiMock = $this->getModelMock('oggetto_shipping/api_shipping', [
            'calculatePrices', 'getCurrency'
        ]);

        $modelApiMock->expects($this->once())
            ->method('calculatePrices')
            ->with($orig, $dest)
            ->willReturn($prices);

        $modelApiMock->expects($this->once())
            ->method('getCurrency')
            ->willReturn($currency);


        $this->replaceByMock('model', 'oggetto_shipping/api_shipping', $modelApiMock);
    }

    /**
     * Mock Directory Helper Mock for currency converting calculated prices
     *
     * @param array  $prices       calculated prices
     * @param string $currencyCode currency code
     * @return void
     */
    protected function _mockDirectoryHelperForCurrencyConvertingCalculatedPrices($prices, $currencyCode)
    {
        $helperDirectoryMock = $this->getHelperMock('directory', ['currencyConvert']);

        $i = 0;
        foreach ($prices as $price) {
            $helperDirectoryMock->expects($this->at($i++))
                ->method('currencyConvert')
                ->with($price, $currencyCode, $currencyCode)
                ->willReturn($price);
        }

        $this->replaceByMock('helper', 'directory', $helperDirectoryMock);
    }
}
