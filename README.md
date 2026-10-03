# Magento 2 Image Optimizer

Panth Image Optimizer adds frontend image performance controls to a Magento 2 storefront. It adds `loading="lazy"` or `loading="eager"` to `<img>` tags while the page HTML is rendered, marks the first image on the page with `fetchpriority="high"` when "Use fetchpriority Attribute" is Yes, and injects a small inline script at the end of the body that detects browser WebP support, optionally lazy loads `data-src` images with an IntersectionObserver, injects `<link rel="preload">` hints for the first images on the page, and sets `decoding="async"` on all images.

The module only changes HTML attributes and the DOM. It does not convert images to WebP on the server, does not generate resized images or `srcset` values, and does not rewrite image URLs. It is intended for store owners and developers who want to tune Largest Contentful Paint and image loading behaviour from the admin without changing the theme. It works with both Hyva and Luma based themes: product images rendered through Magento's catalog image block are marked `loading="lazy"` by one plugin, and a second plugin on the layout output decides eager or lazy for every `<img>` in the final page HTML (including Hyva templates) in document order.

Product page: [kishansavaliya.com/magento-2-imageoptimizer.html](https://kishansavaliya.com/magento-2-imageoptimizer.html)

## Features

- Adds `loading="lazy"` to `<img>` tags that do not already carry a `loading` attribute, during server-side rendering (strategies "Native" and "Hybrid"). `<img>` text inside `<script>`, `<style>`, `<template>`, `<textarea>`, `<noscript>` and HTML comments is left unchanged and not counted.
- Keeps the first N images on the page eager (`loading="eager"`) and gives the very first one `fetchpriority="high"` (only when "Use fetchpriority Attribute" is Yes); N is configurable and counted in document order over the final page HTML, so header images such as the logo come before product list images.
- Optional IntersectionObserver lazy loading for images that carry `data-src` / `data-srcset` attributes, with a configurable root margin, above-the-fold exclusion and a fade-in effect (strategies "Intersection Observer" and "Hybrid").
- Browser WebP detection via a canvas probe; when WebP is unsupported, `<source type="image/webp">` elements inside `<picture>` are removed so the browser loads the fallback `<img>`.
- Injects `<link rel="preload" as="image">` into `<head>` for the first N `<img>` elements found in the DOM, optionally with `fetchpriority="high"`.
- Sets `decoding="async"` on every `<img>` element.
- Debug mode that logs what the script did to the browser console.
- Every option is scoped per default, website and store view.
- Admin menu entry "Image Optimizer" > "Configuration" under "Panth Extensions", protected by its own ACL resource.
- Unit tests for the config helper and the product image plugin under `Test/Unit`.

## Compatibility

| Platform | Versions |
|---|---|
| Magento Open Source | 2.4.4, 2.4.5, 2.4.6, 2.4.7, 2.4.8 (as published on the product page) |
| Adobe Commerce | 2.4.4, 2.4.5, 2.4.6, 2.4.7, 2.4.8 (as published on the product page) |
| PHP | 8.1 or newer (`composer.json`: `php >=8.1`) |
| Themes | Hyva and Luma |

Composer constraints on Magento packages: `magento/framework ^103.0`, `magento/module-store ^101.0`, `magento/module-catalog ^103.0 || ^104.0`.

## Requirements

- Magento Open Source or Adobe Commerce 2.4.4 to 2.4.8
- PHP 8.1 or newer with the `json` extension (`ext-json`)
- `mage2kishan/module-core` `^1.0` (module `Panth_Core`); it provides the "Panth Extensions" admin tab, the parent ACL resource `Panth_Core::panth_extensions` and the `Panth\Core\Helper\AbstractConfig` base class the helper extends
- Development only (`require-dev`): `phpunit/phpunit ^9.5 || ^10.0`, `squizlabs/php_codesniffer ^3.7`

No PHP image extension (GD, Imagick) or system binary is used; the module never processes image files.

## Installation

```bash
composer require mage2kishan/module-imageoptimizer
bin/magento module:enable Panth_Core Panth_ImageOptimizer
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento cache:flush
```

`setup:di:compile` is only required in production mode. The module ships no files under `view/*/web`, so `setup:static-content:deploy` is not needed for it.

Check that the module is enabled:

```bash
bin/magento module:status Panth_ImageOptimizer
```

The module is disabled by default after installation ("Enable Image Optimizer" is set to No), so nothing changes on the storefront until you turn it on.

## Configuration

Admin path: Stores > Configuration > Panth Extensions > Image Optimizer. All settings can be set at default, website and store view scope. Config paths start with `panth_imageoptimizer/`.

### General Settings

| Setting | Default | What it does |
|---|---|---|
| Enable Image Optimizer (`general/enabled`) | No | Master switch. When off, both PHP plugins return the HTML unchanged and the frontend script is not printed. |
| Enable Debug Mode (`general/debug_mode`) | No | Logs the script's activity to the browser console with the prefix `[ImageOptimizer]`. Shown only when the module is enabled. |

### WebP Detection (Frontend Only)

| Setting | Default | What it does |
|---|---|---|
| Enable WebP Detection (`webp/enabled`) | Yes | Runs a canvas based WebP support check in the browser. |
| Enable Fallback Behavior (`webp/fallback_enabled`) | Yes | When WebP is not supported, removes `<source type="image/webp">` elements whose parent is a `<picture>` so the browser loads the fallback `<img>`. |

### Lazy Loading

| Setting | Default | What it does |
|---|---|---|
| Enable Lazy Loading (`lazy_loading/enabled`) | Yes | Turns the lazy loading plugins and script on. |
| Loading Strategy (`lazy_loading/loading_strategy`) | Native (loading="lazy" attribute) | Options: "Native (loading="lazy" attribute)", "Intersection Observer (JavaScript)", "Hybrid (Native + Intersection Observer)". Native and Hybrid enable the PHP plugins; Intersection Observer and Hybrid enable the JavaScript observer. |
| Threshold (pixels) (`lazy_loading/threshold`) | 300 | Used as the IntersectionObserver `rootMargin`, so images start loading this many pixels before entering the viewport. Intersection Observer and Hybrid only. |
| Placeholder Type (`lazy_loading/placeholder`) | Blur Effect (LQIP) | Options: "None", "Blur Effect (LQIP)", "Dominant Color", "Loading Spinner", "SVG Placeholder". The value is passed to the frontend script configuration, but the shipped script does not render any placeholder, so this setting currently has no visible effect. |
| Enable Fade-In Effect (`lazy_loading/fade_in`) | Yes | Sets opacity 0 and a 0.3 s opacity transition on an observed image, then fades it to 1 on load. Intersection Observer and Hybrid only. |
| Exclude Above-the-Fold Images (`lazy_loading/exclude_above_fold`) | Yes | The first N images are rendered with `loading="eager"` (PHP) or loaded immediately without observing (JavaScript). |
| Exclude First N Images (`lazy_loading/exclude_count`) | 3 | Value of N for the setting above. Shown only when "Exclude Above-the-Fold Images" is Yes. An empty value means 3; 0 keeps no image eager. |

### Performance Settings

| Setting | Default | What it does |
|---|---|---|
| Preload Critical Images (`performance/preload_critical_images`) | Yes | Appends `<link rel="preload" as="image" href="...">` to `<head>` for the first N `<img>` elements in the DOM, using their `src` or `data-src`. |
| Preload Image Count (`performance/preload_count`) | 2 | Value of N for the setting above. |
| Async Image Decoding (`performance/decode_async`) | Yes | Sets `decoding="async"` on every `<img>` element. |
| Use fetchpriority Attribute (`performance/fetchpriority`) | Yes | When Yes, the layout plugin adds `fetchpriority="high"` to the first eager image and the script sets `fetchPriority = "high"` on the preload links and on the preloaded images. When No, no fetchpriority is added. |

Default behaviour once "Enable Image Optimizer" is switched to Yes: all `<img>` tags after the first three get `loading="lazy"`, the first three stay eager with `fetchpriority="high"` on the first, WebP fallback stripping runs in unsupported browsers, the first two images are preloaded and every image gets `decoding="async"`.

## Usage

Everything happens at page render and page load time. No images are written to disk, no cron job runs and no console command exists.

### Server-side (PHP plugins), strategies Native and Hybrid

1. `Panth\ImageOptimizer\Plugin\Image::afterToHtml` runs after `Magento\Catalog\Model\Product\Image::toHtml()` (the Luma style catalog image block). Every `<img>` in the returned HTML without a `loading` attribute receives `loading="lazy"`. This plugin does not decide which images are above the fold.
2. `Panth\ImageOptimizer\Plugin\Layout\LazyLoadingPlugin::afterGetOutput` runs on `Magento\Framework\View\Layout::getOutput()` in the frontend area only. It walks every `<img ...>` tag in the final page HTML in document order, skipping `<script>`, `<style>`, `<template>`, `<textarea>`, `<noscript>` and HTML comments. Positions 1 to N (N = "Exclude First N Images", when exclusion is on) are eager: `loading="lazy"` is rewritten to `loading="eager"` and a missing `loading` becomes `loading="eager"`. Later images without a `loading` attribute get `loading="lazy"`. Any other existing `loading` value is kept. The first image gets `fetchpriority="high"` when "Use fetchpriority Attribute" is Yes and it has none.

The layout plugin resets and uses `Panth\ImageOptimizer\Service\RequestImageCounter` for each page output, so the "first N images" rule follows the order of the final HTML. Product images marked lazy by the first plugin are promoted to eager when they fall within the first N.

### Client-side (inline script)

`view/frontend/layout/default.xml` adds the block `panth.imageoptimizer.init` (`Panth\ImageOptimizer\Block\ImageOptimizer`, template `Panth_ImageOptimizer::image-optimizer.phtml`) to `before.body.end` on every frontend page. The template prints nothing when the module is disabled. Otherwise it prints an inline `<script>` with the configuration as JSON and runs, in this order:

1. WebP detection: `canvas.toDataURL('image/webp')`; if unsupported and fallback is on, `<source type="image/webp">` elements inside `<picture>` are removed.
2. Lazy loading: with "Native", it only logs how many `img[loading="lazy"]` elements exist. With "Intersection Observer" or "Hybrid", it selects `img[data-src]`, loads the first N immediately, and observes the rest with `rootMargin` set to the threshold; when an image intersects, `data-src` and `data-srcset` are copied to `src` and `srcset` and the data attributes removed. If the browser has no `IntersectionObserver`, it falls back to logging only. The module does not itself rewrite `src` to `data-src`; the theme or templates must provide `data-src` markup for this strategy to have an effect.
3. Preload: for the first N `<img>` elements in DOM order, a `<link rel="preload" as="image">` is appended to `<head>`.
4. Async decoding: `decoding = 'async'` on all images.

Formats are not changed: the browser receives whatever image files the theme or another module already serves.

## Developer Notes

- Module name: `Panth_ImageOptimizer` (loads after `Panth_Core`, `Magento_Catalog` and `Magento_Theme`)
- Composer package: `mage2kishan/module-imageoptimizer`, version 1.0.8
- PHP namespace: `Panth\ImageOptimizer`
- Configuration helper: `Panth\ImageOptimizer\Helper\Data` (extends `Panth\Core\Helper\AbstractConfig`); public getters for every setting plus `getConfigJson()` which builds the JSON passed to the script
- Block: `Panth\ImageOptimizer\Block\ImageOptimizer` with `isEnabled()`, `getConfigJson()` and `getHelper()`
- Plugins: `Panth\ImageOptimizer\Plugin\Image` (`etc/di.xml`, plugin name `panth_imageoptimizer_lazy_loading_product_image`, sortOrder 10) and `Panth\ImageOptimizer\Plugin\Layout\LazyLoadingPlugin` (`etc/frontend/di.xml`, plugin name `panth_imageoptimizer_lazy_loading_layout`, sortOrder 100)
- Shared service: `Panth\ImageOptimizer\Service\RequestImageCounter` with `increment()`, `current()` and `reset()`
- Config source models: `Panth\ImageOptimizer\Model\Config\Source\LoadingStrategy` (`native`, `intersection`, `hybrid`) and `Panth\ImageOptimizer\Model\Config\Source\PlaceholderType` (`none`, `blur`, `color`, `spinner`, `svg`)
- Backend model on the enable field: `Panth\ImageOptimizer\Model\Config\Backend\Enabled` (currently only delegates to the parent `beforeSave()`)
- ACL resources: `Panth_ImageOptimizer::imageoptimizer` ("Image Optimizer") and `Panth_ImageOptimizer::config` ("Configuration"), both under `Panth_Core::panth_extensions`
- Admin menu: `Panth_ImageOptimizer::group` and `Panth_ImageOptimizer::settings`, linking to `adminhtml/system_config/edit/section/panth_imageoptimizer`
- Translations: `i18n/en_US.csv`
- No database tables, no observers, no routes, no web API, no cron jobs and no console commands are declared
- To lazy load with the IntersectionObserver strategy, templates must output `<img data-src="..." data-srcset="...">`; the plugins do not generate this markup

## Uninstallation

```bash
bin/magento module:disable Panth_ImageOptimizer
composer remove mage2kishan/module-imageoptimizer
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento cache:flush
```

The module creates no database tables, so nothing is left in the schema. The values saved under `panth_imageoptimizer/*` remain in the `core_config_data` table until removed manually. Generated code under `generated/` is rebuilt by `setup:di:compile`. Remove `Panth_Core` as well only if no other Panth module depends on it.

## Support

- Product page: [kishansavaliya.com/magento-2-imageoptimizer.html](https://kishansavaliya.com/magento-2-imageoptimizer.html)
- Contact form: [kishansavaliya.com/contact](https://kishansavaliya.com/contact)
- Email: kishansavaliyakb@gmail.com
- GitHub issues: [github.com/mage2sk/module-imageoptimizer/issues](https://github.com/mage2sk/module-imageoptimizer/issues)

## Documentation

[USER_GUIDE.md](USER_GUIDE.md) covers installation (Composer and manual zip), verifying that the extension is active, each admin setting group (General, WebP Detection, Lazy Loading with a strategy comparison, Performance), how the server-side plugin and the client-side script work, and troubleshooting.

## License

Commercial software license. See [LICENSE.txt](LICENSE.txt) in this repository.

## Changelog

See [CHANGELOG.md](CHANGELOG.md).

## Links

- Website: [kishansavaliya.com](https://kishansavaliya.com)
- All extensions: [kishansavaliya.com/magento-extensions.html](https://kishansavaliya.com/magento-extensions.html)
- GitHub: [github.com/mage2sk/module-imageoptimizer](https://github.com/mage2sk/module-imageoptimizer)
- Packagist: [packagist.org/packages/mage2kishan/module-imageoptimizer](https://packagist.org/packages/mage2kishan/module-imageoptimizer)
