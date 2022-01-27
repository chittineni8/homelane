<?php
namespace Codilar\Roomtype\Model\Category\Attribute\Source;
use Codilar\Roomtype\Model\RoomtypeFactory;
class RoomType extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{

      /**
       * Block collection factory
       *
       * @var CollectionFactory
       */
      protected $_roomCollectionFactory;

      /**
       * Construct
       *
       * @param CollectionFactory $blockCollectionFactory
       */
      public function __construct(RoomtypeFactory $roomCollectionFactory)
      {
          $this->_roomCollectionFactory = $roomCollectionFactory;
      }

       public function getAllOptions()
       {
         if(!$this->_options) {
           $sellerModel = $this->_roomCollectionFactory->create()->getCollection()
                                              ->addFieldToSelect( array('roomtype_id','roomtype_label','roomtype_label') );
           $options = array();
           if( $sellerModel->getSize() ){
               foreach ( $sellerModel  as $seller) {
                   $options[] = array(
                   'label' => ucfirst( $seller->getData('roomtype_label') ) ,
                   'value' => $seller->getData('roomtype_label')
                   );
               }
           }
           $this->_options = $options;
        }
        return $this->_options;
       }


}
