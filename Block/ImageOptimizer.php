<?php
declare(strict_types=1);

namespace Panth\ImageOptimizer\Block;

use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;
use Panth\ImageOptimizer\Helper\Data as ImageOptimizerHelper;

class ImageOptimizer extends Template
{
    private ImageOptimizerHelper $imageOptimizerHelper;

    public function __construct(
        Context $context,
        ImageOptimizerHelper $imageOptimizerHelper,
        array $data = []
    ) {
        $this->imageOptimizerHelper = $imageOptimizerHelper;
        parent::__construct($context, $data);
    }

    public function getHelper(): ImageOptimizerHelper
    {
        return $this->imageOptimizerHelper;
    }

    public function isEnabled(): bool
    {
        return $this->imageOptimizerHelper->isEnabled();
    }

    public function getConfigJson(): string
    {
        return $this->imageOptimizerHelper->getConfigJson();
    }
}
