<?php
declare(strict_types=1);

namespace Panth\ImageOptimizer\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class LoadingStrategy implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        return [
            ['value' => 'native', 'label' => __('Native (loading="lazy" attribute)')],
            ['value' => 'intersection', 'label' => __('Intersection Observer (JavaScript)')],
            ['value' => 'hybrid', 'label' => __('Hybrid (Native + Intersection Observer)')]
        ];
    }

    public function toArray(): array
    {
        return [
            'native' => __('Native (loading="lazy" attribute)'),
            'intersection' => __('Intersection Observer (JavaScript)'),
            'hybrid' => __('Hybrid (Native + Intersection Observer)')
        ];
    }
}
