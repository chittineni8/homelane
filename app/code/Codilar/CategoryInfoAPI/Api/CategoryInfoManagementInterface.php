<?php
declare(strict_types=1);

namespace Codilar\CategoryInfoAPI\Api;

interface CategoryInfoManagementInterface
{
    /**
     * @param int $id
     * @return mixed
     */
    public function getCategoryInfo($id);
}

