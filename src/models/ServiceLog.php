<?php

namespace ServiceExtention\Models;

use Illuminate\Database\Eloquent\Model;
use ServiceExtention\Events\ServiceLogCreated;

class ServiceLog extends Model
{
	protected $events = [
			'created' => ServiceLogCreated::class
	];
	protected $table = 'service_log';
	protected $fillable = [
			'user_id',
			'service_id',
			'service_log_type', // 0:�� 1:��
			'days',
			'times',
			'memo'
	];
}
