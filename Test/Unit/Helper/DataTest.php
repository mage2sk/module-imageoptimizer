<?php
declare(strict_types=1);

namespace Panth\ImageOptimizer\Test\Unit\Helper;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;
use Panth\ImageOptimizer\Helper\Data;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class DataTest extends TestCase
{
    private const PREFIX = 'panth_imageoptimizer/';

    /**
     * @var array<int, array{0: string, 1: string, 2: mixed}>
     */
    private array $calls = [];

    /**
     * Build the helper on top of a scope config that serves the given values.
     *
     * Keys are paths relative to "panth_imageoptimizer/". A key may be suffixed
     * with "@<storeId>" to serve a value for one store only.
     *
     * @param array $values
     * @return Data
     */
    private function helper(array $values): Data
    {
        $this->calls = [];
        $scopeConfig = $this->createStub(ScopeConfigInterface::class);
        $scopeConfig->method('getValue')->willReturnCallback(
            function (string $path, string $scope = 'default', $storeId = null) use ($values) {
                $this->calls[] = [$path, $scope, $storeId];
                $relative = substr($path, strlen(self::PREFIX));
                if ($storeId !== null && array_key_exists($relative . '@' . $storeId, $values)) {
                    return $values[$relative . '@' . $storeId];
                }
                return $values[$relative] ?? null;
            }
        );

        $context = $this->createStub(Context::class);
        $context->method('getScopeConfig')->willReturn($scopeConfig);

        return new Data($context);
    }

    public function testIsEnabledReadsGeneralEnabledInStoreScope(): void
    {
        $helper = $this->helper(['general/enabled' => '1']);

        $this->assertTrue($helper->isEnabled());
        $this->assertSame(
            [self::PREFIX . 'general/enabled', ScopeInterface::SCOPE_STORE, null],
            $this->calls[0]
        );
    }

    public function testIsEnabledIsFalseWhenUnset(): void
    {
        $this->assertFalse($this->helper([])->isEnabled());
    }

    public function testIsEnabledPassesStoreIdThrough(): void
    {
        $helper = $this->helper(['general/enabled@2' => '1']);

        $this->assertTrue($helper->isEnabled(2));
        $this->assertFalse($helper->isEnabled(3));
        $this->assertSame(3, $this->calls[1][2]);
    }

    #[DataProvider('gatedFlagProvider')]
    public function testGatedFlagsRequireTheModuleToBeEnabled(
        string $method,
        string $path,
        bool $moduleEnabled,
        bool $flag,
        bool $expected
    ): void {
        $helper = $this->helper([
            'general/enabled' => $moduleEnabled ? '1' : '0',
            $path => $flag ? '1' : '0',
        ]);

        $this->assertSame($expected, $helper->{$method}());
    }

    public static function gatedFlagProvider(): array
    {
        $cases = [];
        $flags = [
            'isDebugMode' => 'general/debug_mode',
            'isWebpEnabled' => 'webp/enabled',
            'isLazyLoadingEnabled' => 'lazy_loading/enabled',
        ];
        foreach ($flags as $method => $path) {
            $cases[$method . ' both on'] = [$method, $path, true, true, true];
            $cases[$method . ' module off'] = [$method, $path, false, true, false];
            $cases[$method . ' flag off'] = [$method, $path, true, false, false];
            $cases[$method . ' both off'] = [$method, $path, false, false, false];
        }
        return $cases;
    }

    #[DataProvider('ungatedFlagProvider')]
    public function testUngatedFlagsIgnoreTheModuleSwitch(string $method, string $path): void
    {
        $this->assertTrue($this->helper(['general/enabled' => '0', $path => '1'])->{$method}());
        $this->assertFalse($this->helper(['general/enabled' => '1', $path => '0'])->{$method}());
        $this->assertFalse($this->helper(['general/enabled' => '1'])->{$method}());
    }

    public static function ungatedFlagProvider(): array
    {
        return [
            'fallback' => ['isFallbackEnabled', 'webp/fallback_enabled'],
            'fade in' => ['isFadeInEnabled', 'lazy_loading/fade_in'],
            'exclude above fold' => ['shouldExcludeAboveFold', 'lazy_loading/exclude_above_fold'],
            'preload' => ['shouldPreloadCriticalImages', 'performance/preload_critical_images'],
            'decode async' => ['isDecodeAsyncEnabled', 'performance/decode_async'],
            'fetchpriority' => ['isFetchpriorityEnabled', 'performance/fetchpriority'],
        ];
    }

    #[DataProvider('stringSettingProvider')]
    public function testStringSettingsUseConfiguredValueOrDefault(
        string $method,
        string $path,
        ?string $configured,
        string $expected
    ): void {
        $this->assertSame($expected, $this->helper([$path => $configured])->{$method}());
    }

    public static function stringSettingProvider(): array
    {
        $strategy = 'lazy_loading/loading_strategy';
        $placeholder = 'lazy_loading/placeholder';
        return [
            'strategy native' => ['getLoadingStrategy', $strategy, 'native', 'native'],
            'strategy intersection' => ['getLoadingStrategy', $strategy, 'intersection', 'intersection'],
            'strategy hybrid' => ['getLoadingStrategy', $strategy, 'hybrid', 'hybrid'],
            'strategy null' => ['getLoadingStrategy', $strategy, null, 'native'],
            'strategy empty' => ['getLoadingStrategy', $strategy, '', 'native'],
            'placeholder color' => ['getPlaceholderType', $placeholder, 'color', 'color'],
            'placeholder none' => ['getPlaceholderType', $placeholder, 'none', 'none'],
            'placeholder svg' => ['getPlaceholderType', $placeholder, 'svg', 'svg'],
            'placeholder null' => ['getPlaceholderType', $placeholder, null, 'blur'],
            'placeholder empty' => ['getPlaceholderType', $placeholder, '', 'blur'],
        ];
    }

    #[DataProvider('integerSettingProvider')]
    public function testIntegerSettingsUseConfiguredValueOrDefault(
        string $method,
        string $path,
        $configured,
        int $expected
    ): void {
        $this->assertSame($expected, $this->helper([$path => $configured])->{$method}());
    }

    public static function integerSettingProvider(): array
    {
        $threshold = 'lazy_loading/threshold';
        $preload = 'performance/preload_count';
        return [
            'threshold set' => ['getThreshold', $threshold, '500', 500],
            'threshold int' => ['getThreshold', $threshold, 150, 150],
            'threshold null' => ['getThreshold', $threshold, null, 300],
            'threshold non numeric' => ['getThreshold', $threshold, 'abc', 300],
            'threshold zero is honoured' => ['getThreshold', $threshold, '0', 0],
            'preload set' => ['getPreloadCount', $preload, '3', 3],
            'preload null' => ['getPreloadCount', $preload, null, 2],
            'preload zero is honoured' => ['getPreloadCount', $preload, '0', 0],
            'preload empty uses default' => ['getPreloadCount', $preload, '', 2],
        ];
    }

    #[DataProvider('excludeCountProvider')]
    public function testGetExcludeCount($configured, int $expected): void
    {
        $helper = $this->helper(['lazy_loading/exclude_count' => $configured]);

        $this->assertSame($expected, $helper->getExcludeCount());
    }

    public static function excludeCountProvider(): array
    {
        return [
            'configured' => ['5', 5],
            'integer' => [7, 7],
            'null uses default' => [null, 3],
            'empty string uses default' => ['', 3],
            'zero is kept' => ['0', 0],
            'negative is clamped' => ['-4', 0],
        ];
    }

    public function testConfigJsonReflectsAllSettings(): void
    {
        $helper = $this->helper([
            'general/enabled' => '1',
            'general/debug_mode' => '1',
            'webp/enabled' => '1',
            'webp/fallback_enabled' => '0',
            'lazy_loading/enabled' => '1',
            'lazy_loading/loading_strategy' => 'hybrid',
            'lazy_loading/threshold' => '450',
            'lazy_loading/placeholder' => 'spinner',
            'lazy_loading/fade_in' => '1',
            'lazy_loading/exclude_above_fold' => '1',
            'lazy_loading/exclude_count' => '4',
            'performance/preload_critical_images' => '1',
            'performance/preload_count' => '5',
            'performance/decode_async' => '0',
            'performance/fetchpriority' => '1',
        ]);

        $this->assertSame(
            [
                'enabled' => true,
                'debug' => true,
                'webp' => ['enabled' => true, 'fallback' => false],
                'lazyLoading' => [
                    'enabled' => true,
                    'strategy' => 'hybrid',
                    'threshold' => 450,
                    'placeholder' => 'spinner',
                    'fadeIn' => true,
                    'excludeAboveFold' => true,
                    'excludeCount' => 4,
                ],
                'performance' => [
                    'preload' => true,
                    'preloadCount' => 5,
                    'decodeAsync' => false,
                    'fetchpriority' => true,
                ],
            ],
            json_decode($helper->getConfigJson(), true)
        );
    }

    public function testConfigJsonUsesDefaultsWhenNothingIsConfigured(): void
    {
        $data = json_decode($this->helper([])->getConfigJson(), true);

        $this->assertFalse($data['enabled']);
        $this->assertFalse($data['debug']);
        $this->assertFalse($data['webp']['enabled']);
        $this->assertFalse($data['lazyLoading']['enabled']);
        $this->assertSame('native', $data['lazyLoading']['strategy']);
        $this->assertSame(300, $data['lazyLoading']['threshold']);
        $this->assertSame('blur', $data['lazyLoading']['placeholder']);
        $this->assertSame(3, $data['lazyLoading']['excludeCount']);
        $this->assertSame(2, $data['performance']['preloadCount']);
    }

    public function testConfigJsonEscapesHtmlSensitiveCharacters(): void
    {
        $raw = '</script><b a=\'1\' & "x">';
        $json = $this->helper(['lazy_loading/placeholder' => $raw])->getConfigJson();

        $this->assertStringNotContainsString('<', $json);
        $this->assertStringNotContainsString('>', $json);
        $this->assertStringNotContainsString('&', $json);
        $this->assertStringNotContainsString("'", $json);
        $this->assertStringContainsString('u003C' . chr(92) . '/script' . chr(92) . 'u003E', $json);
        $this->assertSame($raw, json_decode($json, true)['lazyLoading']['placeholder']);
    }

    public function testConfigJsonPassesStoreIdToEveryLookup(): void
    {
        $helper = $this->helper(['general/enabled@5' => '1', 'lazy_loading/enabled@5' => '1']);

        $data = json_decode($helper->getConfigJson(5), true);

        $this->assertTrue($data['enabled']);
        $this->assertTrue($data['lazyLoading']['enabled']);
        $this->assertNotEmpty($this->calls);
        foreach ($this->calls as $call) {
            $this->assertSame(5, $call[2], 'Lookup for ' . $call[0] . ' lost the store id');
        }
    }
}
