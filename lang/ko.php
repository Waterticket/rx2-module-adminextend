<?php

$lang->cmd_adminextend = '관리자 확장팩';
$lang->cmd_adminextend_general_config = '기본 설정';
$lang->cmd_adminextend_subadmin_config = '부관리자 설정';
$lang->cmd_adminextend_admin_log = '관리자 로그';
$lang->log_idx = '로그 번호';
$lang->act = 'Act';
$lang->member = '회원';
$lang->is_authorized = '허용 상태';
$lang->is_authorized_U = '알 수 없음';
$lang->is_authorized_Y = '허용';
$lang->is_authorized_N = '거부';
$lang->is_authorized_S = '허용(최고관리자)';
$lang->msg_not_permitted_only_super_admin = '최고 관리자만 접근할 수 있습니다.';

$lang->login_ip_range = '로그인 IP 대역';
$lang->about_login_ip_range = '그룹별로 로그인을 허용할 IP 대역을 설정할 수 있습니다. 예) 10.0.0.0/8, 192.168.0.12<br>한 줄에 하나의 아이피, 대역만 입력해주세요';
$lang->msg_not_allowed_ip = '접근이 허용된 아이피가 아닙니다. 관리자에게 문의하세요.';
$lang->msg_current_ip_will_be_denied = '주어진 설정에 따르면 현재 로그인하신 IP 주소도 차단됩니다. 다시 확인해 주십시오.';
$lang->module_enabled = '모듈 활성화';
$lang->about_module_enabled = '모듈을 활성화하면 관리자 확장팩의 기능을 사용할 수 있습니다.';
$lang->super_admin_member_srl = '최고 관리자 srl';
$lang->about_super_admin_member_srl = '최고 관리자의 srl을 입력해주세요. 최고 관리자는 모든 권한을 가집니다.<br>또한 모듈 활성화 후에는 최고관리자만 이 모듈의 설정을 조회/변경할 수 있습니다.';

$lang->admin_log_enabled = '관리자 로그 기록';
$lang->about_admin_log_enabled = '관리자 로그 기록을 활성화하면 관리자가 수행한 모든 작업을 기록합니다. 다만 비활성화 하더라도 관리자 확장팩 관련 설정은 기록됩니다.';
$lang->report_super_admin_when_unauthorized_act = '권한 없는 작업 발생시 최고 관리자에게 알림';
$lang->about_report_super_admin_when_unauthorized_act = '다른 관리자들이 권한 없는 작업에 접근할 경우 최고 관리자에게 알림을 보냅니다. (알림은 최소 10분 간격으로 발송됩니다.)';
$lang->notification_unauthorized = '허용되지 않은 작업 시도 알림';

$lang->permission = '권한';
$lang->about_permission = '권한을 설정하면 해당 권한을 가진 관리자만 해당 메뉴를 볼 수 있습니다.';

$lang->permission___all__ = '전체 허용';
$lang->permission___dashboard__ = '대시보드';
$lang->permission_member_list = '회원 목록';
$lang->permission_member_view = '회원 조회';
$lang->permission_member_manage = '회원 추가/수정/관리';
$lang->permission_point_config_view = '포인트 설정 조회';
$lang->permission_point_config_manage = '포인트 설정 수정';
$lang->permission_document_list_view = '문서 목록 조회';
$lang->permission_document_declared_list_view = '신고된 문서 목록 조회';
$lang->permission_document_manage = '문서 수정/이동/관리';
$lang->permission_comment_list_view = '댓글 목록 조회';
$lang->permission_comment_declared_list_view = '신고된 댓글 목록 조회';
$lang->permission_comment_manage = '댓글 수정/관리';
$lang->permission_trash_list_view = '휴지통 목록 조회';
$lang->permission_trash_restore = '휴지통에서 복원';
$lang->permission_trash_delete = '휴지통에서 삭제';
$lang->permission_spamfilter_view = '스팸필터 조회';
$lang->permission_spamfilter_manage = '스팸필터 수정/관리';
$lang->permission_spamfilter_captcha_view = '스팸필터 캡챠설정 조회';
$lang->permission_spamfilter_captcha_manage = '스팸필터 캡챠설정 수정';
$lang->permission_installed_module_view = '설치된 모듈 조회';
$lang->permission_installed_addon = '설치된 애드온 조회/설정';
$lang->permission_installed_widget = '설치된 위젯 조회/생성';
$lang->permission_multi_lang_manage = '다국어 관리';
$lang->permission_rss_manage = 'RSS 관리';