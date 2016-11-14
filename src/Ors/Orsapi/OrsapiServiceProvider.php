<?php namespace Ors\Orsapi;

use Illuminate\Support\ServiceProvider;
use Ors\Orsapi\Handlers\ConnConfigApiHandler;

class OrsapiServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('ors/orsapi');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->registerConnConfigApi();
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides() {
		return array('orsapi.connconfig');
	}
	
	/**
	 * Register ConnConfigApiWrapper
	 * @return \Ors\Orsapi\ConnConfigApiWrapper
	 */
	protected function registerConnConfigApi()
	{
		$this->app['orsapi.connconfig'] = $this->app->share(function($app)
	    {
	        return new \Ors\Orsapi\ConnConfigApiWrapper(new ConnConfigApiHandler());
	    });
	}

}
