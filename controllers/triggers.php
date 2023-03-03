<?php

namespace Rhymix\Modules\Adminextend\Controllers;

use BaseObject;
use Context;

use Rhymix\Modules\Adminextend\Base;
use Rhymix\Modules\Adminextend\Models\Login;

/**
 * 관리자 확장팩
 * 
 * Copyright (c) Waterticket
 * 
 * Generated with https://www.poesis.org/tools/modulegen/
 */
class Triggers extends Base
{
	public function beforeDoLogin($obj)
	{
		$config = $this->getConfig();
		if ($config->module_enabled !== 'Y') return;
		
		$user_id = $obj->user_id;
		$member_info = \MemberModel::getMemberInfoByUserID($user_id);

		if(!Login::checkMemberAllowedIpRangeByGroup($member_info->member_srl))
		{
			return new BaseObject(-1, 'msg_not_allowed_ip');
		}
	}

	public static $allowed_acts = [];

	public function beforeModuleProc($obj)
	{
		$config = $this->getConfig();
		if ($config->module_enabled !== 'Y') return;

		if ($obj->mid === 'admin' || $obj->module === 'admin' || str_contains(strtolower($obj->act), 'admin'))
		{
			self::$allowed_acts = \Rhymix\Modules\Adminextend\Models\Permission::getAllowedActs(array_keys($this->user->group_list));
			
			if ($this->user->member_srl === $config->super_admin_member_srl) return;
			if (!in_array($obj->act, self::$allowed_acts)) return new BaseObject(-1, 'msg_not_permitted_act');

			if (!Login::checkMemberAllowedIpRangeByGroup($this->user->member_srl))
			{
				return new BaseObject(-1, 'msg_not_allowed_ip');
			}
		}
	}

	public function afterModuleProc($obj)
	{
		$config = $this->getConfig();
		if ($config->module_enabled !== 'Y') return;

		if ($obj->mid === 'admin' || $obj->module === 'admin' || str_contains(strtolower($obj->act), 'admin'))
		{
			if ($this->user->member_srl === $config->super_admin_member_srl) return;
		
			$gnbUrlList = Context::get('gnbUrlList');
			debugPrint($gnbUrlList);
			$gnbUrlList = $this->updateUrlList($gnbUrlList);
			Context::set('gnbUrlList', $gnbUrlList);

		}
	}

	public function updateUrlList($urlList)
	{
		foreach ($urlList as $key => &$obj)
		{
			if ($obj['text'] == '대시보드' && in_array('__dashboard__', self::$allowed_acts))
			{
				continue;
			}
			
			if (!empty($obj['list']))
			{
				$obj['list'] = $this->updateUrlList($obj['list']);
				if (!empty($obj['list'])) continue;
			}

			$str = parse_url(htmlspecialchars_decode($obj['href']), PHP_URL_QUERY);
			parse_str($str, $args);

			if (in_array($args['act'], self::$allowed_acts))
			{
				continue;
			}
			else
			{
				unset($urlList[$key]);
			}
		}

		return $urlList;
	}
}
