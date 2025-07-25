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

namespace PrestaShop\Module\PrestashopCheckout\PayPal;

use PrestaShop\Module\PrestashopCheckout\Configuration\PrestaShopConfiguration;
use PrestaShop\Module\PrestashopCheckout\Exception\PsCheckoutException;

class PayPalPayLaterConfiguration
{
    const PS_CHECKOUT_PAY_LATER_PRODUCT_PAGE = 'PS_CHECKOUT_PAY_IN_4X_PRODUCT_PAGE';
    const PS_CHECKOUT_PAY_LATER_ORDER_PAGE = 'PS_CHECKOUT_PAY_IN_4X_ORDER_PAGE';

    const PS_CHECKOUT_PAY_LATER_HOME_PAGE_BANNER = 'PS_CHECKOUT_PAY_LATER_HOME_PAGE_BANNER';
    const PS_CHECKOUT_PAY_LATER_CATEGORY_PAGE_BANNER = 'PS_CHECKOUT_PAY_LATER_CATEGORY_PAGE_BANNER';
    const PS_CHECKOUT_PAY_LATER_PRODUCT_PAGE_BANNER = 'PS_CHECKOUT_PAY_LATER_PRODUCT_PAGE_BANNER';
    const PS_CHECKOUT_PAY_LATER_ORDER_PAGE_BANNER = 'PS_CHECKOUT_PAY_LATER_ORDER_PAGE_BANNER';

    const PS_CHECKOUT_PAY_LATER_PRODUCT_PAGE_BUTTON = 'PS_CHECKOUT_PAY_IN_4X_PRODUCT_PAGE_BUTTON';
    const PS_CHECKOUT_PAY_LATER_ORDER_PAGE_BUTTON = 'PS_CHECKOUT_PAY_IN_4X_ORDER_PAGE_BUTTON';
    const PS_CHECKOUT_PAY_LATER_CART_PAGE_BUTTON = 'PS_CHECKOUT_PAY_IN_4X_CART_PAGE_BUTTON';

    /**
     * @param PrestaShopConfiguration $configuration
     */
    public function __construct(private PrestaShopConfiguration $configuration)
    {
    }

    public function isOrderPageMessageActive()
    {
        return (bool) $this->configuration->get(self::PS_CHECKOUT_PAY_LATER_ORDER_PAGE);
    }

    public function isProductPageMessageActive()
    {
        return (bool) $this->configuration->get(self::PS_CHECKOUT_PAY_LATER_PRODUCT_PAGE);
    }

    /**
     * @param bool $status
     *
     * @throws PsCheckoutException
     */
    public function setProductPageMessage($status)
    {
        $this->configuration->set(self::PS_CHECKOUT_PAY_LATER_PRODUCT_PAGE, $status);
    }

    /**
     * @param bool $status
     *
     * @throws PsCheckoutException
     */
    public function setOrderPageMessage($status)
    {
        $this->configuration->set(self::PS_CHECKOUT_PAY_LATER_ORDER_PAGE, $status);
    }

    /**
     * @param bool $status
     *
     * @throws PsCheckoutException
     */
    public function setProductPageBanner($status)
    {
        $this->configuration->set(self::PS_CHECKOUT_PAY_LATER_PRODUCT_PAGE_BANNER, $status);
    }

    /**
     * @param bool $status
     *
     * @throws PsCheckoutException
     */
    public function setOrderPageBanner($status)
    {
        $this->configuration->set(self::PS_CHECKOUT_PAY_LATER_ORDER_PAGE_BANNER, $status);
    }

    /**
     * @param bool $status
     *
     * @throws PsCheckoutException
     */
    public function setHomePageBanner($status)
    {
        $this->configuration->set(self::PS_CHECKOUT_PAY_LATER_HOME_PAGE_BANNER, $status);
    }

    /**
     * @param bool $status
     *
     * @throws PsCheckoutException
     */
    public function setCategoryPageBanner($status)
    {
        $this->configuration->set(self::PS_CHECKOUT_PAY_LATER_CATEGORY_PAGE_BANNER, $status);
    }

    /**
     * @param bool $status
     *
     * @throws PsCheckoutException
     */
    public function setCartPageButton($status)
    {
        $this->configuration->set(self::PS_CHECKOUT_PAY_LATER_CART_PAGE_BUTTON, $status);
    }

    /**
     * @param bool $status
     *
     * @throws PsCheckoutException
     */
    public function setOrderPageButton($status)
    {
        $this->configuration->set(self::PS_CHECKOUT_PAY_LATER_ORDER_PAGE_BUTTON, $status);
    }

    /**
     * @param bool $status
     *
     * @throws PsCheckoutException
     */
    public function setProductPageButton($status)
    {
        $this->configuration->set(self::PS_CHECKOUT_PAY_LATER_PRODUCT_PAGE_BUTTON, $status);
    }

    /**
     * @return bool
     */
    public function isOrderPageButtonActive()
    {
        return (bool) $this->configuration->get(self::PS_CHECKOUT_PAY_LATER_ORDER_PAGE_BUTTON);
    }

    /**
     * @return bool
     */
    public function isCartPageButtonActive()
    {
        return (bool) $this->configuration->get(self::PS_CHECKOUT_PAY_LATER_CART_PAGE_BUTTON);
    }

    /**
     * @return bool
     */
    public function isProductPageButtonActive()
    {
        return (bool) $this->configuration->get(self::PS_CHECKOUT_PAY_LATER_PRODUCT_PAGE_BUTTON);
    }

    /**
     * @return bool
     */
    public function isOrderPageBannerActive()
    {
        return (bool) $this->configuration->get(self::PS_CHECKOUT_PAY_LATER_ORDER_PAGE_BANNER);
    }

    /**
     * @return bool
     */
    public function isProductPageBannerActive()
    {
        return (bool) $this->configuration->get(self::PS_CHECKOUT_PAY_LATER_PRODUCT_PAGE_BANNER);
    }

    /**
     * @return bool
     */
    public function isHomePageBannerActive()
    {
        return (bool) $this->configuration->get(self::PS_CHECKOUT_PAY_LATER_HOME_PAGE_BANNER);
    }

    /**
     * @return bool
     */
    public function isCategoryPageBannerActive()
    {
        return (bool) $this->configuration->get(self::PS_CHECKOUT_PAY_LATER_CATEGORY_PAGE_BANNER);
    }
}
