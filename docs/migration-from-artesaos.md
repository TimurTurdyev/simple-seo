# Migration from artesaos/seotools

New API, no compat layer. Old calls map like this:

`SEOTools::setTitle` and `setDescription` become `seo()->title()` / `seo()->description()`. `setCanonical` becomes `seo()->canonical()` and also fills `og:url`. String robots like `setRobots('noindex, nofollow')` turn into `seo()->noindex()` or granular meta calls (`nosnippet()`, `noarchive()`, `maxSnippet()`, `maxImagePreview()`). `addAlternateLanguage` is `meta()->alternate()`, `setPrev`/`setNext` keep their names on `meta()`.

`OpenGraph::addImage` is `seo()->image()` (fills twitter too) or `openGraph()->image()` with width/height/alt. `addProperty` is `openGraph()->property()`. Twitter setters live on `twitterCard()`, card type is an enum now.

JsonLd and JsonLdMulti are gone. Call `seo()->jsonLd()->add()` once per entity, either with an array or a `Schema::` builder. No current block, no phantom first block. `SEOTools::generate()` in the layout becomes the `@seo` directive. The `jsonLdMulti()->setType('Product')` plus repeated `setTitle`/`setDescription` pattern collapses into `jsonLd()->fromPage('Product', $overrides)`: it reads the already set title, description, image and canonical at render time, extra fields like offers go into the second argument. The `setType('QAPage')` plus `addValue('mainEntity', [...])` pattern becomes `jsonLd()->add(Schema::qaPage()->question(...))` with typed Question and Answer builders; answerCount is derived from the marked up answers unless set explicitly.

Config keys move from `meta.defaults.title` style to `meta.title.default`, `meta.title.suffix`, `meta.description.default`, `open_graph.site_name`, `open_graph.type`, `twitter.card`, `twitter.site`.

Watch out for three behavior changes. Old config values leaked into state; here they are render-time fallbacks, so `setDescription(false)` tricks become `meta()->withoutDefaults()`. The twitter section prints nothing until the page sets an explicit value. Values are escaped on output, so drop any manual escaping.

Do not port fabricated markup: hardcoded upvote counts, fake ratings, QAPage blocks with marketing text. Those rich results are dead and fake values risk a manual action.

After the swap, diff the head output of a few typical pages against the old version and run them through the Rich Results Test.
