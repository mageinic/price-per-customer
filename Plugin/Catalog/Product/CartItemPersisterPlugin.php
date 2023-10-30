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

use Exception;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Api\CartItemRepositoryInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Api\Data\CartItemInterface;
use Magento\Quote\Model\Quote\Item\CartItemOptionsProcessor;
use Magento\Quote\Model\Quote\Item\CartItemPersister;
use MageINIC\PricePerCustomer\Helper\Data;

class CartItemPersisterPlugin
{
    /**
     * @var ProductRepositoryInterface
     */
    private ProductRepositoryInterface $productRepository;

    /**
     * @var CartItemOptionsProcessor
     */
    private CartItemOptionsProcessor $cartItemOptionProcessor;

    /**
     * @var Data
     */
    protected Data $helperData;

    /**
     * @var CartItemRepositoryInterface
     */
    protected CartItemRepositoryInterface $cartItemRepository;

    /**
     * @param CartItemOptionsProcessor $cartItemOptionProcessor
     * @param CartItemRepositoryInterface $cartItemRepository
     * @param Data $helperData
     * @param ProductRepositoryInterface $productRepository
     */
    public function __construct(
        CartItemOptionsProcessor   $cartItemOptionProcessor,
        CartItemRepositoryInterface $cartItemRepository,
        Data                       $helperData,
        ProductRepositoryInterface $productRepository
    ) {
        $this->cartItemOptionProcessor = $cartItemOptionProcessor;
        $this->cartItemRepository = $cartItemRepository;
        $this->helperData = $helperData;
        $this->productRepository = $productRepository;
    }

    /**
     * Around Plugin For Custom Price
     *
     * @param CartItemPersister $subject
     * @param callable $proceed
     * @param CartInterface $quote
     * @param CartItemInterface $item
     * @return CartItemInterface
     * @throws CouldNotSaveException
     * @throws InputException
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function aroundSave(
        CartItemPersister $subject,
        callable $proceed,
        CartInterface $quote,
        CartItemInterface $item
    ): CartItemInterface {
        if (!$item->getPrice()) {
            return $proceed($quote, $item);
        }
        $qty = $item->getQty();
        if (!is_numeric($qty) || $qty <= 0) {
            throw InputException::invalidFieldValue('qty', $qty);
        }
        $cartId = $item->getQuoteId();
        $itemId = $item->getItemId();
        $product = $this->productRepository->get($item->getSku());
        try {
            if (isset($itemId)) {
                $currentItem = $quote->getItemById($itemId);
                if (!$currentItem) {
                    throw new NoSuchEntityException(__(
                        'The %1 Cart doesn\'t contain the %2 item.',
                        $cartId,
                        $itemId
                    ));
                }
                $productType = $currentItem->getProduct()->getTypeId();
                $buyRequestData = $this->cartItemOptionProcessor->getBuyRequest($productType, $item);
                if (is_object($buyRequestData)) {
                    if ($currentItem->getQty() !== $buyRequestData->getQty()) {
                        $item = $quote->updateItem($itemId, $buyRequestData);
                    }
                } else {
                    if ($item->getQty() !== $currentItem->getQty()) {
                        $currentItem->clearMessage();
                        $currentItem->setQty($qty);
                        if (!empty($currentItem->getMessage()) && $currentItem->getHasError()) {
                            throw new LocalizedException(__($currentItem->getMessage()));
                        }
                    }
                }
            } else {
                $productType = $product->getTypeId();
                $item = $quote->addProduct(
                    $product,
                    $this->cartItemOptionProcessor->getBuyRequest($productType, $item)
                );
                if (is_string($item)) {
                    throw new LocalizedException(__($item));
                }
            }
        } catch (Exception $e) {
            throw new CouldNotSaveException(__("The quote couldn't be saved."));
        }
        $itemId = $item->getId();
        foreach ($quote->getAllItems() as $quoteItem) {
            if ($itemId == $quoteItem->getId()) {
                if ($this->helperData->isEnable()) {
                    $customerId = $quote->getCustomerId();
                    if ($customerId) {
                        $qty = $this->helperData->setCustomQty($product, $customerId);
                        if (!empty($qty) && $quoteItem->getQty() > $qty) {
                                $this->cartItemRepository->deleteById($quoteItem['quote_id'], $quoteItem['item_id']);
                                throw new CouldNotSaveException(
                                    __("This quantity is not allowed to make purchase from  product")
                                );
                        }
                    }
                }
                $item = $this->cartItemOptionProcessor->addProductOptions($productType, $quoteItem);
                return $this->cartItemOptionProcessor->applyCustomOptions($item);
            }
        }
        throw new CouldNotSaveException(__("The quote couldn't be saved."));
    }
}
