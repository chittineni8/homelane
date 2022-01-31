<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Codilar\Roomtype\Model;

use Codilar\Roomtype\Api\Data\RoomtypeInterface;
use Codilar\Roomtype\Api\Data\RoomtypeInterfaceFactory;
use Codilar\Roomtype\Api\Data\RoomtypeSearchResultsInterfaceFactory;
use Codilar\Roomtype\Api\RoomtypeRepositoryInterface;
use Codilar\Roomtype\Model\ResourceModel\Roomtype as ResourceRoomtype;
use Codilar\Roomtype\Model\ResourceModel\Roomtype\CollectionFactory as RoomtypeCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class RoomtypeRepository implements RoomtypeRepositoryInterface
{

    /**
     * @var RoomtypeInterfaceFactory
     */
    protected $roomtypeFactory;

    /**
     * @var ResourceRoomtype
     */
    protected $resource;

    /**
     * @var Roomtype
     */
    protected $searchResultsFactory;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var RoomtypeCollectionFactory
     */
    protected $roomtypeCollectionFactory;


    /**
     * @param ResourceRoomtype $resource
     * @param RoomtypeInterfaceFactory $roomtypeFactory
     * @param RoomtypeCollectionFactory $roomtypeCollectionFactory
     * @param RoomtypeSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourceRoomtype $resource,
        RoomtypeInterfaceFactory $roomtypeFactory,
        RoomtypeCollectionFactory $roomtypeCollectionFactory,
        RoomtypeSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->roomtypeFactory = $roomtypeFactory;
        $this->roomtypeCollectionFactory = $roomtypeCollectionFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @inheritDoc
     */
    public function save(RoomtypeInterface $roomtype)
    {
        try {
            $this->resource->save($roomtype);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__(
                'Could not save the roomtype: %1',
                $exception->getMessage()
            ));
        }
        return $roomtype;
    }

    /**
     * @inheritDoc
     */
    public function get($roomtypeId)
    {
        $roomtype = $this->roomtypeFactory->create();
        $this->resource->load($roomtype, $roomtypeId);
        if (!$roomtype->getId()) {
            throw new NoSuchEntityException(__('Roomtype with id "%1" does not exist.', $roomtypeId));
        }
        return $roomtype;
    }

    /**
     * @inheritDoc
     */
    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $criteria
    ) {
        $collection = $this->roomtypeCollectionFactory->create();
        
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
    public function delete(RoomtypeInterface $roomtype)
    {
        try {
            $roomtypeModel = $this->roomtypeFactory->create();
            $this->resource->load($roomtypeModel, $roomtype->getRoomtypeId());
            $this->resource->delete($roomtypeModel);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__(
                'Could not delete the Roomtype: %1',
                $exception->getMessage()
            ));
        }
        return true;
    }

    /**
     * @inheritDoc
     */
    public function deleteById($roomtypeId)
    {
        return $this->delete($this->get($roomtypeId));
    }
}

