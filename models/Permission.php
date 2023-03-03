<?php

namespace Rhymix\Modules\Adminextend\Models;

class Permission {
    private const PERMISSION_LIST = [
        "member_list" => [
            "dispMemberAdminList",
        ],
        "member_view" => [
            "dispMemberAdminInsert",
        ],
        "member_manage" => [
            "procMemberAdminInsert",
            "procMemberAdminSelectedMemberManage",
        ],
    ];

    public static function getPermissionList() {
        return array_keys(self::PERMISSION_LIST);
    }

    public static function checkPermissionByAct($permissions, $act) {
        $allowed_acts = [];
        foreach ($permissions as $permission) {
            if (isset(self::PERMISSION_LIST[$permission])) {
                $allowed_acts = array_merge($allowed_acts, self::PERMISSION_LIST[$permission]);
            }
        }

        if (in_array($act, $allowed_acts)) {
            return true;
        }

        return false;
    }
}