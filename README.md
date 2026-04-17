<!-- SEO Meta -->
<!--
  Title: Panth Image Optimizer for Magento 2 - Lazy Loading, WebP Detection & Core Web Vitals | Panth Infotech
  Description: Panth Image Optimizer is a Magento 2 frontend image performance extension that adds native + IntersectionObserver lazy loading, WebP browser detection with PNG/JPG fallback, preload hints, async decoding, and fetchpriority to boost Core Web Vitals and LCP. Compatible with Magento 2.4.4 - 2.4.8 and PHP 8.1 - 8.4. Built by Top Rated Plus Magento developer Kishan Savaliya.
  Keywords: magento 2 image optimization, magento 2 lazy loading, webp magento, magento 2 webp fallback, image performance magento, core web vitals images, magento 2 LCP, magento 2 fetchpriority, magento 2 preload images, hyva image optimizer
  Author: Kishan Savaliya (Panth Infotech)
  Canonical: https://github.com/mage2sk/module-imageoptimizer
-->

# Panth Image Optimizer for Magento 2 — Lazy Loading, WebP Detection & Core Web Vitals Booster

[![Magento 2.4.4 - 2.4.8](https://img.shields.io/badge/Magento-2.4.4%20--%202.4.8-orange?logo=magento&logoColor=white)](https://magento.com)
[![PHP 8.1 - 8.4](https://img.shields.io/badge/PHP-8.1%20--%208.4-blue?logo=php&logoColor=white)](https://php.net)
[![Hyva Compatible](https://img.shields.io/badge/Hyva-Compatible-14b8a6)](https://www.hyva.io/)
[![Packagist](https://img.shields.io/badge/Packagist-mage2kishan%2Fmodule--imageoptimizer-orange?logo=packagist&logoColor=white)](https://packagist.org/packages/mage2kishan/module-imageoptimizer)
[![GitHub](https://img.shields.io/badge/GitHub-mage2sk%2Fmodule--imageoptimizer-181717?logo=github&logoColor=white)](https://github.com/mage2sk/module-imageoptimizer)
[![Upwork Top Rated Plus](https://img.shields.io/badge/Upwork-Top%20Rated%20Plus-14a800?logo=upwork&logoColor=white)](https://www.upwork.com/freelancers/~016dd1767321100e21)
[![Panth Infotech Agency](https://img.shields.io/badge/Agency-Panth%20Infotech-14a800?logo=upwork&logoColor=white)](https://www.upwork.com/agencies/1881421506131960778/)
[![Get a Quote](https://img.shields.io/badge/Get%20a%20Quote-Free%20Estimate-DC2626)](https://kishansavaliya.com/get-quote)

> **Frontend image performance for Magento 2** — lazy loading (Native / IntersectionObserver / Hybrid), WebP browser detection with PNG/JPG fallback, `<link rel="preload">` hints for critical images, async decoding, and `fetchpriority="high"` for LCP candidates. Compatible with Magento 2.4.4 – 2.4.8, PHP 8.1 – 8.4, Hyva and Luma themes.

**Panth Image Optimizer** is a lightweight, frontend-focused Magento 2 extension that improves **image delivery performance** and directly targets **Core Web Vitals** (LCP, CLS, INP). It ships three complementary lazy-loading strategies — **Native** (`loading="lazy"` attribute injected via PHP plugin), **IntersectionObserver** (JS-based with fade-in, configurable threshold, and above-the-fold exclusion), and **Hybrid** (both together for maximum coverage). It also includes **client-side WebP detection**: when the visitor's browser cannot render WebP, `<source type="image/webp">` elements are stripped from `<picture>` tags so the browser falls back to PNG/JPG automatically. On top of that, the module injects `<link rel="preload" as="image">` for critical above-the-fold images, sets `decoding="async"` on all images so the browser decodes them off the main thread, and applies `fetchpriority="high"` to LCP candidates (Chrome 101+).

This module does **not** perform server-side WebP conversion, CDN rewriting, or srcset/sizes generation — it is purely a frontend performance layer that works on top of whatever image pipeline you already have. Perfect for merchants who want measurable Core Web Vitals improvements without changing their storage, CDN, or image-generation setup.

---

## 🚀 Need Custom Magento 2 Image / Performance Work?

> **Get a free quote in 24 hours** — Core Web Vitals audits, image pipeline optimization, CDN integration, Hyva performance tuning, and custom module development.

<p align="center">
  <a href="https://kishansavaliya.com/get-quote">
    <img src="https://img.shields.io/badge/Get%20a%20Free%20Quote%20%E2%86%92-Reply%20within%2024%20hours-DC2626?style=for-the-badge" alt="Get a Free Quote" />
  </a>
</p>

<table>
<tr>
<td width="50%" align="center">

### 🏆 Kishan Savaliya
**Top Rated Plus on Upwork**

[![Hire on Upwork](https://img.shields.io/badge/Hire%20on%20Upwork-Top%20Rated%20Plus-14a800?style=for-the-badge&logo=upwork&logoColor=white)](https://www.upwork.com/freelancers/~016dd1767321100e21)

100% Job Success • 10+ Years Magento Experience
Adobe Certified • Hyva Specialist

</td>
<td width="50%" align="center">

### 🏢 Panth Infotech Agency
**Magento Development Team**

[![Visit Agency](https://img.shields.io/badge/Visit%20Agency-Panth%20Infotech-14a800?style=for-the-badge&logo=upwork&logoColor=white)](https://www.upwork.com/agencies/1881421506131960778/)

Custom Modules • Theme Design • Migrations
Performance • SEO • Adobe Commerce Cloud

</td>
</tr>
</table>

**Visit our website:** [kishansavaliya.com](https://kishansavaliya.com) &nbsp;|&nbsp; **Get a quote:** [kishansavaliya.com/get-quote](https://kishansavaliya.com/get-quote)

---

## Table of Contents

- [Key Features](#key-features)
- [How It Works](#how-it-works)
- [Compatibility](#compatibility)
- [Installation](#installation)
- [Configuration](#configuration)
- [Lazy Loading Strategies](#lazy-loading-strategies)
- [WebP Detection & Fallback](#webp-detection--fallback)
- [Performance Hints](#performance-hints)
- [FAQ](#faq)
- [Support](#support)
- [About Panth Infotech](#about-panth-infotech)
- [Quick Links](#quick-links)

---

## Key Features

### Lazy Loading (Three Strategies)

- **Native lazy loading** — `loading="lazy"` attribute injected via PHP plugin on product images (zero JS required, best compatibility)
- **IntersectionObserver** — JavaScript-based lazy loading with fade-in effect, configurable pixel threshold, and placeholder support
- **Hybrid mode** — combine both strategies for maximum browser coverage
- **Above-the-fold exclusion** — skip lazy loading for the first N images to protect LCP
- **Configurable threshold** — start loading images N pixels before they enter the viewport
- **Placeholder types** — blur, solid color, or SVG placeholder while images load
- **Fade-in effect** — smooth opacity transition when images finish loading

### WebP Browser Detection (Frontend Only)

- **Client-side WebP support check** — detects whether the visitor's browser can render WebP
- **Automatic fallback** — when WebP is unsupported, strips `<source type="image/webp">` from `<picture>` elements so the browser loads PNG/JPG fallbacks
- **No server-side conversion** — works on top of any existing image pipeline (use a CDN or dedicated module for conversion)
- **Zero layout shift** — fallback logic runs before image decode

### Core Web Vitals Boosters

- **Preload critical images** — dynamically injects `<link rel="preload" as="image">` for the first N images on the page (LCP booster)
- **Async decoding** — adds `decoding="async"` to all images so decode work happens off the main thread (improves INP)
- **fetchpriority="high"** — hints Chrome 101+ to prioritize LCP candidates in the network queue
- **Configurable preload count** — recommended 1–2 images for product and category pages

### Admin & Developer Experience

- **Unified admin config** — lives under `Stores → Configuration → Panth Extensions → Image Optimizer`
- **Debug mode** — logs optimization activity to the browser console for troubleshooting
- **Store-scope aware** — all settings respect default → website → store view hierarchy
- **MEQP compliant** — passes Adobe's Magento Extension Quality Program checks
- **Hyva + Luma ready** — works on both storefronts out of the box

---

## How It Works

Panth Image Optimizer operates purely on the **frontend layer**:

1. A PHP plugin on Magento's image factory/renderer adds `loading="lazy"`, `decoding="async"`, and `fetchpriority` attributes to product image markup during HTML generation.
2. A small JavaScript bundle is injected on every storefront page. It:
   - Runs a WebP feature test (`canvas.toDataURL('image/webp')`) and caches the result
   - Strips unsupported `<source>` tags when WebP is not available
   - Sets up an `IntersectionObserver` that swaps `data-src` → `src` when images near the viewport
   - Injects `<link rel="preload" as="image">` for the first N critical images
3. All behavior is gated by admin settings — disable any piece to fall back to Magento's default rendering.

**What this module does NOT do:**
- No server-side WebP generation (use a dedicated image CDN or conversion module)
- No responsive image (`srcset` / `sizes`) rewriting
- No CDN URL rewriting

---

## Compatibility

| Requirement | Versions Supported |
|---|---|
| Magento Open Source | 2.4.4, 2.4.5, 2.4.6, 2.4.7, 2.4.8 |
| Adobe Commerce | 2.4.4, 2.4.5, 2.4.6, 2.4.7, 2.4.8 |
| Adobe Commerce Cloud | 2.4.4 — 2.4.8 |
| PHP | 8.1.x, 8.2.x, 8.3.x, 8.4.x |
| Hyva Theme | 1.3+ (fully compatible) |
| Luma Theme | Native support |
| Required Dependency | [`mage2kishan/module-core`](https://packagist.org/packages/mage2kishan/module-core) (free) |

Tested on:
- Magento 2.4.8-p4 with PHP 8.4 + Hyva 1.3
- Magento 2.4.7 with PHP 8.3 + Luma
- Magento 2.4.6 with PHP 8.2 + Hyva

---

## Installation

### Composer Installation (Recommended)

```bash
composer require mage2kishan/module-imageoptimizer
bin/magento module:enable Panth_Core Panth_ImageOptimizer
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento setup:static-content:deploy -f
bin/magento cache:flush
```

### Manual Installation via ZIP

1. Download the latest release ZIP from [Packagist](https://packagist.org/packages/mage2kishan/module-imageoptimizer) or the [Adobe Commerce Marketplace](https://commercemarketplace.adobe.com).
2. Extract to `app/code/Panth/ImageOptimizer/` in your Magento installation.
3. Ensure `Panth_Core` is installed (free dependency).
4. Run the commands above starting from `bin/magento module:enable`.

### Verify Installation

```bash
bin/magento module:status Panth_ImageOptimizer
# Expected output: Module is enabled
```

Then navigate to:
```
Admin → Stores → Configuration → Panth Extensions → Image Optimizer
```

---

## Configuration

All settings are at `Stores → Configuration → Panth Extensions → Image Optimizer`.

### General Settings

| Setting | Default | Description |
|---|---|---|
| Enable Image Optimizer | Yes | Master switch for all frontend optimizations. |
| Enable Debug Mode | No | Log optimization activity to the browser console. Disable in production. |

### WebP Detection (Frontend Only)

| Setting | Default | Description |
|---|---|---|
| Enable WebP Detection | Yes | Detect browser WebP support via JavaScript. |
| Enable Fallback Behavior | Yes | Strip `<source type="image/webp">` tags when WebP is unsupported so the browser loads PNG/JPG. |

### Lazy Loading

| Setting | Default | Description |
|---|---|---|
| Enable Lazy Loading | Yes | Defer offscreen images until they approach the viewport. |
| Loading Strategy | Hybrid | Native / IntersectionObserver / Hybrid. |
| Threshold (pixels) | 200 | Start loading images N pixels before viewport (IntersectionObserver only). |
| Placeholder Type | Blur | Visual placeholder while an image loads (IntersectionObserver only). |
| Enable Fade-In Effect | Yes | Smooth opacity transition when images finish loading. |
| Exclude Above-the-Fold Images | Yes | Skip lazy loading for the first N images (protects LCP). |
| Exclude First N Images | 3 | Number of images at the top of the page to load immediately. |

### Performance Settings

| Setting | Default | Description |
|---|---|---|
| Preload Critical Images | Yes | Inject `<link rel="preload" as="image">` for the first N images. |
| Preload Image Count | 1 | Number of images to preload (recommended: 1–2). |
| Async Image Decoding | Yes | Add `decoding="async"` to all images. |
| Use fetchpriority Attribute | Yes | Set `fetchpriority="high"` on LCP candidates (Chrome 101+). |

---

## Lazy Loading Strategies

### Native (`loading="lazy"`)

The simplest and most compatible approach. A PHP plugin injects the `loading="lazy"` attribute on product images during HTML rendering. No JavaScript required, supported in all modern browsers.

**Best for:** stores with minimal JS overhead, Hyva storefronts, or sites that prioritize server-rendered HTML.

### IntersectionObserver (JS-based)

A JavaScript loop watches images and swaps `data-src` → `src` when they near the viewport. Supports fade-in effects, placeholders, and configurable thresholds.

**Best for:** stores that want a smooth fade-in UX, older browsers, or granular control over when images load.

### Hybrid (Both)

Applies native `loading="lazy"` as the baseline and layers IntersectionObserver on top for enhanced UX (fade-in, blur placeholder). Maximum compatibility and UX.

**Best for:** most production stores — recommended default.

---

## WebP Detection & Fallback

On page load, a short JavaScript snippet tests WebP support:

```js
const webp = new Image();
webp.onload = webp.onerror = () => {
  document.documentElement.classList.toggle('webp', webp.height === 2);
};
webp.src = 'data:image/webp;base64,UklGRh4AAABXRUJQVlA4TBEAAAAvAAAAAAfQ//73v/+BiOh/AAA=';
```

When WebP is **not** supported, the module walks the DOM and removes `<source type="image/webp">` elements from `<picture>` tags. The browser then loads the `<img>` fallback (PNG/JPG) automatically.

> **Note:** this module does **not** generate WebP files on the server. Use a dedicated module (e.g., `yireo/magento2-webp2`) or a CDN (Cloudflare Polish, Fastly IO, Bunny Optimizer) to produce WebP sources. Panth Image Optimizer handles the **client-side fallback** layer.

---

## Performance Hints

### Preload Critical Images

On every page, the module injects `<link rel="preload" as="image">` tags into the `<head>` for the first N images (configurable). This tells the browser to fetch LCP candidates in parallel with the HTML, dramatically reducing LCP on product and category pages.

### Async Decoding

All images receive `decoding="async"`, which tells the browser to decode images off the main thread. This reduces blocking time and improves INP (Interaction to Next Paint).

### fetchpriority

LCP candidates receive `fetchpriority="high"`, hinting the browser (Chrome 101+) to prioritize them in the network queue ahead of lower-priority assets. Combined with preload, this is the single biggest LCP win available today.

---

## FAQ

### Does this module convert my images to WebP on the server?

No. This module only handles **client-side** WebP detection and fallback. For server-side WebP generation, use a dedicated conversion module or an image CDN (Cloudflare Polish, Fastly IO, Bunny Optimizer).

### Will lazy loading hurt my LCP score?

Only if configured incorrectly. This module ships with **Exclude Above-the-Fold Images** enabled by default (first 3 images skip lazy loading) and also supports **preload** + **fetchpriority** for LCP candidates. Properly configured, lazy loading improves overall Core Web Vitals without hurting LCP.

### Does this work with Hyva?

Yes. All features work on both Hyva and Luma storefronts. Hyva's image component already uses `loading="lazy"` natively — this module adds the IntersectionObserver layer, WebP fallback, preload hints, and fetchpriority on top.

### Will it conflict with my CDN's image optimizer?

No. This module only manipulates HTML attributes and DOM elements — it does not rewrite image URLs, generate files, or intercept HTTP requests. It works seamlessly with Cloudflare, Fastly, Bunny, KeyCDN, and any other CDN.

### Can I disable individual features?

Yes. Every feature (lazy loading, WebP detection, preload, async decode, fetchpriority) has its own admin toggle. Enable only what you need.

### Does it support multi-store setups?

Yes. All settings respect Magento's standard scope hierarchy (default → website → store view).

### Which lazy loading strategy should I pick?

**Hybrid** is recommended for most stores — it combines the compatibility of native with the UX of IntersectionObserver. Pick **Native** if you want zero JS. Pick **IntersectionObserver** if you need fade-in/blur placeholders without native behavior.

### Is the source code available?

Yes. The full source is on GitHub at [github.com/mage2sk/module-imageoptimizer](https://github.com/mage2sk/module-imageoptimizer).

---

## Support

| Channel | Contact |
|---|---|
| Email | kishansavaliyakb@gmail.com |
| Website | [kishansavaliya.com](https://kishansavaliya.com) |
| WhatsApp | +91 84012 70422 |
| GitHub Issues | [github.com/mage2sk/module-imageoptimizer/issues](https://github.com/mage2sk/module-imageoptimizer) |
| Upwork (Top Rated Plus) | [Hire Kishan Savaliya](https://www.upwork.com/freelancers/~016dd1767321100e21) |
| Upwork Agency | [Panth Infotech](https://www.upwork.com/agencies/1881421506131960778/) |

Response time: 1–2 business days.

### 💼 Need a Core Web Vitals Audit or Custom Performance Work?

<p align="center">
  <a href="https://kishansavaliya.com/get-quote">
    <img src="https://img.shields.io/badge/%F0%9F%92%AC%20Get%20a%20Free%20Quote-kishansavaliya.com%2Fget--quote-DC2626?style=for-the-badge" alt="Get a Free Quote" />
  </a>
</p>

<p align="center">
  <a href="https://www.upwork.com/freelancers/~016dd1767321100e21">
    <img src="https://img.shields.io/badge/Hire%20Kishan-Top%20Rated%20Plus-14a800?style=for-the-badge&logo=upwork&logoColor=white" alt="Hire on Upwork" />
  </a>
  &nbsp;&nbsp;
  <a href="https://www.upwork.com/agencies/1881421506131960778/">
    <img src="https://img.shields.io/badge/Visit-Panth%20Infotech%20Agency-14a800?style=for-the-badge&logo=upwork&logoColor=white" alt="Visit Agency" />
  </a>
  &nbsp;&nbsp;
  <a href="https://kishansavaliya.com">
    <img src="https://img.shields.io/badge/Visit%20Website-kishansavaliya.com-0D9488?style=for-the-badge" alt="Visit Website" />
  </a>
</p>

---

## About Panth Infotech

Built and maintained by **Kishan Savaliya** — [kishansavaliya.com](https://kishansavaliya.com) — a **Top Rated Plus** Magento developer on Upwork with 10+ years of eCommerce experience.

**Panth Infotech** is a Magento 2 development agency specializing in high-quality, security-focused extensions and themes for both Hyva and Luma storefronts. Our extension suite covers SEO, performance, checkout, product presentation, customer engagement, and store management — over 34 modules built to MEQP standards and tested across Magento 2.4.4 to 2.4.8.

Browse the full catalog on the [Adobe Commerce Marketplace](https://commercemarketplace.adobe.com) or [Packagist](https://packagist.org/packages/mage2kishan/).

---

## Quick Links

- 🌐 **Website:** [kishansavaliya.com](https://kishansavaliya.com)
- 💬 **Get a Quote:** [kishansavaliya.com/get-quote](https://kishansavaliya.com/get-quote)
- 👨‍💻 **Upwork (Top Rated Plus):** [upwork.com/freelancers/~016dd1767321100e21](https://www.upwork.com/freelancers/~016dd1767321100e21)
- 🏢 **Upwork Agency:** [upwork.com/agencies/1881421506131960778](https://www.upwork.com/agencies/1881421506131960778/)
- 📦 **Packagist:** [packagist.org/packages/mage2kishan/module-imageoptimizer](https://packagist.org/packages/mage2kishan/module-imageoptimizer)
- 🐙 **GitHub:** [github.com/mage2sk/module-imageoptimizer](https://github.com/mage2sk/module-imageoptimizer)
- 🛒 **Adobe Marketplace:** [commercemarketplace.adobe.com](https://commercemarketplace.adobe.com)
- 📧 **Email:** kishansavaliyakb@gmail.com
- 📱 **WhatsApp:** +91 84012 70422

---

<p align="center">
  <strong>Ready to make your Magento 2 store faster?</strong><br/>
  <a href="https://kishansavaliya.com/get-quote">
    <img src="https://img.shields.io/badge/%F0%9F%9A%80%20Get%20Started%20%E2%86%92-Free%20Quote%20in%2024h-DC2626?style=for-the-badge" alt="Get Started" />
  </a>
</p>

---

**SEO Keywords:** magento 2 image optimization, magento 2 lazy loading, webp magento, magento 2 webp fallback, magento 2 webp detection, image performance magento, core web vitals images, magento 2 LCP optimization, magento 2 fetchpriority, magento 2 preload images, magento 2 async decoding, magento 2 intersectionobserver lazy loading, magento 2 native lazy loading, hyva image optimizer, luma image optimizer, magento 2 picture element, magento 2 image performance extension, magento 2 core web vitals module, magento 2.4.8 image optimization, php 8.4 magento image, magento 2 page speed, magento 2 LCP booster, panth image optimizer, panth infotech, mage2kishan, mage2sk, hire magento performance developer, top rated plus magento freelancer, kishan savaliya magento, magento 2 image lazy load plugin, magento 2 WebP browser detection, magento 2 image fade-in, magento 2 image placeholder, magento 2 above the fold images, magento 2 image preload link
