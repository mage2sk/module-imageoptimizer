<?php
declare(strict_types=1);

namespace Panth\ImageOptimizer\Test\Unit\Service;

use Panth\ImageOptimizer\Service\RequestImageCounter;
use PHPUnit\Framework\TestCase;

class RequestImageCounterTest extends TestCase
{
    public function testStartsAtZero(): void
    {
        $this->assertSame(0, (new RequestImageCounter())->current());
    }

    public function testIncrementReturnsTheNewPosition(): void
    {
        $counter = new RequestImageCounter();

        $this->assertSame(1, $counter->increment());
        $this->assertSame(2, $counter->increment());
        $this->assertSame(3, $counter->increment());
        $this->assertSame(3, $counter->current());
    }

    public function testCurrentDoesNotAdvance(): void
    {
        $counter = new RequestImageCounter();
        $counter->increment();

        $this->assertSame(1, $counter->current());
        $this->assertSame(1, $counter->current());
    }

    public function testResetStartsCountingAgain(): void
    {
        $counter = new RequestImageCounter();
        $counter->increment();
        $counter->increment();

        $counter->reset();

        $this->assertSame(0, $counter->current());
        $this->assertSame(1, $counter->increment());
    }

    public function testInstancesAreIndependent(): void
    {
        $first = new RequestImageCounter();
        $second = new RequestImageCounter();
        $first->increment();
        $first->increment();

        $this->assertSame(0, $second->current());
        $this->assertSame(1, $second->increment());
        $this->assertSame(2, $first->current());
    }
}
