<?php

namespace Codilar\AttributeSet\Setup\Patch\Data;

use Codilar\AttributeSet\Model\Attributeset;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;
use Magento\Framework\Module\Setup\Migration;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Eav\Api\Data\AttributeSetInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Catalog\Api\AttributeSetRepositoryInterface;

/**
 * Class AddData
 * @package Ced\GraphQl\Setup\Patch\Data
 */
class AddData implements DataPatchInterface, PatchVersionInterface

{
    /**
     * @var AttributeSetRepositoryInterface
     */
    private $attributeSetRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    private $author;

    public function __construct(

        AttributeSetRepositoryInterface $attributeSetRepository,
        SearchCriteriaBuilder           $searchCriteriaBuilder,
        Attributeset                    $author

    )
    {
        $this->author = $author;
        $this->attributeSetRepository = $attributeSetRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    public function apply()

    {

        $attributeSetList = null;
        try {
            $searchCriteria = $this->searchCriteriaBuilder->create();
            $attributeSet = $this->attributeSetRepository->getList($searchCriteria);
        } catch (Exception $exception) {
            throw new Exception($exception->getMessage());
        }

        if ($attributeSet->getTotalCount()) {
            $attributeSetList = $attributeSet;
        }

//        return $attributeSetList;
//        $authorData = [];
//foreach($attributeSetList as $attributeset){
//
//    $authorData['attribute_set_name'] = $attributeset;
//
//}

//
        $authorData['attribute_set_name'] = "Default";
//
//        $authorData['affliation'] = "Andrew Company";
//
//        $authorData['age'] = 32;

        $this->author->addData($authorData);

        $this->author->getResource()->save($this->author);
    }


    /**
     * {@inheritdoc}
     */

    public static function getDependencies()

    {
        return [];
    }


    /**
     * {@inheritdoc}
     */

    public static function getVersion()

    {
        return '2.0.0';
    }

    /**
     * {@inheritdoc}
     */

    public function getAliases()
    {
        return [];
    }
}
