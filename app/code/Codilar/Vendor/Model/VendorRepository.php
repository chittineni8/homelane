<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Codilar\Vendor\Model;

use Codilar\Vendor\Api\Data\VendorInterface;
use Codilar\Vendor\Api\Data\VendorInterfaceFactory;
use Codilar\Vendor\Api\Data\VendorSearchResultsInterfaceFactory;
use Codilar\Vendor\Api\VendorRepositoryInterface;
use Codilar\Vendor\Model\ResourceModel\Vendor as ResourceVendor;
use Codilar\Vendor\Model\ResourceModel\Vendor\CollectionFactory as VendorCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class VendorRepository implements VendorRepositoryInterface
{

    /**
     * @var Vendor
     */
    protected $searchResultsFactory;

    /**
     * @var VendorCollectionFactory
     */
    protected $vendorCollectionFactory;

    /**
     * @var ResourceVendor
     */
    protected $resource;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var VendorInterfaceFactory
     */
    protected $vendorFactory;


    /**
     * @param ResourceVendor $resource
     * @param VendorInterfaceFactory $vendorFactory
     * @param VendorCollectionFactory $vendorCollectionFactory
     * @param VendorSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourceVendor $resource,
        VendorInterfaceFactory $vendorFactory,
        VendorCollectionFactory $vendorCollectionFactory,
        VendorSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->vendorFactory = $vendorFactory;
        $this->vendorCollectionFactory = $vendorCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritDoc
     */
    public function save(VendorInterface $vendor)
    {
        try {
            $this->resource->save($vendor);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the vendor: %1',
                $exception->getMessage()
            ));
        }
        return $vendor;
    }

    /**
     * @inheritDoc
     */
    public function get($vendorId)
    {
        $vendor = $this->vendorFactory->create();
        $this->resource->load($vendor, $vendorId);
        if (!$vendor->getId()) {
            throw new NoSuchEntityException(__('Vendor with id "%1" does not exist.', $vendorId));
        }
        return $vendor;
    }

    /**
     * @inheritDoc
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->vendorCollectionFactory->create();
        
        $this->collectionProcessor->process($criteria, $collection);
        
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        
        $items = [];
        foreach ($collection as $model) {
            $items[] = $model;
        }
        
        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function delete(VendorInterface $vendor)
    {
        try {
            $vendorModel = $this->vendorFactory->create();
            $this->resource->load($vendorModel, $vendor->getVendorId());
            $this->resource->delete($vendorModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Vendor: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($vendorId)
    {
        return $this->delete($this->get($vendorId));
    }
}

