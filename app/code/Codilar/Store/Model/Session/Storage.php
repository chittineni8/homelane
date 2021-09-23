<?php
namespace Codilar\Store\Model\Session;

use Magento\Framework\Session\Storage as SessionStorage;
use Magento\Store\Model\StoreManagerInterface;

class Storage extends SessionStorage
{
    /**
     * @param StoreManagerInterface $storeManager
     * @param string $namespace
     * @param array $data
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        $namespace = 'website_code',
        array $data = []
    ) {
        parent::__construct($namespace, $data);
    }
}
