<?php

namespace ServiceExtention\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceOptions extends Model
{
	protected $table = 'service_options';
	protected $fillable = [
			'service_id',
			'service_option_name'
	];
}
