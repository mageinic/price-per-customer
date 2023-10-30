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

namespace MageINIC\PricePerCustomer\Controller\Adminhtml\Index;

use Exception;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use MageINIC\PricePerCustomer\Api\CustomerPriceRepositoryInterface as CustomerPriceRepository;
use Magento\Framework\App\Action\HttpGetActionInterface;

/**
 * Class For Delete Data
 */
class Delete extends Action implements HttpGetActionInterface
{
    /**
     * Customer Admin Grid Url Path
     */
    public const CUSTOMER_INDEX_ADMIN_PATH = 'customer/index/index';

    /**
     * @var CustomerPriceRepository
     */
    protected CustomerPriceRepository $customerPriceRepository;

    /**
     * Delete Constructor
     *
     * @param Context $context
     * @param CustomerPriceRepository $customerPriceRepository
     */
    public function __construct(
        Context       $context,
        CustomerPriceRepository $customerPriceRepository
    ) {
        parent::__construct($context);
        $this->customerPriceRepository = $customerPriceRepository;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        $resultRedirect = $this->resultRedirectFactory->create();
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                $this->customerPriceRepository->deleteById($id);
                $this->messageManager->addSuccessMessage(__('You deleted the special price.'));
                return $resultRedirect->setPath(self::CUSTOMER_INDEX_ADMIN_PATH);
            } catch (Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
            }
        }
        return $resultRedirect->setPath(self::CUSTOMER_INDEX_ADMIN_PATH);
    }
}
