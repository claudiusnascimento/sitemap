<?php 

namespace ClaudiusNascimento\Sitemap;

use Illuminate\Support\ServiceProvider;

class SitemapServiceProvider extends ServiceProvider {

	protected $namespace = 'ClaudiusNascimento\Sitemap';

	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot()
	{

		$this->configRoutes();

		$this->loadViewsFrom(__DIR__.'/views', 'sitemap');

		$this->publishes([
	        __DIR__.'/views' => base_path('resources/views/claudiusnascimento/sitemap'),
	    ], 'views');

	    $this->publishes([
		    __DIR__.'/config/sitemap.php' => config_path('sitemap.php'),
		], 'config');

	}

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->mergeConfigFrom(
		    __DIR__.'/config/sitemap.php', 'sitemap'
		);

		$this->app->singleton('claudiusnascimentositemap', function($app)
		{
		    return new \ClaudiusNascimento\Sitemap\Sitemap;
		    
		});
	}

	protected function configRoutes()
	{
		
		$path = config('sitemap.sitemap_url', false);

		if(!$path) return false;

		$routeConfig = [
            'namespace' => $this->namespace
        ];

		$this->app['router']->group($routeConfig, function($router) use ($path) {

            $router->get($path, [
                'uses' => 'SitemapController@sitemap',
                'as' => 'claudiusnascimento.sitemap.xml',
            ]);
        });
	}

}
