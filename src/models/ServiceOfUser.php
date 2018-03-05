<?php

namespace ServiceExtention\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceOfUser extends Model
{
	protected $table = 'service_of_user';
	protected $fillable = [
		'user_id',
		'service_id',
		'start_at',
		'expirated_at',
		'times',
		'tag'
	];
}
