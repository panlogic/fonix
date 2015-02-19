<?php namespace Panlogic\Fonix;
/**
* Fonix helper package by Panlogic Ltd.
*
* NOTICE OF LICENSE
*
* Licensed under the terms from Panlogic Ltd.
*
* @package Fonix
* @version 1.0.3
* @author Panlogic Ltd
* @license GPL3
* @copyright (c) 2015, Panlogic Ltd
* @link http://www.panlogic.co.uk
*/

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\AliasLoader;
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

		AliasLoader::getInstance()->alias(
            'Fonix',
            'Panlogic\Fonix\Facades\FonixFacade'
        );
	}

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->singleton('Panlogic\Fonix\Fonix', function ($app)
		{
			if (is_null(config('panlogic.fonix.live_apikey')))
			{
				throw new FonixException;
			}

			$config = [
				'live_apikey' 		=> config('panlogic.fonix.live_apikey'),
				'test_apikey' 		=> config('panlogic.fonix.test_apikey'),
				'platform' 			=> config('panlogic.fonix.platform'),
				'originator' 		=> config('panlogic.fonix.originator'),
			];

			return new Fonix($config);
		});
	}

	 /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['Panlogic\Fonix\Fonix'];
    }
}