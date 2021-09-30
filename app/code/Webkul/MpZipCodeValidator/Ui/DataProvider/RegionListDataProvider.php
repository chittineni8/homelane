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
namespace Webkul\MpZipCodeValidator\Ui\DataProvider;

use Webkul\MpZipCodeValidator\Model\ResourceModel\Region\CollectionFactory;

class RegionListDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * Feedback collection
     *
     * @var \Webkul\MpZipCodeValidator\Model\ResourceModel\Region\Collection
     */
    protected $collection;

    /**
     * @var \Webkul\MpZipCodeValidator\Helper\Data
     */
    protected $helperData;

    /**
     * Construct
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param \Webkul\MpZipCodeValidator\Helper\Data $helperData
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        \Webkul\MpZipCodeValidator\Helper\Data $helperData,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $sellerId = $helperData->getCustomerId();
        $collectionData = $collectionFactory->create()
        ->addFieldToFilter(
            'seller_id',
            $sellerId
        );
        $this->collection = $collectionData;
    }
}
