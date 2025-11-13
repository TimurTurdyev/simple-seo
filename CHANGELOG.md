# Changelog

## 2.3.0 - 2025-11-13

Commerce and listing builders: `Schema::productGroup()` (productGroupID, variesBy from short schema.org names or urls without repeats, hasVariant), `Schema::itemList()` (positions continue after `offset()`, repeated urls skipped, numberOfItems defaults to the item count), `Schema::videoObject()` (duration in seconds as `PT{n}S`, a single thumbnail as a string) and `Schema::brand()`. Offer gets `priceValidUntil()` (date only), `seller()`, `eligibleQuantity()` and the `Availability` enum; Product gets `model()`, `category()`, `color()`, `material()`, `size()`, `additionalProperty()` and `inProductGroupWithID()`, its `brand()` accepts a Brand. `Breadcrumbs::items()` adds a list of crumbs (`name`/`title`, `url`/`href`), `ratingValue()` turns a decimal comma into a dot, and every type exports plain nested arrays with `toArray()`. Existing output is unchanged, except a rating passed as "4,5".

## 2.2.0 - 2025-07-09

Head tags as data: `SeoManager::tags()` (list of `{tag, attrs, text?}`) and `SeoManager::toArray()` (title, description, canonical, robots, openGraph, twitter, jsonLd, tags), plus `tags()` on every section, `MetaBuilder::robotsDirective()` and `JsonLdBuilder::payload()`. `render()` now prints the same `Tag` objects, so markup and data cannot disagree; output is unchanged.

## 2.1.0 - 2025-02-15

Added QAPage, Question and Answer schema builders. answerCount is derived from the marked up answers unless set explicitly, a plain string author is wrapped into a Person.

## 2.0.0 - 2024-10-19

Package renamed from timur-turdyev/seotools to timurturdyev/simple-seo. The namespace is now TimurTurdyev\SimpleSeo, the config file is simple-seo.php, the publish tag is simple-seo-config. The API itself is unchanged.

## 1.1.0 - 2024-06-22

Added `jsonLd()->fromPage()`: builds a page entity from the already set title, description, image and canonical at render time.

## 1.0.0 - 2024-04-14

First release.
