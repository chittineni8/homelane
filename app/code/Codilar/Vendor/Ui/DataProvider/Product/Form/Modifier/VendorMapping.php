<?php
namespace Codilar\Vendor\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Catalog\Controller\Adminhtml\Product\Initialization\StockDataFilter;
use Magento\Catalog\Model\Locator\LocatorInterface;

use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\CatalogInventory\Api\Data\StockItemInterface;
use Magento\CatalogInventory\Api\StockConfigurationInterface;

use Magento\Ui\Component\Container;
use Magento\Ui\Component\Form\Element\DataType\Number;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\DataType\Boolean;
use Magento\Ui\Component\Form\Element\Checkbox;
use Magento\Ui\Component\Form\Element\Textarea;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Element\Select;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Modal;

/**
 * Data provider for attraction highlights field
 */
class VendorMapping extends AbstractModifier
{
	const ATTRACTION_HIGHLIGHTS_FIELD = 'vendor_mapping';

	 /**
		* @var LocatorInterface
		*/
	 private $locator;

	 /**
		* @var ArrayManager
		*/
	 private $arrayManager;

	 /**
		* @var array
		*/
	 private $meta = [];

	 /**
		* @var string
		*/
	 protected $scopeName;
	 protected $_automat;
	 protected $_sap;
	 protected $website;


	 /**
		* @param LocatorInterface $locator
		* @param ArrayManager $arrayManager
		*/
	 public function __construct(
			 LocatorInterface $locator,
			 ArrayManager $arrayManager,
			 \Codilar\Vendor\Model\Source\AutomatUom $automat,
			 \Codilar\Vendor\Model\Source\SapUom $sap,
			 \Codilar\Priceversion\Model\Priceversion\Website $_website,
			 $scopeName = ''
	 ) {
			 $this->locator = $locator;
			 $this->arrayManager = $arrayManager;
			 $this->scopeName = $scopeName;
			 $this->_automat = $automat;
			 $this->_sap = $sap;
			 $this->website = $_website;
	 }

	 /**
		* {@inheritdoc}
		*/
	 public function modifyData(array $data)
	 {
			 $fieldCode = self::ATTRACTION_HIGHLIGHTS_FIELD;

			 $model = $this->locator->getProduct();
			 $modelId = $model->getId();

			 $highlightsData = $model->getVendorMapping();

			 if ($highlightsData) {
					 $highlightsData = json_decode($highlightsData, true);
					 $path = $modelId . '/' . self::DATA_SOURCE_DEFAULT . '/'. self::ATTRACTION_HIGHLIGHTS_FIELD;
					 $data = $this->arrayManager->set($path, $data, $highlightsData);
			 }
			 return $data;
	 }

	 /**
		* {@inheritdoc}
		*/
	 public function modifyMeta(array $meta)
	 {
			 $this->meta = $meta;
			 $this->initAttractionHighlightFields();
			 return $this->meta;
	 }

	 /**
		* Customize attraction highlights field
		*
		* @return $this
		*/
	 protected function initAttractionHighlightFields()
	 {
			 $highlightsPath = $this->arrayManager->findPath(
					 self::ATTRACTION_HIGHLIGHTS_FIELD,
					 $this->meta,
					 null,
					 'children'
			 );

			 if ($highlightsPath) {
					 $this->meta = $this->arrayManager->merge(
							 $highlightsPath,
							 $this->meta,
							 $this->initHighlightFieldStructure($highlightsPath)
					 );
					 $this->meta = $this->arrayManager->set(
							 $this->arrayManager->slicePath($highlightsPath, 0, -3)
							 . '/' . self::ATTRACTION_HIGHLIGHTS_FIELD,
							 $this->meta,
							 $this->arrayManager->get($highlightsPath, $this->meta)
					 );
					 $this->meta = $this->arrayManager->remove(
							 $this->arrayManager->slicePath($highlightsPath, 0, -2),
							 $this->meta
					 );
			 }

			 return $this;
	 }


	 /**
		* Get attraction highlights dynamic rows structure
		*
		* @param string $highlightsPath
		* @return array
		* @SuppressWarnings(PHPMD.ExcessiveMethodLength)
		*/
	 protected function initHighlightFieldStructure($highlightsPath)
	 {
			 return [
					 'arguments' => [
							 'data' => [
									 'config' => [
											 'componentType' => 'dynamicRows',
											 'label' => __(''),
											 'renderDefaultRecord' => false,
											 'recordTemplate' => 'record',
											 'additionalClasses' => 'admin__field-wide',
											 'dataScope' => '',
											 'dndConfig' => [
													 'enabled' => false,
											 ],
											 'disabled' => false,
											 'sortOrder' =>
													 $this->arrayManager->get($highlightsPath . '/arguments/data/config/sortOrder', $this->meta),
									 ],
							 ],
					 ],
					 'children' => [
							 'record' => [
									 'arguments' => [
											 'data' => [
													 'config' => [
															 'componentType' => Container::NAME,
															 'isTemplate' => true,
															 'is_collection' => true,
															 'component' => 'Magento_Ui/js/dynamic-rows/record',
															 'dataScope' => '',
													 ],
											 ],
									 ],
									 'children' => [
											 'automat_vendor_id' => [
													 'arguments' => [
															 'data' => [
																	 'config' => [
																			 'formElement' => Select::NAME,
																			 'componentType' => Field::NAME,
																			 'dataType' => Text::NAME,
																			 'label' => __('Automat Vendor Id'),
																			 'dataScope' => 'automat_vendor_id',
																			 'require' => '1',
																			 'options' => $this->getAutomatId(),
																	 ],
															 ],
													 ],
											 ],

											 '	automat_vendor_name	' => [
													 'arguments' => [
															 'data' => [
																	 'config' => [
																			 'formElement' => Select::NAME,
																			 'componentType' => Field::NAME,
																			 'dataType' => Text::NAME,
																			 'label' => __('Automat Vendor Name'),
																			 'dataScope' => '	automat_vendor_name	',
																			 'require' => '1',
																			 'options' => $this->getAutomatName(),
																	 ],
															 ],
													 ],
											 ],

											 'sap_vendor_id' => [
													 'arguments' => [
															 'data' => [
																	 'config' => [
																			 'formElement' => Select::NAME,
																			 'componentType' => Field::NAME,
																			 'dataType' => Text::NAME,
																			 'label' => __('SAP Vendor Id'),
																			 'dataScope' => 'sap_vendor_id',
																			 'options' => $this->getSapId(),
																	 ],
															 ],
													 ],
											 ],

											 'sap_vendor_name' => [
													 'arguments' => [
															 'data' => [
																	 'config' => [
																			 'formElement' => Select::NAME,
																			 'componentType' => Field::NAME,
																			 'dataType' => Text::NAME,
																			 'label' => __('SAP Vendor Name'),
																			 'dataScope' => 'sap_vendor_name',
																			 'options' => $this->getSapName(),
																	 ],
															 ],
													 ],
											 ],

											 'city' => [
													 'arguments' => [
															 'data' => [
																	 'config' => [
																			 'formElement' => Select::NAME,
																			 'componentType' => Field::NAME,
																			 'dataType' => Text::NAME,
																			 'label' => __('City'),
																			 'dataScope' => 'city',
																			 'options' => $this->website->toOptionArray(),
																	 ],
															 ],
													 ],
											 ],

											 'lead_time' => [
													 'arguments' => [
															 'data' => [
																	 'config' => [
																			 'formElement' => Input::NAME,
																			 'componentType' => Field::NAME,
																			 'dataType' => Text::NAME,
																			 'label' => __('Lead Time'),
																			 'dataScope' => 'lead_time',
																	 ],
															 ],
													 ],
											 ],

											 'buying_price' => [
													 'arguments' => [
															 'data' => [
																	 'config' => [
																			 'formElement' => Input::NAME,
																			 'componentType' => Field::NAME,
																			 'dataType' => Text::NAME,
																			 'label' => __('Buying Price'),
																			 'dataScope' => 'buying_price',
																	 ],
															 ],
													 ],
											 ],

											 'vendor_sku' => [
													 'arguments' => [
															 'data' => [
																	 'config' => [
																			 'formElement' => Input::NAME,
																			 'componentType' => Field::NAME,
																			 'dataType' => Text::NAME,
																			 'label' => __('Vendor SKU'),
																			 'dataScope' => 'vendor_sku',
																	 ],
															 ],
													 ],
											 ],

											 'vendor_sku_status' => [
													 'arguments' => [
															 'data' => [
																	 'config' => [
																			 'formElement' => Checkbox::NAME,
																			 'componentType' => Field::NAME,
																			 'dataType' => Boolean::NAME,
																			 'label' => __('Availability'),
																			 'dataScope' => 'vendor_sku_status',
																	 ],
															 ],
													 ],
											 ],

											 'actionDelete' => [
													 'arguments' => [
															 'data' => [
																	 'config' => [
																			 'componentType' => 'actionDelete',
																			 'dataType' => Text::NAME,
																			 'label' => '',
																	 ],
															 ],
													 ],
											 ],
									 ],
							 ],
					 ],
			 ];
	 }

	 public function getAutomatId(){
		 //print_r($this->_automat->toOptionArray());die;
		 $data = $this->_automat->toOptionArray();
		 $options = array();
		 foreach($data['data'] as $option){
			 $options[] = ['label'=>$option['id'],'value'=>$option['id']];
		 }
		  return $options;
	 }
	 public function getAutomatName(){
		//print_r($this->_automat->toOptionArray());die;
		$data = $this->_automat->toOptionArray();
		$options = array();
		foreach($data['data'] as $option){
			$options[] = ['label'=>$option['name'],'value'=>$option['name']];
		}
		 return $options;
	}

		public function getSapId(){
		 	//print_r($this->_automat->toOptionArray());die;
		 	$data = $this->_sap->toOptionArray();
		 	$options = array();
		 	foreach($data as $option){
		 		$options[] = ['label'=>$option['VendorCode'][0],'value'=>$option['VendorCode'][0]];
		 	}
		 	 return $options;
		 }
		 public function getSapName(){
			//print_r($this->_automat->toOptionArray());die;
			$data = $this->_sap->toOptionArray();
			$options = array();
			foreach($data as $option){
				$options[] = ['label'=>$option['Name'][0],'value'=>$option['Name'][0]];
			}
			 return $options;
		}

}
