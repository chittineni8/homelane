<?php
/**
 * Image.php
 *
 * @package     Homelane
 * @description Store Module which contains store switching functionality
 * @author      Manav Padhariya <manav.p@codilar.com>
 * @copyright   2021 Codilar Technologies Pvt. Ltd. . All rights reserved.
 * @license     Open Source
 * @see         https://www.codilar.com/
 *
 * Store Module which contains store switching functionality
 */
namespace Codilar\Store\Model\Config\Backend;

use Magento\Config\Model\Config\Backend\Image as ImageFile;

/**
 * Class Image
 *
 * @package     Homelane
 * @description Store Module which contains store switching functionality
 * @author      Manav Padhariya <manav.p@codilar.com>
 * @copyright   2021 Codilar Technologies Pvt. Ltd. . All rights reserved.
 * @license     Open Source
 * @see         https://www.codilar.com/
 *
 * Store Module which contains store switching functionality
 */
class Image extends ImageFile
{
    /**
     * The tail part of directory path for uploading
     */
    const UPLOAD_DIR = 'website/icons';

    /**
     * Return path to directory for upload file
     *
     * @return string
     * @throw  \Magento\Framework\Exception\LocalizedException
     */
    protected function _getUploadDir()
    {
        $scopeInfo = $this->_appendScopeInfo(self::UPLOAD_DIR);
        return $this->_mediaDirectory->getAbsolutePath($scopeInfo);
    }

    /**
     * Makes a decision about whether to add info about the scope.
     *
     * @return boolean
     */
    protected function _addWhetherScopeInfo()
    {
        return true;
    }

    /**
     * Getter for allowed extensions of uploaded files.
     *
     * @return string[]
     */
    protected function _getAllowedExtensions()
    {
        return ['jpg', 'jpeg', 'gif', 'png', 'svg'];
    }
}
