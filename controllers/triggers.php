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
		$user_id = $obj->user_id;
		$member_info = MemberModel::getMemberInfoByUserID($user_id);

		if(!Login::checkMemberAllowedIpRangeByGroup($member_info->member_srl))
		{
			return new BaseObject(-1, 'msg_not_allowed_ip');
		}
	}

	public function beforeModuleProc($obj)
	{
		if ($obj->mid === 'admin' || $obj->module === 'admin' || str_contains('admin', strtolower($obj->act)))
		{
			$logged_info = Context::get('logged_info');
			if ($logged_info->member_srl === 4) return;

			if (!Login::checkMemberAllowedIpRangeByGroup($logged_info->member_srl))
			{
				return new BaseObject(-1, 'msg_not_allowed_ip');
			}
		}
	}

	public function afterModuleProc($obj)
	{
		$gnbUrlList = Context::get('gnbUrlList');
		debugPrint($gnbUrlList);
		// Context::set('gnbUrlList',[]);
	}
}
