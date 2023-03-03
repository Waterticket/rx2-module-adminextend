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
class SubAdmin extends Base
{
	/**
	 * 초기화
	 */
	public function init()
	{
		// 관리자 화면 템플릿 경로 지정
		$this->setTemplatePath($this->module_path . 'views/admin/');
	}
	
	public function dispAdminextendAdminSubAdminConfig()
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

	public function procAdminextendAdminInsertSubAdminConfig()
	{
		// 현재 설정 상태 불러오기
		$config = $this->getConfig();
		
		// 제출받은 데이터 불러오기
		$vars = Context::getRequestVars();

		$config->permission = $vars->permission;
		
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
