<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_MpZipCodeValidator
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\MpZipCodeValidator\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\CustomOptions;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Ui\Component\Form\Fieldset;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Element\Select;
use Magento\Ui\Component\Form\Element\MultiSelect;
use Magento\Ui\Component\Form\Element\DataType\Text;
use \Magento\Store\Model\ScopeInterface;

class ZipCodeOptions extends CustomOptions
{
    /**#@+
     * Group values
     */
    const GROUP_BOOKING_OPTIONS_PREVIOUS_NAME = 'general';
    const GROUP_BOOKING_OPTIONS_DEFAULT_SORT_ORDER = 6;
    /**#@-*/

    /**#@+
     * Field values
     */
    const FIELD_ZIP_CODE_VALIDATION = 'zip_code_validation';
    const FIELD_SELECT_REGION = 'available_region';
    /**#@-*/

    /**
     * @var LocatorInterface
     */
    protected $locator;

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var \Webkul\MpZipCodeValidator\Model\ResourceModel\Region\CollectionFactory
     */
    protected $regionCollectionFactory;

    /**
     * @var \Webkul\MpZipCodeValidator\Block\Adminhtml\Product\Edit
     */
    protected $productEdit;

    /**
     * @var array
     */
    protected $meta = [];

    /**
     * @param LocatorInterface $locator
     * @param RequestInterface $request
     * @param \Webkul\MpZipCodeValidator\Model\Config\Source\RegionOptions $availableRegions
     * @param \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $configurableProductType
     * @param \Webkul\MpZipCodeValidator\Model\ResourceModel\Region\CollectionFactory $regionCollectionFactory,
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Webkul\MpZipCodeValidator\Block\Adminhtml\Product\Edit $productEdit
     */
    public function __construct(
        LocatorInterface $locator,
        RequestInterface $request,
        \Webkul\MpZipCodeValidator\Model\Config\Source\RegionOptions $availableRegions,
        \Magento\ConfigurableProduct\Model\ResourceModel\Product\Type\Configurable $configurableProductType,
        \Webkul\MpZipCodeValidator\Model\ResourceModel\Region\CollectionFactory $regionCollectionFactory,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Webkul\MpZipCodeValidator\Block\Adminhtml\Product\Edit $productEdit
    ) {
        $this->locator = $locator;
        $this->request = $request;
        $this->availableRegions = $availableRegions;
        $this->configurableProductType = $configurableProductType;
        $this->scopeConfig = $scopeConfig;
        $this->productEdit = $productEdit;
        $this->regionCollection = $regionCollectionFactory;
    }
    
    /**
     * Override parent class modifyData method
     *
     * @param array $data
     * @return array
     */
    public function modifyData(array $data)
    {
        $selectedRegionsStr = $this->locator->getProduct()->getAvailableRegion();
        if (!empty($this->availableRegions->getAllOptions())) {
            $regionIds = array_column($this->availableRegions->getAllOptions(), 'value');
        } else {
            $regionIds = [];
        }
        
        if ($selectedRegionsStr) {
            $selectedRegions = explode(",", $selectedRegionsStr);
            $selectedRegions = array_intersect($regionIds, $selectedRegions);
            $selectedRegionsStr = implode(",", $selectedRegions);
        }
        return array_replace_recursive($data, [
            $this->locator->getProduct()->getId() => [
                self::DATA_SOURCE_DEFAULT => [
                    static::FIELD_ZIP_CODE_VALIDATION => $this->locator->getProduct()->getZipCodeValidation() ?? 2,
                    static::FIELD_SELECT_REGION => $selectedRegionsStr,
                ],
            ]
        ]);
    }

    /**
     * Override parent class modifyMeta method
     *
     * @param array $meta
     * @return array $meta
     */
    public function modifyMeta(array $meta)
    {
        $this->meta = $meta;
        $productId = $this->locator->getProduct()->getId();
        $product = $this->configurableProductType->getParentIdsByChild($productId);
        $isAssociated = false;
        if ($product) {
            $isAssociated = true;
        }
        $productType = $this->getProductType();
        $allowedTypes = [
            "simple",
            "configurable",
            "bundle",
            "grouped"
        ];
        if ($this->getEnableDisable() && in_array($productType, $allowedTypes) && !($isAssociated)) {
            $this->createZipCodeValidationPanel();
            $this->createZipCodeRegionsPanel();
        }
        return $this->meta;
    }

    /**
     * Create ZipCodeValidation panel
     *
     * @return void
     */
    protected function createZipCodeValidationPanel()
    {
        $this->meta = array_replace_recursive(
            $this->meta,
            [
                $this->getGeneralPanelName($this->meta) => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __('Zip Code Validation'),
                                'componentType' => Fieldset::NAME,
                                'dataScope' => static::GROUP_CUSTOM_OPTIONS_SCOPE,
                                'collapsible' => true,
                                'sortOrder' => $this->getNextGroupSortOrder(
                                    $this->meta,
                                    static::GROUP_BOOKING_OPTIONS_PREVIOUS_NAME,
                                    static::GROUP_BOOKING_OPTIONS_DEFAULT_SORT_ORDER
                                ),
                                'opened' => true,
                                'canShow' => true,
                                'value' => 2,
                            ],
                        ],
                    ],
                    'children' => [
                        static::FIELD_ZIP_CODE_VALIDATION => $this->getZipCodeValidationFieldConfig(),
                    ]
                ]
            ]
        );
        return $this;
    }

    /**
     * Create ZipCodeRegions Panel
     *
     * @return void
     */
    public function createZipCodeRegionsPanel()
    {
        $this->meta = array_replace_recursive(
            $this->meta,
            [
                $this->getGeneralPanelName($this->meta) => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __('Select the Regions'),
                                'componentType' => Fieldset::NAME,
                                'dataScope' => static::GROUP_CUSTOM_OPTIONS_SCOPE,
                                'collapsible' => true,
                                'sortOrder' => $this->getNextGroupSortOrder(
                                    $this->meta,
                                    static::GROUP_BOOKING_OPTIONS_PREVIOUS_NAME,
                                    static::GROUP_BOOKING_OPTIONS_DEFAULT_SORT_ORDER
                                ),
                                'opened' => true,
                                'canShow' => true,
                            ],
                        ],
                    ],
                    'children' => [
                        static::FIELD_SELECT_REGION => $this->getSelectRegionsFieldConfig(),
                    ]
                ]
            ]
        );
        return $this;
    }

    /**
     * Get Zip Code Validation Field Config
     *
     * @return array
     */
    protected function getZipCodeValidationFieldConfig()
    {
        $optionsArray = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Zip Code Validation'),
                        'template' => 'Webkul_MpZipCodeValidator/form/field',
                        'visible' => true,
                        'componentType' => Field::NAME,
                        'formElement' => Select::NAME,
                        'dataScope' => static::FIELD_ZIP_CODE_VALIDATION,
                        'dataType' => Text::NAME,
                        'additionalClasses' => 'wk-select-wide',
                        'options' => $this->getRegionOptions(),
                        ]
                    ]
                ]
            ];
        return $optionsArray;
    }

    /**
     * Get selected region field config
     *
     * @return array
     */
    protected function getSelectRegionsFieldConfig()
    {
        $regionArray = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'label' => __('Select the Regions'),
                        'template' => 'Webkul_MpZipCodeValidator/form/field',
                        'visible' => true,
                        'componentType' => Field::NAME,
                        'formElement' => MultiSelect::NAME,
                        'dataScope' => static::FIELD_SELECT_REGION,
                        'dataType' => Text::NAME,
                        'additionalClasses' => 'wk-select-wideel',
                        'required' => true,
                        'imports' => [
                            'visible' => '!${$.provider}:' . self::DATA_SCOPE_PRODUCT
                                . '.zip_code_validation:value',
                                '__disableTmpl' => ['visible' => false],
                        ],
                        'validation' => [
                            'required-entry' => true
                        ],
                        'options' => $this->availableRegions->getAllOptions(),
                    ]
                ]
            ]
        ];
        return $regionArray;
    }

    /**
     * Get product type
     *
     * @return null|string
     */
    private function getProductType()
    {
        return $this->locator->getProduct()->getTypeId();
    }

    /**
     * Get Module enable/disable value from configuration
     *
     * @return int
     */
    public function getEnableDisable()
    {
        return $this->scopeConfig->getValue(
            'mpzipcode/general/enable',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get ApplyTo Configuration value
     *
     * @return int
     */
    public function getApplyStatus()
    {
        return $this->scopeConfig->getValue(
            'mpzipcode/general/applyto',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get Region options
     *
     * @return array
     */
    public function getRegionOptions()
    {
        if ($this->getApplyStatus()) {
            $sellerId = $this->productEdit->getProductSellerId();
            $collections = $this->regionCollection->create()
                ->addFieldToFilter('status', 1)
                ->addFieldToFilter('seller_id', $sellerId)
                ->addFieldToFilter('seller_id', ['neq' => 0 ]);
                
            $adminCollections = $this->regionCollection->create()
                ->addFieldToFilter('status', 1)
                ->addFieldToFilter('seller_id', ['eq' => 0 ]);
                
            if ($collections->getSize()) {
                $options = $this->getAllValidationOptions();
            } else {
                if ($sellerId != 0 && empty($adminCollections->getSize())) {
                    $options = [
                        ['value' => 1, 'label' => __('No Validation')],
                    ];
                } elseif ($sellerId == 0) {
                    if (!empty($adminCollections->getSize())) {
                        $options = $this->getAllValidationOptions();
                    } else {
                        $options = [
                            ['value' => 1, 'label' => __('No Validation')],
                        ];
                    }
                } else {
                    $options = [
                        ['value' => 1, 'label' => __('No Validation')],
                        ['value' => 2, 'label' => __('Apply default Configuration')],
                    ];
                }
            }
            return $options;
        } else {
            $options = [
                ['value' => 1, 'label' => __('Disable')],
                ['value' => 0, 'label' => __('Enable')],
            ];
            return $options;
        }
    }

    /**
     * Get all validation options
     *
     * @return array
     */
    public function getAllValidationOptions()
    {
        $options = [
            ['value' => 1, 'label' => __('No Validation')],
            ['value' => 2, 'label' => __('Apply default Configuration')],
            ['value' => 3, 'label' => __('All regions')],
            ['value' => 0, 'label' => __('Select Specific')],
        ];
        return $options;
    }
}
