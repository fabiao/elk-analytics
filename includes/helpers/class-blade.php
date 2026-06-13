<?php

namespace ELKLab\ELKAnalytics\Helpers;

use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Contracts\View\Factory as FactoryContract;
use Illuminate\Contracts\View\View;
use Illuminate\Events\Dispatcher;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Facade;
use Illuminate\View\Compilers\BladeCompiler;
use Illuminate\View\Factory;
use Illuminate\View\ViewServiceProvider;

/**
 * Standalone Blade wrapper compatible with illuminate/view ^13.
 * Drop-in replacement for jenssegers/blade, which only supported up to v11.
 *
 * The inner anonymous Container subclass adds terminating() — a method that
 * illuminate/view v13's ViewServiceProvider calls to register end-of-request
 * Blade cache flush callbacks. WordPress has no Application lifecycle, so the
 * callbacks are deferred to the 'shutdown' action instead.
 */
class Blade implements FactoryContract
{
    private Container $container;
    private Factory $factory;
    private BladeCompiler $compiler;

    public function __construct(array|string $viewPaths, string $cachePath)
    {
        $this->container = new class extends Container {
            public function terminating(callable|array $callback): static
            {
                add_action('shutdown', $callback);
                return $this;
            }
        };

        $this->setupContainer((array) $viewPaths, $cachePath);
        (new ViewServiceProvider($this->container))->register();

        $this->factory  = $this->container->get('view');
        $this->compiler = $this->container->get('blade.compiler');
    }

    public function make($view, $data = [], $mergeData = []): View
    {
        return $this->factory->make($view, $data, $mergeData);
    }

    public function render(string $view, array $data = [], array $mergeData = []): string
    {
        return $this->make($view, $data, $mergeData)->render();
    }

    public function compiler(): BladeCompiler
    {
        return $this->compiler;
    }

    public function directive(string $name, callable $handler): void
    {
        $this->compiler->directive($name, $handler);
    }

    public function exists($view): bool
    {
        return $this->factory->exists($view);
    }

    public function file($path, $data = [], $mergeData = []): View
    {
        return $this->factory->file($path, $data, $mergeData);
    }

    public function share($key, $value = null): mixed
    {
        return $this->factory->share($key, $value);
    }

    public function composer($views, $callback): array
    {
        return $this->factory->composer($views, $callback);
    }

    public function creator($views, $callback): array
    {
        return $this->factory->creator($views, $callback);
    }

    public function addNamespace($namespace, $hints): static
    {
        $this->factory->addNamespace($namespace, $hints);
        return $this;
    }

    public function replaceNamespace($namespace, $hints): static
    {
        $this->factory->replaceNamespace($namespace, $hints);
        return $this;
    }

    public function __call(string $method, array $params): mixed
    {
        return $this->factory->$method(...$params);
    }

    private function setupContainer(array $viewPaths, string $cachePath): void
    {
        $this->container->bindIf('files', fn() => new Filesystem);
        $this->container->bindIf('events', fn() => new Dispatcher);
        $this->container->bindIf('config', fn() => new Repository([
            'view.paths'    => $viewPaths,
            'view.compiled' => $cachePath,
        ]));

        Facade::setFacadeApplication($this->container);
    }
}
