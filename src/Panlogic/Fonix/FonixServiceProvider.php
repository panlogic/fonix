<?php namespace Panlogic\Fonix;
/**
* Fonix helper package by Panlogic Ltd.
*
* NOTICE OF LICENSE
*
* Licensed under the terms from Panlogic Ltd.
*
* @package Fonix
* @version 1.0.5
* @author Panlogic Ltd
* @license MIT
* @copyright (c) 2015, Panlogic Ltd
* @link http://www.panlogic.co.uk
*/

use Illuminate\Support\ServiceProvider;
use Panlogic\Fonix\Exceptions\FonixException;

class FonixServiceProvider extends ServiceProvider {

	/**
	 * Boot the service provider.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->publishes([
			__DIR__ . '/../config/fonix.php' => config_path('panlogic.fonix.php'),
		]);
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->bindShared('fonix', function ($app)
		{
			if (is_null(config('panlogic.fonix.live_apikey')))
			{
				throw new FonixException;
			}

			return new Fonix($app['config']['panlogic.fonix']);
		});

		$this->app->alias('fonix', 'Panlogic\Fonix\Fonix');
	}

	 /**
     * Get the services provided by the provider.
     *
	 * @return array
	 */
	public function provides()
	{
		return ['fonix','Panlogic\Fonix\Fonix'];
	}
}