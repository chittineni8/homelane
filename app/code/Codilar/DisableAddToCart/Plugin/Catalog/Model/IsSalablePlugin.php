<?php
/**
 * IsSalable plugin
 *
 * @package   Codilar\DisableAddToCart
 * @author    Shahed Jamal <shahed@codilar.com>
 * @copyright © 2021 Codilar
 * @license   See LICENSE file for license details.
 */

declare(strict_types=1);

namespace Codilar\DisableAddToCart\Plugin\Catalog\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Http\Context;
use Magento\Store\Model\ScopeInterface;

class IsSalablePlugin
{
    /**
     * Scope config
     *
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * HTTP Context
     * Customer session is not initialized yet
     *
     * @var Context
     */
    protected $context;

    const DISABLE_ADD_TO_CART = 'catalog/frontend/catalog_frontend_disable_add_to_cart';

    /**
     * SalablePlugin constructor.
     *
     * @param ScopeConfigInterface $scopeConfig ScopeConfigInterface
     * @param Context              $context     Context
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Context $context
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->context = $context;
    }

    /**
     * Check if is disable add to cart
     *
     * @return bool
     */
    public function afterIsSalable(): bool
    {
        $scope = ScopeInterface::SCOPE_STORE;

        if ($this->scopeConfig->getValue(self::DISABLE_ADD_TO_CART, $scope)) {
            return false;
        }
        return true;
    }
}
