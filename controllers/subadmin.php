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
use MemberModel;
use Rhymix\Modules\Adminextend\Models\Permission;

/**
 * 관리자 확장팩
 * 
 * Copyright (c) Waterticket
 * 
 * Generated with https://www.poesis.org/tools/modulegen/
 */
class Subadmin extends Base
{
	/**
	 * 초기화
	 */
	public function init()
	{
		// 관리자 화면 템플릿 경로 지정
		$this->setTemplatePath($this->module_path . 'views/admin/');
	}
	
	public function dispAdminextendAdminSubadminConfig()
	{
		$oMemberModel = MemberModel::getInstance();
		$group_list = $oMemberModel->getGroups();
		Context::set('group_list', $group_list);

		$permission_list = Permission::getPermissionList();
		Context::set('permission_list', $permission_list);

		$config = $this->getConfig();
		Context::set('config', $config);

		$this->setTemplateFile('subadmin_config');
	}

	public function procAdminextendAdminInsertSubadminConfig()
	{
		// 현재 설정 상태 불러오기
		$config = $this->getConfig();
		
		// 제출받은 데이터 불러오기
		$vars = Context::getRequestVars();

		if ($config->module_enabled == 'Y' && $this->user->member_srl != $config->super_admin_member_srl)
		{
			throw new Exception('msg_not_permitted_only_super_admin');
		}

		$config->permission = $vars->permission;

		$custom_allowed_act = [];
		$oMemberModel = MemberModel::getInstance();
		$group_list = $oMemberModel->getGroups();
		foreach($group_list as $group_srl => $group_info)
		{
			$allowed_act = array_map('trim', preg_split('/[\r\n]/', $vars->custom_allowed_act[$group_srl]));
			$allowed_act = array_unique(array_filter($allowed_act, function($item) {
				return $item !== '';
			}));

			$custom_allowed_act[$group_srl] = $allowed_act;
		}

		$config->custom_allowed_act = $custom_allowed_act;
		
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
