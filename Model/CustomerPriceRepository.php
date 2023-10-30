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

use Exception;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface as CollectionProcessor;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use MageINIC\PricePerCustomer\Api\Data\CustomerPriceInterface;
use MageINIC\PricePerCustomer\Api\CustomerPriceRepositoryInterface;
use MageINIC\PricePerCustomer\Model\ResourceModel\CustomerPrice as CustomerPriceResource;
use MageINIC\PricePerCustomer\Model\ResourceModel\CustomerPrice\CollectionFactory;
use MageINIC\PricePerCustomer\Api\Data\CustomerPriceSearchResultsInterfaceFactory as SearchResultFactory;

/**
 * Repository For Price Per Customer
 */
class CustomerPriceRepository implements CustomerPriceRepositoryInterface
{
    /**
     * @var CustomerPriceResource
     */
    private CustomerPriceResource $resource;

    /**
     * @var CollectionFactory
     */
    private CollectionFactory $collectionFactory;

    /**
     * @var CollectionProcessor
     */
    protected CollectionProcessor $collectionProcessor;

    /**
     * @var SearchResultFactory
     */
    protected SearchResultFactory $customerPriceSearchResult;

    /**
     * @var CustomerPriceFactory
     */
    private CustomerPriceFactory $customerPriceModelFactory;

    /**
     * Customer Price Repository Constructor
     *
     * @param CustomerPriceResource $resource
     * @param CollectionFactory $collectionFactory
     * @param CollectionProcessor $collectionProcessor
     * @param SearchResultFactory $customerPriceSearchResult
     * @param CustomerPriceFactory $customerPriceModelFactory
     */
    public function __construct(
        CustomerPriceResource   $resource,
        CollectionFactory       $collectionFactory,
        CollectionProcessor     $collectionProcessor,
        SearchResultFactory     $customerPriceSearchResult,
        CustomerPriceFactory    $customerPriceModelFactory,
    ) {
        $this->resource = $resource;
        $this->collectionFactory = $collectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->customerPriceModelFactory = $customerPriceModelFactory;
        $this->customerPriceSearchResult = $customerPriceSearchResult;
    }

   /**
    * @inheritdoc
    */
    public function getById(int $id): CustomerPriceInterface
    {
        $data = $this->customerPriceModelFactory->create();
        $this->resource->load($data, $id);
        if (!$data->getId()) {
            throw new NoSuchEntityException(
                __('Entity Id: ' . $id . ' does not exist.')
            );
        }
        return $data;
    }

    /**
     * @inheritdoc
     */

    public function save(CustomerPriceInterface $data): CustomerPriceInterface
    {
        try {
            $this->resource->save($data);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(__($e->getMessage()));
        }
        return $data;
    }

    /**
     * @inheritdoc
     */
    public function deleteById(int $id): bool
    {
        if ($id) {
            $this->delete($this->getById($id));
            return true;
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function delete(CustomerPriceInterface $data): bool
    {
        try {
            $this->resource->delete($data);
        } catch (Exception $e) {
            throw new CouldNotDeleteException(
                __('Could not delete the custom price: %1', $e->getMessage())
            );
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $collection = $this->collectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $collection);
        $searchResults = $this->customerPriceSearchResult->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }
}
