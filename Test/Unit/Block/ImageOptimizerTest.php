<?php
declare(strict_types=1);

namespace Panth\ImageOptimizer\Test\Unit\Block;

use Panth\ImageOptimizer\Block\ImageOptimizer;
use Panth\ImageOptimizer\Helper\Data as ImageOptimizerHelper;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

class ImageOptimizerTest extends TestCase
{
    /**
     * The Template constructor pulls in the whole view stack, so the block is
     * built without it and only the helper dependency is injected.
     *
     * @param ImageOptimizerHelper $helper
     * @return ImageOptimizer
     */
    private function block(ImageOptimizerHelper $helper): ImageOptimizer
    {
        $reflection = new ReflectionClass(ImageOptimizer::class);
        /** @var ImageOptimizer $block */
        $block = $reflection->newInstanceWithoutConstructor();
        $property = $reflection->getProperty('imageOptimizerHelper');
        $property->setValue($block, $helper);

        return $block;
    }

    public function testGetHelperReturnsTheInjectedHelper(): void
    {
        $helper = $this->createStub(ImageOptimizerHelper::class);

        $this->assertSame($helper, $this->block($helper)->getHelper());
    }

    public function testIsEnabledDelegatesToTheHelper(): void
    {
        $on = $this->createStub(ImageOptimizerHelper::class);
        $on->method('isEnabled')->willReturn(true);
        $off = $this->createStub(ImageOptimizerHelper::class);
        $off->method('isEnabled')->willReturn(false);

        $this->assertTrue($this->block($on)->isEnabled());
        $this->assertFalse($this->block($off)->isEnabled());
    }

    public function testGetConfigJsonReturnsTheHelperJson(): void
    {
        $helper = $this->createMock(ImageOptimizerHelper::class);
        $helper->expects($this->once())->method('getConfigJson')->willReturn('{"enabled":true}');

        $this->assertSame('{"enabled":true}', $this->block($helper)->getConfigJson());
    }
}
