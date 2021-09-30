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

namespace Webkul\MpZipCodeValidator\Block\Adminhtml\Region\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;
use Magento\Backend\Block\Widget\Tab\TabInterface;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Registry;
use Magento\Framework\Data\FormFactory;

class Info extends Generic implements TabInterface
{
    /**
     * Prepare form fields
     *
     * @return \Magento\Backend\Block\Widget\Form
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('mpzipcodevalidator');
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('region_');
        $form->setFieldNameSuffix('region');
        $fieldset = $form->addFieldset(
            'base_fieldset',
            ['legend' => __('Region Information')]
        );
        if ($model->getId()) {
            $fieldset->addField(
                'id',
                'hidden',
                ['name' => 'region_id']
            );
        }
        $fieldset->addField(
            'region_name',
            'text',
            [
                'name'     => 'region_name',
                'label'    => __('Region Name'),
                'title'    => __("Region Name"),
                'required'     => true
            ]
        );
        $fieldset->addField(
            'status',
            'select',
            [
                'name'      => 'status',
                'label'     => __('Status'),
                'title'     => __('Status'),
                'options'   => ['1' => __('Enabled'), '0' => __('Disabled')]
            ]
        );
        $fieldset->addField(
            'zipcodes-csv',
            'file',
            ['name' => 'zipcodes-csv', 'label' => __('CSV'), 'title' => __('CSV')]
        );
        $fieldset->addField(
            'product_id',
            'hidden',
            [
                'name' => 'product_id',
                'label' => __('Product Id'),
                'title' => __('Product Id')
            ]
        );
        $data = $model->getData();
        $form->setValues($data);
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Region Infomation');
    }
 
    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Region Infomation');
    }
 
    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return true;
    }
 
    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return false;
    }
}
