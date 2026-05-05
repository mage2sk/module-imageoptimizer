<?php
/**
 * Copyright © Panth Infotech. All rights reserved.
 */
declare(strict_types=1);

namespace Panth\ImageOptimizer\Plugin\Layout;

use Magento\Framework\View\LayoutInterface;
use Panth\ImageOptimizer\Helper\Data as ConfigHelper;
use Panth\ImageOptimizer\Service\RequestImageCounter;

/**
 * Layout-level pass that decides loading="eager"|"lazy" for every <img>
 * tag in the rendered page output, regardless of which block or theme
 * template emitted it. Theme-agnostic — Hyva product cards (Alpine
 * `:src` bindings), CMS/banner-slider images, mega-menu images, the
 * logo template, and product-rail images all flow through here.
 *
 * Decisions:
 *   - <img> already declares a `loading=` attribute → leave alone
 *     (respect explicit caller intent).
 *   - exclude_above_fold = 1 and counter <= exclude_count → loading="eager"
 *     (and fetchpriority="high" on the very first one — the LCP candidate).
 *   - otherwise → loading="lazy".
 *
 * The shared `RequestImageCounter` makes the "first N images" rule
 * page-global rather than block-local, which is how the user-facing
 * config promised to behave.
 */
class LazyLoadingPlugin
{
    public function __construct(
        private readonly ConfigHelper $configHelper,
        private readonly RequestImageCounter $counter
    ) {
    }

    /**
     * @param LayoutInterface $subject
     * @param mixed $result
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetOutput(LayoutInterface $subject, $result)
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
        if (stripos($result, '<img') === false) {
            return $result;
        }

        $excludeAboveFold = $this->configHelper->shouldExcludeAboveFold();
        $excludeCount = max(0, $this->configHelper->getExcludeCount());

        return preg_replace_callback(
            '/<img\b[^>]*>/i',
            function (array $match) use ($excludeAboveFold, $excludeCount): string {
                $tag = $match[0];
                $position = $this->counter->increment();
                $shouldBeEager = $excludeAboveFold && $position <= $excludeCount;
                $isFirst = $position === 1;

                $hasEager = (bool) preg_match('/\bloading\s*=\s*["\']?eager/i', $tag);
                $hasLazy = (bool) preg_match('/\bloading\s*=\s*["\']?lazy/i', $tag);
                $hasFetchPriority = (bool) preg_match('/\bfetchpriority\s*=/i', $tag);

                if ($shouldBeEager) {
                    // Force above-fold images to load eagerly even if a theme
                    // template hard-coded loading="lazy" — admin opted into
                    // this behavior to fix LCP, and the theme's blanket lazy
                    // would defeat the point.
                    if ($hasLazy) {
                        $tag = preg_replace(
                            '/\bloading\s*=\s*["\']?lazy["\']?/i',
                            'loading="eager"',
                            $tag,
                            1
                        ) ?? $tag;
                    } elseif (!$hasEager) {
                        $tag = preg_replace('/<img\b/i', '<img loading="eager"', $tag, 1) ?? $tag;
                    }
                    if ($isFirst && !$hasFetchPriority) {
                        $tag = preg_replace('/<img\b/i', '<img fetchpriority="high"', $tag, 1) ?? $tag;
                    }
                    return $tag;
                }

                // Below-the-fold (or feature off) — only add lazy if the
                // template didn't already pick a loading mode.
                if (!$hasEager && !$hasLazy) {
                    $tag = preg_replace('/<img\b/i', '<img loading="lazy"', $tag, 1) ?? $tag;
                }
                return $tag;
            },
            $result
        ) ?? $result;
    }
}
