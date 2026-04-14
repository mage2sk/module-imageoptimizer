# Panth Image Optimizer -- User Guide

This guide walks a Magento store administrator through every screen
and setting of the Panth Image Optimizer extension. No coding required.

---

## Table of contents

1. [Installation](#1-installation)
2. [Verifying the extension is active](#2-verifying-the-extension-is-active)
3. [General Settings](#3-general-settings)
4. [WebP Detection](#4-webp-detection)
5. [Lazy Loading](#5-lazy-loading)
6. [Performance Settings](#6-performance-settings)
7. [How it works](#7-how-it-works)
8. [Troubleshooting](#8-troubleshooting)

---

## 1. Installation

### Composer (recommended)

```bash
composer require mage2kishan/module-imageoptimizer
bin/magento module:enable Panth_Core Panth_ImageOptimizer
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento setup:static-content:deploy -f
bin/magento cache:flush
```

### Manual zip

1. Download the extension package zip
2. Extract to `app/code/Panth/ImageOptimizer`
3. Make sure `app/code/Panth/Core` is also present
4. Run the same `module:enable ... cache:flush` commands above

### Confirm

```bash
bin/magento module:status Panth_ImageOptimizer
# Module is enabled
```

---

## 2. Verifying the extension is active

After installation, two things should be true:

1. **Configuration page exists** -- Stores > Configuration > Panth Extensions > Image Optimizer is reachable
2. **Frontend script loads** -- view page source on the storefront and search for "Panth Image Optimizer" in the `<script>` tag near `</body>`

If either fails, see [Troubleshooting](#8-troubleshooting).

---

## 3. General Settings

Navigate to **Stores > Configuration > Panth Extensions > Image Optimizer**.

| Setting | Default | What it does |
|---|---|---|
| **Enable Image Optimizer** | No | Master switch for all frontend image optimizations. When set to No, the module is completely inactive. |
| **Enable Debug Mode** | No | When enabled, the frontend JavaScript logs all optimization activity to the browser console. Useful for verifying the module is working. Disable in production. |

---

## 4. WebP Detection

This is a **frontend-only** feature. It does NOT convert images to WebP
on the server. It detects whether the visitor's browser supports WebP
and removes unsupported `<source>` elements so the fallback image loads.

| Setting | Default | What it does |
|---|---|---|
| **Enable WebP Detection** | Yes | Detect browser WebP support via JavaScript canvas probe |
| **Enable Fallback Behavior** | Yes | When WebP is not supported, strip `<source type="image/webp">` tags from `<picture>` elements so the browser loads the fallback PNG/JPG `<img>` |

### When is this useful?

If your store serves WebP images via `<picture>` elements (common with
Hyva themes and WebP conversion modules), this feature ensures older
browsers that cannot render WebP gracefully fall back to the original
format instead of showing a broken image.

---

## 5. Lazy Loading

Lazy loading defers the loading of offscreen images until they are about
to enter the viewport. This reduces initial page weight and improves
LCP (Largest Contentful Paint).

| Setting | Default | What it does |
|---|---|---|
| **Enable Lazy Loading** | Yes | Master switch for all lazy loading features |
| **Loading Strategy** | Native | See strategy comparison below |
| **Threshold (pixels)** | 300 | Start loading images this many pixels before they enter the viewport. Only applies to Intersection Observer strategy. |
| **Placeholder Type** | Blur Effect (LQIP) | Visual placeholder shown while an image is loading. Only applies to Intersection Observer strategy. Options: None, Blur Effect (LQIP), Dominant Color, Loading Spinner, SVG Placeholder |
| **Enable Fade-In Effect** | Yes | Smoothly fade images in when they finish loading. Only applies to Intersection Observer strategy. |
| **Exclude Above-the-Fold Images** | Yes | Skip lazy loading for the first N images on the page to improve LCP |
| **Exclude First N Images** | 3 | Number of images at the top of the page to load immediately (not lazy loaded) |

### Strategy comparison

| Strategy | How it works | Best for |
|---|---|---|
| **Native** | Adds `loading="lazy"` attribute via PHP plugin. The browser handles all loading decisions. | Maximum compatibility, minimal overhead |
| **Intersection Observer** | JavaScript watches `data-src` images and swaps to real `src` when they approach the viewport. Supports fade-in, placeholders, and above-fold exclusion. | Fine-grained control, visual effects |
| **Hybrid** | Combines both -- `loading="lazy"` attribute AND Intersection Observer. | Belt-and-suspenders approach |

---

## 6. Performance Settings

These settings add browser hints that improve Core Web Vitals scores.

| Setting | Default | What it does |
|---|---|---|
| **Preload Critical Images** | Yes | Dynamically inject `<link rel="preload" as="image">` tags for the first N images on the page to improve LCP |
| **Preload Image Count** | 2 | Number of images to preload (recommended: 1-2) |
| **Async Image Decoding** | Yes | Add `decoding="async"` to all images so the browser decodes them off the main thread |
| **Use fetchpriority Attribute** | Yes | Set `fetchpriority="high"` on critical images to hint the browser to download them first (Chrome 101+) |

---

## 7. How it works

### Server-side (PHP plugin)

The module registers an after-plugin on
`Magento\Catalog\Model\Product\Image::toHtml()`. When the Native or
Hybrid lazy loading strategy is selected, the plugin injects
`loading="lazy"` into `<img>` tags that do not already have a `loading`
attribute.

### Client-side (JavaScript)

A `<script>` block is injected before `</body>` on every frontend page.
It runs the following steps in order:

1. **WebP detection** -- probes canvas WebP support and optionally
   removes unsupported `<source>` elements
2. **Lazy loading** -- logs native lazy images, and/or sets up an
   IntersectionObserver for `data-src` images with fade-in and
   above-fold exclusion
3. **Performance hints** -- injects preload links, sets
   `decoding="async"`, and applies `fetchpriority="high"`

---

## 8. Troubleshooting

| Symptom | Likely cause | Fix |
|---|---|---|
| No effect on the storefront | Module disabled | Check Stores > Configuration > Panth Extensions > Image Optimizer > General Settings > Enable Image Optimizer = Yes, then flush cache |
| Images not lazy loading | Strategy mismatch | If using Intersection Observer strategy, images need `data-src` attributes. Native strategy only adds `loading="lazy"` to product images via the PHP plugin. |
| Debug messages not appearing | Debug mode off | Enable Debug Mode in General Settings and open browser DevTools console |
| WebP fallback not working | WebP detection disabled | Enable WebP Detection AND Enable Fallback Behavior |
| Above-fold images loading slowly | Too many images excluded from preload | Increase Preload Image Count and decrease Exclude First N Images |
| Configuration page not visible | Module not enabled or DI not compiled | Run `bin/magento module:enable Panth_Core Panth_ImageOptimizer && bin/magento setup:di:compile && bin/magento cache:flush` |

---

## Support

For all questions, bug reports, or feature requests:

- **Email:** kishansavaliyakb@gmail.com
- **Website:** https://kishansavaliya.com
- **WhatsApp:** +91 84012 70422

Response time: 1-2 business days for paid licenses.
