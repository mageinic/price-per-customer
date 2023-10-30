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

namespace MageINIC\PricePerCustomer\Block\Adminhtml\CustomerEdit\Tab;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Model\Url;
use Magento\Catalog\Helper\Product as HelperProduct;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ProductFactory;
use Magento\Customer\Controller\RegistryConstants;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Magento\Ui\Component\Layout\Tabs\TabInterface;
use MageINIC\PricePerCustomer\Api\Data\CustomerPriceInterface;
use MageINIC\PricePerCustomer\Helper\Data;

/**
 * Class for Add New Tab in Customer View
 */
class CustomerView extends Template implements TabInterface
{
    /**
     * Catalog Product Edit Admin Ui Form Path
     */
    public const PRODUCT_EDIT_PATH = 'catalog/product/edit/id/';

    /**
     * Price Per Customer Delete Path
     */
    public const CUSTOMER_PRICE_DELETE_PATH = 'customerprice/index/delete';

    /**
     * @var string
     */
    protected $_template = 'tab/customerTabView.phtml';

    /**
     * @var Registry
     */
    protected Registry $_coreRegistry;

    /**
     * @var Data
     */
    protected Data $helperData;

    /**
     * @var ProductFactory
     */
    protected ProductFactory $productRepository;

    /**
     * @var HelperProduct
     */
    protected HelperProduct $productHelper;

    /**
     * @var Url
     */
    protected Url $backendUrlManager;

    /**
     * Customer View Constructor
     *
     * @param Context $context
     * @param Registry $registry
     * @param Data $helperData
     * @param ProductFactory $productRepository
     * @param HelperProduct $productHelper
     * @param Url $backendUrlManager
     * @param array $data
     */
    public function __construct(
        Context        $context,
        Registry       $registry,
        Data           $helperData,
        ProductFactory $productRepository,
        HelperProduct  $productHelper,
        Url            $backendUrlManager,
        array          $data = []
    ) {
        $this->_coreRegistry = $registry;
        $this->helperData = $helperData;
        $this->productRepository = $productRepository;
        $this->productHelper = $productHelper;
        $this->backendUrlManager = $backendUrlManager;
        parent::__construct($context, $data);
    }

    /**
     * Get Current CustomerId
     *
     * @return mixed|null
     */
    public function getCustomerId(): mixed
    {
        return $this->_coreRegistry->registry(RegistryConstants::CURRENT_CUSTOMER_ID);
    }

    /**
     * @inheritdoc
     */
    public function getTabLabel(): Phrase|string
    {
        return __('Price Per Customer');
    }

    /**
     * @inheritdoc
     */
    public function getTabTitle(): Phrase|string
    {
        return __('Price Per Customer');
    }

    /**
     * @inheritdoc
     */
    public function canShowTab(): bool
    {
        if ($this->getCustomerId()) {
            return true;
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function isHidden(): bool
    {
        if ($this->getCustomerId()) {
            return false;
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function getTabClass(): string
    {
        return '';
    }

    /**
     * @inheritdoc
     */
    public function getTabUrl(): string
    {
        return '';
    }

    /**
     * @inheritdoc
     */
    public function isAjaxLoaded(): bool
    {
        return false;
    }

    /**
     * Get Customer Product Collection By CustomerId
     *
     * @param int $customerId
     * @return CustomerPriceInterface[]
     */
    public function getProductCollection(int $customerId): array
    {
        return $this->helperData->getCollectionByCustomerId($customerId);
    }

    /**
     * Delete Action Url
     *
     * @return string
     */
    public function getDeleteAction(): string
    {
        return $this->getUrl(self::CUSTOMER_PRICE_DELETE_PATH, ['_secure' => true]);
    }

    /**
     * Load Product Data By ProductId
     *
     * @param int $productId
     * @return Product
     */
    public function getProductData(int $productId): Product
    {
        return $this->productRepository->create()->load($productId);
    }

    /**
     * Get Product Thumbnail Url
     *
     * @param Product $product
     * @return bool|string
     */
    public function getThumbnailUrl(Product $product): bool|string
    {
        return $this->productHelper->getThumbnailUrl($product);
    }

    /**
     * Get Product Edit Url
     *
     * @param int $productId
     * @return string
     */
    public function getProductEditUrl(int $productId): string
    {
        return $this->backendUrlManager->getUrl(self::PRODUCT_EDIT_PATH . $productId);
    }
}
