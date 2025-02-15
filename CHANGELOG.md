# Changelog

## 2.1.0 - 2025-02-15

Added QAPage, Question and Answer schema builders. answerCount is derived from the marked up answers unless set explicitly, a plain string author is wrapped into a Person.

## 2.0.0 - 2024-10-19

Package renamed from timur-turdyev/seotools to timurturdyev/simple-seo. The namespace is now TimurTurdyev\SimpleSeo, the config file is simple-seo.php, the publish tag is simple-seo-config. The API itself is unchanged.

## 1.1.0 - 2024-06-22

Added `jsonLd()->fromPage()`: builds a page entity from the already set title, description, image and canonical at render time.

## 1.0.0 - 2024-04-14

First release.
