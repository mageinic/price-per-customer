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

namespace MageINIC\PricePerCustomer\Controller\Index;

use Magento\Customer\Model\Session;
use Magento\Customer\Model\Url;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\Controller\Result\ForwardFactory;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\View\Result\PageFactory;
use MageINIC\PricePerCustomer\Helper\Data;

/**
 * Class For Product Listing Controller
 */
class ProductListing implements HttpGetActionInterface
{
    /**
     * @var PageFactory
     */
    protected PageFactory $resultPageFactory;

    /**
     * @var Data
     */
    protected Data $helperData;

    /**
     * @var Session
     */
    protected Session $customerSession;

    /**
     * @var RedirectFactory
     */
    protected RedirectFactory $resultRedirect;

    /**
     * @var ForwardFactory
     */
    protected ForwardFactory $forwardFactory;

    /**
     * Product Listing Constructor
     *
     * @param PageFactory $resultPageFactory
     * @param RedirectFactory $resultRedirect
     * @param Data $helperData
     * @param Session $customerSession
     * @param ForwardFactory $forwardFactory
     */
    public function __construct(
        PageFactory     $resultPageFactory,
        RedirectFactory $resultRedirect,
        Data            $helperData,
        Session         $customerSession,
        ForwardFactory  $forwardFactory
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->helperData = $helperData;
        $this->customerSession = $customerSession;
        $this->resultRedirect = $resultRedirect;
        $this->forwardFactory = $forwardFactory;
    }

    /**
     * @inheritdoc
     */
    public function execute()
    {
        if ($this->helperData->isEnable()) {
            if (!$this->customerSession->isLoggedIn()) {
                $result = $this->resultRedirect->create();
                $result->setPath(Url::ROUTE_ACCOUNT_LOGIN);
            } else {
                $result = $this->resultPageFactory->create();
                $title = $this->helperData->getPageTitle();
                $layout = $this->helperData->getLayout();
                $result->getConfig()->addBodyClass('page-products');
                $result->getConfig()->addBodyClass('catalog-category-view');
                $result->getConfig()->addBodyClass('page-with-filter');
                $result->getConfig()->getTitle()->set(__($title));
                $result->getConfig()->setPageLayout($layout);
            }
        } else {
            $resultForward = $this->forwardFactory->create();
            $resultForward->setController('index');
            $resultForward->forward('defaultNoRoute');
            return $resultForward;
        }
        return $result;
    }
}
