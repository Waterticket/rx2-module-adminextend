<?php

namespace Rhymix\Modules\Adminextend\Controllers;

use Rhymix\Modules\Adminextend\Models\Log;

use Rhymix\Framework\Cookie;
use ncenterliteController;
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
		if ($config->module_enabled !== 'Y' || $config->access_level === 'none') return;
		
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

	public function beforeDoAutoLogin($member_info)
	{
		$config = $this->getConfig();
		if ($config->module_enabled !== 'Y' || $config->access_level === 'none') return;
		if ($config->apply_access_control_on_auto_login !== 'Y') return;

		if(!Login::checkMemberAllowedIpRangeByGroup($member_info->member_srl))
		{
			Cookie::set('auto_login_failed_due_ip', 'Y', ['expires' => 3600]);
			return new BaseObject(-1, 'msg_not_allowed_ip');
		}
	}

	public static $allowed_acts = [];
	public static $log_enabled = false;

	public function beforeModuleProc($obj)
	{
		$config = $this->getConfig();
		if ($config->module_enabled !== 'Y') return;

		if (Cookie::get('auto_login_failed_due_ip') === 'Y' && str_contains(strtolower($obj->act), 'disp'))
		{
			Cookie::remove('auto_login_failed_due_ip');
			return new BaseObject(-1, 'msg_auto_loggout_due_to_ip_restriction');
		}

		self::$log_enabled = $config->admin_log_enabled === 'Y';
		if (str_contains(strtolower($obj->act), 'adminextend'))
		{
			self::$log_enabled = true;
		}

		if ($obj->mid === 'admin' || $obj->module === 'admin' || str_contains(strtolower($obj->act), 'admin'))
		{
			$log_srl = $this->insertLog('U', $obj->act);
			self::$allowed_acts = \Rhymix\Modules\Adminextend\Models\Permission::getAllowedActs(array_keys($this->user->group_list));
			
			if ($this->user->member_srl === $config->super_admin_member_srl)
			{
				$this->updateLogAuthroizedStatus($log_srl, 'S');
				return;
			}

			if (in_array($config->access_level, ['login_and_admin_act', 'all_act']) && !Login::checkMemberAllowedIpRangeByGroup($this->user->member_srl))
			{
				Context::set('gnbUrlList', []);
				$this->updateLogAuthroizedStatus($log_srl, 'N');
				return new BaseObject(-1, 'msg_not_allowed_ip');
			}

			if (in_array('__all__', self::$allowed_acts))
			{
				$this->updateLogAuthroizedStatus($log_srl, 'Y');
				return;
			}

			$gnbUrlList = Context::get('gnbUrlList');
			$gnbUrlList = $this->updateUrlList($gnbUrlList);
			Context::set('gnbUrlList', $gnbUrlList);

			if (!in_array($obj->act, self::$allowed_acts))
			{
				$this->updateLogAuthroizedStatus($log_srl, 'N');
				return new BaseObject(-1, 'msg_not_permitted_act');
			}

			$this->updateLogAuthroizedStatus($log_srl, 'Y');
		}
		else
		{
			if ($config->access_level !== 'all_act') return;
			if (!$this->user->isMember()) return;
			if ($this->user->member_srl === $config->super_admin_member_srl) return;
			if (in_array($obj->act, ['dispMemberLogout'])) return;

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

	public function insertLog(string $is_authorized = 'N', string $act = ''): int
	{
		if (!self::$log_enabled) return -1;

		$args = new stdClass;
		$args->module = Context::get('module') ?: 'admin';
		$args->act = $act ?? (Context::get('act') ?: 'unknown');
		$args->request_vars = print_r(Context::getRequestVars(), TRUE);
		$args->member_srl = $this->user->member_srl;
		$args->ipaddress = \RX_CLIENT_IP;
		$args->regdate = date('YmdHis');
		$args->is_authorized = $is_authorized;
		$id = Log::insertLog($args);

		if ($is_authorized === 'N')
		{
			$this->reportToSuperAdmin($id);
		}

		return $id;
	}

	public function updateLogAuthroizedStatus(int $log_srl, string $is_authorized): void
	{
		if (!self::$log_enabled) return;
		
		$args = new stdClass;
		$args->log_idx = $log_srl;
		$args->is_authorized = $is_authorized;

		Log::updateLog($args);

		if ($is_authorized === 'N')
		{
			$this->reportToSuperAdmin($log_srl);
		}
	}

	public function reportToSuperAdmin(int $log_srl): void
	{
		$config = $this->getConfig();
		if ($config->report_super_admin_when_unauthorized_act !== 'Y') return;

		if ($config->last_reported_time > time() - 600)
		{
			return;
		}

		$oNcenterliteController = ncenterliteController::getInstance();
		$oNcenterliteController->sendNotification($config->super_admin_member_srl, $config->super_admin_member_srl, lang('adminextend.notification_unauthorized'), getNotEncodedUrl('','module','admin','act','dispAdminextendAdminLogIndex'));
	}
}
