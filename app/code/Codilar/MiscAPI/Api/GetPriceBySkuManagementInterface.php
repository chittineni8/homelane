<?php
declare(strict_types=1);

namespace Codilar\MiscAPI\Api;

interface GetPriceBySkuManagementInterface
{


    /**
    * @api
    * @param string
    * @return string
    */
    public function getPriceBySku($sku);
}
