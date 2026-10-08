# Changelog

## 2.2.0 - 2026-10-08

Head tags as data: `SeoManager::tags()` (list of `{tag, attrs, text?}`) and `SeoManager::toArray()` (title, description, canonical, robots, openGraph, twitter, jsonLd, tags), plus `tags()` on every section, `MetaBuilder::robotsDirective()` and `JsonLdBuilder::payload()`. `render()` now prints the same `Tag` objects, so markup and data cannot disagree; output is unchanged.

## 2.1.0 - 2026-08-05

Added QAPage, Question and Answer schema builders. answerCount is derived from the marked up answers unless set explicitly, a plain string author is wrapped into a Person.

## 2.0.0 - 2026-08-05

Package renamed from timur-turdyev/seotools to timurturdyev/simple-seo. The namespace is now TimurTurdyev\SimpleSeo, the config file is simple-seo.php, the publish tag is simple-seo-config. The API itself is unchanged.

## 1.1.0 - 2026-08-05

Added `jsonLd()->fromPage()`: builds a page entity from the already set title, description, image and canonical at render time.

## 1.0.0 - 2026-08-04

First release.
