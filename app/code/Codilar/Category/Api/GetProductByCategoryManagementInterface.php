<?php
declare(strict_types=1);

namespace Codilar\Category\Api;

interface GetProductByCategoryManagementInterface
{


    /**
    * @api
    * @param string
    * @return string
    */
    public function getProductByCategory($id);
}
