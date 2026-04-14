<?php
/**
 * Copyright © Panth Infotech. All rights reserved.
 */
declare(strict_types=1);

namespace Panth\ImageOptimizer\Test\Unit\Plugin;

use Panth\ImageOptimizer\Plugin\Image;
use Panth\ImageOptimizer\Helper\Data as ConfigHelper;
use Magento\Catalog\Model\Product\Image as ProductImage;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class ImageTest extends TestCase
{
    /**
     * @var Image
     */
    private Image $plugin;

    /**
     * @var ConfigHelper|MockObject
     */
    private $configHelperMock;

    /**
     * @var ProductImage|MockObject
     */
    private $productImageMock;

    /**
     * Set up test fixtures
     */
    protected function setUp(): void
    {
        $this->configHelperMock = $this->getMockBuilder(ConfigHelper::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->productImageMock = $this->getMockBuilder(ProductImage::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->plugin = new Image($this->configHelperMock);
    }

    /**
     * Test afterToHtml returns result unchanged when module is disabled
     *
     * @return void
     */
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

    /**
     * Test afterToHtml returns result unchanged when lazy loading is disabled
     *
     * @return void
     */
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

    /**
     * Test afterToHtml adds loading="lazy" attribute when native strategy is used
     *
     * @return void
     */
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

    /**
     * Test afterToHtml adds loading="lazy" for hybrid strategy too
     *
     * @return void
     */
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

    /**
     * Test afterToHtml handles multiple img tags
     *
     * @return void
     */
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

    /**
     * Test afterToHtml does not add loading attribute when strategy is intersection-only
     *
     * @return void
     */
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

    /**
     * Test afterToHtml returns empty string when result is empty
     *
     * @return void
     */
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

    /**
     * Test afterToHtml does not duplicate loading attribute on images that already have one
     *
     * @return void
     */
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
        // Should NOT add a second loading attribute
        $this->assertSame(1, substr_count($result, 'loading='));
    }

    /**
     * Test afterToHtml handles case-insensitive img tags
     *
     * @return void
     */
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
