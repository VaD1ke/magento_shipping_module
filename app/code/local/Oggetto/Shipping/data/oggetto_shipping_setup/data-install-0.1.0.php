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

$regions = [
    'RU-AMU' => 'Amur Region',
    'RU-ARK' => 'Arkhangelsk Region',
    'RU-AST' => 'Astrakhan Region',
    'RU-BEL' => 'Belgorod Region',
    'RU-BRY' => 'Bryansk Region',
    'RU-VLA' => 'Vladimir Region',
    'RU-VGG' => 'Volgograd Region',
    'RU-VLG' => 'Vologda Region',
    'RU-VOR' => 'Voronezh Region',
    'RU-IVA' => 'Ivanovo Region',
    'RU-IRK' => 'Irkutsk Region',
    'RU-KGD' => 'Kaliningrad Region',
    'RU-KLU' => 'Kaluga Region',
    'RU-KEM' => 'Kemerovo Region',
    'RU-KIR' => 'Kirov Region',
    'RU-KOS' => 'Kostroma Region',
    'RU-KGN' => 'Kurgan Region',
    'RU-KRS' => 'Kursk Region',
    'RU-LEN' => 'Leningrad Region',
    'RU-LIP' => 'Lipetsk Region',
    'RU-MAG' => 'Magadan Region',
    'RU-MOS' => 'Moscow Region',
    'RU-MUR' => 'Murmansk Region',
    'RU-NIZ' => 'Nizhny Novgorod Region',
    'RU-NGR' => 'Novgorod Region',
    'RU-NVS' => 'Novosibirsk Region',
    'RU-OMS' => 'Omsk Region',
    'RU-ORE' => 'Orenburg Region',
    'RU-ORL' => 'Orel Region',
    'RU-PNZ' => 'Penza Region',
    'RU-PSK' => 'Pskov Region',
    'RU-ROS' => 'Rostov Region',
    'RU-RYA' => 'Ryazan Region',
    'RU-SAM' => 'Samara Region',
    'RU-SAR' => 'Saratov Region',
    'RU-SAK' => 'Sakhalin Region',
    'RU-SVE' => 'Sverdlovsk Region',
    'RU-SMO' => 'Smolensk Region',
    'RU-TAM' => 'Tambov Region',
    'RU-TVE' => 'Tver Region',
    'RU-TOM' => 'Tomsk Region',
    'RU-TUL' => 'Tula Region',
    'RU-TYU' => 'Tyumen Region',
    'RU-ULY' => 'Ulyanovsk Region',
    'RU-CHE' => 'Chelyabinsk Region',
    'RU-YAR' => 'Yaroslavl Region',
    'RU-AD'  => 'Republic of Adygeya',
    'RU-AL'  => 'Republic of Altai',
    'RU-BA'  => 'Republic of Bashkortostan',
    'RU-BU'  => 'Republic of Buryatia',
    'RU-DA'  => 'Republic of Dagestan',
    'RU-IN'  => 'Republic of Ingushetia',
    'RU-KB'  => 'Kabardino-Balkarian Republic',
    'RU-KL'  => 'Republic of Kalmykia',
    'RU-KC'  => 'Karachayevo-Circassian Republic',
    'RU-KR'  => 'Republic of Karelia',
    'RU-KO'  => 'Komi Republic',
    'RU-KM'  => 'Republic of Crimea',
    'RU-ME'  => 'Republic of Mari El',
    'RU-MO'  => 'Republic of Mordovia',
    'RU-SA'  => 'Republic of Sakha (Yakutia)',
    'RU-SE'  => 'Republic of North Ossetia – Alania',
    'RU-TA'  => 'Republic of Tatarstan',
    'RU-TY'  => 'Republic of Tuva',
    'RU-UD'  => 'Udmurtian Republic',
    'RU-KK'  => 'Republic of Khakassia',
    'RU-CE'  => 'Chechen Republic',
    'RU-CU'  => 'Chuvash Republic',
    'RU-ALT' => 'Altai Territory',
    'RU-ZAB' => 'Trans-Baikal Territory',
    'RU-KAM' => 'Kamchatka Territory',
    'RU-KDA' => 'Krasnodar Territory',
    'RU-KYA' => 'Krasnoyarsk Territory',
    'RU-PER' => 'Perm Territory',
    'RU-PRI' => 'Primorye Territory',
    'RU-STA' => 'Stavropol Territory',
    'RU-KHA' => 'Khabarovsk Territory',
    'RU-NEN' => 'Nenets Autonomous Area',
    'RU-KHM' => 'Khanty-Mansi Autonomous Area – Yugra',
    'RU-CHU' => 'Chukotka Autonomous Area',
    'RU-YAN' => 'Yamal-Nenets Autonomous Area',
    'RU-MOW' => 'Moscow',
    'RU-SPE' => 'St. Petersburg',
    'RU-SEV' => 'Sevastopol',
    'RU-YEV' => 'Jewish Autonomous Region'
];

try {
    foreach ($regions as $code => $region) {
        Mage::getModel('directory/region')
            ->setData([
                'country_id'   => 'RU',
                'code'         => $code,
                'default_name' => $region
            ])
            ->save();
    }
} catch (Exception $e) {
    Mage::logException($e);
}
