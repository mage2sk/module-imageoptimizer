<?php
/**
 * Copyright © Panth Infotech. All rights reserved.
 *
 * Configuration helper for Panth_ImageOptimizer.
 *
 * Every public getter here maps to a real admin field in system.xml
 * and is consumed either by the PHP plugin (Image.php) or by the
 * frontend JavaScript (via getConfigJson()).
 */
declare(strict_types=1);

namespace Panth\ImageOptimizer\Helper;

use Panth\Core\Helper\AbstractConfig;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractConfig
{
    private const XML_PATH_IMAGE_OPTIMIZER = 'panth_imageoptimizer/';

    /**
     * Get config value from Image Optimizer configuration
     *
     * @param string $group
     * @param string $field
     * @param int|null $storeId
     * @return mixed
     */
    protected function getConfigValue(string $group, string $field, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_IMAGE_OPTIMIZER . $group . '/' . $field,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    /**
     * Check if Core module is enabled
     *
     * @return bool
     */
    protected function isCoreModuleEnabled(): bool
    {
        return true;
    }

    // =========================================================================
    // General
    // =========================================================================

    /**
     * Check if Image Optimizer is enabled
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isEnabled($storeId = null): bool
    {
        if (!$this->isCoreModuleEnabled()) {
            return false;
        }

        return (bool)$this->getConfigValue('general', 'enabled', $storeId);
    }

    /**
     * Check if debug mode is enabled
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isDebugMode($storeId = null): bool
    {
        return $this->isEnabled($storeId) && (bool)$this->getConfigValue('general', 'debug_mode', $storeId);
    }

    // =========================================================================
    // WebP Detection (frontend-only: detects browser support, strips unsupported sources)
    // =========================================================================

    /**
     * Check if WebP detection is enabled
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isWebpEnabled($storeId = null): bool
    {
        return $this->isEnabled($storeId) && (bool)$this->getConfigValue('webp', 'enabled', $storeId);
    }

    /**
     * Check if fallback behavior is enabled (strip WebP sources when unsupported)
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isFallbackEnabled($storeId = null): bool
    {
        return (bool)$this->getConfigValue('webp', 'fallback_enabled', $storeId);
    }

    // =========================================================================
    // Lazy Loading
    // =========================================================================

    /**
     * Check if lazy loading is enabled
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isLazyLoadingEnabled($storeId = null): bool
    {
        return $this->isEnabled($storeId) && (bool)$this->getConfigValue('lazy_loading', 'enabled', $storeId);
    }

    /**
     * Get loading strategy (native, intersection, or hybrid)
     *
     * @param int|null $storeId
     * @return string
     */
    public function getLoadingStrategy($storeId = null): string
    {
        return (string)$this->getConfigValue('lazy_loading', 'loading_strategy', $storeId) ?: 'native';
    }

    /**
     * Get lazy loading threshold in pixels (IntersectionObserver rootMargin)
     *
     * @param int|null $storeId
     * @return int
     */
    public function getThreshold($storeId = null): int
    {
        return (int)$this->getConfigValue('lazy_loading', 'threshold', $storeId) ?: 300;
    }

    /**
     * Get placeholder type (none, blur, color, spinner, svg)
     *
     * @param int|null $storeId
     * @return string
     */
    public function getPlaceholderType($storeId = null): string
    {
        return (string)$this->getConfigValue('lazy_loading', 'placeholder', $storeId) ?: 'blur';
    }

    /**
     * Check if fade-in effect is enabled
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isFadeInEnabled($storeId = null): bool
    {
        return (bool)$this->getConfigValue('lazy_loading', 'fade_in', $storeId);
    }

    /**
     * Check if above-fold images should be excluded from lazy loading
     *
     * @param int|null $storeId
     * @return bool
     */
    public function shouldExcludeAboveFold($storeId = null): bool
    {
        return (bool)$this->getConfigValue('lazy_loading', 'exclude_above_fold', $storeId);
    }

    /**
     * Get number of images to exclude from lazy loading (above the fold)
     *
     * @param int|null $storeId
     * @return int
     */
    public function getExcludeCount($storeId = null): int
    {
        return (int)$this->getConfigValue('lazy_loading', 'exclude_count', $storeId) ?: 3;
    }

    // =========================================================================
    // Performance Hints
    // =========================================================================

    /**
     * Check if critical images should be preloaded via link rel="preload"
     *
     * @param int|null $storeId
     * @return bool
     */
    public function shouldPreloadCriticalImages($storeId = null): bool
    {
        return (bool)$this->getConfigValue('performance', 'preload_critical_images', $storeId);
    }

    /**
     * Get number of images to preload
     *
     * @param int|null $storeId
     * @return int
     */
    public function getPreloadCount($storeId = null): int
    {
        return (int)$this->getConfigValue('performance', 'preload_count', $storeId) ?: 2;
    }

    /**
     * Check if async decoding is enabled (decoding="async")
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isDecodeAsyncEnabled($storeId = null): bool
    {
        return (bool)$this->getConfigValue('performance', 'decode_async', $storeId);
    }

    /**
     * Check if fetchpriority="high" is enabled for critical images
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isFetchpriorityEnabled($storeId = null): bool
    {
        return (bool)$this->getConfigValue('performance', 'fetchpriority', $storeId);
    }

    // =========================================================================
    // JSON config for frontend JavaScript
    // =========================================================================

    /**
     * Build configuration array for the frontend script.
     * Only includes settings that the JavaScript actually reads.
     *
     * @param int|null $storeId
     * @return string
     */
    public function getConfigJson($storeId = null): string
    {
        return (string)json_encode([
            'enabled' => $this->isEnabled($storeId),
            'debug' => $this->isDebugMode($storeId),
            'webp' => [
                'enabled' => $this->isWebpEnabled($storeId),
                'fallback' => $this->isFallbackEnabled($storeId),
            ],
            'lazyLoading' => [
                'enabled' => $this->isLazyLoadingEnabled($storeId),
                'strategy' => $this->getLoadingStrategy($storeId),
                'threshold' => $this->getThreshold($storeId),
                'placeholder' => $this->getPlaceholderType($storeId),
                'fadeIn' => $this->isFadeInEnabled($storeId),
                'excludeAboveFold' => $this->shouldExcludeAboveFold($storeId),
                'excludeCount' => $this->getExcludeCount($storeId),
            ],
            'performance' => [
                'preload' => $this->shouldPreloadCriticalImages($storeId),
                'preloadCount' => $this->getPreloadCount($storeId),
                'decodeAsync' => $this->isDecodeAsyncEnabled($storeId),
                'fetchpriority' => $this->isFetchpriorityEnabled($storeId),
            ],
        ], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    }
}
