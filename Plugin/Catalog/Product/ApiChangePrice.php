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

namespace MageINIC\PricePerCustomer\Plugin\Catalog\Product;

use MageINIC\PricePerCustomer\Helper\Data;
use Magento\Authorization\Model\UserContextInterface;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class For Api Change Price Qty Plugin
 */
class ApiChangePrice
{
    /**
     * @var Data
     */
    protected Data $helperData;

    /**
     * @var UserContextInterface
     */
    private UserContextInterface $userContext;

    /**
     * Change Price Qty Constructor
     *
     * @param Data $helperData
     * @param UserContextInterface $userContext
     */
    public function __construct(
        Data                             $helperData,
        UserContextInterface             $userContext
    ) {
        $this->helperData = $helperData;
        $this->userContext = $userContext;
    }

    /**
     * Around Plugin On getPrice Function
     *
     * @param Product $subject
     * @param mixed $result
     * @return float|int|null
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function aroundGetPrice(Product $subject, mixed $result): float|int|null
    {
        if ($this->helperData->isEnable()) {
            if ($this->userContext->getUserType() === UserContextInterface::USER_TYPE_CUSTOMER) {
                $customerId = $this->userContext->getUserId();
                if ($customerId) {
                    return $this->helperData->setCustomPrice($subject, $customerId);
                }
            }
        }
        return $subject->getData(ProductInterface::PRICE);
    }

    /**
     * Around Plugin On GetQty Function
     *
     * @param Product $subject
     * @param mixed $result
     * @return float|int
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function aroundGetQty(Product $subject, mixed $result)
    {
        if ($this->helperData->isEnable()) {
            if ($this->userContext->getUserType() === UserContextInterface::USER_TYPE_CUSTOMER) {
                $customerId = $this->userContext->getUserId();
                if ($customerId) {
                    return $this->helperData->setCustomQty($subject, $customerId);
                }
            }
        }
        return (float)$subject->getData(Data::QUANTITY);
    }
}
