<?php
declare(strict_types=1);

namespace Panth\ImageOptimizer\Test\Unit\Plugin;

use Panth\ImageOptimizer\Plugin\Image;
use Panth\ImageOptimizer\Helper\Data as ConfigHelper;
use Magento\Catalog\Model\Product\Image as ProductImage;
use PHPUnit\Framework\TestCase;

class ImageTest extends TestCase
{
    private Image $plugin;

    private $configHelperMock;

    private $productImageMock;

    protected function setUp(): void
    {
        $this->configHelperMock = $this->getMockBuilder(ConfigHelper::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->productImageMock = $this->createStub(ProductImage::class);

        $this->plugin = new Image($this->configHelperMock);
    }

    public function testAfterToHtmlModuleDisabled(): void
    {
        $htmlResult = '<img src="test.jpg" alt="Test" />';

        $this->configHelperMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn(false);

        $this->configHelperMock->expects($this->never())
            ->method('isLazyLoadingEnabled');

        $result = $this->plugin->afterToHtml($this->productImageMock, $htmlResult);
        $this->assertSame($htmlResult, $result);
    }

    public function testAfterToHtmlLazyLoadingDisabled(): void
    {
        $htmlResult = '<img src="test.jpg" alt="Test" />';

        $this->configHelperMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        $this->configHelperMock->expects($this->once())
            ->method('isLazyLoadingEnabled')
            ->willReturn(false);

        $result = $this->plugin->afterToHtml($this->productImageMock, $htmlResult);
        $this->assertSame($htmlResult, $result);
    }

    public function testAfterToHtmlNativeLoadingStrategy(): void
    {
        $htmlResult = '<img src="test.jpg" alt="Test" />';

        $this->configHelperMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        $this->configHelperMock->expects($this->once())
            ->method('isLazyLoadingEnabled')
            ->willReturn(true);

        $this->configHelperMock->expects($this->once())
            ->method('getLoadingStrategy')
            ->willReturn('native');

        $result = $this->plugin->afterToHtml($this->productImageMock, $htmlResult);
        $this->assertStringContainsString('loading="lazy"', $result);
    }

    public function testAfterToHtmlHybridStrategy(): void
    {
        $htmlResult = '<img src="test.jpg" alt="Test" />';

        $this->configHelperMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        $this->configHelperMock->expects($this->once())
            ->method('isLazyLoadingEnabled')
            ->willReturn(true);

        $this->configHelperMock->expects($this->once())
            ->method('getLoadingStrategy')
            ->willReturn('hybrid');

        $result = $this->plugin->afterToHtml($this->productImageMock, $htmlResult);
        $this->assertStringContainsString('loading="lazy"', $result);
    }

    public function testAfterToHtmlMultipleImgTags(): void
    {
        $htmlResult = '<img src="test1.jpg" alt="Test1" /><img src="test2.jpg" alt="Test2" />';

        $this->configHelperMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        $this->configHelperMock->expects($this->once())
            ->method('isLazyLoadingEnabled')
            ->willReturn(true);

        $this->configHelperMock->expects($this->once())
            ->method('getLoadingStrategy')
            ->willReturn('native');

        $result = $this->plugin->afterToHtml($this->productImageMock, $htmlResult);
        $this->assertSame(2, substr_count($result, 'loading="lazy"'));
    }

    public function testAfterToHtmlIntersectionStrategy(): void
    {
        $htmlResult = '<img src="test.jpg" alt="Test" />';

        $this->configHelperMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        $this->configHelperMock->expects($this->once())
            ->method('isLazyLoadingEnabled')
            ->willReturn(true);

        $this->configHelperMock->expects($this->once())
            ->method('getLoadingStrategy')
            ->willReturn('intersection');

        $result = $this->plugin->afterToHtml($this->productImageMock, $htmlResult);
        $this->assertStringNotContainsString('loading=', $result);
    }

    public function testAfterToHtmlEmptyResult(): void
    {
        $htmlResult = '';

        $this->configHelperMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        $this->configHelperMock->expects($this->once())
            ->method('isLazyLoadingEnabled')
            ->willReturn(true);

        $this->configHelperMock->expects($this->once())
            ->method('getLoadingStrategy')
            ->willReturn('native');

        $result = $this->plugin->afterToHtml($this->productImageMock, $htmlResult);
        $this->assertSame('', $result);
    }

    public function testAfterToHtmlExistingLoadingAttribute(): void
    {
        $htmlResult = '<img loading="eager" src="test.jpg" alt="Test" />';

        $this->configHelperMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        $this->configHelperMock->expects($this->once())
            ->method('isLazyLoadingEnabled')
            ->willReturn(true);

        $this->configHelperMock->expects($this->once())
            ->method('getLoadingStrategy')
            ->willReturn('native');

        $result = $this->plugin->afterToHtml($this->productImageMock, $htmlResult);

        $this->assertSame(1, substr_count($result, 'loading='));
    }

    public function testAfterToHtmlCaseInsensitive(): void
    {
        $htmlResult = '<IMG src="test.jpg" alt="Test" />';

        $this->configHelperMock->expects($this->once())
            ->method('isEnabled')
            ->willReturn(true);

        $this->configHelperMock->expects($this->once())
            ->method('isLazyLoadingEnabled')
            ->willReturn(true);

        $this->configHelperMock->expects($this->once())
            ->method('getLoadingStrategy')
            ->willReturn('native');

        $result = $this->plugin->afterToHtml($this->productImageMock, $htmlResult);
        $this->assertStringContainsString('loading="lazy"', $result);
    }
}
