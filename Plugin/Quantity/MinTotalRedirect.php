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

namespace MageINIC\PricePerCustomer\Plugin\Quantity;

use MageINIC\PricePerCustomer\Api\CustomerPriceRepositoryInterface as CustomerPriceRepository;
use MageINIC\PricePerCustomer\Helper\Data as Helper;
use Magento\Checkout\Controller\Index\Index;
use Magento\Customer\Model\Session;
use Magento\Framework\App\Response\Http;
use Magento\Framework\App\Response\HttpInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Multishipping\Helper\Data;
use Magento\Quote\Model\Quote;

/**
 * Class For Quantity Redirect Plugin
 */
class MinTotalRedirect
{
    /**
     * @var Http
     */
    protected Http $response;

    /**
     * @var Helper
     */
    private Helper $helper;

    /**
     * @var CustomerPriceRepository
     */
    private CustomerPriceRepository $customerPriceRepository;

    /**
     * @var ManagerInterface
     */
    private ManagerInterface $messageManager;

    /**
     * @var Session
     */
    private Session $customerSession;

    /**
     * Min Total Redirect Constructor
     *
     * @param Helper $helper
     * @param ManagerInterface $messageManager
     * @param Session $customerSession
     * @param CustomerPriceRepository $customerPriceRepository
     * @param Http $response
     */
    public function __construct(
        Helper                  $helper,
        ManagerInterface        $messageManager,
        Session                 $customerSession,
        CustomerPriceRepository $customerPriceRepository,
        Http                    $response
    ) {
        $this->response = $response;
        $this->helper = $helper;
        $this->customerPriceRepository = $customerPriceRepository;
        $this->messageManager = $messageManager;
        $this->customerSession = $customerSession;
    }

    /**
     * Execute Checkout
     *
     * @param Index $subject
     * @param ResultInterface $result
     * @return bool|Http|HttpInterface|ResultInterface|mixed|null]
     */
    public function afterExecute(Index $subject, ResultInterface $result)
    {
        if ($this->helper->isEnable() && $this->customerSession->isLoggedIn()) {
            /** @var Quote $quote */
            $quot = $subject->getOnepage()->getQuote();
            return $this->validateCheckout($quot, $result);
        }
        return $result;
    }

    /**
     * Multi Shipping Checkout
     *
     * @param Data $subject
     * @param mixed $result
     * @return bool|Http|HttpInterface|mixed|null
     */
    public function afterIsMultishippingCheckoutAvailable(Data $subject, mixed $result): mixed
    {
        if ($this->helper->isEnable() && $this->customerSession->isLoggedIn()) {
            /** @var Quote $quote */
            $quot = $subject->getQuote();
            return $this->validateCheckout($quot, $result);
        }
        return $result;
    }

    /**
     * Validate Quantity Checkout
     *
     * @param Quote $quot
     * @param mixed $result
     * @return bool|mixed
     */
    public function validateCheckout(Quote $quot, mixed $result): mixed
    {
        $quotItems = $quot->getItems();
        $customerId = $quot->getCustomerId();
        foreach ($quotItems as $quotItem) {
            $productId = $quotItem->getProductId();
            $checkoutQty = $quotItem->getQty();
            $product = $quotItem->getName();
            $dataId = $this->helper->getCollection($productId, $customerId);
            if (!empty($dataId)) {
                try {
                    $data = $this->customerPriceRepository->getById((int)$dataId[0]);
                    $qty = $data->getQuantity();
                    if ($checkoutQty > $qty) {
                        $text = 'This Quantity(' . $checkoutQty . ') is not allowed to make purchase from  product "'
                            . $product . '"';
                        $message = $this->messageManager->addNoticeMessage((__($text)));
                        return $this->response->setRedirect('cart') && $message;
                    }
                } catch (NoSuchEntityException|LocalizedException $e) {
                    throw $this->messageManager->addErrorMessage(__($e->getMessage()));
                }
            }
        }
        return $result;
    }
}
