<?

require_once __DIR__ . '/autoload.php';

/*patchlimitationmutatormark1*/
CJSCore::RegisterExt(
    'bp_selector',
    array(
        'js' => '/bitrix/js/bizproc/bp_selector.js',
        'css' => '/bitrix/js/bizproc/css/bp_selector.css',
        'lang' => '/bitrix/modules/bizproc/lang/' . LANGUAGE_ID . '/install/js/bp_selector.php',
        'rel' => array('core', 'popup', 'translit'),
    )
);

CJSCore::RegisterExt(
    'bp_starter',
    array(
        'js' => '/bitrix/js/bizproc/starter.js',
        //'css' => '/bitrix/js/bizproc/css/starter.css',
        'lang' => '/bitrix/modules/bizproc/lang/' . LANGUAGE_ID . '/install/js/starter.php',
        'rel' => array('core', 'popup', 'socnetlogdest'),
    )
);

CJSCore::RegisterExt(
    'bp_user_selector',
    array(
        'js' => '/bitrix/js/bizproc/user_selector.js',
        //'css' => '/bitrix/js/bizproc/css/starter.css',
        'lang' => '/bitrix/modules/bizproc/lang/' . LANGUAGE_ID . '/install/js/user_selector.php',
        'rel' => ['core', 'popup', 'socnetlogdest', 'bp_field_type'],
    )
);

CJSCore::RegisterExt(
    'bp_field_type',
    array(
        'js' => '/bitrix/js/bizproc/fieldtype.js',
        'css' => '/bitrix/js/bizproc/css/fieldtype.css',
        'lang' => '/bitrix/modules/bizproc/lang/' . LANGUAGE_ID . '/install/js/fieldtype.php',
        'rel' => array('core', 'popup', 'socnetlogdest', 'bp_user_selector'),
        'oninit' => function () {
            \Bitrix\Main\Loader::includeModule('socialnetwork');
        },
    )
);