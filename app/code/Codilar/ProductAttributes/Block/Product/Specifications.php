<?php
/**
 * Specifications.php
 *
 * @package     Homelane
 * @description ProductAttributes Module to show custom attributes value in PDP tabs
 * @author      Manav Padhariya <manav.p@codilar.com>
 * @copyright   2021 Codilar Technologies Pvt. Ltd. . All rights reserved.
 * @license     Open Source
 * @see         https://www.codilar.com/
 *
 * ProductAttributes Module to show custom attributes value in PDP tabs
 */

namespace Codilar\ProductAttributes\Block\Product;

use Magento\Catalog\Model\Product;
use Magento\Framework\View\Element\Template;
use Magento\Catalog\Block\Product\Context;

/**
 * Class Specifications
 *
 * @package     Homelane
 * @description Block class for checking customer's session
 * @author      Manav Padhariya <manav.p@codilar.com>
 * @copyright   2021 Codilar Technologies Pvt. Ltd. . All rights reserved.
 * @license     Open Source
 * @see         https://www.codilar.com/
 *
 * Block class for getting custom attributes data
 */
class Specifications extends Template
{
    protected $coreRegistry = null;
    /**
     * @param Context $context
     * @param array $data
     */
    public function __construct(
       Context $context,
       array $data = []
   ) {
       $this->coreRegistry = $context->getRegistry();
       parent::__construct($context, $data);
   }

    /**
     * @return Product
     */
    public function getProduct()
    {
        if (!$this->hasData('product')) {
            $this->setData('product', $this->coreRegistry->registry('product'));
        }
        return $this->getData('product');
    }

    public function getTabsContent()
    {
        $data = [
            'brand' => $this->getProduct()->getBrand(),
            'height' => $this->getProduct()->getHeight(),
            'material' => $this->getProduct()->getMaterial()
        ];
        return $data;
    }

    /**
     * @return string
     */
    protected function _toHtml(): string
    {
        $tabContent = $this->getTabsContent();
        if (empty($this->getTabsContent()) && $tabContent['brand'] == null && $tabContent['height'] == null && $tabContent['material'] == null) {
            return false;
        }
        else {
            return parent::_toHtml();
        }
    }
}
