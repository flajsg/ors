# ors-orsapi
ORS Tehnologije d.o.o.

This is a wrapper for **Laravel 4.2** using ORS API.

## Installation

To install the package you must run composer require "ors/orsapi:dev-master" and set service provider and alias in your app.php file.

To include service provider add this line in 'providers' array:

	'Ors\Orsapi\OrsapiServiceProvider',

And make sure you've added aliases in 'aliases' array:

	'ConnConfig'		=> 'Ors\Orsapi\Facades\ConnConfigApi',
	'PassengerApi'		=> 'Ors\Orsapi\Facades\PassengerApi',
	'OrmApi'			=> 'Ors\Orsapi\Facades\OrmApi',
	'ReservationsApi'	=> 'Ors\Orsapi\Facades\ReservationsApi',
	'SearchApi'			=> 'Ors\Orsapi\Facades\SearchApi',
	'TypHotelApi'		=> 'Ors\Orsapi\Facades\TypHotelApi',
	'TypDhotelApi'		=> 'Ors\Orsapi\Facades\TypDhotelApi',
	'TypPauschalApi'	=> 'Ors\Orsapi\Facades\TypPauschalApi',
	'TypTripsApi'		=> 'Ors\Orsapi\Facades\TypTripsApi',
	'ObjectInfoApi'		=> 'Ors\Orsapi\Facades\ObjectInfoApi',
	'FlightInfoApi'		=> 'Ors\Orsapi\Facades\FlightInfoApi',
  
**Publishing migrations and configuration:**

You will need some custom configurations so make sure you have published config files:

	php artisan config:publish ors/orsapi

## Basic Usage

A list of available ORS API connections:

	$connections = ConnConfig::listConnections()

A list of agency passengers:

	$passengers = PassengerApi::all()

A list of hotel-only destinations (stay 3 nights, 2 adults, 3 months in advance):

	$params = array(
		'epc' => 2,
		'vnd' => date('Y-m-d'),
		'bsd' => date("Y-m-d", strtotime("+3 months")),
		'tdc' => '3-3',
		'uniqid' => '123456789',
		'ibeid' => 'xxx',
	);
	
	$regions = TypHotelApi::regions($params);
	
Object info (description, images, characteristics, weather, ratings):

	$params = array(
		'gid' => 6715,
		'toc' => 'FTI',
	);
	
	$info = ObjectInfoApi::infoToc($params);
	
Set API authorisation:

	$auth = new \Ors\Orsapi\Oam\OAMAuth(array(
		'agid' => XXXX, 
		'usr' => 'api-username', 
		'pass' => 'api-password'
	));
	
	$regions = TypHotelApi::setAuthLogin($auth)->regions($params);
	