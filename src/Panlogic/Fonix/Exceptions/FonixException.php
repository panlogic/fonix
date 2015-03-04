<?php namespace Panlogic\Fonix\Exceptions;
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
* @license MIT
* @copyright (c) 2015, Panlogic Ltd
* @link http://www.panlogic.co.uk
*/

use Exception;

class FonixException extends Exception {
    /**
     * The exception message.
     *
     * @var string
     */
    protected $message = 'Fonix configuration must be published. Use: "php artisan vendor:publish".';
}