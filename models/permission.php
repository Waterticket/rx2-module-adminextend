<?php

namespace Rhymix\Modules\Adminextend\Models;

class Permission {
    private const PERMISSION_LIST = [
        "__all__" => [
            "__all__",
        ],
        "__dashboard__" => [
            "__dashboard__",
            "dispAdminIndex",
        ],
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
        "point_config_view" => [
            "dispPointAdminConfig",
            "dispPointAdminModuleConfig",
            "dispPointAdminPointList",
        ],
        "point_config_manage" => [
            "procPointAdminInsertConfig",
            "procPointAdminInsertModuleConfig",
            "procPointAdminUpdatePoint",
            "procPointAdminReCal",
            "procPointAdminApplyPoint",
            "procPointAdminReset",
        ],
        "document_list_view" => [
            "dispDocumentAdminList",
            "dispDocumentAdminConfig",
            "procDocumentGetList",
        ],
        "document_declared_list_view" => [
            "dispDocumentAdminDeclared",
        ],
        "document_manage" => [
            "procDocumentManageCheckedDocument",
            "getSiteAllList",
            "getMenuAdminSiteMap",
        ],
        "comment_list_view" => [
            "dispCommentAdminList",
            "procCommentGetList",
        ],
        "comment_declared_list_view" => [
            "dispCommentAdminDeclared",
        ],
        "comment_manage" => [
            "procCommentAdminDeleteChecked",
        ],
        "trash_list_view" => [
            "dispTrashAdminList",
            "procTrashAdminGetList",
        ],
        "trash_restore" => [
            "procTrashAdminRestore",
        ],
        "trash_delete" => [
            "procTrashAdminEmptyTrash",
        ],
        "spamfilter_view" => [
            "dispSpamfilterAdminDeniedIPList",
            "dispSpamfilterAdminDeniedWordList",
            "dispSpamfilterAdminConfigBlock",
            "getModuleAdminMultilingualHtml",
        ],
        "spamfilter_manage" => [
            "procSpamfilterAdminInsertDeniedIP",
            "procSpamfilterAdminDeleteDeniedIP",
            "procSpamfilterAdminInsertDeniedWord",
            "procSpamfilterAdminDeleteDeniedWord",
            "procSpamfilterAdminInsertConfig",
        ],
        "spamfilter_captcha_view" => [
            "dispSpamfilterAdminConfigCaptcha",
        ],
        "spamfilter_captcha_manage" => [
            "procSpamfilterAdminInsertConfigCaptcha",
        ],
        "installed_module_view" => [
            "dispModuleAdminContent",
            "dispModuleAdminCategory",
        ],
        "installed_addon" => [
            "dispAddonAdminIndex",
            "procAddonAdminSaveActivate",
        ],
        "installed_widget" => [
            "dispWidgetAdminDownloadedList",
            "dispWidgetAdminGenerateCode",
            "procWidgetGetColorsetList",
            "getSiteAllList",
            "getMenuAdminSiteMap",
            "procWidgetGenerateCode",
        ],
        "multi_lang_manage" => [
            "dispModuleAdminLangcode",
            "getModuleAdminLangListHtml",
            "procModuleAdminInsertLang",
            "getModuleAdminLangCode",
            "procModuleAdminDeleteLang",
        ],
        "rss_manage" => [
            "dispRssAdminIndex",
            "procRssAdminInsertConfig",
            "procRssAdminInsertModuleConfig",
        ]
    ];

    public static function getPermissionList() {
        return array_keys(self::PERMISSION_LIST);
    }

    public static function getAllowedPermission($group_srls)
    {
        $config = \Rhymix\Modules\Adminextend\Base::getConfig();
        $allowed_permissions = [];
        foreach ($config->permission as $group_srl => $permission)
        {
            if (in_array($group_srl, $group_srls))
            {
                $allowed_permissions = array_merge($allowed_permissions, array_keys($permission));
            }
        }

        return array_unique($allowed_permissions);
    }

    public static function getAllowedActs($group_srls)
    {
        $allowed_acts = [];
        $allowed_permissions = self::getAllowedPermission($group_srls);
        foreach ($allowed_permissions as $permission)
        {
            if (isset(self::PERMISSION_LIST[$permission]))
            {
                $allowed_acts = array_merge($allowed_acts, self::PERMISSION_LIST[$permission]);
            }
        }

        $config = \Rhymix\Modules\Adminextend\Base::getConfig();
        foreach ($group_srls as $group_srl)
        {
            if (isset($config->custom_allowed_act[$group_srl]))
            {
                $allowed_acts = array_merge($allowed_acts, $config->custom_allowed_act[$group_srl]);
            }
        }

        return array_unique($allowed_acts);
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