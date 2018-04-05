<?php
namespace ServiceExtention\Classes;

use ServiceExtention\Models\Service as ServiceModel;
use ServiceExtention\Models\ServiceLog;
use Log;
use ServiceExtention\Models\ServiceOfUser;
use DB;
class Service{
	/**
	 * 增加服务天数或日期
	 * @param unknown $user_id
	 * @param unknown $service_id
	 * @param unknown $num
	 * @param string $memo
	 * @return boolean
	 */
	public function increase($user_id,$service_id,$num,$memo = null){
		$service=ServiceModel::where(['service_id'=>$service_id])->first();
		if ($service) {
			switch ($service->service_type) {
				case 1:
					return $this->increaseDays($user_id, $service_id, $num, $memo);
				case 2:
					return $this->increaseTimes($user_id, $service_id, $num, $memo);
				default:
					return [
						'success'=>false,
						'message'=>'服务类型'.$service->service_type.'服务类型不存在!'
					];
			}
		}else{
			return [
				'success'=>false,
				'message'=>'ID'.$service_id.'的服务不存在！'
			];
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
	public function reduce($user_id,$service_id,$num,$memo = null){
		$service=ServiceModel::where(['service_id'=>$service_id])->first();
		if ($service) {
			switch ($service->service_type) {
				case 1:
					return $this->reduceDays($user_id, $service_id, $num, $memo);
				case 2:
					return $this->reduceTimes($user_id, $service_id, $num, $memo);
				default:
					return [
						'success'=>false,
						'message'=>'服务类型不存在!'
					];
			}
		}else{
			return [
				'success'=>false,
				'message'=>'ID'.$service_id.'的服务不存在！'
			];
		}
	}
	/**
	 * 增加服务的天数
	 * @param unknown $user_id
	 * @param unknown $service_id
	 * @param unknown $num
	 * @param string $memo
	 */
	
	private function increaseDays($user_id,$service_id,$num,String $memo = null){
		$s=ServiceLog::create([
				'user_id'	=>$user_id,
				'service_id'=>$service_id,
				'days'		=>$num,
				'memo'		=>$memo,
				'service_log_type'=>0
		]);
		if ($s) {
			return [
				'success'=>true,
				'message'=>'服务成功延长'.$num.'天!'
			];
		}else{
			return [
				'success'=>false,
				'message'=>'服务延长'.$num.'天失败!'
			];
		}
	}
	/**
	 * 增加服务的次数
	 * @param unknown $user_id
	 * @param unknown $service_id
	 * @param unknown $num
	 * @param string $memo
	 */
	private function increaseTimes($user_id,$service_id, $num, $memo = null){
		$s=ServiceLog::create([
				'user_id'	=>$user_id,
				'service_id'=>$service_id,
				'times'		=>$num,
				'memo'		=>$memo,
				'service_log_type'=>0
		]);
		if ($s) {
			return [
					'success'=>true,
					'message'=>'成功增加服务次数'.$num.'次!'
			];
		}else{
			return [
					'success'=>false,
					'message'=>'增加服务次数'.$num.'次失败!'
			];
		}
	}
	/**
	 * 减少服务的天数
	 * @param unknown $user_id
	 * @param unknown $service_id
	 * @param unknown $num
	 * @param string $memo
	 */
	private function reduceDays($user_id,$service_id, $num, $memo = null){
		$s=ServiceLog::create([
				'user_id'	=>$user_id,
				'service_id'=>$service_id,
				'days'		=>$num,
				'memo'		=>$memo,
				'service_log_type'=>1
		]);
		if ($s) {
			return [
					'success'=>true,
					'message'=>'服务成功缩短'.$num.'天!'
			];
		}else{
			return [
					'success'=>false,
					'message'=>'服务缩短'.$num.'天失败!'
			];
		}
	}
	/**
	 * 减少服务的次数
	 * @param unknown $user_id
	 * @param unknown $service_id
	 * @param unknown $num
	 * @param string $memo
	 */
	private function reduceTimes($user_id,$service_id, $num, $memo = null){
		$s=ServiceLog::create([
				'user_id'	=>$user_id,
				'service_id'=>$service_id,
				'times'		=>$num,
				'memo'		=>$memo,
				'service_log_type'=>1
		]);
		if ($s) {
			return [
					'success'=>true,
					'message'=>'成功减少服务次数'.$num.'次!'
			];
		}else{
			return [
					'success'=>false,
					'message'=>'减少服务次数'.$num.'次失败!'
			];
		}
	}
	/**
	 * 获取用户的所有服务信息
	 * @param unknown $user_id
	 * @return unknown
	 */
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
					DB::raw('IF(DATEDIFF(service_of_user.expirated_at,service_of_user.start_at) > 0,DATEDIFF(service_of_user.expirated_at,service_of_user.start_at),0) as last_days'),
					'service_of_user.times'
			])
			->get();
	}
	/**
	 * 取用户的指定服务信息
	 * @param unknown $user_id
	 * @param unknown $service_id
	 * @return unknown
	 */
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
				DB::raw('DATEDIFF(service_of_user.expirated_at,service_of_user.start_at) as last_days'),
				'service_of_user.times'
			])
			->first();
	}
	/**
	 * 服务转换
	 * @param unknown $user_id
	 * @param unknown $old_service_id
	 * @param unknown $new_service_id
	 * @param number $reduce_old_num 默认为旧服务的天数/次数
	 * @param number $increase_new_num 默认为旧服务的天数/次数
	 * @return boolean[]|string[]
	 */
	public function changeService($user_id,$old_service_id,$new_service_id,$reduce_old_num = 0,$increase_new_num = 0) {
		$oldService = ServiceModel::where(['service_id'=>$old_service_id])->first();
		$newService = ServiceModel::where(['service_id'=>$new_service_id])->first();
		if ($oldService && $newService) {
			$userService = $this->getServiceOfUser($user_id, $old_service_id);
			if ($userService) {
				switch($userService->service_type) {
					case 1:
						$num = $userService->last_days;
						break;
					case 2:
						$num = $userService->times;
						break;
					default:
						return [
							'success'=>false,
							'message'=>'服务类型'.$userService->service_type.'不存在!'
						];
				}
				$this->reduce($user_id, $old_service_id, $reduce_old_num?$reduce_old_num:$num);
				$this->increase($user_id, $new_service_id, $increase_new_num?$increase_new_num:$num);
				return [
						'success'=>true,
						'message'=>'成功转换服务!'
				];
			}else{
				return [
					'success'=>false,
					'message'=>'被转换的服务不存在！'
				];
			}
		}else{
			return [
				'success'=>false,
				'message'=>'转换的服务不存在！'
			];
		}
		
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
							->where('last_id','<',$logs->max('service_log_id'))
							->update([
								'start_at'=>date('Y-m-d H:i:s',$start_at),
								'expirated_at'=>date('Y-m-d H:i:s',$expirated_at),
								'last_id'=>$logs->max('service_log_id')
							]);
					}else{
						ServiceOfUser::create([
								'start_at'=>date('Y-m-d H:i:s',$start_at),
								'expirated_at'=>date('Y-m-d H:i:s',$expirated_at),
								'service_id'=>$service_id,
								'user_id'=>$user_id,
								'last_id'=>$logs->max('service_log_id')
						]);
					}
					return [
							'success'=>true,
							'message'=>'用户服务信息已更新！'
					];
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
							->where('last_id','<',$logs->max('service_log_id'))
							->update([
									'times'=>$times,
									'last_id'=>$logs->max('service_log_id')
							]);
					}else{
						ServiceOfUser::create([
								'times'=>$times,
								'service_id'=>$service_id,
								'user_id'=>$user_id,
								'last_id'=>$logs->max('service_log_id')
						]);
					}
					return [
							'success'=>true,
							'message'=>'用户服务信息已更新！'
					];
					break;
				default:
					return [
							'success'=>false,
							'message'=>'服务类型不存在!'
					];
			}
		}else{
			return [
					'success'=>false,
					'message'=>'ID'.$service_id.'的服务不存在！'
			];
		}
	}
}