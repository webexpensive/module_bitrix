<?php

use \Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

global $USER;
if ($USER->IsAdmin()) {
    return [
        [
            'parent_menu' => 'global_menu_settings',
            'sort' => 300,
            'url' => 'record.php',
            'text' => Loc::getMessage('ACU_RECORD_MENU_NAME'),
            'title' => Loc::getMessage('ACU_RECORD_MENU_TITLE'),
            'icon' => 'record_menu_icon'
        ]
    ];
}
