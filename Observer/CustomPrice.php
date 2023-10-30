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

namespace MageINIC\PricePerCustomer\Observer;

use Magento\Customer\Model\SessionFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use MageINIC\PricePerCustomer\Helper\Data;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Observer Of Custom Price
 */
class CustomPrice implements ObserverInterface
{
    /**
     * @var Data
     */
    private Data $helperData;

    /**
     * @var SessionFactory
     */
    private SessionFactory $customerSession;

    /**
     * Observer Constructor
     *
     * @param Data $helperData
     * @param SessionFactory $customerSession
     */
    public function __construct(
        Data $helperData,
        SessionFactory $customerSession
    ) {
        $this->helperData = $helperData;
        $this->customerSession = $customerSession;
    }

    /**
     * Product Checkout Cart After Observer
     *
     * @param Observer $observer
     * @return void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        if ($this->helperData->isEnable()) {
            if ($this->customerSession->create()->isLoggedIn()) {
                $customerId = $this->customerSession->create()->getCustomerId();
                $quote_item = $observer->getEvent()->getQuoteItem();
                $price = $this->helperData->setCustomPrice($quote_item->getProduct(), $customerId);
                if (!empty($price)) {
                    $quote_item->setCustomPrice($price);
                    $quote_item->setOriginalCustomPrice($price);
                    $quote_item->getProduct()->setIsSuperMode(true);
                }
            }
        }
    }
}
