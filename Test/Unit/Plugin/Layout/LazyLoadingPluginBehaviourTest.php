<?php
declare(strict_types=1);

namespace Panth\ImageOptimizer\Test\Unit\Plugin\Layout;

use Magento\Framework\View\LayoutInterface;
use Panth\ImageOptimizer\Helper\Data as ConfigHelper;
use Panth\ImageOptimizer\Plugin\Layout\LazyLoadingPlugin;
use Panth\ImageOptimizer\Service\RequestImageCounter;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class LazyLoadingPluginBehaviourTest extends TestCase
{
    private RequestImageCounter $counter;

    protected function setUp(): void
    {
        $this->counter = new RequestImageCounter();
    }

    private function plugin(array $settings = []): LazyLoadingPlugin
    {
        $settings += [
            'enabled' => true,
            'lazy' => true,
            'strategy' => 'native',
            'aboveFold' => true,
            'excludeCount' => 2,
            'fetchPriority' => false,
        ];
        $helper = $this->createStub(ConfigHelper::class);
        $helper->method('isEnabled')->willReturn($settings['enabled']);
        $helper->method('isLazyLoadingEnabled')->willReturn($settings['lazy']);
        $helper->method('getLoadingStrategy')->willReturn($settings['strategy']);
        $helper->method('shouldExcludeAboveFold')->willReturn($settings['aboveFold']);
        $helper->method('getExcludeCount')->willReturn($settings['excludeCount']);
        $helper->method('isFetchpriorityEnabled')->willReturn($settings['fetchPriority']);

        return new LazyLoadingPlugin($helper, $this->counter);
    }

    private function apply(LazyLoadingPlugin $plugin, $html)
    {
        return $plugin->afterGetOutput($this->createStub(LayoutInterface::class), $html);
    }

    public function testNonStringAndEmptyOutputIsReturnedAsIs(): void
    {
        $plugin = $this->plugin();

        $this->assertNull($this->apply($plugin, null));
        $this->assertSame('', $this->apply($plugin, ''));
        $this->assertSame(5, $this->apply($plugin, 5));
    }

    #[DataProvider('inactiveProvider')]
    public function testOutputIsUntouchedWhenLazyLoadingDoesNotApply(array $settings): void
    {
        $html = '<img src="a.jpg"><img src="b.jpg"><img src="c.jpg">';

        $this->assertSame($html, $this->apply($this->plugin($settings), $html));
        $this->assertSame(0, $this->counter->current());
    }

    public static function inactiveProvider(): array
    {
        return [
            'module disabled' => [['enabled' => false]],
            'lazy loading disabled' => [['lazy' => false]],
            'intersection strategy' => [['strategy' => 'intersection']],
            'unknown strategy' => [['strategy' => '']],
        ];
    }

    public function testHybridStrategyIsHandledLikeNative(): void
    {
        $result = $this->apply(
            $this->plugin(['strategy' => 'hybrid', 'excludeCount' => 1]),
            '<img src="a.jpg"><img src="b.jpg">'
        );

        $this->assertSame('<img loading="eager" src="a.jpg"><img loading="lazy" src="b.jpg">', $result);
    }

    public function testOutputWithoutImagesIsReturnedWithoutTouchingTheCounter(): void
    {
        $this->counter->increment();
        $html = '<div><p>No pictures here</p></div>';

        $this->assertSame($html, $this->apply($this->plugin(), $html));
        $this->assertSame(1, $this->counter->current());
    }

    public function testEveryImageIsLazyWhenAboveFoldExclusionIsOff(): void
    {
        $result = $this->apply(
            $this->plugin(['aboveFold' => false, 'fetchPriority' => true]),
            '<img src="a.jpg"><img src="b.jpg">'
        );

        $this->assertSame('<img loading="lazy" src="a.jpg"><img loading="lazy" src="b.jpg">', $result);
    }

    public function testExcludeCountZeroMakesEveryImageLazy(): void
    {
        $result = $this->apply($this->plugin(['excludeCount' => 0]), '<img src="a.jpg"><img src="b.jpg">');

        $this->assertSame('<img loading="lazy" src="a.jpg"><img loading="lazy" src="b.jpg">', $result);
    }

    public function testNegativeExcludeCountIsTreatedAsZero(): void
    {
        $result = $this->apply($this->plugin(['excludeCount' => -3]), '<img src="a.jpg">');

        $this->assertSame('<img loading="lazy" src="a.jpg">', $result);
    }

    public function testExplicitLoadingOnLaterImagesIsKept(): void
    {
        $result = $this->apply(
            $this->plugin(['excludeCount' => 1]),
            '<img src="a.jpg"><img loading="eager" src="b.jpg"><img loading="auto" src="c.jpg">'
        );

        $this->assertSame(
            '<img loading="eager" src="a.jpg"><img loading="eager" src="b.jpg"><img loading="auto" src="c.jpg">',
            $result
        );
    }

    #[DataProvider('lazyVariantProvider')]
    public function testLazyVariantsOnAboveFoldImagesBecomeEager(string $tag, string $expected): void
    {
        $this->assertSame($expected, $this->apply($this->plugin(), $tag));
    }

    public static function lazyVariantProvider(): array
    {
        return [
            'single quotes' => ["<img loading='lazy' src=\"a.jpg\">", '<img loading="eager" src="a.jpg">'],
            'unquoted' => ['<img loading=lazy src="a.jpg">', '<img loading="eager" src="a.jpg">'],
            'upper case' => ['<img LOADING="LAZY" src="a.jpg">', '<img loading="eager" src="a.jpg">'],
            'spaced' => ['<img src="a.jpg" loading = "lazy">', '<img src="a.jpg" loading="eager">'],
        ];
    }

    public function testNonLazyLoadingOnAboveFoldImageIsKept(): void
    {
        $html = '<img loading="auto" src="a.jpg">';

        $this->assertSame($html, $this->apply($this->plugin(), $html));
    }

    public function testFetchPriorityIsOnlyAddedToTheFirstImage(): void
    {
        $result = $this->apply(
            $this->plugin(['fetchPriority' => true, 'excludeCount' => 3]),
            '<img src="a.jpg"><img src="b.jpg"><img src="c.jpg">'
        );

        $this->assertSame(1, substr_count($result, 'fetchpriority="high"'));
        $this->assertStringStartsWith('<img fetchpriority="high" loading="eager" src="a.jpg">', $result);
    }

    public function testExistingFetchPriorityIsNotDuplicated(): void
    {
        $result = $this->apply(
            $this->plugin(['fetchPriority' => true]),
            '<img fetchpriority="low" src="a.jpg">'
        );

        $this->assertSame('<img loading="eager" fetchpriority="low" src="a.jpg">', $result);
    }

    public function testDataLoadingAttributeDoesNotCountAsLoading(): void
    {
        $result = $this->apply($this->plugin(['excludeCount' => 0]), '<img data-loading="lazy" src="a.jpg">');

        $this->assertSame('<img loading="lazy" data-loading="lazy" src="a.jpg">', $result);
    }

    public function testRawTextContainersAreSkippedAndDoNotConsumePositions(): void
    {
        $html = '<style>.x{background:url("<img src=s.jpg>")}</style>'
            . '<textarea><img src="t.jpg"></textarea>'
            . '<noscript><img src="n.jpg"></noscript>'
            . '<img src="real.jpg">';

        $result = $this->apply($this->plugin(['excludeCount' => 1]), $html);

        $this->assertSame(
            '<style>.x{background:url("<img src=s.jpg>")}</style>'
            . '<textarea><img src="t.jpg"></textarea>'
            . '<noscript><img src="n.jpg"></noscript>'
            . '<img loading="eager" src="real.jpg">',
            $result
        );
        $this->assertSame(1, $this->counter->current());
    }

    public function testQuotedGreaterThanInsideScriptAttributesDoesNotBreakSkipping(): void
    {
        $html = '<script type="text/x-magento-init" data-x="a>b">{"img":"<img src=x>"}</script><img src="a.jpg">';

        $result = $this->apply($this->plugin(['excludeCount' => 0]), $html);

        $this->assertSame(
            '<script type="text/x-magento-init" data-x="a>b">{"img":"<img src=x>"}</script>'
            . '<img loading="lazy" src="a.jpg">',
            $result
        );
    }

    public function testCounterIsResetForEveryLayoutOutput(): void
    {
        $plugin = $this->plugin(['excludeCount' => 1]);

        $first = $this->apply($plugin, '<img src="a.jpg"><img src="b.jpg">');
        $second = $this->apply($plugin, '<img src="c.jpg">');

        $this->assertSame('<img loading="eager" src="a.jpg"><img loading="lazy" src="b.jpg">', $first);
        $this->assertSame('<img loading="eager" src="c.jpg">', $second);
        $this->assertSame(1, $this->counter->current());
    }

    public function testSelfClosingAndMultilineTagsAreHandled(): void
    {
        $result = $this->apply(
            $this->plugin(['excludeCount' => 0]),
            "<img\n  src=\"a.jpg\"\n  alt=\"A\" /><IMG SRC=\"b.jpg\"/>"
        );

        $this->assertSame(
            "<img loading=\"lazy\"\n  src=\"a.jpg\"\n  alt=\"A\" /><img loading=\"lazy\" SRC=\"b.jpg\"/>",
            $result
        );
    }

    public function testLoadingTextInsideAltIsNotTreatedAsAttribute(): void
    {
        $result = $this->apply(
            $this->plugin(['excludeCount' => 0]),
            '<img alt="set loading=lazy here" src="a.jpg">'
        );

        $this->assertSame('<img loading="lazy" alt="set loading=lazy here" src="a.jpg">', $result);
    }

    public function testEagerRewriteLeavesAltTextUntouched(): void
    {
        $result = $this->apply(
            $this->plugin(['excludeCount' => 1]),
            '<img alt="x loading=\'lazy\' y" src="a.jpg" loading="lazy">'
        );

        $this->assertSame('<img alt="x loading=\'lazy\' y" src="a.jpg" loading="eager">', $result);
    }
}
