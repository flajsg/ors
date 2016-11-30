<?php namespace Ors\Orsapi;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Ors\Orsapi\Handlers\ConnConfigApiHandler;
use Ors\Orsapi\Handlers\PassengerApiHandler;
use Ors\Orsapi\Handlers\OrmApiHandler;
use Ors\Orsapi\Handlers\ReservationsApiHandler;

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
		$this->registerPassengerApi();
		$this->registerOrmApi();
		$this->registerReservationsApi();
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides() {
		return array('orsapi.connconfig', 'orsapi.passenger', 'orsapi.orm', 'orsapi.reservations');
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
	    }
		);
	}
	
	/**
	 * Register PassengerApiWrapper
	 * @return \Ors\Orsapi\PassengerApiWrapper
	 */
	protected function registerPassengerApi()
	{
		$this->app['orsapi.passenger'] = $this->app->share(function($app)
	    {
	        return new \Ors\Orsapi\PassengerApiWrapper(new PassengerApiHandler(Config::get('orsapi::passenger.agency_id'), Config::get('orsapi::passenger.master_key')));
	    });
	}
	
	/**
	 * Register OrmApiWrapper
	 * @return \Ors\Orsapi\OrmApiWrapper
	 */
	protected function registerOrmApi()
	{
		$this->app['orsapi.orm'] = $this->app->share(function($app)
	    {
	        return new \Ors\Orsapi\OrmApiWrapper(new OrmApiHandler());
	    });
	}
	
	/**
	 * Register ReservationsApiWrapper
	 * @return \Ors\Orsapi\ReservationsApiWrapper
	 */
	protected function registerReservationsApi()
	{
		$this->app['orsapi.reservations'] = $this->app->share(function($app)
	    {
	        return new \Ors\Orsapi\ReservationsApiWrapper(new ReservationsApiHandler());
	    });
	}

}
