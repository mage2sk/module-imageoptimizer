<?php
/**
 * Copyright © Panth Infotech. All rights reserved.
 */
declare(strict_types=1);

namespace Panth\ImageOptimizer\Test\Unit\Helper;

use Panth\ImageOptimizer\Helper\Data;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

class DataTest extends TestCase
{
    /**
     * @var Data
     */
    private Data $helper;

    /**
     * @var ScopeConfigInterface|MockObject
     */
    private $scopeConfigMock;

    /**
     * Set up test fixtures
     */
    protected function setUp(): void
    {
        $this->scopeConfigMock = $this->getMockForAbstractClass(ScopeConfigInterface::class);

        $this->helper = new Data(
            $this->scopeConfigMock
        );
    }

    /**
     * Test isWebpEnabled returns true when both module and WebP detection are enabled
     *
     * @dataProvider webpEnabledDataProvider
     * @param bool $moduleEnabled
     * @param bool $webpEnabled
     * @param bool $expected
     * @return void
     */
    public function testIsWebpEnabled(bool $moduleEnabled, bool $webpEnabled, bool $expected): void
    {
        $this->scopeConfigMock->method('getValue')
            ->willReturnMap([
                [
                    'panth_core/general/enabled',
                    ScopeInterface::SCOPE_STORE,
                    null,
                    1
                ],
                [
                    'panth_imageoptimizer/general/enabled',
                    ScopeInterface::SCOPE_STORE,
                    null,
                    $moduleEnabled ? 1 : 0
                ],
                [
                    'panth_imageoptimizer/webp/enabled',
                    ScopeInterface::SCOPE_STORE,
                    null,
                    $webpEnabled ? 1 : 0
                ]
            ]);

        $result = $this->helper->isWebpEnabled();
        $this->assertSame($expected, $result);
    }

    /**
     * Test getLoadingStrategy returns configured strategy
     *
     * @dataProvider loadingStrategyDataProvider
     * @param string $strategy
     * @return void
     */
    public function testGetLoadingStrategy(string $strategy): void
    {
        $this->scopeConfigMock->method('getValue')
            ->with(
                'panth_imageoptimizer/lazy_loading/loading_strategy',
                ScopeInterface::SCOPE_STORE,
                null
            )
            ->willReturn($strategy);

        $result = $this->helper->getLoadingStrategy();
        $this->assertSame($strategy, $result);
    }

    /**
     * Test getLoadingStrategy returns default strategy when not configured
     *
     * @return void
     */
    public function testGetLoadingStrategyDefaultValue(): void
    {
        $this->scopeConfigMock->method('getValue')
            ->with(
                'panth_imageoptimizer/lazy_loading/loading_strategy',
                ScopeInterface::SCOPE_STORE,
                null
            )
            ->willReturn(null);

        $result = $this->helper->getLoadingStrategy();
        $this->assertSame('native', $result);
    }

    /**
     * Test isEnabled respects store ID parameter
     *
     * @return void
     */
    public function testIsEnabledWithStoreId(): void
    {
        $storeId = 2;
        $this->scopeConfigMock->method('getValue')
            ->willReturnMap([
                [
                    'panth_core/general/enabled',
                    ScopeInterface::SCOPE_STORE,
                    null,
                    1
                ],
                [
                    'panth_imageoptimizer/general/enabled',
                    ScopeInterface::SCOPE_STORE,
                    $storeId,
                    1
                ]
            ]);

        $result = $this->helper->isEnabled($storeId);
        $this->assertTrue($result);
    }

    /**
     * Test getThreshold returns configured threshold value
     *
     * @return void
     */
    public function testGetThreshold(): void
    {
        $threshold = 500;
        $this->scopeConfigMock->method('getValue')
            ->with(
                'panth_imageoptimizer/lazy_loading/threshold',
                ScopeInterface::SCOPE_STORE,
                null
            )
            ->willReturn($threshold);

        $result = $this->helper->getThreshold();
        $this->assertSame($threshold, $result);
    }

    /**
     * Test getThreshold returns default value when not configured
     *
     * @return void
     */
    public function testGetThresholdDefault(): void
    {
        $this->scopeConfigMock->method('getValue')
            ->with(
                'panth_imageoptimizer/lazy_loading/threshold',
                ScopeInterface::SCOPE_STORE,
                null
            )
            ->willReturn(null);

        $result = $this->helper->getThreshold();
        $this->assertSame(300, $result);
    }

    /**
     * Test getPlaceholderType returns configured placeholder type
     *
     * @dataProvider placeholderTypeDataProvider
     * @param string $type
     * @return void
     */
    public function testGetPlaceholderType(string $type): void
    {
        $this->scopeConfigMock->method('getValue')
            ->with(
                'panth_imageoptimizer/lazy_loading/placeholder',
                ScopeInterface::SCOPE_STORE,
                null
            )
            ->willReturn($type);

        $result = $this->helper->getPlaceholderType();
        $this->assertSame($type, $result);
    }

    /**
     * Test getPlaceholderType returns default value when not configured
     *
     * @return void
     */
    public function testGetPlaceholderTypeDefault(): void
    {
        $this->scopeConfigMock->method('getValue')
            ->with(
                'panth_imageoptimizer/lazy_loading/placeholder',
                ScopeInterface::SCOPE_STORE,
                null
            )
            ->willReturn(null);

        $result = $this->helper->getPlaceholderType();
        $this->assertSame('blur', $result);
    }

    /**
     * Test isFallbackEnabled returns boolean
     *
     * @return void
     */
    public function testIsFallbackEnabled(): void
    {
        $this->scopeConfigMock->method('getValue')
            ->with(
                'panth_imageoptimizer/webp/fallback_enabled',
                ScopeInterface::SCOPE_STORE,
                null
            )
            ->willReturn(1);

        $result = $this->helper->isFallbackEnabled();
        $this->assertTrue($result);
    }

    /**
     * Test getPreloadCount returns configured value
     *
     * @return void
     */
    public function testGetPreloadCount(): void
    {
        $this->scopeConfigMock->method('getValue')
            ->with(
                'panth_imageoptimizer/performance/preload_count',
                ScopeInterface::SCOPE_STORE,
                null
            )
            ->willReturn(3);

        $result = $this->helper->getPreloadCount();
        $this->assertSame(3, $result);
    }

    /**
     * Test getPreloadCount returns default when not configured
     *
     * @return void
     */
    public function testGetPreloadCountDefault(): void
    {
        $this->scopeConfigMock->method('getValue')
            ->with(
                'panth_imageoptimizer/performance/preload_count',
                ScopeInterface::SCOPE_STORE,
                null
            )
            ->willReturn(null);

        $result = $this->helper->getPreloadCount();
        $this->assertSame(2, $result);
    }

    /**
     * Test getExcludeCount returns configured value
     *
     * @return void
     */
    public function testGetExcludeCount(): void
    {
        $this->scopeConfigMock->method('getValue')
            ->with(
                'panth_imageoptimizer/lazy_loading/exclude_count',
                ScopeInterface::SCOPE_STORE,
                null
            )
            ->willReturn(5);

        $result = $this->helper->getExcludeCount();
        $this->assertSame(5, $result);
    }

    /**
     * Test getExcludeCount returns default when not configured
     *
     * @return void
     */
    public function testGetExcludeCountDefault(): void
    {
        $this->scopeConfigMock->method('getValue')
            ->with(
                'panth_imageoptimizer/lazy_loading/exclude_count',
                ScopeInterface::SCOPE_STORE,
                null
            )
            ->willReturn(null);

        $result = $this->helper->getExcludeCount();
        $this->assertSame(3, $result);
    }

    /**
     * Data provider for WebP enabled test cases
     *
     * @return array
     */
    public static function webpEnabledDataProvider(): array
    {
        return [
            'both_enabled' => [true, true, true],
            'module_disabled' => [false, true, false],
            'webp_disabled' => [true, false, false],
            'both_disabled' => [false, false, false],
        ];
    }

    /**
     * Data provider for loading strategy test cases
     *
     * @return array
     */
    public static function loadingStrategyDataProvider(): array
    {
        return [
            'native_strategy' => ['native'],
            'intersection_strategy' => ['intersection'],
            'hybrid_strategy' => ['hybrid'],
        ];
    }

    /**
     * Data provider for placeholder type test cases
     *
     * @return array
     */
    public static function placeholderTypeDataProvider(): array
    {
        return [
            'blur_placeholder' => ['blur'],
            'color_placeholder' => ['color'],
            'spinner_placeholder' => ['spinner'],
            'svg_placeholder' => ['svg'],
        ];
    }
}
