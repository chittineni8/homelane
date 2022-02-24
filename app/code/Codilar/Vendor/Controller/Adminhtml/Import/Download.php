<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Codilar\Vendor\Controller\Adminhtml\Import;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\ImportExport\Controller\Adminhtml\Import as ImportController;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\ImportExport\Controller\Adminhtml\Import\Download as Mdownload;

class Download extends Mdownload
{
    protected $moduleReader;
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\Filesystem\Directory\ReadFactory $readFactory,
        \Magento\Framework\Component\ComponentRegistrar $componentRegistrar,
        \Magento\Framework\Module\Dir\Reader $moduleReader
    ) {
        parent::__construct(
            $context,
            $fileFactory,
            $resultRawFactory,
            $readFactory,
            $componentRegistrar

        );
        $this->moduleReader = $moduleReader;
    }
    public function getDirectory()
    {
        $viewDir = $this->moduleReader->getModuleDir(
            \Magento\Framework\Module\Dir::MODULE_VIEW_DIR,
            'Codilar_Vendor'
        );
        return $viewDir . '/adminhtml/Files/Sample/';
    }

    /**
     * Download sample file action
     *
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
        $fileName = 'codilar_vendor_vendor.csv';
        $moduleDir = $this->componentRegistrar->getPath(ComponentRegistrar::MODULE, self::SAMPLE_FILES_MODULE);
        $fileAbsolutePath = $moduleDir . '/Files/Sample/' . $fileName;
        $directoryRead = $this->readFactory->create($moduleDir);
        $filePath = $directoryRead->getRelativePath($fileAbsolutePath);

        if (!$directoryRead->isFile($filePath)) {

            $fileAbsolutePath = $this->getDirectory() . $fileName;
            $directoryRead = $this->readFactory->create($this->getDirectory());
            $filePath = $directoryRead->getRelativePath($fileAbsolutePath);
            if (!$directoryRead->isFile($filePath)) {
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $this->messageManager->addError(__('There is no sample file for this entity.'));
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('*/import');
                return $resultRedirect;
            }
        }

        $fileSize = isset($directoryRead->stat($filePath)['size'])
            ? $directoryRead->stat($filePath)['size'] : null;

        $this->fileFactory->create(
            $fileName,
            null,
            DirectoryList::VAR_DIR,
            'application/octet-stream',
            $fileSize
        );
        /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
        $resultRaw = $this->resultRawFactory->create();
        $resultRaw->setContents($directoryRead->readFile($filePath));
        return $resultRaw;
    }
}
