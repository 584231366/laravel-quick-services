<?php
namespace ServiceExtention\Classes;

use ServiceExtention\Models\Service as ServiceModel;
use ServiceExtention\Models\ServiceLog;
use Log;
use ServiceExtention\Models\ServiceOfUser;

class Service{
	/**
	 * 增加服务天数或日期
	 * @param unknown $user_id
	 * @param unknown $service_id
	 * @param unknown $num
	 * @param string $memo
	 * @return boolean
	 */
	public function increase($user_id,$service_id,$num,$memo = ''){
		$service=ServiceModel::where(['service_id'=>$service_id])->first();
		if ($service) {
			switch ($service->service_type) {
				case 1:
					return $this->increaseDays($user_id, $service_id, $num, $memo);
				case 2:
					return $this->increaseTimes($user_id, $service_id, $num, $memo);
				default:
					Log::error('服务类型不存在！');
					abort(500,'服务类型不存在！');
			}
		}else{
			Log::error('服务不存在！');
			abort(500,'服务不存在！');
		}
	}
	/**
	 * 减少服务天数或日期
	 * @param unknown $user_id
	 * @param unknown $service_id
	 * @param unknown $num
	 * @param string $memo
	 * @return boolean
	 */
	public function reduce($user_id,$service_id,$num,$memo = ''){
		$service=ServiceModel::where(['service_id'=>$service_id])->first();
		if ($service) {
			switch ($service->service_type) {
				case 1:
					return $this->reduceDays($user_id, $service_id, $num, $memo);
				case 2:
					return $this->reduceTimes($user_id, $service_id, $num, $memo);
				default:
					Log::error('服务类型不存在！');
					abort(500);
			}
		}else{
			Log::error('服务不存在！');
			abort(500);
		}
	}
	/**
	 * 增加服务的天数
	 * @param unknown $user_id
	 * @param unknown $service_id
	 * @param unknown $num
	 * @param string $memo
	 */
	
	public function increaseDays($user_id,$service_id,$num,$memo = ''){
		$s=ServiceLog::create([
				'user_id'	=>$user_id,
				'service_id'=>$service_id,
				'days'		=>$num,
				'memo'		=>$memo,
				'service_log_type'=>0
		]);
		return $s?true:false;
	}
	/**
	 * 增加服务的次数
	 * @param unknown $user_id
	 * @param unknown $service_id
	 * @param unknown $num
	 * @param string $memo
	 */
	public function increaseTimes($user_id,$service_id, $num, $memo = ''){
		$s=ServiceLog::create([
				'user_id'	=>$user_id,
				'service_id'=>$service_id,
				'times'		=>$num,
				'memo'		=>$memo,
				'service_log_type'=>0
		]);
		return $s?true:false;
	}
	/**
	 * 减少服务的天数
	 * @param unknown $user_id
	 * @param unknown $service_id
	 * @param unknown $num
	 * @param string $memo
	 */
	public function reduceDays($user_id,$service_id, $num, $memo = ''){
		$s=ServiceLog::create([
				'user_id'	=>$user_id,
				'service_id'=>$service_id,
				'days'		=>$num,
				'memo'		=>$memo,
				'service_log_type'=>1
		]);
		return $s?true:false;
	}
	/**
	 * 减少服务的次数
	 * @param unknown $user_id
	 * @param unknown $service_id
	 * @param unknown $num
	 * @param string $memo
	 */
	public function reduceTimes($user_id,$service_id, $num, $memo = ''){
		$s=ServiceLog::create([
				'user_id'	=>$user_id,
				'service_id'=>$service_id,
				'times'		=>$num,
				'memo'		=>$memo,
				'service_log_type'=>1
		]);
		return $s?true:false;
	}
	// 获取用户的所有服务信息
	public function getServicesOfUser($user_id){
		return ServiceModel::crossjoin('service_of_user','service_of_user.service_id','=','service.service_id')
			->where([
					'service_of_user.user_id'=>$user_id
			])
			->select([
					'service.service_id',
					'service.service_name',
					'service.service_type',
					'service_of_user.start_at',
					'service_of_user.expirated_at',
					'service_of_user.times'
			])
			->get();
	}
	// 获取用户的指定服务信息
	public function getServiceOfUser($user_id, $service_id){
		return ServiceModel::crossjoin('service_of_user','service_of_user.service_id','=','service.service_id')
			->where([
					'service_of_user.service_id'=>$service_id,
					'service_of_user.user_id'=>$user_id
			])
			->select([
				'service.service_id',
				'service.service_name',
				'service.service_type',
				'service_of_user.start_at',
				'service_of_user.expirated_at',
				'service_of_user.times'
			])
			->first();
	}
	/**
	 * 计算用户当前有效期/有效次数
	 * @param unknown $user_id
	 * @param unknown $service_id
	 */
	public function calculateServiceResult($user_id, $service_id) {
		$service=ServiceModel::where(['service_id'=>$service_id])->first();
		if ($service) {
			$logs = ServiceLog::where(['service_id'=>$service_id,'user_id'=>$user_id])->orderBy('created_at','asc')->get();
			switch ($service->service_type) {
				case 1:
					$start_at = null;
					$expirated_at = null;
					foreach ($logs as $key => $log) {
						if ($key == 0) { // 初次记录 - 计算开始和过期日期
							$start_at = strtotime($log->created_at);
							$expirated_at = strtotime($log->created_at) + $log->days*24*60*60;
						}else{
							switch ($log->service_log_type) {
								case 0: // 增加天数
									if (strtotime($log->created_at) < $expirated_at) { // 后续记录 - 判断记录是否是上次过期日期前添加
										$expirated_at = $expirated_at + $log->days*24*60*60;
									}else{
										$start_at = strtotime($log->created_at);
										$expirated_at = strtotime($log->created_at) + $log->days*24*60*60;
									}
									break;
								case 1: // 减少天数
									if (strtotime($log->created_at) < $expirated_at) { // 后续记录 - 判断记录是否是上次过期日期前添加
										$expirated_at = $expirated_at - $log->days*24*60*60;
									}
							}
						}
					}
					if (ServiceOfUser::where(['service_id'=>$service_id,'user_id'=>$user_id])->count()) { // 判断记录是否存在
						ServiceOfUser::where(['service_id'=>$service_id,'user_id'=>$user_id])
							->where('tag','<',$logs->max('service_log_id'))
							->update([
								'start_at'=>date('Y-m-d H:i:s',$start_at),
								'expirated_at'=>date('Y-m-d H:i:s',$expirated_at),
								'tag'=>$logs->max('service_log_id')
							]);
					}else{
						ServiceOfUser::create([
								'start_at'=>date('Y-m-d H:i:s',$start_at),
								'expirated_at'=>date('Y-m-d H:i:s',$expirated_at),
								'service_id'=>$service_id,
								'user_id'=>$user_id,
								'tag'=>$logs->max('service_log_id')
						]);
					}
					break;
				case 2:
					$times = 0;
					foreach ($logs as $key => $log) {
						switch ($log->service_log_type) {
							case 0: // 增加次数
								$times = $times + $log->times;
								break;
							case 1: // 减少次数
								$times = $times - $log->times;
								$times = $times<0?0:$times;
								break;
						}
					}
					if (ServiceOfUser::where(['service_id'=>$service_id,'user_id'=>$user_id])->count()) { // 判断记录是否存在
						ServiceOfUser::where(['service_id'=>$service_id,'user_id'=>$user_id])
							->where('tag','<',$logs->max('service_log_id'))
							->update([
									'times'=>$times,
									'tag'=>$logs->max('service_log_id')
							]);
					}else{
						ServiceOfUser::create([
								'times'=>$times,
								'service_id'=>$service_id,
								'user_id'=>$user_id,
								'tag'=>$logs->max('service_log_id')
						]);
					}
					break;
				default:
					Log::error('服务类型不存在！');
					abort(500,'服务类型不存在！');
			}
		}else{
			Log::error('服务不存在！');
			abort(500);
		}
	}
}