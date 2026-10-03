<?php
declare(strict_types=1);

namespace Panth\ImageOptimizer\Test\Unit\Plugin;

use Magento\Catalog\Model\Product\Image as ProductImage;
use Panth\ImageOptimizer\Helper\Data as ConfigHelper;
use Panth\ImageOptimizer\Plugin\Image;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Markup edge cases for the product image plugin with lazy loading switched on.
 */
class ImageMarkupTest extends TestCase
{
    private function plugin(string $strategy = 'native'): Image
    {
        $helper = $this->createStub(ConfigHelper::class);
        $helper->method('isEnabled')->willReturn(true);
        $helper->method('isLazyLoadingEnabled')->willReturn(true);
        $helper->method('getLoadingStrategy')->willReturn($strategy);

        return new Image($helper);
    }

    private function apply(string $strategy, $html)
    {
        return $this->plugin($strategy)->afterToHtml($this->createStub(ProductImage::class), $html);
    }

    public function testNonStringResultsAreReturnedUntouched(): void
    {
        $this->assertNull($this->apply('native', null));
        $this->assertSame(42, $this->apply('native', 42));
        $this->assertSame(['x'], $this->apply('hybrid', ['x']));
    }

    public function testLoadingTextInsideAQuotedValueIsNotAnAttribute(): void
    {
        $this->assertSame(
            '<img loading="lazy" alt="see loading=fast" src="a.jpg">',
            $this->apply('native', '<img alt="see loading=fast" src="a.jpg">')
        );
    }

    public function testLoadingTextInsideASingleQuotedValueIsNotAnAttribute(): void
    {
        $this->assertSame(
            "<img loading=\"lazy\" title=' loading=x' src=\"a.jpg\">",
            $this->apply('native', "<img title=' loading=x' src=\"a.jpg\">")
        );
    }

    public function testDataLoadingAttributeIsNotMistakenForLoading(): void
    {
        $this->assertSame(
            '<img loading="lazy" data-loading="x" src="a.jpg">',
            $this->apply('hybrid', '<img data-loading="x" src="a.jpg">')
        );
    }

    #[DataProvider('existingLoadingProvider')]
    public function testExistingLoadingAttributeIsRespected(string $html): void
    {
        $this->assertSame($html, $this->apply('native', $html));
    }

    public static function existingLoadingProvider(): array
    {
        return [
            'spaced' => ['<img src="a.jpg" loading = "eager">'],
            'unquoted' => ['<img src="a.jpg" loading=auto>'],
            'upper case' => ['<img src="a.jpg" LOADING="lazy">'],
            'after newline' => ["<img src=\"a.jpg\"\nloading=\"eager\">"],
        ];
    }

    public function testTagsThatOnlyStartWithImgAreIgnored(): void
    {
        $html = '<imgur-card src="a.jpg"></imgur-card><p>img</p>';

        $this->assertSame($html, $this->apply('native', $html));
    }

    public function testOnlyImagesWithoutLoadingAreChangedInMixedMarkup(): void
    {
        $this->assertSame(
            '<img loading="lazy" src="a.jpg"><img loading="eager" src="b.jpg"><img loading="lazy" src="c.jpg">',
            $this->apply('native', '<img src="a.jpg"><img loading="eager" src="b.jpg"><img src="c.jpg">')
        );
    }

    public function testMarkupWithoutImagesIsUnchanged(): void
    {
        $html = '<div class="product-image-container"><span>no image</span></div>';

        $this->assertSame($html, $this->apply('native', $html));
    }

    public function testUnknownStrategyLeavesMarkupAlone(): void
    {
        $this->assertSame('<img src="a.jpg">', $this->apply('something', '<img src="a.jpg">'));
    }
}
