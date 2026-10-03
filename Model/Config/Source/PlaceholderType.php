<?php
declare(strict_types=1);

namespace Panth\ImageOptimizer\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class PlaceholderType implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        return [
            ['value' => 'none', 'label' => __('None')],
            ['value' => 'blur', 'label' => __('Blur Effect (LQIP)')],
            ['value' => 'color', 'label' => __('Dominant Color')],
            ['value' => 'spinner', 'label' => __('Loading Spinner')],
            ['value' => 'svg', 'label' => __('SVG Placeholder')]
        ];
    }

    public function toArray(): array
    {
        return [
            'none' => __('None'),
            'blur' => __('Blur Effect (LQIP)'),
            'color' => __('Dominant Color'),
            'spinner' => __('Loading Spinner'),
            'svg' => __('SVG Placeholder')
        ];
    }
}
