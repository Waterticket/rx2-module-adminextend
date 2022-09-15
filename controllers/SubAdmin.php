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
		$this->setTemplateFile('subadmin_config');
	}
}
