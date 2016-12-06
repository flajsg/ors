<?php namespace Ors\Orsapi;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;
use Ors\Orsapi\Oam\OAMAuth;
use Ors\Orsapi\Handlers\ConnConfigApiHandler;
use Ors\Orsapi\Handlers\PassengerApiHandler;
use Ors\Orsapi\Handlers\OrmApiHandler;
use Ors\Orsapi\Handlers\ReservationsApiHandler;
use Ors\Orsapi\Handlers\SearchApiHandler;
use Ors\Orsapi\Handlers\ObjectInfoHandler;
use Ors\Orsapi\Handlers\FlightInfoHandler;

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
		$this->registerSearchApi();
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides() {
		return array(
			'orsapi.connconfig', 
			'orsapi.passenger', 
			'orsapi.orm', 
			'orsapi.reservations', 
			'orsapi.search',
			'orsapi.typhotel',
			'orsapi.typdhotel',
			'orsapi.typpauschal',
			'orsapi.typtrips',
			'orsapi.objectinfo',
			'orsapi.flightinfo',
		);
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
	        return new \Ors\Orsapi\PassengerApiWrapper(new PassengerApiHandler($this->getAuth()));
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
	        return new \Ors\Orsapi\OrmApiWrapper(new OrmApiHandler($this->getAuth()));
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
	        return new \Ors\Orsapi\ReservationsApiWrapper(new ReservationsApiHandler($this->getAuth()));
	    });
	}

	/**
	 * Register SearchApiWrapper
	 * @return \Ors\Orsapi\SearchApiWrapper
	 */
	protected function registerSearchApi()
	{
		$this->app['orsapi.search'] = $this->app->share(function($app)
	    {
	        return new \Ors\Orsapi\SearchApiWrapper(new SearchApiHandler($this->getAuth()));
	    });
		
		$this->app['orsapi.typhotel'] = $this->app->share(function($app)
	    {
	        return new \Ors\Orsapi\TypHotelApiWrapper(new SearchApiHandler($this->getAuth()));
	    });
		$this->app['orsapi.typdhotel'] = $this->app->share(function($app)
	    {
	        return new \Ors\Orsapi\TypDhotelApiWrapper(new SearchApiHandler($this->getAuth()));
	    });
		$this->app['orsapi.typpauschal'] = $this->app->share(function($app)
	    {
	        return new \Ors\Orsapi\TypPauschalApiWrapper(new SearchApiHandler($this->getAuth()));
	    });
		$this->app['orsapi.typtrips'] = $this->app->share(function($app)
	    {
	        return new \Ors\Orsapi\TypTripsApiWrapper(new SearchApiHandler($this->getAuth()));
	    });
		$this->app['orsapi.objectinfo'] = $this->app->share(function($app)
	    {
	        return new \Ors\Orsapi\ObjectInfoApiWrapper(new ObjectInfoApiHandler($this->getAuth()));
	    });
		$this->app['orsapi.flightinfo'] = $this->app->share(function($app)
	    {
	        return new \Ors\Orsapi\FlightInfoApiWrapper(new FlightInfoHandler($this->getAuth()));
	    });
	}

	/**
	 * Get basic API auth info.
	 * @return \Ors\Orsapi\Oam\OAMAuth
	 */
	protected function getAuth() {
		return new OAMAuth(array(
    	    'agid' => Config::get('orsapi::agency_id'),
    	    'ibeid' => '',
    	    'master_key' => Config::get('orsapi::master_key')
    	));
	}
}
