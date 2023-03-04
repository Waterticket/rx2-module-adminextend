<?php

namespace Rhymix\Modules\Adminextend\Models;

use Rhymix\Modules\Adminextend\Base;
use Rhymix\Framework\Cache;
use Rhymix\Framework\DB;
use Rhymix\Framework\Exception;
use Rhymix\Framework\Storage;
use BaseObject;
use Context;

use Rhymix\Framework\Filters\IpFilter;
use \Rhymix\Framework\Session;

/**
 * 관리자 확장팩
 * 
 * Copyright (c) Waterticket
 * 
 * Generated with https://www.poesis.org/tools/modulegen/
 */
class Login
{
	/**
	 * check allowed target ip address by member group when login.
	 *
	 * @return boolean (true : allowed, false : refuse)
	 */
	public static function checkMemberAllowedIpRangeByGroup($member_srl = null, $allow_range_by_group = null)
	{
		if ($member_srl === null)
		{
            $member_srl = Session::getMemberSrl();
		}

		$member_info = \MemberModel::getMemberInfoByMemberSrl($member_srl);
		if (!$member_info || !$member_info->member_srl)
		{
			return false;
		}

		if ($allow_range_by_group === null)
		{
			$config = Base::getConfig();
			$allow_range_by_group = $config->login_ip_range_by_group;
		}

		$member_group_srl = array_keys($member_info->group_list);

		foreach ($member_group_srl as $group_srl)
		{
			$allow_list = $allow_range_by_group[$group_srl] ?? [];
			if (empty($allow_list))
			{
				continue;
			}

			$inrange = IpFilter::inRanges(\RX_CLIENT_IP, $allow_list);
			if(!$inrange)
			{
				return false;
			}
		}

		return true;
	}
}
