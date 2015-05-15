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
        $testConfigTitle = $this->expected()->getConfigTitle();

        $methodsQuantity = count($this->expected()->getData()['methods']);
        $methodsNames = array_values($this->expected()->getData()['methods']);

        $prices = $this->expected('prices')->getData();


        $request = $this->_getRequestWithEstablishedDestinationCountryRegionAndCity($providerDest);


        $this->_mockOggettoShippingHelperDataForGettingOriginAddressFromStoreConfig($providerOrig);


        $modelCarrierMock = $this->getModelMock('oggetto_shipping/carrier', [
            'getConfigData',
            '_getCountryNameById',
            '_getRegionNameById',
            '_getCurrentCurrencyCode'
        ]);

        foreach (['country', 'region'] as $index => $location) {

            $modelCarrierMock->expects($this->at($index))
                ->method('_get' . ucfirst($location) . 'NameById')
                ->with($providerOrig[$location])
                ->willReturn($originWithNames[$location]);

            $modelCarrierMock->expects($this->at($index + 2))
                ->method('_get' . ucfirst($location) . 'NameById')
                ->with($providerDest[$location])
                ->willReturn($destinationWithNames[$location]);

        }

        $modelCarrierMock->expects($this->once())
            ->method('_getCurrentCurrencyCode')
            ->willReturn($testCurrencyCode);

        $modelCarrierMock->expects($this->exactly($methodsQuantity))
            ->method('getConfigData')
            ->with('title')
            ->willReturn($testConfigTitle);


        $this->_mockOggettoShippingApiModelForCalculatingPricesAndGetCurrency(
            $originWithNames, $destinationWithNames, $prices, $testCurrencyCode
        );


        $this->_mockCurrencyModelForSettingCurrencyCode($testCurrencyCode);

        $this->_mockDirectoryHelperMockForCurrencyConvertingCalculatedPrices($prices, $testCurrencyCode);

        $modelRateMethodMock = $this->getModelMock('shipping/rate_result_method', [
            'setCarrier', 'setCarrierTitle',
            'setMethod',  'setMethodTitle',
            'setPrice',   'setCost'
        ]);

        $modelRateMethodMock->expects($this->exactly($methodsQuantity))
            ->method('setCarrier')
            ->with($this->expected()->getData()['carrier']);

        $modelRateMethodMock->expects($this->exactly($methodsQuantity))
            ->method('setCarrierTitle')
            ->with($testConfigTitle);

        $i = 2;
        foreach ($methodsNames as $methodName) {
            $modelRateMethodMock->expects($this->at($i++))
                ->method('setMethod')
                ->with($methodName);

            $modelRateMethodMock->expects($this->at($i++))
                ->method('setMethodTitle')
                ->with(ucfirst($methodName));

            $modelRateMethodMock->expects($this->at($i++))
                ->method('setPrice')
                ->with($prices[$methodName]);

            $modelRateMethodMock->expects($this->at($i++))
                ->method('setCost')
                ->with($prices[$methodName]);
            $i += 2;
        }


        $modelRateMock = $this->getModelMock('shipping/rate_result', ['append']);

        $modelRateMock->expects($this->exactly($methodsQuantity))
            ->method('append')
            ->with($modelRateMethodMock);


        $this->replaceByMock('model', 'shipping/rate_result_method', $modelRateMethodMock);
        $this->replaceByMock('model', 'oggetto_shipping/carrier', $modelCarrierMock);
        $this->replaceByMock('model', 'shipping/rate_result', $modelRateMock);


        $modelCarrierMock->collectRates($request);
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
     * Mock Currency Model for setting currency code
     *
     * @param string $currencyCode currency code
     * @return void
     */
    protected function _mockCurrencyModelForSettingCurrencyCode($currencyCode)
    {
        $modelCurrencyMock = $this->getModelMock('directory/currency', ['setData']);

        $modelCurrencyMock->expects($this->once())
            ->method('setData')
            ->with(['currency_code' => $currencyCode]);

        $this->replaceByMock('model', 'directory/currency', $modelCurrencyMock);
    }

    /**
     * Mock Directory Helper Mock for currency converting calculated prices
     *
     * @param array  $prices       calculated prices
     * @param string $currencyCode currency code
     * @return void
     */
    protected function _mockDirectoryHelperMockForCurrencyConvertingCalculatedPrices($prices, $currencyCode)
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
