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

namespace Webkul\MpZipCodeValidator\Block\Adminhtml\Region\Edit;

use Magento\Backend\Block\Widget\Tabs as WidgetTabs;

class Tabs extends WidgetTabs
{
    /**
     * Class constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('region_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Region Information'));
    }

    /**
     * Before to html
     *
     * @return $this
     */
    protected function _beforeToHtml()
    {
        $this->addTab(
            'region_info',
            [
                'label' => __('Region'),
                'title' => __('Region'),
                'content' => $this->getLayout()->createBlock(
                    \Webkul\MpZipCodeValidator\Block\Adminhtml\Region\Edit\Tab\Info::class
                )->toHtml(),
                'active' => true
            ]
        );
        $this->addTab(
            'product_list',
            [
                'label' => __('Assign Product'),
                'title' => __('Assign Product'),
                'content' => $this->getLayout()->createBlock(
                    \Webkul\MpZipCodeValidator\Block\Adminhtml\Region\Edit\Tab\Products::class
                )->toHtml(),
                'active' => false
            ]
        );
        return parent::_beforeToHtml();
    }
}
