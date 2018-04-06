## Laravel-Services
  这是一个service管理模块,提供服务时长和服务次数的管理。包含服务表，服务属性，服务日志，服务状态。一切的服务状态从服务日志的记录计算获取。服务日志便于观察用户服务的变更等情况。以及组合使用于各种场景。

## 安装步骤
1. composer require yawning-cat/service-module
2. 在config/app.php内配置

	'providers' => [

		...

		ServiceExtention\ServicesProvider::class

	]

	...

	'aliases' => [

		...

		ServiceExtention\Facades\Service::class
		
	]

3. php artisan migrate

## 服务模块函数
 - 服务类型: 1:有效期服务 2:次数服务
 - increase(user_id, service_id, num) 增加有效天数/服务次数
 - reduce(user_id, service_id, num) 减少有效天数/服务次数
 - getServicesOfUser(user_id) 获取指定用户的所有服务信息
 - getServiceOfUser(user_id, service_id) 获取指定用户的指定服务信息
 - calculateServiceResult(user_id, service_id) 更新指定用户的指定服务信息
 - model(name) 获取对应model  service log options status