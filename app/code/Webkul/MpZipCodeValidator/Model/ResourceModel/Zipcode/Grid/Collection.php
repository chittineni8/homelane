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
namespace Webkul\MpZipCodeValidator\Model\ResourceModel\Zipcode\Grid;

use Magento\Framework\Api\Search\SearchResultInterface as SearchInterface;
use Magento\Framework\Search\AggregationInterface;
use Webkul\MpZipCodeValidator\Model\ResourceModel\Zipcode\Collection as ZipcodeCollection;

class Collection extends ZipcodeCollection implements SearchInterface
{
    /**
     * @var AggregationInterface
     */
    protected $_aggregations;

    /**
     * @var Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Backend\Model\Session $session
     */
    protected $session;

    /**
     * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entity
     * @param \Magento\Backend\Model\Session $session
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetch
     * @param \Magento\Framework\Event\ManagerInterface $event
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param mixed|null $mainTable
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb $eventPrefix
     * @param mixed $eventObject
     * @param mixed $resourceModel
     * @param string $model
     * @param null $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb|null $resource
     */
    public function __construct(
        \Magento\Framework\Data\Collection\EntityFactoryInterface $entity,
        \Magento\Backend\Model\Session $session,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetch,
        \Magento\Framework\Event\ManagerInterface $event,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\RequestInterface $request,
        $mainTable,
        $eventPrefix,
        $eventObject,
        $resourceModel,
        $model = \Magento\Framework\View\Element\UiComponent\DataProvider\Document::class,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) {
        parent::__construct(
            $entity,
            $logger,
            $fetch,
            $event,
            $storeManager,
            $connection,
            $resource
        );
        $this->_storeManager = $storeManager;
        $this->session = $session;
        $this->_request = $request;
        $this->_eventPrefix = $eventPrefix;
        $this->_eventObject = $eventObject;
        $this->_init($model, $resourceModel);
        $this->setMainTable($mainTable);
    }

    /**
     * Get Aggregations
     *
     * @return AggregationInterface
     */
    public function getAggregations()
    {
        return $this->_aggregations;
    }

    /**
     * Set Aggregations
     *
     * @param AggregationInterface $aggregations
     * @return $this
     */
    public function setAggregations($aggregations)
    {
        $this->_aggregations = $aggregations;
    }
    
    /**
     * Retrieve all ids for collection
     * Backward compatibility with EAV collection
     *
     * @param int $limitCount
     * @param int $offset
     * @return array
     */
    public function getAllIds($limitCount = null, $offset = null)
    {
        return $this->getConnection()->fetchCol($this->_getAllIdsSelect($limitCount, $offset), $this->_bindParams);
    }

    /**
     * Get search criteria.
     *
     * @return \Magento\Framework\Api\SearchCriteriaInterface|null
     */
    public function getSearchCriteria()
    {
        return null;
    }

    /**
     * Set search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return $this
     */
    public function setSearchCriteria(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria = null)
    {
        return $this;
    }

    /**
     * Get total count.
     *
     * @return int
     */
    public function getTotalCount()
    {
        return $this->getSize();
    }

    /**
     * Set total count.
     *
     * @param int $totalCount
     * @return $this
     */
    public function setTotalCount($totalCount)
    {
        return $this;
    }

    /**
     * Set items list.
     *
     * @param \Magento\Framework\Api\ExtensibleDataInterface[] $items
     * @return $this
     */
    public function setItems(array $items = null)
    {
        return $this;
    }

    /**
     * Join store relation table if there is store filter
     *
     * @return void
     */
    protected function _renderFiltersBefore()
    {
        $regionId = $this->session->getRegionId();
        if ($regionId != 0) {
            $this->getSelect(
                [
                    'id' => 'id',
                    'region_zipcode' => 'region_zipcode'
                ]
            )->where("main_table.region_id = ".$regionId);
            parent::_renderFiltersBefore();
        }
    }
}
