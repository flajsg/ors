# ors-orsapi
ORS Tehnologije d.o.o.

This is a wrapper for **Laravel 4.2** using ORS API.

## Installation

To install the package you must run composer require "ors/orsapi:dev-master" and set service provider and alias in your app.php file.

To include service provider add this line in 'providers' array:

'Ors\Orsapi\OrsapiServiceProvider',

And make sure you've added aliases in 'aliases' array:

'ConnConfig'		=> 'Ors\Orsapi\Facades\ConnConfigApi',

