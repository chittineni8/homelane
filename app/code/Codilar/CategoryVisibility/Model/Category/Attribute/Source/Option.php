<?php
namespace Codilar\CategoryVisibility\Model\Category\Attribute\Source;

class Option extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * Catalog config
     *
     * @var \Magento\Catalog\Model\Config
     */
    protected $_catalogConfig;

    /**
     * Construct
     *
     * @param \Magento\Catalog\Model\Config $catalogConfig
     */
    public function __construct(\Magento\Catalog\Model\Config $catalogConfig)
    {
        $this->_catalogConfig = $catalogConfig;
    }

    /**
     * Retrieve Catalog Config Singleton
     *
     * @return \Magento\Catalog\Model\Config
     */
    protected function _getCatalogConfig()
    {
        return $this->_catalogConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllOptions()
    {
        if ($this->_options === null) {
            $this->_options = [
                ['label' => __('HomeLane'), 'value' => 'homeLane'],
                ['label' => __('HomeLane Store'), 'value' => 'homeLane store'],
                ['label' => __('SpaceCraft'), 'value' => 'spacecraft']
            ];

        }
        return $this->_options;
    }
}
