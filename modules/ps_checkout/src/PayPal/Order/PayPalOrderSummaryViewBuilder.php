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

use PrestaShop\Module\PrestashopCheckout\Exception\PsCheckoutException;
use PrestaShop\Module\PrestashopCheckout\Order\OrderDataProvider;
use PrestaShop\Module\PrestashopCheckout\PayPal\Order\ValueObject\PayPalOrderId;
use PrestaShop\Module\PrestashopCheckout\PayPal\PayPalOrderProvider;
use PrestaShop\Module\PrestashopCheckout\PsCheckoutDataProvider;
use PrestaShop\Module\PrestashopCheckout\Repository\PayPalOrderRepository;
use PrestaShop\Module\PrestashopCheckout\Repository\PsCheckoutCartRepository;
use PrestaShop\Module\PrestashopCheckout\Routing\Router;

class PayPalOrderSummaryViewBuilder
{
    public function __construct(
        private PsCheckoutCartRepository $psCheckoutCartRepository,
        private PayPalOrderProvider $orderPayPalProvider,
        private Router $router,
        private PayPalOrderTranslationProvider $orderPayPalTranslationProvider,
        private PayPalOrderRepository $payPalOrderRepository,
    ) {
    }

    /**
     * @param \Order $order
     *
     * @return PayPalOrderSummaryView
     *
     * @throws PsCheckoutException
     */
    public function build(\Order $order)
    {
        try {
            $psCheckoutCart = $this->psCheckoutCartRepository->findOneByCartId($order->id_cart);
        } catch (\Exception $exception) {
            throw new PsCheckoutException('Unable to retrieve cart data', 0, $exception);
        }

        if (!$psCheckoutCart) {
            throw new PsCheckoutException('Unable to retrieve cart data');
        }

        try {
            $payPalOrder = $this->payPalOrderRepository->getPayPalOrderById(new PayPalOrderId($psCheckoutCart->paypal_order));
        } catch (PsCheckoutException $exception) {
            $payPalOrder = null;
        }

        try {
            $orderPayPal = $this->orderPayPalProvider->getById($psCheckoutCart->paypal_order);
        } catch (\Exception $exception) {
            $orderPayPal = [];
        }

        if ($orderPayPal === false) {
            throw new PsCheckoutException('Unable to retrieve PayPal order data');
        }

        $orderPayPalDataProvider = new PaypalOrderDataProvider($orderPayPal, $payPalOrder);
        $checkoutDataProvider = new PsCheckoutDataProvider($psCheckoutCart);

        return new PayPalOrderSummaryView(
            $orderPayPalDataProvider,
            new OrderDataProvider($order),
            $checkoutDataProvider,
            $this->router,
            new PayPalOrderPresenter($orderPayPalDataProvider, $this->orderPayPalTranslationProvider)
        );
    }
}
