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

namespace MageINIC\PricePerCustomer\Model;

use MageINIC\PricePerCustomer\Api\Data\CustomerPriceInterface;
use Magento\Framework\Model\AbstractModel;
use MageINIC\PricePerCustomer\Model\ResourceModel\CustomerPrice as ResourceModel;

/**
 * Class For Price Per-Customer Model
 */
class CustomerPrice extends AbstractModel implements CustomerPriceInterface
{
    /**
     * Customer Price Cache Tag
     */
    public const CACHE_TAG = 'customer_price';

    /**
     * @var string
     */
    protected $cacheTag = self::CACHE_TAG;

    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init(ResourceModel::class);
    }

    /**
     * @inheritdoc
     */
    public function getEntityId(): ?int
    {
        return $this->getData(CustomerPriceInterface::ID);
    }

    /**
     * @inheritdoc
     */
    public function setEntityId($entityId): CustomerPriceInterface
    {
        return $this->setData(CustomerPriceInterface::ID, $entityId);
    }

    /**
     * @inheritdoc
     */
    public function getProductId(): int
    {
        return $this->getData(CustomerPriceInterface::PRODUCT_ID);
    }

    /**
     * @inheritdoc
     */
    public function setProductId(int $productId): CustomerPriceInterface
    {
        return $this->setData(CustomerPriceInterface::PRODUCT_ID, $productId);
    }

    /**
     * @inheritdoc
     */
    public function getCustomerId(): int
    {
        return $this->getData(CustomerPriceInterface::CUSTOMER_ID);
    }

    /**
     * @inheritdoc
     */
    public function setCustomerId(int $customerId): CustomerPriceInterface
    {
        return $this->setData(CustomerPriceInterface::CUSTOMER_ID, $customerId);
    }

    /**
     * @inheritdoc
     */
    public function getPrice(): float
    {
        return $this->getData(CustomerPriceInterface::PRICE);
    }

    /**
     * @inheritdoc
     */
    public function setPrice(float|int $price): CustomerPriceInterface
    {
        return $this->setData(CustomerPriceInterface::PRICE, $price);
    }

    /**
     * @inheritdoc
     */
    public function getQuantity(): int
    {
        return $this->getData(CustomerPriceInterface::QUANTITY);
    }

    /**
     * @inheritdoc
     */
    public function setQuantity(int $quantity): CustomerPriceInterface
    {
        return $this->setData(CustomerPriceInterface::QUANTITY, $quantity);
    }
}
