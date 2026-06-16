<?php

declare(strict_types=1);

namespace FlipDev\GoogleCustomerReviews\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Provides select options for the GCR survey opt-in dialog style.
 */
class OptInStyle implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        return [
            ['value' => 'CENTER_DIALOG',       'label' => __('Center Dialog (default)')],
            ['value' => 'BOTTOM_RIGHT_DIALOG', 'label' => __('Bottom Right Dialog')],
            ['value' => 'BOTTOM_LEFT_DIALOG',  'label' => __('Bottom Left Dialog')],
            ['value' => 'TOP_RIGHT_DIALOG',    'label' => __('Top Right Dialog')],
            ['value' => 'TOP_LEFT_DIALOG',     'label' => __('Top Left Dialog')],
            ['value' => 'BOTTOM_TRAY',         'label' => __('Bottom Tray')],
        ];
    }
}
