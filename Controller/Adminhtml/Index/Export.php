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
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\App\Action\HttpGetActionInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Filesystem;
use MageINIC\PricePerCustomer\Api\CustomerPriceRepositoryInterface;
use Magento\Framework\Filesystem\Directory\WriteInterface;

/**
 * Class For Csv Export
 */
class Export extends Action implements HttpGetActionInterface
{
    /**
     * @var WriteInterface
     */
    protected WriteInterface $directory;

    /**
     * @var CustomerFactory
     */
    protected CustomerFactory $customerFactory;

    /**
     * @var SearchCriteriaBuilder
     */
    protected SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @var CustomerPriceRepositoryInterface
     */
    protected CustomerPriceRepositoryInterface $customerPriceRepository;

    /**
     * @var FileFactory
     */
    protected FileFactory $fileFactory;

    /**
     * Export Constructor
     *
     * @param Context $context
     * @param Filesystem $filesystem
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param CustomerPriceRepositoryInterface $customerPriceRepository
     * @param FileFactory $fileFactory
     * @throws FileSystemException
     */
    public function __construct(
        Context                $context,
        Filesystem             $filesystem,
        SearchCriteriaBuilder  $searchCriteriaBuilder,
        CustomerPriceRepositoryInterface $customerPriceRepository,
        FileFactory            $fileFactory
    ) {
        $this->directory = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->customerPriceRepository = $customerPriceRepository;
        $this->fileFactory = $fileFactory;
        parent::__construct($context);
    }

    /**
     * @inheritdoc
     *
     * @throws LocalizedException
     * @throws Exception
     */
    public function execute()
    {
        $time = date('m_d_Y_H_i_s');
        $filepath = 'export/customerprice_' . $time . '.csv';
        $this->directory->create('export');
        $stream = $this->directory->openFile($filepath, 'w+');
        $stream->lock();
        $header = ['Id', 'Customer Id', 'Product Id', 'Price', 'Quantity'];
        $stream->writeCsv($header);
        $searchCriteria = $this->searchCriteriaBuilder->create();
        $collection = $this->customerPriceRepository->getList($searchCriteria)->getItems();
        foreach ($collection as $customer) {
            $data = [];
            $data[] = $customer->getId();
            $data[] = $customer->getCustomerId();
            $data[] = $customer->getProductId();
            $data[] = $customer->getPrice();
            $data[] = $customer->getQuantity();
            $stream->writeCsv($data);
        }
        $content = [];
        $content['type'] = 'filename';
        $content['value'] = $filepath;
        $content['rm'] = '1';
        $csvFileName = 'customerprice_' . $time . '.csv';
        return $this->fileFactory->create($csvFileName, $content, DirectoryList::VAR_DIR);
    }
}
