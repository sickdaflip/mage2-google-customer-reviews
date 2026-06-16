<?php

declare(strict_types=1);

namespace FlipDev\GoogleCustomerReviews\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Typed access to FlipDev Google Customer Reviews configuration values.
 */
class Provider
{
    private const XML_ENABLED       = 'flipdev_gcr/general/enabled';
    private const XML_MERCHANT_ID   = 'flipdev_gcr/general/merchant_id';
    private const XML_DELIVERY_DAYS = 'flipdev_gcr/general/delivery_days';
    private const XML_OPT_IN_STYLE  = 'flipdev_gcr/general/opt_in_style';
    private const XML_LANGUAGE      = 'flipdev_gcr/general/language';

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig
    ) {}

    public function isEnabled(?string $scopeCode = null): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_ENABLED, ScopeInterface::SCOPE_STORE, $scopeCode);
    }

    public function getMerchantId(?string $scopeCode = null): string
    {
        return (string) $this->scopeConfig->getValue(self::XML_MERCHANT_ID, ScopeInterface::SCOPE_STORE, $scopeCode);
    }

    public function getDeliveryDays(?string $scopeCode = null): int
    {
        return (int) ($this->scopeConfig->getValue(self::XML_DELIVERY_DAYS, ScopeInterface::SCOPE_STORE, $scopeCode) ?: 5);
    }

    public function getOptInStyle(?string $scopeCode = null): string
    {
        return (string) ($this->scopeConfig->getValue(self::XML_OPT_IN_STYLE, ScopeInterface::SCOPE_STORE, $scopeCode) ?: 'CENTER_DIALOG');
    }

    /**
     * Returns the configured language code, or "auto" to derive from store locale.
     */
    public function getLanguage(?string $scopeCode = null): string
    {
        return (string) ($this->scopeConfig->getValue(self::XML_LANGUAGE, ScopeInterface::SCOPE_STORE, $scopeCode) ?: 'auto');
    }
}
