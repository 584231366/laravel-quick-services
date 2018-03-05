<?php

namespace ServiceExtention\Facades;

use Illuminate\Support\Facades\Facade;

class Service extends Facade
{
	protected static function getFacadeAccessor()
	{
		return 'Service';
	}
}