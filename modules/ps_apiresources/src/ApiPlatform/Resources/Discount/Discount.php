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

namespace PrestaShop\Module\APIResources\ApiPlatform\Resources\Discount;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Discount\Command\AddDiscountCommand;
use PrestaShop\PrestaShop\Core\Domain\Discount\Command\DeleteDiscountCommand;
use PrestaShop\PrestaShop\Core\Domain\Discount\Exception\DiscountConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Discount\Exception\DiscountNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Discount\Query\GetDiscountForEditing;
use PrestaShopBundle\ApiPlatform\Metadata\CQRSCreate;
use PrestaShopBundle\ApiPlatform\Metadata\CQRSDelete;
use PrestaShopBundle\ApiPlatform\Metadata\CQRSGet;
use PrestaShopBundle\ApiPlatform\Metadata\LocalizedValue;
use Symfony\Component\HttpFoundation\Response;

#[ApiResource(
    operations: [
        new CQRSGet(
            uriTemplate: '/discount/{discountId}',
            requirements: ['discountId' => '\d+'],
            CQRSQuery: GetDiscountForEditing::class,
            scopes: ['discount_read']
        ),
        new CQRSCreate(
            uriTemplate: '/discount',
            validationContext: ['groups' => ['Default', 'Create']],
            CQRSCommand: AddDiscountCommand::class,
            CQRSQuery: GetDiscountForEditing::class,
            scopes: ['discount_write'],
            CQRSCommandMapping: [
                '[names]' => '[localizedNames]',
            ],
        ),
        new CQRSDelete(
            uriTemplate: '/discount/{discountId}',
            CQRSQuery: DeleteDiscountCommand::class,
            scopes: [
                'discount_write',
            ],
        ),
    ],
    exceptionToStatus: [
        DiscountNotFoundException::class => Response::HTTP_NOT_FOUND,
        DiscountConstraintException::class => Response::HTTP_UNPROCESSABLE_ENTITY,
    ],
)]
class Discount
{
    #[ApiProperty(identifier: true)]
    public int $discountId;
    #[Assert\NotBlank(groups: ['Create'])]
    #[LocalizedValue]
    public array $names;
    public int $priority;
    public bool $active;
    public \DateTimeImmutable $validFrom;
    public \DateTimeImmutable $validTo;
    public int $totalQuantity;
    public int $quantityPerUser;
    public string $description;
    public string $code;
    public int $customerId;
    public bool $highlightInCart;
    public bool $allowPartialUse;
    #[Assert\NotBlank(groups: ['Create'])]
    public string $type;
    public ?DecimalNumber $percentDiscount;
    public ?DecimalNumber $amountDiscount;
    public int $currencyId;
    public bool $isTaxIncluded;
    public int $productId;
    public array $combinations;
    public int $reductionProduct;
}
