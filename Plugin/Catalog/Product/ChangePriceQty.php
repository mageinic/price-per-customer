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

use Magento\Catalog\Model\Product;
use Magento\Customer\Model\SessionFactory;
use Magento\Framework\App\CacheInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use MageINIC\PricePerCustomer\Helper\Data;

/**
 * Class For Change Price Qty Plugin
 */
class ChangePriceQty
{
    /**
     * @var Data
     */
    protected Data $helperData;

    /**
     * @var SessionFactory
     */
    protected SessionFactory $customerSession;

    /**
     * @var CacheInterface
     */
    private CacheInterface $cache;

    /**
     * Change Price Qty Constructor
     *
     * @param Data $helperData
     * @param SessionFactory $customerSession
     * @param CacheInterface $cache
     */
    public function __construct(
        Data           $helperData,
        SessionFactory $customerSession,
        CacheInterface $cache
    ) {
        $this->helperData = $helperData;
        $this->customerSession = $customerSession;
        $this->cache = $cache;
    }

    /**
     * After Plugin On GetPrice Function
     *
     * @param Product $subject
     * @param mixed $result
     * @return float|int|null
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function afterGetPrice(Product $subject, mixed $result): float|int|null
    {
        if ($this->helperData->isEnable()) {
            if ($this->customerSession->create()->isLoggedIn()) {
                $customerId = $this->customerSession->create()->getCustomerId();
                if ($customerId) {
                    $this->cache->clean();
                    return $this->helperData->setCustomPrice($subject, $customerId);
                }
            }
        }
        return $result;
    }

    /**
     * After Plugin On GetQty Function
     *
     * @param Product $subject
     * @param mixed $result
     * @return float|int|null
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function afterGetQty(Product $subject, mixed $result): float|int|null
    {
        if ($this->helperData->isEnable()) {
            if ($this->customerSession->create()->isLoggedIn()) {
                $customerId = $this->customerSession->create()->getCustomerId();
                if ($customerId) {
                    return $this->helperData->setCustomQty($subject, $customerId);
                }
            }
        }
        return (float)$subject->getData(Data::QUANTITY);
    }
}
