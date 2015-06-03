<?php

return [

	/*
	|---------------------------------------------------------
	|	LIVE API SECRET KEY
	|---------------------------------------------------------
	|
	| The live api secret key provided by Fonix.
	|
	*/

	'live_apikey'	=> env('FONIX_API_KEY_LIVE','your-live-api-key-here'),

	/*
	|---------------------------------------------------------
	|	TEST API SECRET KEY
	|---------------------------------------------------------
	|
	| The test api secret key provided by Fonix.
	|
	*/

	'test_apikey'	=> env('FONIX_API_KEY_TEST','your-test-api-key-here'),

	/*
	|---------------------------------------------------------
	|	PLATFORM
	|---------------------------------------------------------
	|
	| Determine whether to use the LIVE API key or the TEST API key
	| OPTIONS: live|test
	|
	*/

	'platform'		=> env('FONIX_PLATFORM','test'),

	/*
	|---------------------------------------------------------
	|	ORIGINATOR
	|---------------------------------------------------------
	|
	| The short code issued by Fonix
	|
	*/

	'originator'		=> env('FONIX_ORIGINATOR','12345'),

];