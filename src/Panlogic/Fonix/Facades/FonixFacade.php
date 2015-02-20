<?php namespace Panlogic\Fonix\Facades;
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
* @license MIT
* @copyright (c) 2015, Panlogic Ltd
* @link http://www.panlogic.co.uk
*/

use Illuminate\Support\Facades\Facade;

class FonixFacade extends Facade {

	/**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'fonix'; }
}