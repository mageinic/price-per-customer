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

namespace MageINIC\PricePerCustomer\Helper;

use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\ScopeInterface;
use MageINIC\PricePerCustomer\Api\Data\CustomerPriceInterface;
use MageINIC\PricePerCustomer\Api\CustomerPriceRepositoryInterface;

/**
 * Price Per Customer Helper Data Class
 */
class Data extends AbstractHelper
{
    /**
     * Default Product Quantity Field Name
     */
    public const QUANTITY = 'qty';

    /**
     * Recipient Enable Config Path
     */
    public const XML_PATH_ENABLE = 'price_per_customer/general/enable';

    /**
     * Recipient Page Title Config Path
     */
    public const XML_PATH_PAGE_TITLE = 'price_per_customer/general/page_title';

    /**
     * Recipient Link Name Config Path
     */
    public const XML_PATH_LINK_LABEL = 'price_per_customer/general/link_name';

    /**
     * Recipient Link Layout Config Path
     */
    public const XML_PATH_PAGE_LAYOUT = 'price_per_customer/general/page_layout';

    /**
     * @var SearchCriteriaBuilder
     */
    protected SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @var CustomerPriceRepositoryInterface
     */
    protected CustomerPriceRepositoryInterface $customerPriceRepository;

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var ProductRepositoryInterface
     */
    protected ProductRepositoryInterface $productRepository;

    /**
     * Data Constructor
     *
     * @param Context $context
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param CustomerPriceRepositoryInterface $customerPriceRepository
     * @param ProductRepositoryInterface $productRepository
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        Context                    $context,
        SearchCriteriaBuilder      $searchCriteriaBuilder,
        CustomerPriceRepositoryInterface     $customerPriceRepository,
        ProductRepositoryInterface $productRepository,
        ScopeConfigInterface       $scopeConfig
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->customerPriceRepository = $customerPriceRepository;
        $this->scopeConfig = $scopeConfig;
        $this->productRepository = $productRepository;
        parent::__construct($context);
    }

    /**
     * Returning Page Title Config Value
     *
     * @return string
     */
    public function isEnable(): string
    {
        $storeScope = ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_ENABLE, $storeScope);
    }

    /**
     * Returning Page Title Config Value
     *
     * @return string
     */
    public function getPageTitle(): string
    {
        $storeScope = ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_PAGE_TITLE, $storeScope);
    }

    /**
     * Returning Link Label Config Value
     *
     * @return string
     */
    public function getLinkLabel(): string
    {
        $storeScope = ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_LINK_LABEL, $storeScope);
    }

    /**
     * Returning Page Layout Config Value
     *
     * @return string
     */
    public function getLayout(): string
    {
        $storeScope = ScopeInterface::SCOPE_STORE;
        return $this->scopeConfig->getValue(self::XML_PATH_PAGE_LAYOUT, $storeScope);
    }

    /**
     * Returning Collection By Customer ID
     *
     * @param int $customerId
     * @return CustomerPriceInterface[]
     */
    public function getCollectionByCustomerId(int $customerId): array
    {
        $searchCriteriaBuilder = $this->searchCriteriaBuilder->addFilter('customer_id', $customerId);
        $searchCriteria = $searchCriteriaBuilder->create();
        $collection = $this->customerPriceRepository->getList($searchCriteria);
        return $collection->getItems();
    }

    /**
     * Returning Collection By Product ID And Customer ID
     *
     * @param int $product_id
     * @param int $customerId
     * @return array
     */
    public function getCollection(int $product_id, int $customerId): array
    {
        $searchCriteriaBuilder = $this->searchCriteriaBuilder->addFilter('product_id', $product_id);
        $searchCriteriaBuilder->addFilter('customer_id', $customerId);
        $searchCriteria = $searchCriteriaBuilder->create();
        $collection = $this->customerPriceRepository->getList($searchCriteria)->getItems();
        $ids = [];
        foreach ($collection as $item) {
            $ids[] = $item->getId();
        }
        return $ids;
    }

    /**
     * Returning Product Ids Collection
     *
     * @param array $items
     * @return array
     */
    public function getProductIdsCollection(array $items): array
    {
        $productIds = [];
        foreach ($items as $item) {
            $productIds[] = $item['product_id'];
        }
        return $productIds;
    }

    /**
     * Returning Product Collection
     *
     * @param array $collection
     * @return array
     * @throws NoSuchEntityException
     */
    public function getProductCollection(array $collection): array
    {
        $ids = $this->getProductIdsCollection($collection);
        $data = [];
        foreach ($ids as $id) {
            $data[] = $this->productRepository->getById($id);
        }
        return $data;
    }

    /**
     * Returning Price Per Customer
     *
     * @param Product $subject
     * @param int $customerId
     * @return array
     */
    public function getCustomerPriceId(Product $subject, int $customerId): array
    {
        $productId = $subject->getId();
        return $this->getCollection($productId, $customerId);
    }

    /**
     * Get Discounted Price
     *
     * @param CustomerPriceInterface $data
     * @param float|int $originalPrice
     * @return float|int|void
     * @throws LocalizedException
     */
    public function getDiscountedPrice(CustomerPriceInterface $data, float|int $originalPrice)
    {
        if ((bool)strpos($data->getPrice(), '%') === true) {
            $percentage = str_replace("%", "", $data->getPrice());
            $discountPrice = $originalPrice * ($percentage/100);
            return $discountPrice;
        }
    }

    /**
     * Set Custom Price
     *
     * @param Product $subject
     * @param int $customerId
     * @return int|float
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function setCustomPrice(Product $subject, int $customerId)
    {
        $collectionId = $this->getCustomerPriceId($subject, $customerId);
        if (!empty($collectionId)) {
            $data = $this->customerPriceRepository->getById($collectionId[0]);
            if (is_numeric($data->getPrice())) {
                return $data->getPrice();
            } else {
                return $this->getDiscountedPrice($data, $subject->getData(ProductInterface::PRICE));
            }
        } else {
            return $subject->getData(ProductInterface::PRICE);
        }
    }

    /**
     * Set Custom Quantity
     *
     * @param Product $subject
     * @param int $customerId
     * @return int
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function setCustomQty(Product $subject, int $customerId): int
    {
        $collectionId = $this->getCustomerPriceId($subject, $customerId);
        if (!empty($collectionId)) {
            $data = $this->customerPriceRepository->getById($collectionId[0]);
            return (float)$data->getQuantity();
        } else {
            return (float)$subject->getData(self::QUANTITY);
        }
    }
}
