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

namespace MageINIC\PricePerCustomer\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Phrase;
use Magento\Framework\UrlInterface;
use Magento\Store\Api\WebsiteRepositoryInterface;
use Magento\Ui\Component\DynamicRows;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Fieldset;
use Magento\Ui\Component\Modal;
use MageINIC\PricePerCustomer\Helper\Data;
use MageINIC\PricePerCustomer\Model\ResourceModel\CustomerPrice\CollectionFactory;

/**
 * Class For Product Modifier Price Per Customer
 */
class PricePerCustomer extends AbstractModifier
{
    /**#@+
     * Constants for keys of data array. Identical to the name of the getter in snake case
     */
    public const DATA_SCOPE = '';
    public const DATA_SCOPE_CUSTOMERPRICE = 'customer_price';
    public const GROUP_CUSTOMERPRICE = 'customerprice';
    /**#@-*/

    /**
     * @var string
     */
    private static string $previousGroup = 'search-engine-optimization';

    /**
     * @var int
     */
    private static int $sortOrder = 120;

    /**
     * @var LocatorInterface
     */
    protected LocatorInterface $locator;

    /**
     * @var UrlInterface
     */
    protected UrlInterface $urlBuilder;

    /**
     * @var string
     */
    protected string $scopeName;

    /**
     * @var string
     */
    protected string $scopePrefix;

    /**
     * @var CustomerRepositoryInterface
     */
    protected CustomerRepositoryInterface $customerRepository;

    /**
     * @var WebsiteRepositoryInterface
     */
    protected WebsiteRepositoryInterface $websiteRepository;

    /**
     * @var GroupRepositoryInterface
     */
    protected GroupRepositoryInterface $groupRepository;

    /**
     * @var Data
     */
    protected Data $helperData;

    /**
     * @var CollectionFactory
     */
    private CollectionFactory $collectionFactory;

    /**
     * Price Per Customer Constructor
     *
     * @param LocatorInterface $locator
     * @param UrlInterface $urlBuilder
     * @param Data $helperData
     * @param CustomerRepositoryInterface $customerRepository
     * @param WebsiteRepositoryInterface $websiteRepository
     * @param GroupRepositoryInterface $groupRepository
     * @param CollectionFactory $collectionFactory
     * @param string $scopeName
     * @param string $scopePrefix
     */
    public function __construct(
        LocatorInterface            $locator,
        UrlInterface                $urlBuilder,
        Data                        $helperData,
        CustomerRepositoryInterface $customerRepository,
        WebsiteRepositoryInterface  $websiteRepository,
        GroupRepositoryInterface    $groupRepository,
        CollectionFactory           $collectionFactory,
        string                      $scopeName = '',
        string                      $scopePrefix = ''
    ) {
        $this->locator = $locator;
        $this->urlBuilder = $urlBuilder;
        $this->scopeName = $scopeName;
        $this->scopePrefix = $scopePrefix;
        $this->customerRepository = $customerRepository;
        $this->websiteRepository = $websiteRepository;
        $this->groupRepository = $groupRepository;
        $this->helperData = $helperData;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @inheritdoc
     */
    public function modifyMeta(array $meta): array
    {
        $meta = array_replace_recursive(
            $meta,
            [
                static::GROUP_CUSTOMERPRICE => [
                    'children' => [
                        $this->scopePrefix . static::DATA_SCOPE_CUSTOMERPRICE => $this->getCustomerPriceFieldset(),
                    ],
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __('Price Per Customer'),
                                'collapsible' => true,
                                'componentType' => Fieldset::NAME,
                                'dataScope' => static::DATA_SCOPE,
                                'sortOrder' =>
                                    $this->getNextGroupSortOrder(
                                        $meta,
                                        self::$previousGroup,
                                        self::$sortOrder
                                    ),
                            ],
                        ],

                    ],
                ],
            ]
        );

        return $meta;
    }

    /**
     * Prepares Config For The Related Products Fieldset
     *
     * @return array
     */
    protected function getCustomerPriceFieldset(): array
    {
        $html = '<p>' . __('Apply Special-Price or Discount to customers for this Product,') . '</p>';
        $html .= '<p>' . __('If you want to apply Discount then must add % after Discount Value in "Add Price",')
            . '</p>';
        $html .= '<p>' . __('Ex: 12%.') . '</p>';
        $content = __($html);
        return [
            'children' => [
                'button_set' => $this->getButtonSet(
                    $content,
                    __('Price Per Customer Configuration'),
                    $this->scopePrefix . static::DATA_SCOPE_CUSTOMERPRICE
                ),
                'modal' => $this->getGenericModal(
                    __('Apply discount to the customer'),
                    $this->scopePrefix . static::DATA_SCOPE_CUSTOMERPRICE
                ),
                static::DATA_SCOPE_CUSTOMERPRICE => $this->getGrid(
                    $this->scopePrefix . static::DATA_SCOPE_CUSTOMERPRICE
                ),
            ],
            'arguments' => [
                'data' => [
                    'config' => [
                        'additionalClasses' => 'admin__fieldset-section',
                        'label' => __('Price Per Customer'),
                        'collapsible' => false,
                        'componentType' => Fieldset::NAME,
                        'dataScope' => '',
                        'sortOrder' => 10,
                    ],
                ],
            ]
        ];
    }

    /**
     * Retrieve Button Set
     *
     * @param Phrase $content
     * @param Phrase $buttonTitle
     * @param string $scope
     * @return array
     * @since 101.0.0
     */
    protected function getButtonSet(Phrase $content, Phrase $buttonTitle, string $scope): array
    {
        $modalTarget = $this->scopeName . '.' . static::GROUP_CUSTOMERPRICE . '.' . $scope . '.modal';
        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'formElement' => 'container',
                        'componentType' => 'container',
                        'label' => false,
                        'content' => $content,
                        'template' => 'ui/form/components/complex',
                    ],
                ],
            ],
            'children' => [
                'button_' . $scope => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'formElement' => 'container',
                                'componentType' => 'container',
                                'component' => 'Magento_Ui/js/form/components/button',
                                'actions' => [
                                    [
                                        'targetName' => $modalTarget,
                                        'actionName' => 'toggleModal',
                                    ],
                                    [
                                        'targetName' => $modalTarget . '.' . $scope . '_listing',
                                        'actionName' => 'render',
                                    ]
                                ],
                                'title' => $buttonTitle,
                                'provider' => null,
                            ],
                        ],
                    ],

                ],
            ],
        ];
    }

    /**
     * Prepares Config For Modal Slide-Out Panel
     *
     * @param Phrase $title
     * @param string $scope
     * @return \array[][]
     */
    protected function getGenericModal(Phrase $title, string $scope): array
    {
        $listingTarget = $scope . '_listing';
        $modal = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Modal::NAME,
                        'dataScope' => '',
                        'options' => [
                            'title' => $title,
                            'buttons' => [
                                [
                                    'text' => __('Cancel'),
                                    'actions' => [
                                        'closeModal'
                                    ]
                                ],
                                [
                                    'text' => __('Add Selected Products'),
                                    'class' => 'action-primary',
                                    'actions' => [
                                        [
                                            'targetName' => 'index = ' . $listingTarget,
                                            'actionName' => 'save'
                                        ],
                                        'closeModal'
                                    ]
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'children' => [
                $listingTarget => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'autoRender' => false,
                                'componentType' => 'insertListing',
                                'dataScope' => $listingTarget,
                                'externalProvider' => $listingTarget . '.' . $listingTarget . '_data_source',
                                'selectionsProvider' => $listingTarget . '.' . $listingTarget . '.'
                                    . 'customer_price_columns.ids',
                                'ns' => $listingTarget,
                                'render_url' => $this->urlBuilder->getUrl('mui/index/render'),
                                'realTimeLink' => true,
                                'dataLinks' => [
                                    'imports' => false,
                                    'exports' => true
                                ],
                                'behaviourType' => 'simple',
                                'externalFilterMode' => true,
                                'imports' => [
                                    'productId' => '${ $.provider }:data.product.entity_id',
                                    'storeId' => '${ $.provider }:data.product.current_store_id',
                                    '__disableTmpl' => ['productId' => false, 'storeId' => false],
                                ],
                                'exports' => [
                                    'productId' => '${ $.externalProvider }:params.entity_id',
                                    'storeId' => '${ $.externalProvider }:params.current_store_id',
                                    '__disableTmpl' => ['productId' => false, 'storeId' => false],
                                ]
                            ],
                        ],
                    ],
                ],
            ],
        ];

        return $modal;
    }

    /**
     * Retrieve Grid
     *
     * @param string $scope
     * @return \array[][]
     */
    protected function getGrid(string $scope): array
    {
        $dataProvider = $scope . '_listing';

        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'additionalClasses' => 'admin__field-wide',
                        'componentType' => DynamicRows::NAME,
                        'label' => null,
                        'columnsHeader' => false,
                        'columnsHeaderAfterRender' => true,
                        'renderDefaultRecord' => false,
                        'template' => 'ui/dynamic-rows/templates/grid',
                        'component' => 'Magento_Ui/js/dynamic-rows/dynamic-rows-grid',
                        'addButton' => false,
                        'recordTemplate' => 'record',
                        'dataScope' => 'data.links',
                        'deleteButtonLabel' => __('Remove'),
                        'dataProvider' => $dataProvider,
                        'map' => [
                            'id' => 'entity_id',
                            'name' => 'name',
                            'email' => 'email',
                            'website_id' => 'website_text',
                            'group_id' => 'group_text'
                        ],
                        'links' => [
                            'insertData' => '${ $.provider }:${ $.dataProvider }',
                            '__disableTmpl' => ['insertData' => false],
                        ],
                        'sortOrder' => 2,
                    ]
                ]
            ],
            'children' => [
                'record' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => 'container',
                                'isTemplate' => true,
                                'is_collection' => true,
                                'component' => 'Magento_Ui/js/dynamic-rows/record',
                                'dataScope' => '',
                            ],
                        ],
                    ],
                    'children' => $this->fillMeta(),
                ]
            ]
        ];
    }

    /**
     * Retrieve Meta Column
     *
     * @return array
     */
    protected function fillMeta(): array
    {
        return [
            'id' => $this->getTextColumn('id', false, __('ID'), 0),
            'name' => $this->getTextColumn('name', false, __('Name'), 3),
            'email' => $this->getTextColumn('email', false, __('Email'), 2),
            'group_id' => $this->getTextColumn('group_id', false, __('Group'), 5),
            'website_id' => $this->getTextColumn('website_id', false, __('Website'), 4),
            'price' => $this->getTextInput('price', false, __('Add Price'), 6),
            'quantity' => $this->getTextInput('quantity', false, __('Add Quantity'), 7),
            'position' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'dataType' => Text::NAME,
                            'formElement' => Input::NAME,
                            'componentType' => Field::NAME,
                            'componentType' => Field::NAME,
                            'dataScope' => 'position',
                            'sortOrder' => 8,
                            'visible' => false,
                        ],
                    ],
                ],
            ],
            'actionDelete' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'additionalClasses' => 'data-grid-actions-cell',
                            'componentType' => 'actionDelete',
                            'dataType' => Text::NAME,
                            'label' => __('Actions'),
                            'sortOrder' => 90,
                            'fit' => true,
                        ]
                    ]
                ]
            ]
        ];
    }

    /**
     * Retrieve Text Column Structure
     *
     * @param string $dataScope
     * @param bool $fit
     * @param Phrase $label
     * @param int $sortOrder
     * @return array[]
     */
    protected function getTextColumn(string $dataScope, bool $fit, Phrase $label, int $sortOrder): array
    {
        $column = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Field::NAME,
                        'formElement' => Input::NAME,
                        'elementTmpl' => 'ui/dynamic-rows/cells/text',
                        'component' => 'Magento_Ui/js/form/element/text',
                        'dataType' => Text::NAME,
                        'dataScope' => $dataScope,
                        'fit' => $fit,
                        'label' => $label,
                        'sortOrder' => $sortOrder,
                    ],
                ],
            ],
        ];

        return $column;
    }

    /**
     * Retrieve Text Input Column Structure
     *
     * @param string $dataScope
     * @param bool $fit
     * @param Phrase $label
     * @param int $sortOrder
     * @return array[]
     */
    public function getTextInput(string $dataScope, bool $fit, Phrase $label, int $sortOrder): array
    {
        if ($dataScope == 'quantity') {
            $column = [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'componentType' => Field::NAME,
                            'formElement' => Input::NAME,
                            'dataType' => Text::NAME,
                            'dataScope' => $dataScope,
                            'fit' => $fit,
                            'label' => $label,
                            'sortOrder' => $sortOrder,
                            'validation' => [
                                'required-entry' => true,
                                'validate-digits' => true
                            ]
                        ],
                    ],
                ],
            ];
        } else {
            $column = [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'componentType' => Field::NAME,
                            'formElement' => Input::NAME,
                            'dataType' => Text::NAME,
                            'dataScope' => $dataScope,
                            'fit' => $fit,
                            'label' => $label,
                            'sortOrder' => $sortOrder,
                            'validation' => [
                                'required-entry' => true,
                                'numbers-percentage-validation' => true
                            ]
                        ],
                    ],
                ],
            ];
        }
        return $column;
    }

    /**
     * @inheritdoc
     */
    public function modifyData(array $data): array
    {
        /** @var Product $product */
        $product = $this->locator->getProduct();
        $productId = $product->getId();

        if (!$productId) {
            return $data;
        }
        foreach ($this->getDataScopes() as $dataScope) {
            $data[$productId]['links'][$dataScope] = [];
            $customerPriceCollection = $this->collectionFactory->create()->addFieldToFilter(
                'product_id',
                ['eq' => $productId]
            );
            foreach ($customerPriceCollection as $prodColl) {
                $data[$productId]['links'][$dataScope][] = $this->fillData(
                    $prodColl['customer_id'],
                    $prodColl['price'],
                    $prodColl['quantity'],
                    $productId
                );
            }
        }

        return $data;
    }

    /**
     * Retrieve All Data Scopes
     *
     * @return string[]
     */
    protected function getDataScopes(): array
    {
        return [
            static::DATA_SCOPE_CUSTOMERPRICE
        ];
    }

    /**
     * Prepare Data Column
     *
     * @param string $linkedCustomer
     * @param int $price
     * @param int $quantity
     * @param int $product
     * @return array
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    protected function fillData(string $linkedCustomer, int $price, int $quantity, int $product): array
    {
        $customer = $this->customerRepository->getById($linkedCustomer);
        $website = $this->websiteRepository->getById($customer->getWebsiteId());
        $customerGroup = $this->groupRepository->getById($customer->getGroupId());
        return [
            'id' => $linkedCustomer,
            'name' => __($customer->getFirstname() . ' ' . $customer->getLastname()),
            'email' => $customer->getEmail(),
            'group_id' => __($customerGroup->getCode()),
            'website_id' => __($website->getName()),
            'price' => $price,
            'quantity' => $quantity
        ];
    }
}
