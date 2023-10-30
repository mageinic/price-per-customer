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

namespace MageINIC\PricePerCustomer\Plugin\Adminhtml\Catalog\Product;

use Exception;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Catalog\Controller\Adminhtml\Product\Save as OriginSave;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use MageINIC\PricePerCustomer\Api\Data\CustomerPriceInterfaceFactory;
use MageINIC\PricePerCustomer\Api\CustomerPriceRepositoryInterface;
use MageINIC\PricePerCustomer\Helper\Data;

/**
 * Class For Product Save Related Plugin
 */
class Save
{
    /**
     * @var Data
     */
    protected Data $helperData;

    /**
     * @var CustomerPriceInterfaceFactory
     */
    protected CustomerPriceInterfaceFactory $pricePerCustomer;

    /**
     * @var MessageManagerInterface
     */
    protected MessageManagerInterface $messageManager;

    /**
     * @var CustomerPriceRepositoryInterface
     */
    private CustomerPriceRepositoryInterface $customerPriceRepository;

    /**
     * Save Constructor
     *
     * @param CustomerPriceInterfaceFactory $pricePerCustomer
     * @param Data $helperData
     * @param CustomerPriceRepositoryInterface $customerPriceRepository
     * @param MessageManagerInterface $messageManager
     */
    public function __construct(
        CustomerPriceInterfaceFactory    $pricePerCustomer,
        Data                             $helperData,
        CustomerPriceRepositoryInterface $customerPriceRepository,
        MessageManagerInterface          $messageManager
    ) {
        $this->pricePerCustomer = $pricePerCustomer;
        $this->helperData = $helperData;
        $this->customerPriceRepository = $customerPriceRepository;
        $this->messageManager = $messageManager;
    }

    /**
     * After Plugin On Execute Function
     *
     * @param OriginSave $subject
     * @param Redirect $result
     * @return mixed
     */
    public function afterExecute(OriginSave $subject, Redirect $result)
    {
        $productId = $subject->getRequest()->getParam('id');
        $links = $subject->getRequest()->getParam('links');
        if (isset($links['customer_price'])) {
            $customers = $links['customer_price'];
            foreach ($customers as $customer) {
                $collection = $this->helperData->getCollection($productId, $customer['id']);
                try {
                    if (!empty($collection)) {
                        $model = $this->customerPriceRepository->getById($collection[0]);
                    } else {
                        $model = $this->pricePerCustomer->create();
                        $model->setCustomerId($customer['id']);
                        $model->setProductId($productId);
                    }
                    $model->setPrice($customer['price']);
                    $model->setQuantity($customer['quantity']);
                    $this->customerPriceRepository->save($model);
                } catch (Exception $e) {
                    $this->messageManager->addExceptionMessage(
                        $e,
                        __('Something went wrong while saving the price per customer')
                    );
                }
            }
        }
        return $result;
    }
}
