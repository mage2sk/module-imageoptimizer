<?php
/**
 * Copyright © Panth Infotech. All rights reserved.
 */
declare(strict_types=1);

namespace Panth\ImageOptimizer\Plugin;

use Magento\Catalog\Model\Product\Image as ProductImage;
use Panth\ImageOptimizer\Helper\Data as ConfigHelper;
use Panth\ImageOptimizer\Service\RequestImageCounter;

/**
 * Decides per-image whether to add loading="lazy" or loading="eager" +
 * fetchpriority="high".
 *
 * The first `lazy_loading/exclude_count` images on the page (when
 * `exclude_above_fold = 1`) are emitted eagerly so the LCP candidate is
 * not pushed to the back of the network queue. Image #1 also gets
 * fetchpriority="high" — the strongest hint to the browser that this is
 * the LCP image.
 */
class Image
{
    public function __construct(
        private readonly ConfigHelper $configHelper,
        private readonly RequestImageCounter $counter
    ) {
    }

    /**
     * @param ProductImage $subject
     * @param string $result
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterToHtml(ProductImage $subject, $result)
    {
        if (!is_string($result) || $result === '') {
            return $result;
        }
        if (!$this->configHelper->isEnabled() || !$this->configHelper->isLazyLoadingEnabled()) {
            return $result;
        }

        $strategy = $this->configHelper->getLoadingStrategy();
        if ($strategy !== 'native' && $strategy !== 'hybrid') {
            return $result;
        }

        // Skip <img> tags that already declare a loading attribute — respect
        // any explicit caller intent (e.g. theme-set loading="eager").
        if (!preg_match('/<img\b(?![^>]*\bloading\s*=)/i', $result)) {
            return $result;
        }

        $position = $this->counter->increment();

        $excludeAboveFold = $this->configHelper->shouldExcludeAboveFold();
        $excludeCount = max(0, $this->configHelper->getExcludeCount());

        if ($excludeAboveFold && $position <= $excludeCount) {
            // Above-the-fold image. The very first one is the LCP candidate —
            // give it loading="eager" + fetchpriority="high".
            $injected = $position === 1
                ? '<img loading="eager" fetchpriority="high"'
                : '<img loading="eager"';

            return preg_replace(
                '/<img\b(?![^>]*\bloading\s*=)/i',
                $injected,
                $result,
                1
            );
        }

        // Below-the-fold (or feature disabled): standard lazy load.
        return preg_replace(
            '/<img\b(?![^>]*\bloading\s*=)/i',
            '<img loading="lazy"',
            $result,
            1
        );
    }
}
