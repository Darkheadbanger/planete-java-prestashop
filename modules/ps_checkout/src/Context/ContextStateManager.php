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

namespace PrestaShop\Module\PrestashopCheckout\Context;

use Cart;
use Context;
use Country;
use Currency;
use Customer;
use Language;
use Shop;

/**
 * Allows manipulating context state.
 * This was adapted for specific broken legacy use cases when the previous state of context must be restored after some actions.
 *
 * e.g. order creation from back office.
 *  Legacy requires Context properties (currency, country etc.) instead of using cart properties
 *  so some context props must be changed for a while and then restored to previous state.
 */
class ContextStateManager
{
    const MANAGED_FIELDS = [
        'cart',
        'country',
        'currency',
        'language',
        'customer',
        'shop',
        'shopContext',
    ];

    /**
     * @var \Context
     */
    private $context;

    /**
     * @var array|null
     */
    private $contextFieldsStack;

    /**
     * @param \Context|null $context
     */
    public function __construct(?\Context $context = null)
    {
        $this->context = null === $context ? \Context::getContext() : $context;
    }

    /**
     * @return \Context
     */
    public function getContext()
    {
        return $this->context->getContext();
    }

    /**
     * Sets context cart and saves previous value
     *
     * @param \Cart|null $cart
     *
     * @return self
     */
    public function setCart(?\Cart $cart = null)
    {
        $this->saveContextField('cart');
        $this->getContext()->cart = $cart;

        return $this;
    }

    /**
     * Sets context country and saves previous value
     *
     * @param \Country|null $country
     *
     * @return self
     */
    public function setCountry(?\Country $country = null)
    {
        $this->saveContextField('country');
        $this->getContext()->country = $country;

        return $this;
    }

    /**
     * Sets context currency and saves previous value
     *
     * @param \Currency|null $currency
     *
     * @return self
     */
    public function setCurrency(?\Currency $currency = null)
    {
        $this->saveContextField('currency');
        $this->getContext()->currency = $currency;

        return $this;
    }

    /**
     * Sets context language and saves previous value
     *
     * @param \Language|null $language
     *
     * @return self
     */
    public function setLanguage(?\Language $language = null)
    {
        $this->saveContextField('language');
        $this->getContext()->language = $language;

        $this->getContext()->getTranslator()->setLocale($language->locale);

        return $this;
    }

    /**
     * Sets context customer and saves previous value
     *
     * @param \Customer|null $customer
     *
     * @return self
     */
    public function setCustomer(?\Customer $customer = null)
    {
        $this->saveContextField('customer');
        $this->getContext()->customer = $customer;

        return $this;
    }

    /**
     * Sets context shop and saves previous value
     *
     * @param \Shop $shop
     *
     * @return self
     *
     * @throws \PrestaShopException
     */
    public function setShop(\Shop $shop)
    {
        $this->saveContextField('shop');
        $this->getContext()->shop = $shop;
        \Shop::setContext(\Shop::CONTEXT_SHOP, $shop->id);

        return $this;
    }

    /**
     * Sets context shop and saves previous value
     *
     * @param int $shopContext
     * @param int|null $shopContextId
     *
     * @return self
     *
     * @throws \PrestaShopException
     */
    public function setShopContext($shopContext, $shopContextId = null)
    {
        $this->saveContextField('shopContext');

        if ($shopContext === \Shop::CONTEXT_SHOP) {
            $this->getContext()->shop = new \Shop($shopContextId);
        }

        \Shop::setContext($shopContext, $shopContextId);

        return $this;
    }

    /**
     * Restores context to a state before changes
     *
     * @return self
     */
    public function restorePreviousContext()
    {
        $stackFields = array_keys($this->contextFieldsStack[$this->getCurrentStashIndex()]);

        foreach ($stackFields as $fieldName) {
            $this->restoreContextField($fieldName);
        }

        $this->removeLastSavedContext();

        return $this;
    }

    /**
     * Saves the current overridden fields in the context, allowing you to set new values to the
     * current Context. Next time you call restorePreviousContext the context will be refilled with
     * the values that were saved during this call.
     *
     * This is useful if several services use the ContextStateManager, this way if every service
     * saved the context before modifying it there is no risk of removing previous modifications
     * when you restore the context, because the different states have been stacked.
     *
     * @return self
     */
    public function saveCurrentContext()
    {
        // No context field has been overridden yet so no need to save/stack it
        if (null === $this->contextFieldsStack) {
            return $this;
        }

        // Saves all the fields that have not been overridden
        foreach (self::MANAGED_FIELDS as $contextField) {
            $this->saveContextField($contextField);
        }

        // Add a new empty layer
        $this->contextFieldsStack[] = [];

        return $this;
    }

    /**
     * Return the stack of modified fields
     * If it's null, no context field has been overridden
     *
     * @return array|null
     */
    public function getContextFieldsStack()
    {
        return $this->contextFieldsStack;
    }

    /**
     * Save context field into local array
     *
     * @param string $fieldName
     */
    private function saveContextField($fieldName)
    {
        $currentStashIndex = $this->getCurrentStashIndex();

        // NOTE: array_key_exists important here, isset cannot be used because it would not detect if null is stored
        if (!array_key_exists($fieldName, $this->contextFieldsStack[$currentStashIndex])) {
            switch ($fieldName) {
                case 'shop':
                case 'shopContext':
                    $this->contextFieldsStack[$currentStashIndex]['shop'] = $this->getContext()->shop;
                    $this->contextFieldsStack[$currentStashIndex]['shopContext'] = \Shop::getContext();
                    break;
                default:
                    $this->contextFieldsStack[$currentStashIndex][$fieldName] = $this->getContext()->$fieldName;
            }
        }
    }

    /**
     * Restores context saved value, and remove save value from local array
     *
     * @param string $fieldName
     */
    private function restoreContextField($fieldName)
    {
        $currentStashIndex = $this->getCurrentStashIndex();

        // NOTE: array_key_exists important here, isset cannot be used because it would not detect if null is stored
        if (array_key_exists($fieldName, $this->contextFieldsStack[$currentStashIndex])) {
            if ('shop' === $fieldName) {
                $this->restoreShopContext($currentStashIndex);
            }

            $this->getContext()->getTranslator()->setLocale($this->contextFieldsStack[$currentStashIndex][$fieldName]->locale);

            $this->getContext()->$fieldName = $this->contextFieldsStack[$currentStashIndex][$fieldName];
            unset($this->contextFieldsStack[$currentStashIndex][$fieldName]);
        }
    }

    /**
     * Returns the index of the current stack
     *
     * @return int
     */
    private function getCurrentStashIndex()
    {
        // If this is the first time the index is needed we need to init the stack
        if (null === $this->contextFieldsStack) {
            $this->contextFieldsStack = [[]];
        }

        return key(array_slice($this->contextFieldsStack, -1));
    }

    /**
     * Restore the ShopContext, this is used when Shop has been overridden, we need to
     * restore context->shop of course But also the static fields in Shop class
     *
     * @param int $currentStashIndex
     */
    private function restoreShopContext($currentStashIndex)
    {
        $shop = $this->contextFieldsStack[$currentStashIndex]['shop'];
        $shopId = $shop instanceof \Shop ? $shop->id : null;
        $shopContext = $this->contextFieldsStack[$currentStashIndex]['shopContext'];

        if (null !== $shopContext) {
            \Shop::setContext($shopContext, $shopId);
        }

        unset($this->contextFieldsStack[$currentStashIndex]['shopContext']);
    }

    /**
     * Removes the last saved stashed context, in case this method is called too many times
     * we always keep one layer available
     */
    private function removeLastSavedContext()
    {
        array_pop($this->contextFieldsStack);

        // When all layers have been popped we restore the initial null value
        if (empty($this->contextFieldsStack)) {
            $this->contextFieldsStack = null;
        }
    }
}
