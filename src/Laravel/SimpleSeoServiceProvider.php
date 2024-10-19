<?php

declare(strict_types=1);

namespace TimurTurdyev\SimpleSeo\Laravel;

use Illuminate\Contracts\Config\Repository;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use TimurTurdyev\SimpleSeo\JsonLd\JsonLdBuilder;
use TimurTurdyev\SimpleSeo\Meta\MetaBuilder;
use TimurTurdyev\SimpleSeo\OpenGraph\OpenGraphBuilder;
use TimurTurdyev\SimpleSeo\SeoManager;
use TimurTurdyev\SimpleSeo\TwitterCard\TwitterCardBuilder;
use TimurTurdyev\SimpleSeo\TwitterCard\TwitterCardType;

final class SimpleSeoServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../config/simple-seo.php', 'simple-seo');

        // Scoped, not singleton: the container flushes scoped instances
        // between requests, which keeps state clean under Octane.
        $this->app->scoped(SeoManager::class, function (Application $app): SeoManager {
            /** @var array{
             *     meta: array{title: array{default: ?string, suffix: ?string}, description: array{default: ?string}},
             *     open_graph: array{site_name: ?string, type: ?string, locale: ?string},
             *     twitter: array{card: ?string, site: ?string}
             * } $config
             */
            $config = $app->make(Repository::class)->get('simple-seo');

            $card = $config['twitter']['card'];

            return new SeoManager(
                new MetaBuilder(
                    defaultTitle: $config['meta']['title']['default'],
                    titleSuffix: $config['meta']['title']['suffix'],
                    defaultDescription: $config['meta']['description']['default'],
                ),
                new OpenGraphBuilder(
                    defaultSiteName: $config['open_graph']['site_name'],
                    defaultType: $config['open_graph']['type'],
                    defaultLocale: $config['open_graph']['locale'],
                ),
                new TwitterCardBuilder(
                    defaultCard: $card === null ? null : TwitterCardType::from($card),
                    defaultSite: $config['twitter']['site'],
                ),
                new JsonLdBuilder(),
            );
        });
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/simple-seo.php' => config_path('simple-seo.php'),
        ], 'simple-seo-config');

        Blade::directive('seo', function (): string {
            return '<?php $__simpleSeo = app(\TimurTurdyev\SimpleSeo\SeoManager::class); '
                . 'echo $__simpleSeo->render(); '
                . 'if (config(\'app.debug\') === true) { echo "\n" . $__simpleSeo->debugComment(); } '
                . 'unset($__simpleSeo); ?>';
        });
    }
}
