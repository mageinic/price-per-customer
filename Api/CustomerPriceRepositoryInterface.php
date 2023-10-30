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

namespace MageINIC\PricePerCustomer\Api;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use MageINIC\PricePerCustomer\Api\Data\CustomerPriceInterface;

/**
 * Price Per Customer Crud Interface
 */
interface CustomerPriceRepositoryInterface
{
    /**
     * Get Data By EntityId
     *
     * @param int $id
     * @return \MageINIC\PricePerCustomer\Api\Data\CustomerPriceInterface
     * @throws NoSuchEntityException|LocalizedException
     */
    public function getById(int $id): CustomerPriceInterface;

    /**
     * Save Data
     *
     * @param \MageINIC\PricePerCustomer\Api\Data\CustomerPriceInterface $data
     * @return \MageINIC\PricePerCustomer\Api\Data\CustomerPriceInterface
     * @throws CouldNotSaveException
     */
    public function save(CustomerPriceInterface $data): CustomerPriceInterface;

    /**
     * Delete Data By EntityId
     *
     * @param int $id
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException|LocalizedException
     */
    public function deleteById(int $id): bool;

    /**
     * Delete Data
     *
     * @param \MageINIC\PricePerCustomer\Api\Data\CustomerPriceInterface $data
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(CustomerPriceInterface $data): bool;

    /**
     * Retrieve Info Matching The Specified Criteria.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @return \MageINIC\PricePerCustomer\Api\Data\CustomerPriceSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria);
}
