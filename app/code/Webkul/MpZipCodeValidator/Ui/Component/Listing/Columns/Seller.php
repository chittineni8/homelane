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

namespace Webkul\MpZipCodeValidator\Ui\Component\Listing\Columns;

class Seller extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var \Magento\Customer\Model\ResourceModel\Grid\CollectionFactory
     */
    protected $customerFactory;

    /**
     * @param \Magento\Framework\View\Element\UiComponent\ContextInterface $context
     * @param \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory
     * @param\Magento\Customer\Model\ResourceModel\Grid\CollectionFactory $customerFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        \Magento\Customer\Model\ResourceModel\Grid\CollectionFactory $customerFactory,
        array $components = [],
        array $data = []
    ) {
        $this->customerFactory = $customerFactory;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        $collection = $this->customerFactory->create();
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $sellerId = $item['seller_id'];
                if ($sellerId == 0) {
                    $item['name'] = __('Admin');
                }
            }
        }
        return $dataSource;
    }
}
