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

namespace PrestaShop\Module\PrestashopCheckout\PayPal\Order;

class PayPalOrderPresenter
{
    public function __construct(
        private PaypalOrderDataProvider $paypalOrderDataProvider,
        private PayPalOrderTranslationProvider $paypalOrderTranslationProvider,
    ) {
    }

    /**
     * @param string $orderStatus
     *
     * @return string
     */
    public function getOrderStatusTranslated($orderStatus)
    {
        return $this->paypalOrderTranslationProvider->getOrderStatusTranslated($orderStatus);
    }

    /**
     * @param string $transactionStatus
     *
     * @return string
     */
    public function getTransactionStatusTranslated($transactionStatus)
    {
        return $this->paypalOrderTranslationProvider->getTransactionStatusTranslated($transactionStatus);
    }

    /**
     * @param string $fundingSource
     *
     * @return string
     */
    public function getFundingSourceTranslated($fundingSource)
    {
        return $this->paypalOrderTranslationProvider->getFundingSourceTranslated($fundingSource);
    }

    /**
     * @return string
     */
    public function getTotalAmountFormatted()
    {
        if (!$this->paypalOrderDataProvider->getTotalAmount() || !$this->paypalOrderDataProvider->getCurrencyCode()) {
            return '';
        }

        return \Tools::getContextLocale(\Context::getContext())->formatPrice((float) $this->paypalOrderDataProvider->getTotalAmount(), $this->paypalOrderDataProvider->getCurrencyCode());
    }

    /**
     * @return array
     */
    public function getSummaryTranslations()
    {
        return $this->paypalOrderTranslationProvider->getSummaryTranslations();
    }
}
