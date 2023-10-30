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

namespace MageINIC\PricePerCustomer\Block\Product;

use MageINIC\PricePerCustomer\Api\Data\CustomerPriceInterface;
use MageINIC\PricePerCustomer\Helper\Data as CustomerPriceHelper;
use Magento\Catalog\Api\CategoryRepositoryInterface as CategoryRepository;
use Magento\Catalog\Block\Product\Context;
use Magento\Catalog\Block\Product\ListProduct as OriginListProduct;
use Magento\Catalog\Helper\Output as OutputHelper;
use Magento\Catalog\Model\Layer\Resolver;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Customer\Model\SessionFactory;
use Magento\Eav\Model\Entity\Collection\AbstractCollection;
use Magento\Framework\Data\Helper\PostHelper;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Url\Helper\Data;

/**
 * Class For Product Listing
 */
class ListProduct extends OriginListProduct
{
    /**
     * @var CustomerPriceHelper
     */
    protected CustomerPriceHelper $helperData;

    /**
     * @var SessionFactory
     */
    protected SessionFactory $_customerSession;

    /**
     * @var CollectionFactory
     */
    private CollectionFactory $collectionFactory;

    /**
     * @param Context $context
     * @param PostHelper $postDataHelper
     * @param Resolver $layerResolver
     * @param CategoryRepository $categoryRepository
     * @param Data $urlHelper
     * @param CustomerPriceHelper $helperData
     * @param SessionFactory $customerSession
     * @param CollectionFactory $collectionFactory
     * @param array $data
     * @param OutputHelper|null $outputHelper
     */
    public function __construct(
        Context               $context,
        PostHelper            $postDataHelper,
        Resolver              $layerResolver,
        CategoryRepository    $categoryRepository,
        Data                  $urlHelper,
        CustomerPriceHelper   $helperData,
        SessionFactory        $customerSession,
        CollectionFactory     $collectionFactory,
        array                 $data = [],
        ?OutputHelper         $outputHelper = null
    ) {
        $this->helperData = $helperData;
        $this->_customerSession = $customerSession;
        $this->collectionFactory = $collectionFactory;
        parent::__construct(
            $context,
            $postDataHelper,
            $layerResolver,
            $categoryRepository,
            $urlHelper,
            $data,
            $outputHelper
        );
    }

    /**
     * Get Product Collection
     *
     * @return Collection|AbstractCollection
     * @throws LocalizedException
     */
    protected function _getProductCollection(): Collection|AbstractCollection
    {
        if ($this->_productCollection === null) {
            $customerId = $this->_customerSession->create()->getCustomerId();
            $collection = $this->helperData->getCollectionByCustomerId($customerId);
            $productIds = $this->getProductIds($collection);
            $productCollection = $this->collectionFactory->create();
            $productCollection->addAttributeToSelect('*')
                ->addAttributeToFilter('visibility', Visibility::VISIBILITY_BOTH)
                ->addFieldToFilter('entity_id', ['in' => $productIds])
                ->load();
            $this->_productCollection = $productCollection;
        }
        return $this->_productCollection;
    }

    /**
     * Get Product Ids
     *
     * @param CustomerPriceInterface[] $collection
     * @return array
     * @throws LocalizedException
     */
    public function getProductIds(array $collection): array
    {
        $productIds = [];
        foreach ($collection as $item) {
            $productIds[] = $item->getProductId();
        }
        return $productIds;
    }
}
