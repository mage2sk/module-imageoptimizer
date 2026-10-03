<?php
declare(strict_types=1);

namespace Panth\ImageOptimizer\Plugin\Layout;

use Magento\Framework\View\LayoutInterface;
use Panth\ImageOptimizer\Helper\Data as ConfigHelper;
use Panth\ImageOptimizer\Service\RequestImageCounter;

class LazyLoadingPlugin
{
    private const TOKEN_PATTERN = '#<!--.*?-->'
        . '|<(script|style|template|textarea|noscript)\b(?:[^>"\']++|"[^"]*+"|\'[^\']*+\')*+>.*?</\1\s*>'
        . '|<img\b(?:[^>"\']++|"[^"]*+"|\'[^\']*+\')*+>#is';

    public function __construct(
        private readonly ConfigHelper $configHelper,
        private readonly RequestImageCounter $counter
    ) {
    }

    public function afterGetOutput(LayoutInterface $subject, $result)
    {
        if (!is_string($result) || $result === '') {
            return $result;
        }
        if (!$this->configHelper->isEnabled() || !$this->configHelper->isLazyLoadingEnabled()) {
            return $result;
        }
        $strategy = $this->configHelper->getLoadingStrategy();
        if ($strategy !== 'native' && $strategy !== 'hybrid') {
            return $result;
        }
        if (stripos($result, '<img') === false) {
            return $result;
        }

        $excludeAboveFold = $this->configHelper->shouldExcludeAboveFold();
        $excludeCount = max(0, $this->configHelper->getExcludeCount());
        $useFetchPriority = $this->configHelper->isFetchpriorityEnabled();
        $this->counter->reset();

        return preg_replace_callback(
            self::TOKEN_PATTERN,
            function (array $match) use ($excludeAboveFold, $excludeCount, $useFetchPriority): string {
                $tag = $match[0];
                if (!empty($match[1]) || strncmp($tag, '<!--', 4) === 0) {
                    return $tag;
                }

                $position = $this->counter->increment();
                $shouldBeEager = $excludeAboveFold && $position <= $excludeCount;

                $attributes = $this->parseAttributes($tag);
                $loading = $attributes['loading'] ?? null;
                $hasLoading = $loading !== null;
                $hasLazy = $hasLoading && strtolower(trim((string) $loading['value'])) === 'lazy';
                $hasFetchPriority = isset($attributes['fetchpriority']);

                if ($shouldBeEager) {
                    if ($hasLazy) {
                        $tag = substr_replace(
                            $tag,
                            $loading['space'] . 'loading="eager"',
                            $loading['offset'],
                            $loading['length']
                        );
                    } elseif (!$hasLoading) {
                        $tag = preg_replace('/^<img\b/i', '<img loading="eager"', $tag, 1) ?? $tag;
                    }
                    if ($position === 1 && $useFetchPriority && !$hasFetchPriority) {
                        $tag = preg_replace('/^<img\b/i', '<img fetchpriority="high"', $tag, 1) ?? $tag;
                    }
                    return $tag;
                }

                if (!$hasLoading) {
                    $tag = preg_replace('/^<img\b/i', '<img loading="lazy"', $tag, 1) ?? $tag;
                }
                return $tag;
            },
            $result
        ) ?? $result;
    }

    private function parseAttributes(string $tag): array
    {
        $attributes = [];
        $matches = [];
        preg_match_all(
            '/(\s+)([^\s=\/>"\']+)(?:\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|([^\s>"\']+)))?/',
            $tag,
            $matches,
            PREG_SET_ORDER | PREG_OFFSET_CAPTURE
        );
        foreach ($matches as $match) {
            $name = strtolower($match[2][0]);
            if (isset($attributes[$name])) {
                continue;
            }
            $value = '';
            foreach ([3, 4, 5] as $group) {
                if (isset($match[$group]) && $match[$group][1] !== -1) {
                    $value = $match[$group][0];
                    break;
                }
            }
            $attributes[$name] = [
                'value' => $value,
                'space' => substr($match[1][0], -1),
                'offset' => $match[0][1] + strlen($match[1][0]) - 1,
                'length' => strlen($match[0][0]) - strlen($match[1][0]) + 1,
            ];
        }

        return $attributes;
    }
}
