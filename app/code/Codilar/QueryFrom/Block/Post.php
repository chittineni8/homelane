<?php

namespace Codilar\QueryFrom\Block;

use Magento\Framework\View\Element\Template;

class Post extends Template
{
    
    public function getPostUrl()
    {
        return $this->getUrl('query/query/save');
    }

    
}