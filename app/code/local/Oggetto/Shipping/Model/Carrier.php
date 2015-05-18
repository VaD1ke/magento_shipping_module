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
 * Carrier Model
 *
 * @category   Oggetto
 * @package    Oggetto_Shipping
 * @subpackage Model
 * @author     Vladislav Slesarenko <vslesarenko@oggettoweb.com>
 */
class Oggetto_Shipping_Model_Carrier extends Mage_Shipping_Model_Carrier_Abstract
    implements Mage_Shipping_Model_Carrier_Interface
{
    /**
     * Code
     *
     * @var string
     */
    protected $_code = 'oggetto_shipping';


    /**
     * Collect rates
     *
     * @param Mage_Shipping_Model_Rate_Request $request Request
     * @return false|Mage_Core_Model_Abstract
     */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        if (!$this->isActive()) {
            return false;
        }

        $origin = $this->getOriginAddress();

        $destination = $this->getDestinationAddress($request);


        /** @var Oggetto_Shipping_Model_Api_Shipping $shippingApi */
        $shippingApi = Mage::getModel('oggetto_shipping/api_shipping');

        try {
            $prices = $shippingApi->calculatePrices($origin, $destination);
        } catch (Oggetto_Shipping_Model_Exceptions_CalculatePricesError $e) {
            return false;
        }

        /** @var Mage_Directory_Model_Currency $currency */
        $currency = Mage::getModel('directory/currency');
        $currentCurrencyCode = $this->_getCurrentCurrencyCode();
        $currency->setData(['currency_code' => $currentCurrencyCode]);
        $apiCurrencyCode = $shippingApi->getCurrency();

        /** @var Mage_Shipping_Model_Rate_Result $rateResult */
        $rateResult = Mage::getModel('shipping/rate_result');

        foreach ($prices as $method => $price) {
            if (!array_key_exists($method, $this->getAllowedMethods())) {
                continue;
            }

            $price = $this->_reduceAndRoundPrice($price, $apiCurrencyCode, $currentCurrencyCode);

            $rateResult->append($this->_getRateMethod($method, $price));
        }

        return $rateResult;

    }

    /**
     * Get allowed shipping methods
     *
     * @return array
     */
    public function getAllowedMethods()
    {
        return [
            'standard' => 'Standard',
            'fast'     => 'Fast'
        ];
    }

    /**
     * Get origin address
     *
     * @return array
     */
    public function getOriginAddress()
    {
        $origin = $this->_getOriginAddressFromConfig();
        $origin['country'] = $this->_getCountryNameById($origin['country']);
        $origin['region'] = $this->_getRegionNameById($origin['region']);

        return $origin;
    }

    /**
     * Get destination address
     *
     * @param Mage_Shipping_Model_Rate_Request $request Request
     * @return array
     */
    public function getDestinationAddress(Mage_Shipping_Model_Rate_Request $request)
    {
        $destination = $this->_getDestinationAddressFromRequest($request);
        $destination['country'] = $this->_getCountryNameById($destination['country']);
        $destination['region'] = $this->_getRegionNameById($destination['region']);

        return $destination;
    }



    /**
     * Get origin address array (country, region, city) from store config
     *
     * @return array
     */
    protected function _getOriginAddressFromConfig()
    {
        /** @var  Oggetto_Shipping_Helper_Data $helper */
        $helper = Mage::helper('oggetto_shipping');

        $originAddress = [
            'country' => $helper->getShippingOriginCountryId(),
            'region'  => $helper->getShippingOriginRegionId(),
            'city'    => $helper->getShippingOriginCity()
        ];

        return $originAddress;
    }

    /**
     * Get destination address array (country, region, city) from request
     *
     * @param Mage_Shipping_Model_Rate_Request $request Request
     * @return array
     */
    protected function _getDestinationAddressFromRequest(Mage_Shipping_Model_Rate_Request $request)
    {
        $destinationAddress = [
            'country' => $request->getDestCountryId(),
            'region'  => $request->getDestRegionId(),
            'city'    => $request->getDestCity()
        ];

        return $destinationAddress;
    }

    /**
     * Get region name from region id
     *
     * @param string $regionId region id
     * @return string
     */
    protected function _getRegionNameById($regionId)
    {
        /** @var Mage_Directory_Model_Region $region */
        $region = Mage::getModel('directory/region')->load($regionId);

        $regionName = Mage::helper('oggetto_shipping')->__($region->getName());

        return $regionName;
    }

    /**
     * Get country name from country id
     *
     * @param string $countryId country id
     * @return null|string
     */
    protected function _getCountryNameById($countryId)
    {
        return Mage::app()->getLocale()->getCountryTranslation($countryId);
    }

    /**
     * Get current currency code
     *
     * @return string
     */
    protected function _getCurrentCurrencyCode()
    {
        return Mage::app()->getStore()->getCurrentCurrencyCode();
    }

    /**
     * Get rate method
     *
     * @param string $method shipping method
     * @param float  $price  price for shipping
     *
     * @return Mage_Shipping_Model_Rate_Result_Method
     */
    protected function _getRateMethod($method, $price)
    {
        /** @var Mage_Shipping_Model_Rate_Result_Method $rateMethod */
        $rateMethod = Mage::getModel('shipping/rate_result_method');

        $rateMethod->setCarrier($this->_code);
        $rateMethod->setCarrierTitle($this->getConfigData('title'));
        $rateMethod->setMethod($method);
        $rateMethod->setMethodTitle(ucfirst($method));

        $rateMethod->setPrice($price);
        $rateMethod->setCost($price);

        return $rateMethod;
    }

    /**
     * Reduce money from currency to currency and round it to 2 decimal places
     *
     * @param int    $price        reducing price
     * @param string $fromCurrency from currency
     * @param string $toCurrency   to currency
     *
     * @return float
     */
    protected function _reduceAndRoundPrice($price, $fromCurrency, $toCurrency)
    {
        $price = Mage::helper('directory')->currencyConvert($price, $fromCurrency, $toCurrency);
        $price = Mage::app()->getStore()->roundPrice($price);

        return $price;
    }
}
