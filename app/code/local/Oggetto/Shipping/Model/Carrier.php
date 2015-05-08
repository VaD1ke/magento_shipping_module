<?php

class Oggetto_Shipping_Model_Carrier extends Mage_Shipping_Model_Carrier_Abstract
    implements Mage_Shipping_Model_Carrier_Interface
{
    protected $_code = 'oggetto_shipping';

    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {

    }

    public function getAllowedMethods()
    {

    }
}
