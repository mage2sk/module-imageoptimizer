<?php
declare(strict_types=1);

namespace Panth\ImageOptimizer\Test\Unit\Plugin\Layout;

use Magento\Framework\View\LayoutInterface;
use Panth\ImageOptimizer\Helper\Data as ConfigHelper;
use Panth\ImageOptimizer\Plugin\Layout\LazyLoadingPlugin;
use Panth\ImageOptimizer\Service\RequestImageCounter;
use PHPUnit\Framework\TestCase;

class LazyLoadingPluginTest extends TestCase
{
    private function createPlugin(bool $fetchPriority): LazyLoadingPlugin
    {
        $helper = $this->createStub(ConfigHelper::class);
        $helper->method('isEnabled')->willReturn(true);
        $helper->method('isLazyLoadingEnabled')->willReturn(true);
        $helper->method('getLoadingStrategy')->willReturn('native');
        $helper->method('shouldExcludeAboveFold')->willReturn(true);
        $helper->method('getExcludeCount')->willReturn(2);
        $helper->method('isFetchpriorityEnabled')->willReturn($fetchPriority);

        return new LazyLoadingPlugin($helper, new RequestImageCounter());
    }

    public function testDocumentOrderDecidesEagerImages(): void
    {
        $html = '<img src="logo.svg" alt="a > b">'
            . '<img loading="lazy" src="p1.jpg">'
            . '<img loading="lazy" src="p2.jpg">'
            . '<img src="p3.jpg">';

        $result = $this->createPlugin(true)->afterGetOutput($this->createStub(LayoutInterface::class), $html);

        $this->assertSame(
            '<img fetchpriority="high" loading="eager" src="logo.svg" alt="a > b">'
            . '<img loading="eager" src="p1.jpg">'
            . '<img loading="lazy" src="p2.jpg">'
            . '<img loading="lazy" src="p3.jpg">',
            $result
        );
    }

    public function testFetchPriorityRespectsSetting(): void
    {
        $result = $this->createPlugin(false)->afterGetOutput(
            $this->createStub(LayoutInterface::class),
            '<img src="logo.svg">'
        );

        $this->assertSame('<img loading="eager" src="logo.svg">', $result);
    }

    public function testScriptTemplateAndCommentAreNotModified(): void
    {
        $html = '<script>var t = "<img src=\'x.jpg\'>";</script>'
            . '<template x-if="open"><img :src="u"></template>'
            . '<!-- <img src="c.jpg"> -->';

        $result = $this->createPlugin(true)->afterGetOutput($this->createStub(LayoutInterface::class), $html);

        $this->assertSame($html, $result);
    }
}
