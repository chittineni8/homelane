<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Codilar\Priceversion\Model;

use Codilar\Priceversion\Api\Data\PriceversionInterface;
use Codilar\Priceversion\Api\Data\PriceversionInterfaceFactory;
use Codilar\Priceversion\Api\Data\PriceversionSearchResultsInterfaceFactory;
use Codilar\Priceversion\Api\PriceversionRepositoryInterface;
use Codilar\Priceversion\Model\ResourceModel\Priceversion as ResourcePriceversion;
use Codilar\Priceversion\Model\ResourceModel\Priceversion\CollectionFactory as PriceversionCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class PriceversionRepository implements PriceversionRepositoryInterface
{

    /**
     * @var PriceversionInterfaceFactory
     */
    protected $priceversionFactory;

    /**
     * @var ResourcePriceversion
     */
    protected $resource;

    /**
     * @var Priceversion
     */
    protected $searchResultsFactory;

    /**
     * @var PriceversionCollectionFactory
     */
    protected $priceversionCollectionFactory;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;


    /**
     * @param ResourcePriceversion $resource
     * @param PriceversionInterfaceFactory $priceversionFactory
     * @param PriceversionCollectionFactory $priceversionCollectionFactory
     * @param PriceversionSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourcePriceversion $resource,
        PriceversionInterfaceFactory $priceversionFactory,
        PriceversionCollectionFactory $priceversionCollectionFactory,
        PriceversionSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->priceversionFactory = $priceversionFactory;
        $this->priceversionCollectionFactory = $priceversionCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritDoc
     */
    public function save(PriceversionInterface $priceversion)
    {
        try {
            $this->resource->save($priceversion);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the priceversion: %1',
                $exception->getMessage()
            ));
        }
        return $priceversion;
    }

    /**
     * @inheritDoc
     */
    public function get($priceversionId)
    {
        $priceversion = $this->priceversionFactory->create();
        $this->resource->load($priceversion, $priceversionId);
        if (!$priceversion->getId()) {
            throw new NoSuchEntityException(__('Priceversion with id "%1" does not exist.', $priceversionId));
        }
        return $priceversion;
    }

    /**
     * @inheritDoc
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->priceversionCollectionFactory->create();

        $this->collectionProcessor->process($criteria, $collection);

        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);

        $items = [];
        foreach ($collection as $model) {
            $model['sub_cat'] = explode(',', $model['sub_cat']);
            $items[] = $model;
        }

        $searchResults->setItems($items);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function delete(PriceversionInterface $priceversion)
    {
        try {
            $priceversionModel = $this->priceversionFactory->create();
            $this->resource->load($priceversionModel, $priceversion->getPriceversionId());
            $this->resource->delete($priceversionModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Priceversion: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($priceversionId)
    {
        return $this->delete($this->get($priceversionId));
    }
}
