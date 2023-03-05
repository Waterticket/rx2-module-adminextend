<?php

namespace Rhymix\Modules\Adminextend\Models;

use Rhymix\Framework\Exceptions\DBError;
use Rhymix\Framework\DB;
use BaseObject;
use stdClass;

class Log {
    /**
     * adminextend_admin_log 테이블에 Log 하나를 추가한다.
     * 
     * @param object $obj
     */
    public static function insertLog(object $obj): int
    {
        $oDB = DB::getInstance();
        $oDB->begin();

        $output = executeQuery('adminextend.insertLog', $obj);
        if(!$output->toBool())
        {
            $oDB->rollback();
            throw new DBError(sprintf("DB Error: %s in %s line %s", $output->getMessage(), __FILE__, __LINE__));
        }
        $id = $oDB->getInsertID();
        $oDB->commit();

        return $id;
    }

    /**
     * adminextend_admin_log 테이블에서 Log를 가져온다.
     * 
     * @param int $log_idx
     */
    public static function getLog(int $log_idx): object
    {
        $args = new stdClass();
        $args->log_idx = $log_idx;

        $output = executeQuery('adminextend.getLog', $args);
        if(!$output->toBool())
        {
            throw new DBError(sprintf("DB Error: %s in %s line %s", $output->getMessage(), __FILE__, __LINE__));
        }

        return $output->data ?: new stdClass();
    }

    /**
     * adminextend_admin_log 테이블에서 Log 여러 건을 가져온다.
     * 
     * @param array $log_idx
     */
    public static function getLogs(array $log_idx): array
    {
        $args = new stdClass();
        $args->log_idx = $log_idx;

        $output = executeQueryArray('adminextend.getLogs', $args);
        if(!$output->toBool())
        {
            throw new DBError(sprintf("DB Error: %s in %s line %s", $output->getMessage(), __FILE__, __LINE__));
        }

        return $output->data ?: array();
    }


    /**
     * adminextend_admin_log 테이블에서 Log를 리스트 형식으로 가져온다.
     * 
     * @param object $obj
     */
    public static function getLogList(object $obj): object
    {
        $obj->sort_index = $obj->sort_index ?? 'log_idx';
        $obj->order_type = $obj->order_type ?? 'desc';
        $obj->list_count = $obj->list_count ?? 20;
        $obj->page_count = $obj->page_count ?? 10;
        $obj->page = $obj->page ?? 1;

        $output = executeQueryArray('adminextend.getLogList', $obj);
        if(!$output->toBool())
        {
            throw new DBError(sprintf("DB Error: %s in %s line %s", $output->getMessage(), __FILE__, __LINE__));
        }

        return $output;
    }


    /**
     * adminextend_admin_log 테이블에서 Log를 업데이트한다.
     * 
     * @param object $obj
     */
    public static function updateLog(object $obj): object
    {
        $oDB = DB::getInstance();
        $oDB->begin();

        $output = executeQuery('adminextend.updateLog', $obj);
        if(!$output->toBool())
        {
            $oDB->rollback();
            throw new DBError(sprintf("DB Error: %s in %s line %s", $output->getMessage(), __FILE__, __LINE__));
        }
        $oDB->commit();

        return new BaseObject();
    }
}