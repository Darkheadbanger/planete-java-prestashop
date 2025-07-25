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

namespace PrestaShop\Module\PrestashopCheckout\Order\QueryHandler;

use PrestaShop\Module\PrestashopCheckout\Cart\Exception\CartNotFoundException;
use PrestaShop\Module\PrestashopCheckout\Exception\PsCheckoutException;
use PrestaShop\Module\PrestashopCheckout\Order\Exception\OrderNotFoundException;
use PrestaShop\Module\PrestashopCheckout\Order\Query\GetOrderForPaymentReversedQuery;
use PrestaShop\Module\PrestashopCheckout\Order\Query\GetOrderForPaymentReversedQueryResult;
use PrestaShop\Module\PrestashopCheckout\Order\State\OrderStateConfigurationKeys;
use PrestaShop\Module\PrestashopCheckout\Repository\PsCheckoutCartRepository;

class GetOrderForPaymentReversedQueryHandler
{
    public function __construct(private PsCheckoutCartRepository $psCheckoutCartRepository)
    {
    }

    /**
     * @param GetOrderForPaymentReversedQuery $query
     *
     * @return GetOrderForPaymentReversedQueryResult
     *
     * @throws PsCheckoutException
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function __invoke(GetOrderForPaymentReversedQuery $query)
    {
        /** @var \PsCheckoutCart|false $psCheckoutCart */
        $psCheckoutCart = $this->psCheckoutCartRepository->findOneByPayPalOrderId($query->getOrderPayPalId()->getValue());

        if (!$psCheckoutCart) {
            throw new CartNotFoundException('No PrestaShop Cart associated to this PayPal Order at this time.');
        }

        $orders = new \PrestaShopCollection(\Order::class);
        $orders->where('id_cart', '=', $psCheckoutCart->getIdCart());

        if (!$orders->count()) {
            throw new OrderNotFoundException('No PrestaShop Order associated to this PayPal Order at this time.');
        }

        /** @var \Order $order */
        $order = $orders->getFirst();

        if (!\Validate::isLoadedObject($order)) {
            throw new OrderNotFoundException('No PrestaShop Order associated to this PayPal Order at this time.');
        }

        $hasBeenPaid = $order->hasBeenPaid();
        $hasBeenCompleted = count($order->getHistory($order->id_lang, (int) \Configuration::getGlobalValue(OrderStateConfigurationKeys::PS_CHECKOUT_STATE_COMPLETED)));
        $hasBeenPartiallyPaid = count($order->getHistory($order->id_lang, (int) \Configuration::getGlobalValue(OrderStateConfigurationKeys::PS_CHECKOUT_STATE_PARTIALLY_PAID)));
        $hasBeenTotallyRefunded = count($order->getHistory($order->id_lang, (int) \Configuration::getGlobalValue(OrderStateConfigurationKeys::PS_CHECKOUT_STATE_REFUNDED)));

        return new GetOrderForPaymentReversedQueryResult(
            (int) $order->id,
            $hasBeenPaid || $hasBeenCompleted || $hasBeenPartiallyPaid,
            (bool) $hasBeenTotallyRefunded
        );
    }
}
