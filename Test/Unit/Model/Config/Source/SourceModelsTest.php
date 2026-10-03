<?php
declare(strict_types=1);

namespace Panth\ImageOptimizer\Test\Unit\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Panth\ImageOptimizer\Model\Config\Source\LoadingStrategy;
use Panth\ImageOptimizer\Model\Config\Source\PlaceholderType;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class SourceModelsTest extends TestCase
{
    public static function sourceProvider(): array
    {
        return [
            'loading strategy' => [LoadingStrategy::class, ['native', 'intersection', 'hybrid']],
            'placeholder type' => [PlaceholderType::class, ['none', 'blur', 'color', 'spinner', 'svg']],
        ];
    }

    #[DataProvider('sourceProvider')]
    public function testOptionArrayListsTheExpectedValuesInOrder(string $class, array $values): void
    {
        $source = new $class();

        $this->assertInstanceOf(OptionSourceInterface::class, $source);
        $this->assertSame($values, array_column($source->toOptionArray(), 'value'));
    }

    #[DataProvider('sourceProvider')]
    public function testEveryOptionHasANonEmptyLabel(string $class, array $values): void
    {
        $options = (new $class())->toOptionArray();
        $this->assertCount(count($values), $options);
        foreach ($options as $option) {
            $this->assertSame(['value', 'label'], array_keys($option));
            $this->assertNotSame('', trim((string)$option['label']));
        }
    }

    #[DataProvider('sourceProvider')]
    public function testToArrayMatchesToOptionArray(string $class, array $values): void
    {
        $source = new $class();
        $flat = [];
        foreach ($source->toOptionArray() as $option) {
            $flat[$option['value']] = (string)$option['label'];
        }

        $this->assertSame($values, array_keys($source->toArray()));
        $this->assertSame($flat, array_map('strval', $source->toArray()));
    }

    public function testLoadingStrategyValuesMatchWhatThePluginsUnderstand(): void
    {
        $values = array_column((new LoadingStrategy())->toOptionArray(), 'value');

        $this->assertContains('native', $values);
        $this->assertContains('hybrid', $values);
    }

    public function testPlaceholderDefaultIsAValidOption(): void
    {
        $this->assertArrayHasKey('blur', (new PlaceholderType())->toArray());
    }
}
