<?php
/**
 * Copyright © Panth Infotech. All rights reserved.
 */
declare(strict_types=1);

namespace Panth\ImageOptimizer\Service;

/**
 * Per-request counter that tracks how many <img> tags have been emitted by
 * the lazy-loading plugin so far. Shared across blocks (logo, banner-slider,
 * product-rail, etc.) so the "exclude first N images" rule is page-global,
 * not block-local.
 *
 * Reset between PHP requests automatically — Magento creates a fresh
 * dependency-injection scope per request, so this object is born at zero.
 */
class RequestImageCounter
{
    private int $count = 0;

    public function increment(): int
    {
        return ++$this->count;
    }

    public function current(): int
    {
        return $this->count;
    }

    public function reset(): void
    {
        $this->count = 0;
    }
}
