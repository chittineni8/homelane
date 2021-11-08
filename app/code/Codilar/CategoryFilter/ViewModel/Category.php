<?php

namespace Codilar\CategoryFilter\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;

Class Category implements ArgumentInterface
{
    protected $url;

    public function __construct(\Magento\Framework\UrlInterface $url)
            {
                $this->url = $url;
            }

            public function getPostUrl(){

              return $this->url->getUrl('cat/category/title');

            }
        }
