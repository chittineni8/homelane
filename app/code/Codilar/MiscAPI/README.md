# Mage2 Module Codilar MiscAPI

    ``codilar/module-miscapi``

 - [Main Functionalities](#markdown-header-main-functionalities)
 - [Installation](#markdown-header-installation)
 - [Configuration](#markdown-header-configuration)
 - [Specifications](#markdown-header-specifications)
 - [Attributes](#markdown-header-attributes)


## Main Functionalities
MISC API FOR ERP SYNC

## Installation
\* = in production please use the `--keep-generated` option

### Type 1: Zip file

 - Unzip the zip file in `app/code/Codilar`
 - Enable the module by running `php bin/magento module:enable Codilar_MiscAPI`
 - Apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`

### Type 2: Composer

 - Make the module available in a composer repository for example:
    - private repository `repo.magento.com`
    - public repository `packagist.org`
    - public github repository as vcs
 - Add the composer repository to the configuration by running `composer config repositories.repo.magento.com composer https://repo.magento.com/`
 - Install the module composer by running `composer require codilar/module-miscapi`
 - enable the module by running `php bin/magento module:enable Codilar_MiscAPI`
 - apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`


## Configuration




## Specifications

 - API Endpoint
	- GET - Codilar\MiscAPI\Api\GetErpSkuManagementInterface > Codilar\MiscAPI\Model\GetErpSkuManagement

 - API Endpoint
	- GET - Codilar\MiscAPI\Api\GetBomTypeManagementInterface > Codilar\MiscAPI\Model\GetBomTypeManagement

 - API Endpoint
	- GET - Codilar\MiscAPI\Api\GetSkuBySearchManagementInterface > Codilar\MiscAPI\Model\GetSkuBySearchManagement


## Attributes



