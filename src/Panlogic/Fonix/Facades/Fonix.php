<?php namespace Panlogic\Fonix\Facades;
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

use Illuminate\Support\Facades\Facade;

class Fonix extends Facade {

	/**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor() { return 'fonix'; }
}