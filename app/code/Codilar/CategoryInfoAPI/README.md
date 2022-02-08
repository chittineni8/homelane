# Mage2 Module Codilar CategoryInfoAPI

    ``codilar/module-categoryinfoapi``

 - [Main Functionalities](#markdown-header-main-functionalities)
 - [Installation](#markdown-header-installation)
 - [Configuration](#markdown-header-configuration)
 - [Specifications](#markdown-header-specifications)
 - [Attributes](#markdown-header-attributes)


## Main Functionalities
REST API for Category Custom Info 

## Installation
\* = in production please use the `--keep-generated` option

### Type 1: Zip file

 - Unzip the zip file in `app/code/Codilar`
 - Enable the module by running `php bin/magento module:enable Codilar_CategoryInfoAPI`
 - Apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`

### Type 2: Composer

 - Make the module available in a composer repository for example:
    - private repository `repo.magento.com`
    - public repository `packagist.org`
    - public github repository as vcs
 - Add the composer repository to the configuration by running `composer config repositories.repo.magento.com composer https://repo.magento.com/`
 - Install the module composer by running `composer require codilar/module-categoryinfoapi`
 - enable the module by running `php bin/magento module:enable Codilar_CategoryInfoAPI`
 - apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`


## Configuration




## Specifications

 - API Endpoint
	- GET - Codilar\CategoryInfoAPI\Api\Category_infoManagementInterface > Codilar\CategoryInfoAPI\Model\Category_infoManagement


## Attributes



