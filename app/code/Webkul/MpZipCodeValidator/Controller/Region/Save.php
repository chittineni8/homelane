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
namespace Webkul\MpZipCodeValidator\Controller\Region;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Filesystem\Driver\File;

class Save extends Action
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var time()
     */
    protected $_time;

    /**
     * @var \Magento\MediaStorage\Model\File\UploaderFactory
     */
    protected $_fileUploader;

    /**
     * @var \Webkul\MpZipCodeValidator\Model\ResourceModel\Region\CollectionFactory
     */
    protected $_region;

    /**
     * @var \Webkul\MpZipCodeValidator\Model\Zipcode $zipcode
     */
    protected $_zipcode;

    /**
     * Filesystem driver to allow reading of module.xml files which live outside of app/code
     *
     * @var DriverInterface
     */
    private $filesystemDriver;

    /**
     * @var \Webkul\Marketplace\Helper\Data $helper
     */
    protected $helper;

    /**
     * @var \Magento\Customer\Model\Url $url
     */
    protected $url;

    /**
     * @var \Webkul\MpZipCodeValidator\Helper\Data
     */
    protected $zipHelper;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    protected $date;

    protected $_csvReader;

    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory
     * @param \Webkul\MpZipCodeValidator\Model\RegionFactory $region
     * @param \Webkul\MpZipCodeValidator\Model\ZipcodeFactory $zipcode
     * @param \Webkul\Marketplace\Helper\Data $helper
     * @param \Magento\Customer\Model\Url $url
     * @param File $filesystemDriver
     * @param \Webkul\MpZipCodeValidator\Helper\Data $zipHelper
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $date
     */
    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Webkul\MpZipCodeValidator\Model\RegionFactory $region,
        \Webkul\MpZipCodeValidator\Model\ZipcodeFactory $zipcode,
        \Webkul\Marketplace\Helper\Data $helper,
        \Magento\Customer\Model\Url $url,
        File $filesystemDriver,
        \Webkul\MpZipCodeValidator\Helper\Data $zipHelper,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\File\Csv $csvReader
    ) {
        $this->_customerSession = $customerSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->_fileUploader = $fileUploaderFactory;
        $this->_time = time();
        $this->_region = $region;
        $this->_zipcode = $zipcode;
        $this->helper = $helper;
        $this->url = $url;
        $this->filesystemDriver = $filesystemDriver;
        $this->zipHelper = $zipHelper;
        $this->date = $date;
        $this->_csvReader = $csvReader;
        parent::__construct($context);
    }

    /**
     * Check customer authentication
     *
     * @param RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(RequestInterface $request)
    {
        $loginUrl = $this->url->getLoginUrl();

        if (!$this->_customerSession->authenticate($loginUrl)) {
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
        }
        return parent::dispatch($request);
    }

    /**
     * Add Store Page
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $msg = [];
        $isPartner = $this->helper->isSeller();
        if ($isPartner == 1) {
            $data = $this->getRequest()->getParams();
            if (isset($data['region_name'])) {
                $data['region_name'] = strip_tags($data['region_name']);
                $sellerId = $this->_customerSession->getCustomer()->getId();
                if (isset($data['region_id'])) {
                    $this->updateRegion();
                } else {
                    $regionId = 0;
                    $collection = $this->_region
                        ->create()
                        ->getCollection()
                        ->addFieldToFilter('seller_id', $sellerId);
                    foreach ($collection as $key => $value) {
                        if (strcasecmp($data['region_name'], $value->getRegionName()) == 0) {
                            $regionId = $value->getId();
                        }
                    }
                    if (!$regionId) {
                        $data['seller_id'] = $sellerId;
                        $data['created_at'] = $this->date->gmtDate();
                        $data['updated_at'] = $this->date->gmtDate();
                        $region = $this->_region->create()->setData($data)->save();
                        $regionId = $region->getId();
                        $this->messageManager->addSuccess(__('Region Added Successfully'));
                    }
                    $this->processCsvData($regionId);
                }
            }
            return $this->resultRedirectFactory->create()->setPath('*/*/view');
        } else {
            return $this->resultRedirectFactory->create()->setPath(
                'marketplace/account/becomeseller',
                ['_secure' => $this->getRequest()->isSecure()]
            );
        }
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
            ->load($data['region_id'])
            ->setRegionName($data['region_name'])
            ->setStatus($data['status'])
            ->setUpdatedAt($this->date->gmtDate())
            ->save();
        $this->messageManager->addSuccess(__('Region Updated Successfully'));
        if (!empty($files['zipcodes-csv']['name'])
            && !empty($files['zipcodes-csv']['tmp_name'])
        ) {
            $this->processCsvData($data['region_id']);
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
        $msg = [];
        $data = $this->getRequest()->getParams();
        try {
            $csvUploader = $this->_fileUploader->create(['fileId' => 'zipcodes-csv']);
            $csvUploader->setAllowedExtensions(['csv']);
            $validateData = $csvUploader->validateFile();
            $csvFilePath = $validateData['tmp_name'];
            $csvFile = $validateData['name'];
            $csvExt = explode('.', $csvFile);
            $ext = end($csvExt);
            $ext = strtolower($ext);
            $status = true;
            $headerArray = [
                "zip_from",
                "zip_to"
            ];
            if ($csvFilePath != '' && $ext == 'csv') {
                $csvFileData = $this->_csvReader->getData($csvFilePath);
                $count = 0;
                if (!empty($csvFileData) && count($csvFileData) > 1) {
                    $msg = $this->importFileCsv($csvFileData, $count, $headerArray, $regionId);
                } elseif (!isset($data['region_id'])) {
                    $check = $this->checkZipcode($data['region_name'], $data['region_name'], $regionId);
                    if (!$check) {
                        $this->_zipcode->create()
                            ->setRegionZipcode($data['region_name'])
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
            if (!isset($data['region_id'])) {
                $this->zipHelper->logDataInLogger(
                    "Controller_Region_Save processCsvData : ".$e->getMessage()
                );
                $this->messageManager->addError(__($e->getMessage()));
            } else {
                $this->zipHelper->logDataInLogger(
                    "Controller_Region_Save processCsvData : ".$e->getMessage()
                );
                $this->messageManager->addWarning(__("No csv file has been updated"));
            }
        }
    }

    /**
     * Csv Header, format check and save
     *
     * @param array $csvFileData
     * @param int $count
     * @param array $headerArray
     * @param int $regionId
     * @return array
     */
    public function importFileCsv($csvFileData, $count, $headerArray, $regionId)
    {
        foreach ($csvFileData as $key => $rowData) {
            if ($count==0) {
                if (!empty($rowData) && count($rowData) < 2) {
                    $this->messageManager->addError(__('CSV file is not a valid file!'));
                    return $this->resultRedirectFactory->create()->setPath('*/*/view');
                } else {
                    $data = $rowData;
                    $status = (empty(array_diff($headerArray, $rowData)) && count($headerArray) == count($rowData));
                    if (!$status) {
                        $this->messageManager->addError(__('Please write the correct header formation of CSV file!'));
                        return $this->resultRedirectFactory->create()->setPath('*/*/view');
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
                    $msg = $this->saveCsvData($updatedWholedata, $regionId, $key);
                } else {
                    $rows[] = $key.': '.$errors[0];
                }
            }
        }
        return $msg;
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
     * @return array
     */
    public function saveCsvData($csvData, $regionId)
    {
        $msg = [];
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
                    $msg[$key] = __(
                        'Skipped row %1. As zipcode from %2 to %3  already exists.',
                        $row,
                        $zipcodeFrom,
                        $zipcodeTo
                    );
                    $count--;
                } elseif ($zipcodeFrom && $zipcodeTo) {
                    $zipData['region_id'] = $regionId;
                    $zipData['region_zipcode_from'] = $zipcodeFrom;
                    $zipData['region_zipcode_to'] = $zipcodeTo;
                    $zipData['created_at'] = $this->date->gmtDate();
                    $zipData['updated_at'] = $this->date->gmtDate();
                    $this->saveZipcode($zipData);
                }
            }
        }
        return $msg;
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
     * Save ZipCode
     *
     * @param object $data
     * @return void
     */
    public function saveZipcode($data)
    {
        $this->_zipcode->create()->setData($data)->save();
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
}
