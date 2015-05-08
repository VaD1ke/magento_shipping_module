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
 * Config test class for config.xml
 *
 * @category   Oggetto
 * @package    Oggetto_Shipping
 * @subpackage Config
 * @author     Vladislav Slesarenko <vslesarenko@oggettoweb.com>
 */
class Oggetto_Shipping_Test_Config_Config extends EcomDev_PHPUnit_Test_Case_Config
{
    /**
     * Test codePool and version of module
     *
     * @return void
     */
    public function testChecksModuleCodePoolAndVersion()
    {
        $this->assertModuleCodePool('local', 'oggetto_shipping');
        $this->assertModuleVersion('0.1.0');
    }

    /**
     * Test class aliases for Model, Resource and Helper
     *
     * @return void
     */
    public function testChecksClassAliasForModelResourceAndHelper()
    {
        $this->assertModelAlias('oggetto_shipping/carrier', 'Oggetto_Shipping_Model_Carrier');
    }
    
}
