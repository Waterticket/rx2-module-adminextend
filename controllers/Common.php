<?php

namespace Rhymix\Modules\Adminextend\Controllers;

use Rhymix\Modules\Adminextend\Base;
use Rhymix\Framework\Cache;
use Rhymix\Framework\DB;
use Rhymix\Framework\Exception;
use Rhymix\Framework\Storage;
use BaseObject;
use Context;
use ModuleController;
use ModuleModel;

use Rhymix\Modules\Adminextend\Models\Login;
use Rhymix\Framework\Filters\IpFilter;

/**
 * 관리자 확장팩
 * 
 * Copyright (c) Waterticket
 * 
 * Generated with https://www.poesis.org/tools/modulegen/
 */
class Common extends Base
{
	/**
	 * 초기화
	 */
	public function init()
	{
		// 관리자 화면 템플릿 경로 지정
		$this->setTemplatePath($this->module_path . 'views/admin/');
	}
	
	/**
	 * 관리자 설정 화면 예제
	 */
	public function dispAdminextendAdminConfig()
	{
		$config = $this->getConfig();
		Context::set('config', $config);

		$oMemberModel = getModel('member');
		$group_list = $oMemberModel->getGroups();
		Context::set('group_list', $group_list);
		
		$this->setTemplateFile('config');
	}
	
	/**
	 * 관리자 설정 저장 액션 예제
	 */
	public function procAdminextendAdminInsertConfig()
	{
		// 현재 설정 상태 불러오기
		$config = $this->getConfig();
		
		// 제출받은 데이터 불러오기
		$vars = Context::getRequestVars();

		$config->module_enabled = ($vars->module_enabled == 'Y') ? 'Y' : 'N';
		$config->super_admin_member_srl = $vars->super_admin_member_srl ?? 4;
		
		$oMemberModel = getModel('member');
		$group_list = $oMemberModel->getGroups();
		$login_ip_range_group = array();
		foreach($group_list as $group_srl => $group_info)
		{
			$allowed_ip = array_map('trim', preg_split('/[\r\n]/', $vars->login_ip_range_by_group[$group_srl]));
			$allowed_ip = array_unique(array_filter($allowed_ip, function($item) {
				return $item !== '';
			}));
			if (!IpFilter::validateRanges($allowed_ip)) {
				throw new Exception('msg_invalid_ip');
			}

			$login_ip_range_group[$group_srl] = $allowed_ip;
		}

		if (!Login::checkMemberAllowedIpRangeByGroup(null, $login_ip_range_group))
		{
			throw new Exception('msg_current_ip_will_be_denied');
		}

		$config->login_ip_range_by_group = $login_ip_range_group;
		
		// 변경된 설정을 저장
		$output = $this->setConfig($config);
		if (!$output->toBool())
		{
			return $output;
		}
		
		// 설정 화면으로 리다이렉트
		$this->setMessage('success_registed');
		$this->setRedirectUrl(Context::get('success_return_url'));
	}
}
