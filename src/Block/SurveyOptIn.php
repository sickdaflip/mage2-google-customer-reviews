<?php

declare(strict_types=1);

namespace FlipDev\GoogleCustomerReviews\Block;

use FlipDev\GoogleCustomerReviews\Model\Config\Provider;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Provides all data required by the GCR survey opt-in template.
 *
 * Locale → GCR language mapping follows the codes documented at:
 * https://support.google.com/merchants/answer/14629205
 */
class SurveyOptIn extends Template
{
    /**
     * Maps Magento locale codes (or prefixes) to GCR-supported language codes.
     * Unmapped locales fall back to the two-letter language prefix (e.g. "de_AT" → "de").
     */
    private const LOCALE_MAP = [
        'en_AU' => 'en-AU',
        'en_GB' => 'en-GB',
        'en_US' => 'en-US',
        'es_MX' => 'es-419',
        'es_AR' => 'es-419',
        'es_CO' => 'es-419',
        'pt_BR' => 'pt-BR',
        'pt_PT' => 'pt-PT',
        'zh_CN' => 'zh-CN',
        'zh_TW' => 'zh-TW',
        'zh_HK' => 'zh-TW',
    ];

    public function __construct(
        Context $context,
        private readonly Provider $configProvider,
        private readonly CheckoutSession $checkoutSession,
        private readonly StoreManagerInterface $storeManager,
        private readonly ProductRepositoryInterface $productRepository,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    // ── Guard ────────────────────────────────────────────────────────────────

    public function isEnabled(): bool
    {
        return $this->configProvider->isEnabled()
            && $this->configProvider->getMerchantId() !== '';
    }

    // ── Config values ────────────────────────────────────────────────────────

    public function getMerchantId(): int
    {
        return (int) $this->configProvider->getMerchantId();
    }

    public function getOptInStyle(): string
    {
        return $this->configProvider->getOptInStyle();
    }

    /**
     * Returns the resolved GCR language code.
     * When configured as "auto", derives the code from the current store locale.
     */
    public function getLanguageCode(): string
    {
        $configured = $this->configProvider->getLanguage();

        if ($configured !== 'auto') {
            return $configured;
        }

        return $this->resolveLocaleToGcrLanguage();
    }

    // ── Order data ───────────────────────────────────────────────────────────

    private function getOrder(): ?OrderInterface
    {
        return $this->checkoutSession->getLastRealOrder() ?: null;
    }

    public function getOrderId(): string
    {
        return (string) ($this->getOrder()?->getIncrementId() ?? '');
    }

    public function getCustomerEmail(): string
    {
        return (string) ($this->getOrder()?->getCustomerEmail() ?? '');
    }

    /**
     * Returns the two-letter ISO 3166-1 alpha-2 delivery country code.
     * Falls back to the store's base country when no shipping address is present.
     */
    public function getDeliveryCountry(): string
    {
        $order = $this->getOrder();

        if ($order !== null) {
            $shipping = $order->getShippingAddress();
            if ($shipping !== null) {
                $code = (string) $shipping->getCountryId();
                if ($code !== '' && $code !== 'ZZ') {
                    return strtoupper($code);
                }
            }
            // Virtual / downloadable orders: use billing address country
            $billing = $order->getBillingAddress();
            if ($billing !== null) {
                $code = (string) $billing->getCountryId();
                if ($code !== '' && $code !== 'ZZ') {
                    return strtoupper($code);
                }
            }
        }

        // Last resort: store base country
        return strtoupper(
            (string) $this->_scopeConfig->getValue(
                'general/country/default',
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            )
        );
    }

    /**
     * Returns the estimated delivery date as YYYY-MM-DD, calculated from
     * the order placement date plus the configured number of business days.
     */
    public function getEstimatedDeliveryDate(): string
    {
        $days  = $this->configProvider->getDeliveryDays();
        $order = $this->getOrder();

        $base = $order?->getCreatedAt()
            ? new \DateTimeImmutable($order->getCreatedAt())
            : new \DateTimeImmutable('now');

        $added   = 0;
        $current = $base;

        while ($added < $days) {
            $current = $current->modify('+1 day');
            // Skip Saturday (6) and Sunday (0)
            $dow = (int) $current->format('w');
            if ($dow !== 0 && $dow !== 6) {
                $added++;
            }
        }

        return $current->format('Y-m-d');
    }

    /**
     * Returns the unique, non-empty list of GTINs for the ordered products,
     * read from the configured product attribute. Returns an empty array when
     * GTIN sending is disabled (no attribute configured) or none are found.
     *
     * @return string[]
     */
    public function getProductGtins(): array
    {
        $attribute = $this->configProvider->getGtinAttribute();
        if ($attribute === '') {
            return [];
        }

        $order = $this->getOrder();
        if ($order === null) {
            return [];
        }

        $gtins = [];
        foreach ($order->getItems() ?? [] as $item) {
            $gtin = $this->loadGtin((int) $item->getProductId(), $attribute);
            if ($gtin !== '') {
                // Keyed assignment deduplicates GTINs shared across items.
                $gtins[$gtin] = true;
            }
        }

        return array_keys($gtins);
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    /**
     * Loads a single product's GTIN value from the given attribute code.
     * Returns an empty string when the product or attribute value is missing.
     */
    private function loadGtin(int $productId, string $attribute): string
    {
        if ($productId <= 0) {
            return '';
        }

        try {
            $product = $this->productRepository->getById($productId);
        } catch (NoSuchEntityException) {
            return '';
        }

        $value = $product->getData($attribute);

        return $value !== null ? trim((string) $value) : '';
    }

    /**
     * Converts a Magento locale string (e.g. "de_DE", "en_GB") to a GCR
     * language code. Uses the locale map for known variants, falls back to
     * the two-letter language prefix.
     */
    private function resolveLocaleToGcrLanguage(): string
    {
        try {
            $locale = $this->storeManager->getStore()->getLocaleCode()
                ?? $this->_scopeConfig->getValue(
                    'general/locale/code',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE
                );
        } catch (\Exception) {
            return 'en';
        }

        $locale = (string) $locale;

        // Exact match first
        if (isset(self::LOCALE_MAP[$locale])) {
            return self::LOCALE_MAP[$locale];
        }

        // Two-letter language prefix (e.g. "de_AT" → "de")
        $prefix = substr($locale, 0, 2);
        return $prefix !== '' ? strtolower($prefix) : 'en';
    }
}
