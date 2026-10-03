<?php
declare(strict_types=1);

namespace Panth\ImageOptimizer\Service;

class RequestImageCounter
{
    private int $count = 0;

    public function increment(): int
    {
        return ++$this->count;
    }

    public function current(): int
    {
        return $this->count;
    }

    public function reset(): void
    {
        $this->count = 0;
    }
}
