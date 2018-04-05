<?php

namespace ServiceExtention\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
	protected $table = 'service';
	protected $fillable = [
		'service_name',
		'service_type', // 1:天数;2:次数
		'service_desc'
	];
}
