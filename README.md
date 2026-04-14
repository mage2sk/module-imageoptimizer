# Panth Image Optimizer for Magento 2

[![Magento 2.4.4 - 2.4.8](https://img.shields.io/badge/Magento-2.4.4%20--%202.4.8-orange)]()
[![PHP 8.1 - 8.4](https://img.shields.io/badge/PHP-8.1%20--%208.4-blue)]()
[![Hyva Compatible](https://img.shields.io/badge/Hyva-Compatible-green)]()
[![Luma Compatible](https://img.shields.io/badge/Luma-Compatible-green)]()

**Frontend image performance module** for Magento 2 -- lazy loading
(Native / Intersection Observer / Hybrid), browser-side WebP detection
with fallback, preload hints, async decoding, and fetchpriority
attributes. Improves Core Web Vitals (LCP, CLS) with zero server-side
image processing.

---

## Features

### Lazy Loading (3 strategies)
- **Native** -- adds `loading="lazy"` via a PHP after-plugin on
  `Magento\Catalog\Model\Product\Image`
- **Intersection Observer** -- JavaScript-based with configurable
  threshold, above-fold exclusion, fade-in effect, and placeholder
  types (blur / dominant color / spinner / SVG)
- **Hybrid** -- combines both for maximum browser coverage

### WebP Browser Detection (frontend-only)
- Detects browser WebP support via canvas probe
- Removes `<source type="image/webp">` tags when the browser cannot
  render WebP, so the fallback PNG/JPG loads instead
- Does NOT perform server-side WebP conversion

### Performance Hints
- **Preload critical images** -- dynamically injects
  `<link rel="preload" as="image">` for the first N images
- **Async decoding** -- adds `decoding="async"` to all images
- **Fetch priority** -- sets `fetchpriority="high"` on critical
  images (Chrome 101+)

### Admin Configuration
- Stores > Configuration > Panth Extensions > Image Optimizer
- Per-store-view configuration for all settings
- Debug mode logs optimization activity to the browser console

---

## Installation

### Via Composer (recommended)

```bash
composer require mage2kishan/module-imageoptimizer
bin/magento module:enable Panth_Core Panth_ImageOptimizer
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento setup:static-content:deploy -f
bin/magento cache:flush
```

### Via uploaded zip

1. Download the extension zip from the Marketplace
2. Extract to `app/code/Panth/ImageOptimizer`
3. Make sure `app/code/Panth/Core` is also installed
4. Run the same commands above starting from `module:enable`

### Verify

```bash
bin/magento module:status Panth_ImageOptimizer
# Module is enabled
```

---

## Requirements

| | Required |
|---|---|
| Magento | 2.4.4 -- 2.4.8 (Open Source / Commerce / Cloud) |
| PHP | 8.1 / 8.2 / 8.3 / 8.4 |
| `mage2kishan/module-core` | ^1.0 (installed automatically as a composer dependency) |

---

## Configuration

Open **Stores > Configuration > Panth Extensions > Image Optimizer**.

### General Settings
- **Enable Image Optimizer** -- master switch for all frontend image optimizations
- **Enable Debug Mode** -- log optimization activity to the browser console (disable in production)

### WebP Detection (Frontend Only)
- **Enable WebP Detection** -- detect browser WebP support via JavaScript
- **Enable Fallback Behavior** -- strip WebP source tags when unsupported

### Lazy Loading
- **Enable Lazy Loading** -- delay offscreen images until they approach the viewport
- **Loading Strategy** -- Native / Intersection Observer / Hybrid
- **Threshold (pixels)** -- IntersectionObserver rootMargin (default 300px)
- **Placeholder Type** -- None / Blur Effect (LQIP) / Dominant Color / Loading Spinner / SVG Placeholder
- **Enable Fade-In Effect** -- smooth opacity transition on load
- **Exclude Above-the-Fold Images** -- skip lazy loading for the first N images to improve LCP
- **Exclude First N Images** -- number of above-fold images to load immediately (default 3)

### Performance Settings
- **Preload Critical Images** -- inject `<link rel="preload">` for the first N images
- **Preload Image Count** -- number of images to preload (default 2)
- **Async Image Decoding** -- add `decoding="async"` to all images
- **Use fetchpriority Attribute** -- set `fetchpriority="high"` on critical images

---

## Support

| Channel | Contact |
|---|---|
| Email | kishansavaliyakb@gmail.com |
| Website | https://kishansavaliya.com |
| WhatsApp | +91 84012 70422 |

Response time: 1-2 business days for paid licenses.

---

## License

Commercial -- see `LICENSE.txt`. One license per Magento production
installation. Includes 12 months of free updates and email support.

---

## About the developer

Built and maintained by **Kishan Savaliya** -- https://kishansavaliya.com.
Builds high-quality Magento 2 extensions and themes for both Hyva and
Luma storefronts.
