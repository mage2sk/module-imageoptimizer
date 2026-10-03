<?php
declare(strict_types=1);

namespace Panth\ImageOptimizer\Plugin;

use Magento\Catalog\Model\Product\Image as ProductImage;
use Panth\ImageOptimizer\Helper\Data as ConfigHelper;

class Image
{
    private const IMG_WITHOUT_LOADING = '/<img\b(?!(?:[^>"\']|"[^"]*+"|\'[^\']*+\')*?\sloading\s*=)/i';

    public function __construct(
        private readonly ConfigHelper $configHelper
    ) {
    }

    public function afterToHtml(ProductImage $subject, $result)
    {
        if (!$this->configHelper->isEnabled() || !$this->configHelper->isLazyLoadingEnabled()) {
            return $result;
        }

        $strategy = $this->configHelper->getLoadingStrategy();
        if ($strategy !== 'native' && $strategy !== 'hybrid') {
            return $result;
        }

        if (!is_string($result) || $result === '') {
            return $result;
        }

        return preg_replace(self::IMG_WITHOUT_LOADING, '<img loading="lazy"', $result) ?? $result;
    }
}
