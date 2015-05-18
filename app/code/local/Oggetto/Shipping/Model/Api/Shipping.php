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
 * @subpackage Model
 * @author     Vladislav Slesarenko <vslesarenko@oggettoweb.com>
 */
class Oggetto_Shipping_Model_Api_Shipping
{
    /**
     * Url for oggetto shipping API
     * @var string
     */
    protected $_apiUrl;

    /**
     * Http client for oggetto shipping API
     *
     * @var Varien_Http_Client
     */
    protected $_httpClient;

    /**
     * Currency code of result price
     *
     * @var string
     */
    protected $_currency;

    /**
     * Init object
     */
    public function __construct()
    {
        $this->_apiUrl = 'http://new.oggy.co/shipping/api/rest.php';
        $this->_currency = 'RUB';
    }

    /**
     * Calculate prices for shipping
     *
     * @param array $origin      Origin
     * @param array $destination Destination
     *
     * @return array
     *
     * @throws Oggetto_Shipping_Model_Exceptions_CalculatePricesError
     * @throws Zend_Http_Client_Exception
     */
    public function calculatePrices($origin, $destination)
    {
        /** @var Zend_Http_Client $client */
        $client = $this->_getHttpClient();
        $client->resetParameters(true);
        $client->setParameterGet([
            'from_country' => $origin['country'],
            'from_region'  => $origin['region'],
            'from_city'    => $origin['city'],
            'to_country'   => $destination['country'],
            'to_region'    => $destination['region'],
            'to_city'      => $destination['city']
        ]);

        $response = $client->request(Varien_Http_Client::GET);

        if ($response->getStatus() == 200) {
            $responseData = Mage::helper('core')->jsonDecode($response->getBody());

            if ($responseData['status'] == 'success') {
                return $responseData['prices'];
            }

            throw new Oggetto_Shipping_Model_Exceptions_CalculatePricesError('Wrong input data');
        }

        throw new Oggetto_Shipping_Model_Exceptions_CalculatePricesError('Response status is not OK');
    }

    /**
     * Get result price currency of oggetto shipping API
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->_currency;
    }

    /**
     * Get http client with apiUrl
     *
     * @return Zend_Http_Client
     */
    protected function _getHttpClient()
    {
        $this->_httpClient = new Varien_Http_Client($this->_apiUrl);
        return $this->_httpClient;
    }
}
