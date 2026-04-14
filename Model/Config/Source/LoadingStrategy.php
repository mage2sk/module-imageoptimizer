<?php
/**
 * Copyright © Panth Infotech. All rights reserved.
 */
declare(strict_types=1);

namespace Panth\ImageOptimizer\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class LoadingStrategy implements OptionSourceInterface
{
    /**
     * Get options as array
     *
     * @return array
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => 'native', 'label' => __('Native (loading="lazy" attribute)')],
            ['value' => 'intersection', 'label' => __('Intersection Observer (JavaScript)')],
            ['value' => 'hybrid', 'label' => __('Hybrid (Native + Intersection Observer)')]
        ];
    }

    /**
     * Get options as key-value array
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'native' => __('Native (loading="lazy" attribute)'),
            'intersection' => __('Intersection Observer (JavaScript)'),
            'hybrid' => __('Hybrid (Native + Intersection Observer)')
        ];
    }
}
