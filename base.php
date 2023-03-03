<?php

namespace Rhymix\Modules\Adminextend;

/**
 * 관리자 확장팩
 * 
 * Copyright (c) Waterticket
 * 
 * Generated with https://www.poesis.org/tools/modulegen/
 */
class Base extends \ModuleObject
{
	/**
	 * 모듈 설정 캐시를 위한 변수.
	 */
	protected static $_config_cache = null;
	
	/**
	 * 모듈 설정을 가져오는 함수.
	 * 
	 * 캐시 처리되기 때문에 ModuleModel을 직접 호출하는 것보다 효율적이다.
	 * 모듈 내에서 설정을 불러올 때는 반드시 이 함수를 사용하도록 한다. 
	 * 
	 * @return object
	 */
	public static function getConfig()
	{
		if (self::$_config_cache === null)
		{
			self::$_config_cache = \ModuleModel::getModuleConfig('adminextend') ?: new \stdClass;

			// 기본값 설정
			if (!isset(self::$_config_cache->module_enabled)) self::$_config_cache->module_enabled = 'N';
			if (!isset(self::$_config_cache->super_admin_member_srl)) self::$_config_cache->super_admin_member_srl = 4;
			if (!isset(self::$_config_cache->login_ip_range_by_group)) self::$_config_cache->login_ip_range_by_group = [];
			if (!isset(self::$_config_cache->permission)) self::$_config_cache->permission = [];
			if (!isset(self::$_config_cache->custom_allowed_act)) self::$_config_cache->custom_allowed_act = [];
		}
		return self::$_config_cache;
	}
	
	/**
	 * 모듈 설정을 저장하는 함수.
	 * 
	 * 설정을 변경할 필요가 있을 때 ModuleController를 직접 호출하지 말고 이 함수를 사용한다.
	 * getConfig()으로 가져온 설정을 적절히 변경하여 setConfig()으로 다시 저장하는 것이 정석.
	 * 
	 * @param object $config
	 * @return object
	 */
	public static function setConfig($config)
	{
		$oModuleController = \ModuleController::getInstance();
		$result = $oModuleController->insertModuleConfig('adminextend', $config);
		if ($result->toBool())
		{
			self::$_config_cache = $config;
		}
		return $result;
	}
	
}
