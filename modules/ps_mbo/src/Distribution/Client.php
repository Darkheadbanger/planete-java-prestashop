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
declare(strict_types=1);

namespace PrestaShop\Module\Mbo\Distribution;

use PrestaShop\Module\Mbo\Helpers\ErrorHelper;
use Symfony\Component\Routing\Router;

class Client extends BaseClient
{
    /**
     * @var Router|null
     */
    private $router;

    public function setRouter(Router $router): self
    {
        $this->router = $router;

        return $this;
    }

    /**
     * Get a new key from Distribution API.
     *
     * @return \stdClass
     */
    public function retrieveNewKey(): \stdClass
    {
        return $this->processRequestAndDecode('shops/get-pub-key');
    }

    /**
     * Retrieve the user menu from NEST Api
     *
     * @return false|\stdClass
     */
    public function getEmployeeMenu()
    {
        $languageIsoCode = \Context::getContext()->language->getIsoCode();
        $cacheKey = __METHOD__ . $languageIsoCode . _PS_VERSION_;

        if ($this->cacheProvider->contains($cacheKey)) {
            return $this->cacheProvider->fetch($cacheKey);
        }

        $catalogUrlParams = [
            'utm_mbo_source' => 'menu-user-back-office',
        ];

        $this->setQueryParams([
            'isoLang' => $languageIsoCode,
            'shopVersion' => _PS_VERSION_,
            'catalogUrl' => $this->router ? $this->router->generate('admin_mbo_catalog_module', $catalogUrlParams, Router::ABSOLUTE_PATH) : '#',
        ]);
        try {
            $conf = $this->processRequestAndDecode('shops/employee-menu');
        } catch (\Throwable $e) {
            return false;
        }
        if (empty($conf)) {
            return false;
        }
        $this->cacheProvider->save($cacheKey, $conf, 60 * 60 * 24); // A day

        return $this->cacheProvider->fetch($cacheKey);
    }

    /**
     * Send a tracking to the API
     * Send it asynchronously to avoid blocking process for this feature
     *
     * @param array $eventData
     */
    public function trackEvent(array $eventData): void
    {
        try {
            $this->processRequestAndDecode(
                'shops/events',
                self::HTTP_METHOD_POST,
                ['form_params' => $eventData]
            );
        } catch (\Throwable $e) {
            // Do nothing if the tracking fails
            ErrorHelper::reportError($e);
        }
    }
}
