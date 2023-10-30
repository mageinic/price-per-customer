<?php
/**
 * MageINIC
 * Copyright (C) 2023 MageINIC <support@mageinic.com>
 *
 * NOTICE OF LICENSE
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see https://opensource.org/licenses/gpl-3.0.html.
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category MageINIC
 * @package MageINIC_PricePerCustomer
 * @copyright Copyright (c) 2023 MageINIC (https://www.mageinic.com/)
 * @license https://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author MageINIC <support@mageinic.com>
 */

namespace MageINIC\PricePerCustomer\Api\Data;

use Magento\Framework\Exception\LocalizedException;

/**
 * Price Per Customer interface.
 */
interface CustomerPriceInterface
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    public const ID = 'entity_id';
    public const PRODUCT_ID = 'product_id';
    public const CUSTOMER_ID = 'customer_id';
    public const PRICE = 'price';
    public const QUANTITY = 'quantity';
    /**#@-*/

    /**
     * Get Entity ID
     *
     * @return int|null
     * @throws LocalizedException
     */
    public function getEntityId(): ?int;

    /**
     * Set Entity ID
     *
     * @param int $entityId
     * @return $this
     * @throws LocalizedException
     */
    public function setEntityId(int $entityId): CustomerPriceInterface;

    /**
     * Get Product ID
     *
     * @return int
     * @throws LocalizedException
     */
    public function getProductId(): int;

    /**
     * Set Product ID
     *
     * @param int $productId
     * @return $this
     * @throws LocalizedException
     */
    public function setProductId(int $productId): CustomerPriceInterface;

    /**
     * Get Customer ID
     *
     * @return int
     * @throws LocalizedException
     */
    public function getCustomerId(): int;

    /**
     * Set Customer ID
     *
     * @param int $customerId
     * @return $this
     * @throws LocalizedException
     */
    public function setCustomerId(int $customerId): CustomerPriceInterface;

    /**
     * Get Price
     *
     * @return float
     * @throws LocalizedException
     */
    public function getPrice(): float;

    /**
     * Set Price
     *
     * @param float|int $price
     * @return $this
     * @throws LocalizedException
     */
    public function setPrice(float|int $price): CustomerPriceInterface;

    /**
     * Get Quantity
     *
     * @return int
     * @throws LocalizedException
     */
    public function getQuantity(): int;

    /**
     * Set Quantity
     *
     * @param int $quantity
     * @return $this
     * @throws LocalizedException
     */
    public function setQuantity(int $quantity): CustomerPriceInterface;
}
