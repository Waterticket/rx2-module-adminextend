<?php

namespace Rhymix\Modules\Adminextend\Controllers;

use BaseObject;
use Context;
use MemberModel;
use stdClass;

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
		$member_config = MemberModel::getMemberConfig();
		$member_info = new stdClass;
		if ((!$member_config->identifiers || in_array('email_address', $member_config->identifiers)) && strpos($user_id, '@') !== false)
		{
			$member_info = MemberModel::getMemberInfoByEmailAddress($user_id);
		}
		else if ($member_config->identifiers && in_array('phone_number', $member_config->identifiers) && strpos($user_id, '@') === false)
		{
			if (preg_match('/^\+([0-9-]+)\.([0-9.-]+)$/', $user_id, $matches))
			{
				$user_id = $matches[2];
				$phone_country = $matches[1];
				if($member_config->phone_number_hide_country === 'Y')
				{
					$phone_country = $member_config->phone_number_default_country;
				}
			}
			else if ($member_config->phone_number_default_country)
			{
				$phone_country = $member_config->phone_number_default_country;
			}
			else
			{
				return new BaseObject(-1, 'invalid_user_id');
			}

			if($phone_country && !preg_match('/^[A-Z]{3}$/', $phone_country))
			{
				$phone_country = \Rhymix\Framework\i18n::getCountryCodeByCallingCode($phone_country);
			}

			$numbers_only = preg_replace('/[^0-9]/', '', $user_id);
			if (!$numbers_only)
			{
				return new BaseObject(-1, 'null_user_id');
			}

			$member_info = MemberModel::getMemberInfoByPhoneNumber($numbers_only, $phone_country);
		}
		else if (!$member_config->identifiers || in_array('user_id', $member_config->identifiers))
		{
			$member_info = MemberModel::getMemberInfoByUserID($user_id);
		}
		else
		{
			return new BaseObject(-1, 'invalid_user_id');
		}

		if (!$member_info->member_srl)
		{
			return new BaseObject(-1, 'invalid_user_id');
		}

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
			$gnbUrlList = Context::get('gnbUrlList');
			$gnbUrlList = $this->updateUrlList($gnbUrlList);
			Context::set('gnbUrlList', $gnbUrlList);

			if (!in_array($obj->act, self::$allowed_acts))
			{
				return new BaseObject(-1, 'msg_not_permitted_act');
			}

			if (!Login::checkMemberAllowedIpRangeByGroup($this->user->member_srl))
			{
				return new BaseObject(-1, 'msg_not_allowed_ip');
			}
		}
	}

	public function afterModuleProc($obj)
	{
		
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
