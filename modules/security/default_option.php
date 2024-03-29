<?php

$security_default_option = array(
    "ipcheck_allow_self_block" => "N",
    "ipcheck_disable_file" => "",
    "filter_action" => "filter", //filter | clear | none
    "filter_stop" => "N",
    "filter_duration" => 30,
    "filter_log" => "Y",
    "session" => "N",
    "hotp_user_window" => 10,
    "otp_enabled" => "N",
    "otp_allow_remember" => "N",
    "otp_allow_recovery_codes" => "N",
    "otp_mandatory_using" => "N",
    "otp_mandatory_skip_days" => "10",
    "otp_mandatory_rights" => "a:1:{i:0;s:2:\"G1\";}",
    "otp_default_algo" => "hotp",
    "redirect_log" => "Y",
    "redirect_referer_check" => "Y",
    "redirect_href_sign" => "Y",
    "redirect_action" => "show_message_and_stay", // force_url | show_message | show_message_and_stay
    "redirect_message_warning" => "",
    "redirect_message_timeout" => 30,
    "redirect_url" => "/",
    "checker_region_kernel" => "Y",
    "checker_region_root" => "Y",
    "checker_region_personal_root" => "Y",
    "checker_region_public" => "Y",
    "checker_exts" => "php,js,htaccess",
    "checker_time" => 30,
    "antivirus_timeout" => 10,
    "antivirus_action" => "notify_only",
    "security_event_db_active" => "Y",
    "security_event_format" => "#AUDIT_TYPE# | #SITE_ID# | #USER_INFO# | #URL# | #VARIABLE_NAME# | #VARIABLE_VALUE_BASE64#",
    "security_event_userinfo_format" => "#REMOTE_ADDR# | #USER_ID#",
    "security_event_syslog_active" => "N",
    "security_event_syslog_facility" => LOG_SYSLOG,
    "security_event_syslog_priority" => LOG_WARNING,
    "security_event_file_active" => "N",
    "security_event_file_path" => "",
    "needed_tests_packages" => "a:3:{i:0;s:10:\"slow_local\";i:1;s:10:\"fast_local\";i:2;s:6:\"remote\";}",
);
