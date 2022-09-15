<?php

namespace Rhymix\Modules\Adminextend\Controllers;

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
}
