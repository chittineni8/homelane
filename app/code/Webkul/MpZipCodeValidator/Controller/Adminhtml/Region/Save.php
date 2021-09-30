<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_MpZipCodeValidator
 * @author    Webkul
 * @copyright Copyright (c)  Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\MpZipCodeValidator\Controller\Adminhtml\Region;

use Magento\Backend\App\Action;

class Save extends Action
{
    const SELECT_SPECIFIC = 0;
    const APPLY_DEFAULT_CONFIGURATON = 2;
    /**
     * @var \Webkul\MpZipCodeValidator\Model\RegionFactory
     */
    protected $_region;

    /**
     * @var \Webkul\MpZipCodeValidator\Model\ZipcodeFactory
     */
    protected $_zipcode;

    /**
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $_fileUploader;

    /**
     * @var \Magento\Catalog\Model\ProductFactory $productFactory
     */
    protected $_productFactory;

    /**
     * @var \Webkul\MpZipCodeValidator\Helper\Data
     */
    protected $zipHelper;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    /**
     * @var \Magento\Framework\File\Csv
     */
    protected $csvReader;

    /**
     * @param Action\Context $context
     * @param \Webkul\MpZipCodeValidator\Model\RegionFactory $region
     * @param \Webkul\MpZipCodeValidator\Model\ZipcodeFactory $zipcode
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Webkul\MpZipCodeValidator\Helper\Data $zipHelper
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     * @param \Magento\Framework\File\Csv $csvReader
     */
    public function __construct(
        Action\Context $context,
        \Webkul\MpZipCodeValidator\Model\RegionFactory $region,
        \Webkul\MpZipCodeValidator\Model\ZipcodeFactory $zipcode,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Webkul\MpZipCodeValidator\Helper\Data $zipHelper,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\File\Csv $csvReader
    ) {
        $this->_region = $region;
        $this->_zipcode = $zipcode;
        $this->_fileUploader = $fileUploaderFactory;
        $this->_productFactory = $productFactory;
        $this->zipHelper = $zipHelper;
        $this->date = $date;
        $this->_csvReader = $csvReader;
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_MpZipCodeValidator::region');
    }

    /**
     * Save action.
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getParams();
        $sellerId = 0;
        try {
            if (isset($data['region']['region_id'])) {
                $this->updateRegion();
            } else {
                $regionId = 0;
                $collection = $this->_region
                    ->create()
                    ->getCollection()
                    ->addFieldToFilter('seller_id', 0);
                foreach ($collection as $key => $value) {
                    if (strcasecmp($data['region']['region_name'], $value->getRegionName()) == 0) {
                        $regionId = $value->getId();
                    }
                }
                if (!$regionId) {
                    $data['region_name'] = $data['region']['region_name'];
                    $data['status'] = $data['region']['status'];
                    $data['seller_id'] = $sellerId;
                    $data['created_at'] = $this->date->gmtDate();
                    $data['updated_at'] = $this->date->gmtDate();
                    $region = $this->_region->create()->setData($data)->save();
                    $regionId = $region->getId();
                    $productIds = explode(',', $data['product_ids']);
                    $this->regionAssignProducts($productIds, $regionId);
                    $this->messageManager->addSuccess(__('Region Added Successfully'));
                }
                $this->processCsvData($regionId);
            }
        } catch (\Exception $e) {
            $this->zipHelper->logDataInLogger(
                "Controller_Admin_Region_Save execute : ".$e->getMessage()
            );
        }
        return $this->resultRedirectFactory->create()->setPath('*/*/index');
    }

    /**
     * Update Region Details
     *
     * @return void
     */
    public function updateRegion()
    {
        $regionId = 0;
        $data = $this->getRequest()->getParams();
        $files = $this->getRequest()->getFiles();
        $this->_region->create()
            ->load($data['region']['region_id'])
            ->setRegionName($data['region']['region_name'])
            ->setStatus($data['region']['status'])
            ->setUpdatedAt($this->date->gmtDate())
            ->save();
        $productIds = explode(',', $data['product_ids']);
        $this->regionAssignProducts($productIds, $data['region']['region_id']);
        $this->messageManager->addSuccess(__('Region Updated Successfully'));
        if (!empty($files['region']['zipcodes-csv']['name'])
            && !empty($files['region']['zipcodes-csv']['tmp_name'])
        ) {
            $this->processCsvData($data['region']['region_id']);
        }
    }

    /**
     * Process csv data
     *
     * @param integer $regionId
     * @return void
     */
    public function processCsvData($regionId)
    {
        $data = $this->getRequest()->getParams();
        try {
            $csvUploader = $this->_fileUploader->create(['fileId' => 'region[zipcodes-csv]']);
            $csvUploader->setAllowedExtensions(['csv']);
            $validateData = $csvUploader->validateFile();
            $file = $validateData['tmp_name'];
            $csvFile = explode('.', $validateData['name']);
            $ext = end($csvFile);
            $ext = strtolower($ext);
            $status = true;
            $headerArray = [
                "zip_from",
                "zip_to"
            ];
            if ($file != '' && $ext == 'csv') {
                $csvFileData = $this->_csvReader->getData($file);
                $count = 0;
                if (!empty($csvFileData) && count($csvFileData) > 1) {
                    $this->importFileCsv($csvFileData, $count, $headerArray, $regionId);
                } elseif (!isset($data['region']['region_id'])) {
                    $check = $this->checkZipcode(
                        $data['region']['region_name'],
                        $data['region']['region_name'],
                        $regionId
                    );
                    if (!$check) {
                        $this->_zipcode->create()
                            ->setRegionZipcode($data['region']['region_name'])
                            ->setRegionId($regionId)
                            ->setCreatedAt($this->date->gmtDate())
                            ->setUpdatedAt($this->date->gmtDate())
                            ->save();
                    }
                }
                if (!empty($rows)) {
                    $this->messageManager->addError(
                        __('Following rows are not valid rows : %1. ', implode(', ', $rows))
                    );
                }
            } else {
                $this->messageManager->addError(__('Please upload CSV file'));
            }
        } catch (\Exception $e) {
            $this->zipHelper->logDataInLogger(
                "Controller_Admin_Region_Save processCsvData : ".$e->getMessage()
            );
            $this->messageManager->addError(__($e->getMessage()));
        }
    }

    /**
     * Csv Header, format check and save
     *
     * @param array $csvFileData
     * @param int $count
     * @param array $headerArray
     * @param int $regionId
     */
    public function importFileCsv($csvFileData, $count, $headerArray, $regionId)
    {
        foreach ($csvFileData as $key => $rowData) {
            if ($count==0) {
                if (!empty($rowData) && count($rowData) < 2) {
                    $this->messageManager->addError(__('CSV file is not a valid file!'));
                    return $this->resultRedirectFactory->create()->setPath('*/*/index');
                } else {
                    $data = $rowData;
                    $status = (empty(array_diff($headerArray, $rowData)) && count($headerArray) == count($rowData));
                    if (!$status) {
                        $this->messageManager->addError(__('Please write the correct header formation of CSV file!'));
                        return $this->resultRedirectFactory->create()->setPath('*/*/index');
                    }
                    ++$count;
                }
            } else {
                $wholedata = [];
                foreach ($rowData as $filekey => $filevalue) {
                    $wholedata[$data[$filekey]] = $filevalue;
                }
                list($updatedWholedata, $errors) = $this->validateCsvDataToSave($wholedata);
                if (empty($errors)) {
                    $this->saveCsvData($updatedWholedata, $regionId, $key);
                } else {
                    $rows[] = $key.': '.$errors[0];
                }
            }
        }
    }

    /**
     * Validate saved data from CSV data
     *
     * @param array $wholedata
     * @return array
     */
    public function validateCsvDataToSave($wholedata)
    {
        $data = [];
        $errors = [];
        if (count($wholedata) < 2) {
            if (empty($wholedata['zip_from'])) {
                $errors[] = __('zip_from field can not be empty');
            } elseif (empty($wholedata['zip_to'])) {
                $errors[] = __('zip_to field can not be empty');
            } else {
                $errors[] = __('invalid format');
            }
        } else {
            foreach ($wholedata as $key => $value) {
                switch ($key) {
                    case 'zip_from':
                        if ($value == '') {
                            $errors[] = __('zip_from field can not be empty');
                        } else {
                            $data[$key] = $value;
                        }
                        break;
                    case 'zip_to':
                        if ($value == '') {
                            $errors[] = __('zip_to field can not be empty');
                        } elseif (isset($data['zip_from']) && $data['zip_from'] > $value) {
                            $errors[] = __('zip_to field should be greater then zip_from field');
                        } else {
                            $data[$key] = $value;
                        }
                        break;
                }
            }
        }
        return [$data, $errors];
    }

    /**
     * Save csv zipcodes
     *
     * @param array $csvData
     * @param integer $regionId
     * @param integer $row
     * @return void
     */
    public function saveCsvData($csvData, $regionId, $row)
    {
        $count = 1;
        $serialNo = $this->getSerialNumber($regionId);
        if ($serialNo && $serialNo > 0) {
            $count = $serialNo;
        }
        if (!empty($csvData) && !empty($csvData['zip_from']) && !empty($csvData['zip_to'])) {
            $zipcodeFrom = $csvData['zip_from'];
            $zipcodeTo = $csvData['zip_to'];
            if (!preg_match('/[^a-z_\-0-9\s]/i', $zipcodeFrom) && !preg_match('/[^a-z_\-0-9\s]/i', $zipcodeTo)) {
                $check = $this->checkZipcode($zipcodeFrom, $zipcodeTo, $regionId);
                if ($check) {
                    $this->messageManager->addError(
                        __(
                            'Skipped row %1. As zipcode from %2 to %3  already exists.',
                            $row,
                            $zipcodeFrom,
                            $zipcodeTo
                        )
                    );
                    $count--;
                } elseif ($zipcodeFrom && $zipcodeTo) {
                    $zipData['region_id'] = $regionId;
                    $zipData['region_zipcode_from'] = $zipcodeFrom;
                    $zipData['region_zipcode_to'] = $zipcodeTo;
                    $zipData['created_at'] = $this->date->gmtDate();
                    $zipData['updated_at'] = $this->date->gmtDate();
                    $zipData['serial_no'] = $count++;
                    $this->saveZipcode($zipData);
                } else {
                    $this->messageManager->addError(
                        __('Skipped row %1.', $row)
                    );
                }
            }
        }
    }
    
    /**
     * Check zipcode already saved or not
     *
     * @param string $zipcodeFrom
     * @param string $zipcodeTo
     * @param integer $regionId
     * @return integer
     */
    public function checkZipcode($zipcodeFrom, $zipcodeTo, $regionId)
    {
        $collection = $this->_zipcode->create()
            ->getCollection()
            ->AddFieldToFilter('region_id', $regionId)
            ->AddFieldToFilter('region_zipcode_from', $zipcodeFrom)
            ->AddFieldToFilter('region_zipcode_to', $zipcodeTo);
        return count($collection);
    }

    /**
     * Save zipcode data
     *
     * @param  object $data
     * @return void
     */
    public function saveZipcode($data)
    {
        $this->_zipcode->create()->setData($data)->save();
    }

    /**
     * Get Serial Number
     *
     * @param integer $regionId
     * @return integer
     */
    public function getSerialNumber($regionId)
    {
        $collection = $this->_zipcode->create()
        ->getCollection()
        ->addFieldToFilter('region_id', $regionId)
        ->getLastItem();
        if ($collection->getSerialNo()) {
            return $collection->getSerialNo()+1;
        }
    }

    /**
     * Region assign of products
     *
     * @param string $productId
     * @param string $regionId
     * @return void
     */
    public function regionAssignProducts($productIds, $regionId)
    {
        $this->removeOldProductRegion($productIds, $regionId);
        $collection = $this->getProductCollection($productIds);
        foreach ($collection as $model) {
            $productRegionId = $model->getAvailableRegion();
            $productRegionIds = explode(',', $productRegionId);
            if (!in_array($regionId, $productRegionIds)) {
                $currentRegionId = [];
                $currentRegionId[] = $regionId;
                $regionIds = array_merge_recursive(
                    $productRegionIds,
                    $currentRegionId
                );
                $productId = $model->getId();
                $this->removeAvailableRegionProducts($productId);
                $model->setAvailableRegion($regionIds)
                ->setZipCodeValidation(self::SELECT_SPECIFIC)->save();
            }
        }
    }

    /**
     * Remove region id for old products
     *
     * @param array $productIds
     * @param string $regionId
     * @return void
     */
    public function removeOldProductRegion($productIds, $regionId)
    {
        try {
            $oldProductIds = $this->getRequest()->getParam('old_product_ids');
            $oldProductIds = explode(',', $oldProductIds);
            $oldProductIds = array_diff($oldProductIds, $productIds);
            $collection = $this->getProductCollection($oldProductIds);
            foreach ($collection as $model) {
                $productRegionId = $model->getAvailableRegion();
                $productRegionIds = explode(',', $productRegionId);
                if (in_array($regionId, $productRegionIds)) {
                    $currentRegionId = [];
                    $currentRegionId[] = $regionId;
                    $regionIds = array_diff($productRegionIds, $currentRegionId);
                    $productId = $model->getId();
                    $this->removeAvailableRegionProducts($productId);
                    $model->setAvailableRegion($regionIds)->save();
                }
            }
        } catch (\Exception $e) {
            $this->zipHelper->logDataInLogger(
                "Controller_Admin_Region_Save removeOldProductRegion : ".$e->getMessage()
            );
        }
    }

    /**
     * Remove available region from products
     *
     * @param string $productId
     * @return void
     */
    public function removeAvailableRegionProducts($productId)
    {
        $product = $this->_productFactory->create()
            ->load($productId)
            ->setZipCodeValidation(self::APPLY_DEFAULT_CONFIGURATON)
            ->setAvailableRegion(null)
            ->save();
    }

    /**
     * Get Product Collection by Ids
     *
     * @param array $productIds
     * @return array $collection
     */
    public function getProductCollection($productIds)
    {
        $collection = $this->_productFactory->create()->getCollection();
        $collection->addFieldToSelect('available_region')
            ->addFieldToFilter('entity_id', ['in' => $productIds]);
        return $collection;
    }
}
