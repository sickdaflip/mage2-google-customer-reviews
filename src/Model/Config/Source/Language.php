<?php

declare(strict_types=1);

namespace FlipDev\GoogleCustomerReviews\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Provides select options for the GCR survey language.
 * "auto" derives the language from the Magento store locale.
 */
class Language implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        return [
            ['value' => 'auto',   'label' => __('Auto (Store Locale)')],
            ['value' => 'af',     'label' => 'Afrikaans'],
            ['value' => 'ar',     'label' => 'Arabic'],
            ['value' => 'cs',     'label' => 'Czech'],
            ['value' => 'da',     'label' => 'Danish'],
            ['value' => 'de',     'label' => 'German'],
            ['value' => 'en',     'label' => 'English'],
            ['value' => 'en-AU',  'label' => 'English (Australia)'],
            ['value' => 'en-GB',  'label' => 'English (UK)'],
            ['value' => 'en-US',  'label' => 'English (US)'],
            ['value' => 'es',     'label' => 'Spanish'],
            ['value' => 'es-419', 'label' => 'Spanish (Latin America)'],
            ['value' => 'fil',    'label' => 'Filipino'],
            ['value' => 'fr',     'label' => 'French'],
            ['value' => 'ga',     'label' => 'Irish'],
            ['value' => 'id',     'label' => 'Indonesian'],
            ['value' => 'it',     'label' => 'Italian'],
            ['value' => 'ja',     'label' => 'Japanese'],
            ['value' => 'ms',     'label' => 'Malay'],
            ['value' => 'nl',     'label' => 'Dutch'],
            ['value' => 'no',     'label' => 'Norwegian'],
            ['value' => 'pl',     'label' => 'Polish'],
            ['value' => 'pt-BR',  'label' => 'Portuguese (Brazil)'],
            ['value' => 'pt-PT',  'label' => 'Portuguese'],
            ['value' => 'ru',     'label' => 'Russian'],
            ['value' => 'sv',     'label' => 'Swedish'],
            ['value' => 'tr',     'label' => 'Turkish'],
            ['value' => 'zh-CN',  'label' => 'Chinese (Simplified)'],
            ['value' => 'zh-TW',  'label' => 'Chinese (Traditional)'],
        ];
    }
}
