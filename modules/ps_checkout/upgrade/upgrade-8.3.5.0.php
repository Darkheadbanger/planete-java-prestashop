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
 * Update main function for module version 8.3.5.0
 *
 * @param Ps_checkout $module
 *
 * @return bool
 */
function upgrade_module_8_3_5_0($module)
{
    try {
        $db = Db::getInstance();
        $databaseFields = [];
        $fields = $db->executeS('SHOW COLUMNS FROM `' . _DB_PREFIX_ . 'pscheckout_cart`');

        if (!empty($fields)) {
            foreach ($fields as $field) {
                if (isset($field['Field'])) {
                    $databaseFields[] = $field['Field'];
                }
            }
        }

        if (!empty($databaseFields) && !in_array('environment', $databaseFields, true)) {
            $db->execute('ALTER TABLE `' . _DB_PREFIX_ . 'pscheckout_cart` ADD COLUMN `environment` varchar(20) DEFAULT NULL;');
        }
    } catch (Exception $exception) {
        PrestaShopLogger::addLog($exception->getMessage(), 4, 1, 'Module', $module->id);

        return false;
    }

    return true;
}
