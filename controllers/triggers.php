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

	public function beforeModuleProc($obj)
	{
		$config = $this->getConfig();
		if ($config->module_enabled !== 'Y') return;

		if ($obj->mid === 'admin' || $obj->module === 'admin' || str_contains('admin', strtolower($obj->act)))
		{
			if ($this->user->member_srl === $config->super_admin_member_srl) return;

			if (!Login::checkMemberAllowedIpRangeByGroup($this->user->member_srl))
			{
				return new BaseObject(-1, 'msg_not_allowed_ip');
			}
		}
	}

	public function afterModuleProc($obj)
	{
		return;
		$gnbUrlList = Context::get('gnbUrlList');
		debugPrint(array_keys($this->user->group_list));
		debugPrint($gnbUrlList);
		return;
		
		// Context::set('gnbUrlList',[]);
	}
}
