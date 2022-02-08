<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Codilar\Priceversion\Model;

use Codilar\Priceversion\Api\Data\PriceversiondetailsInterface;
use Codilar\Priceversion\Api\Data\PriceversiondetailsInterfaceFactory;
use Codilar\Priceversion\Api\Data\PriceversiondetailsSearchResultsInterfaceFactory;
use Codilar\Priceversion\Api\PriceversiondetailsRepositoryInterface;
use Codilar\Priceversion\Model\ResourceModel\Priceversiondetails as ResourcePriceversiondetails;
use Codilar\Priceversion\Model\ResourceModel\Priceversiondetails\CollectionFactory as PriceversiondetailsCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class PriceversiondetailsRepository implements PriceversiondetailsRepositoryInterface
{

    /**
     * @var Priceversiondetails
     */
    protected $searchResultsFactory;

    /**
     * @var ResourcePriceversiondetails
     */
    protected $resource;

    /**
     * @var PriceversiondetailsInterfaceFactory
     */
    protected $priceversiondetailsFactory;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var PriceversiondetailsCollectionFactory
     */
    protected $priceversiondetailsCollectionFactory;


    /**
     * @param ResourcePriceversiondetails $resource
     * @param PriceversiondetailsInterfaceFactory $priceversiondetailsFactory
     * @param PriceversiondetailsCollectionFactory $priceversiondetailsCollectionFactory
     * @param PriceversiondetailsSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourcePriceversiondetails $resource,
        PriceversiondetailsInterfaceFactory $priceversiondetailsFactory,
        PriceversiondetailsCollectionFactory $priceversiondetailsCollectionFactory,
        PriceversiondetailsSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->priceversiondetailsFactory = $priceversiondetailsFactory;
        $this->priceversiondetailsCollectionFactory = $priceversiondetailsCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritDoc
     */
    public function save(
        PriceversiondetailsInterface $priceversiondetails
    ) {
        try {
            $this->resource->save($priceversiondetails);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the priceversiondetails: %1',
                $exception->getMessage()
            ));
        }
        return $priceversiondetails;
    }

    /**
     * @inheritDoc
     */
    public function get($priceversiondetailsId)
    {
        $priceversiondetails = $this->priceversiondetailsFactory->create();
        $this->resource->load($priceversiondetails, $priceversiondetailsId);
        if (!$priceversiondetails->getId()) {
            throw new NoSuchEntityException(__('Priceversiondetails with id "%1" does not exist.', $priceversiondetailsId));
        }
        return $priceversiondetails;
    }

    /**
     * @inheritDoc
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->priceversiondetailsCollectionFactory->create();
        
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
    public function delete(
        PriceversiondetailsInterface $priceversiondetails
    ) {
        try {
            $priceversiondetailsModel = $this->priceversiondetailsFactory->create();
            $this->resource->load($priceversiondetailsModel, $priceversiondetails->getPriceversiondetailsId());
            $this->resource->delete($priceversiondetailsModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Priceversiondetails: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($priceversiondetailsId)
    {
        return $this->delete($this->get($priceversiondetailsId));
    }
}

