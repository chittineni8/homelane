<?php
namespace Codilar\QueryForm\Block\Widget;

use Magento\Framework\View\Element\Template;
use Magento\Widget\Block\BlockInterface;


class Posts extends Template
{
    protected $_template = "widget/posts.phtml";

    public function getPostUrl()
    {
        return $this->getUrl('query/query/save');
    }


}
