<?php

namespace Codilar\Reviews\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Widget\Block\BlockInterface;

class PageWidget extends Template implements BlockInterface
{
    protected $_template = "Codilar_Reviews::widget/page_widget.phtml";
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * PageWidget constructor.
     * @param StoreManagerInterface $storeManager
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        Template\Context $context,
        array $data = []
    ) {

        parent::__construct($context, $data);
        $this->storeManager = $storeManager;
    }

    public function getMediaUrl()
    {
        $mediaUrl = $this->_storeManager
            ->getStore()
            ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);
        return $mediaUrl;
    }
}
