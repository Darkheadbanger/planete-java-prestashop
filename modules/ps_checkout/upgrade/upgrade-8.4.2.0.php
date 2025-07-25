<?php

/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * Update main function for module version 8.4.2.0
 *
 * @param Ps_checkout $module
 *
 * @return bool
 */
function upgrade_module_8_4_2_0($module)
{
    try {
        $module->registerHook('moduleRoutes');

        $db = Db::getInstance();
        $shopsList = Shop::getShops(false, null, true);

        foreach ($shopsList as $shopId) {
            $hasFundingSourceApplePay = (bool) $db->getValue('
                SELECT 1
                FROM `' . _DB_PREFIX_ . 'pscheckout_funding_source`
                WHERE `name` = "apple_pay"
                AND `id_shop` = ' . (int) $shopId
            );

            if (!$hasFundingSourceApplePay) {
                $db->insert(
                    'pscheckout_funding_source',
                    [
                        'name' => 'apple_pay',
                        'position' => 12,
                        'active' => 0,
                        'id_shop' => (int) $shopId,
                    ]
                );
            }
        }
    } catch (Exception $exception) {
        PrestaShopLogger::addLog($exception->getMessage(), 4, 1, 'Module', $module->id);

        return false;
    }

    return true;
}
