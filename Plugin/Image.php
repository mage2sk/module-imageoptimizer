<?php
/**
 * Copyright © Panth Infotech. All rights reserved.
 *
 * Adds loading="lazy" to product image HTML when the native or hybrid
 * lazy loading strategy is selected. Skips images that already have
 * a loading attribute.
 */
declare(strict_types=1);

namespace Panth\ImageOptimizer\Plugin;

use Magento\Catalog\Model\Product\Image as ProductImage;
use Panth\ImageOptimizer\Helper\Data as ConfigHelper;

class Image
{
    /**
     * @var ConfigHelper
     */
    private ConfigHelper $configHelper;

    /**
     * @param ConfigHelper $configHelper
     */
    public function __construct(ConfigHelper $configHelper)
    {
        $this->configHelper = $configHelper;
    }

    /**
     * After product image renders to HTML, inject loading="lazy" when appropriate.
     *
     * The regex uses a negative lookahead to skip <img> tags that already
     * contain a loading= attribute (e.g. loading="eager").
     *
     * @param ProductImage $subject
     * @param string $result
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterToHtml(ProductImage $subject, $result)
    {
        if (!$this->configHelper->isEnabled() || !$this->configHelper->isLazyLoadingEnabled()) {
            return $result;
        }

        $strategy = $this->configHelper->getLoadingStrategy();

        // Native and hybrid strategies both need the loading="lazy" attribute.
        // The intersection-only strategy relies purely on JS data-src swapping.
        if ($strategy === 'native' || $strategy === 'hybrid') {
            $result = preg_replace(
                '/<img(?![^>]*\bloading\s*=)/i',
                '<img loading="lazy"',
                $result
            );
        }

        return $result;
    }
}
