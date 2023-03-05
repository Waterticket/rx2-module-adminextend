<?php

namespace Rhymix\Modules\Adminextend\Controllers;

use Rhymix\Modules\Adminextend\Base;
use Rhymix\Modules\Adminextend\Models\Log;
use Context;

class AdminLog extends Base
{
    /**
     * 초기화
     */
    public function init()
    {
        // 관리자 화면 템플릿 경로 지정
        $this->setTemplatePath($this->module_path . 'views/admin/');
    }
    
    public function dispAdminextendAdminLogIndex() 
    {
        // 현재 설정 상태 불러오기
        $config = $this->getConfig();
        
        // Context에 세팅
        Context::set('config', $config);

        $vars = Context::getRequestVars();
        $args = new \stdClass();
        $args->page = $vars->page ? $vars->page : 1;
        $args->search_target = $vars->search_target ? $vars->search_target : '';
        $args->search_keyword = $vars->search_keyword ? $vars->search_keyword : '';

        $output = Log::getLogList($args);
        Context::set('log_list', $output->data);
        Context::set('total_count', $output->total_count);
        Context::set('total_page', $output->total_page);
        Context::set('page', $output->page);
        Context::set('page_navigation', $output->page_navigation);
        
        // 스킨 파일 지정
        $this->setTemplateFile('index_log');
    }
}
