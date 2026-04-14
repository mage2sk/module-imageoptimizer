<?php
/**
 * Copyright © Panth Infotech. All rights reserved.
 */
declare(strict_types=1);

namespace Panth\ImageOptimizer\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Panth\ImageOptimizer\Helper\Data as ImageOptimizerHelper;

class ImageOptimizer extends Template
{
    /**
     * @var ImageOptimizerHelper
     */
    private ImageOptimizerHelper $imageOptimizerHelper;

    /**
     * @param Context $context
     * @param ImageOptimizerHelper $imageOptimizerHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        ImageOptimizerHelper $imageOptimizerHelper,
        array $data = []
    ) {
        $this->imageOptimizerHelper = $imageOptimizerHelper;
        parent::__construct($context, $data);
    }

    /**
     * Get Image Optimizer helper
     *
     * @return ImageOptimizerHelper
     */
    public function getHelper(): ImageOptimizerHelper
    {
        return $this->imageOptimizerHelper;
    }

    /**
     * Check if module is enabled
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->imageOptimizerHelper->isEnabled();
    }

    /**
     * Get configuration as JSON
     *
     * @return string
     */
    public function getConfigJson(): string
    {
        return $this->imageOptimizerHelper->getConfigJson();
    }
}
