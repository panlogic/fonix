<?php namespace Panlogic\Fonix;
/**
* Fonix helper package by Panlogic Ltd.
*
* NOTICE OF LICENSE
*
* Licensed under the terms from Panlogic Ltd.
*
* @package Fonix
* @version 1.0.0
* @author Panlogic Ltd
* @license GPL3
* @copyright (c) 2015, Panlogic Ltd
* @link http://www.panlogic.co.uk
*/

use Illuminate\Support\ServiceProvider;

classFonixServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	/**
	 * Boot the service provider.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('panlogic/fonix', 'panlogic/fonix');
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app['fonix'] = $this->app->share(function($app)
		{
			return new Fonix;
		});

		$this->app->booting(function()
		{
			$loader = \Illuminate\Foundation\AliasLoader::getInstance();
			$loader->alias('Fonix', 'Panlogic\Fonix\Facades\Fonix');
		});
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array('fonix');
	}
}