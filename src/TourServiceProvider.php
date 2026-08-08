<?php

namespace Platform\Tour;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Platform\Core\PlatformCore;
use Platform\Core\Routing\ModuleRouter;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class TourServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/tour.php', 'tour');
    }

    public function boot(): void
    {
        // Presenter-Tour-Provider im Core registrieren (liefert dem globalen Overlay den aktiven Schritt).
        if (class_exists(\Platform\Core\Support\PresenterTourRegistry::class)) {
            try {
                app(\Platform\Core\Support\PresenterTourRegistry::class)
                    ->setProvider(new \Platform\Tour\Presenter\TourProvider());
            } catch (\Throwable $e) {
                Log::warning('Tour: provider registration failed', ['error' => $e->getMessage()]);
            }
        }

        // Modul registrieren
        if (
            config()->has('tour.routing') &&
            config()->has('tour.navigation') &&
            Schema::hasTable('modules')
        ) {
            PlatformCore::registerModule([
                'key'        => 'tour',
                'title'      => 'Regie',
                'group'      => 'admin',
                'routing'    => config('tour.routing'),
                'guard'      => config('tour.guard'),
                'navigation' => config('tour.navigation'),
                'sidebar'    => config('tour.sidebar'),
            ]);
        }

        if (PlatformCore::getModule('tour')) {
            ModuleRouter::group('tour', function () {
                $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
            });
        }

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->publishes([
            __DIR__ . '/../config/tour.php' => config_path('tour.php'),
        ], 'config');

        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'tour');

        $this->registerLivewireComponents();
        $this->registerTools();
    }

    protected function registerTools(): void
    {
        try {
            $registry = resolve(\Platform\Core\Tools\ToolRegistry::class);
            $registry->register(new \Platform\Tour\Tools\ListToursTool());
            $registry->register(new \Platform\Tour\Tools\CreateTourTool());
            $registry->register(new \Platform\Tour\Tools\AddTourStepTool());
            $registry->register(new \Platform\Tour\Tools\StartTourTool());
            $registry->register(new \Platform\Tour\Tools\StopTourTool());
        } catch (\Throwable $e) {
            Log::warning('Tour: tool registration failed', ['error' => $e->getMessage()]);
        }
    }

    /**
     * File src/Livewire/Tour/Index.php -> alias tour.tour.index
     */
    protected function registerLivewireComponents(): void
    {
        $basePath = __DIR__ . '/Livewire';
        $baseNamespace = 'Platform\\Tour\\Livewire';
        $prefix = 'tour';

        if (!is_dir($basePath)) {
            return;
        }

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($basePath)
        );

        foreach ($iterator as $file) {
            if (!$file->isFile() || $file->getExtension() !== 'php') {
                continue;
            }

            $relativePath = str_replace($basePath . DIRECTORY_SEPARATOR, '', $file->getPathname());
            $classPath = str_replace(['/', '.php'], ['\\', ''], $relativePath);
            $class = $baseNamespace . '\\' . $classPath;

            if (!class_exists($class)) {
                continue;
            }

            $aliasPath = str_replace(['\\', '/'], '.', Str::kebab(str_replace('.php', '', $relativePath)));
            $alias = $prefix . '.' . $aliasPath;

            Livewire::component($alias, $class);
        }
    }
}
