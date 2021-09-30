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

use Webkul\MpZipCodeValidator\Model\ResourceModel\Zipcode\CollectionFactory;

class ZipCodeListDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * Feedback collection
     *
     * @var \Webkul\MpZipCodeValidator\Model\ResourceModel\Region\Collection
     */
    protected $collection;

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
        \Magento\Backend\Model\Session $session,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $regionId = $session->getRegionIds();
        $collectionData = $collectionFactory->create()
        ->addFieldToFilter(
            'region_id',
            $regionId
        );
        $this->collection = $collectionData;
    }
}
