<?php
/** @var modX $modx */
/** @var array $sources */

$settings = array();

$tmp = array(
    'image_font_file' => array(
        'xtype' => 'textfield',
        'area' => 'ogimage_main',
        'value' => '{assets_url}components/ogimage/fonts/OpenSans_Regular.ttf'
    ),
    'previews_url' => array(
        'xtype' => 'textfield',
        'value' => '{assets_url}components/ogimage/previews/',
        'area' => 'ogimage_main',
    ),
);

foreach ($tmp as $k => $v) {
    /** @var modSystemSetting $setting */
    $setting = $modx->newObject('modSystemSetting');
    $setting->fromArray(array_merge(
        array(
            'key' => 'ogimage_' . $k,
            'namespace' => PKG_NAME_LOWER,
        ), $v
    ), '', true, true);

    $settings[] = $setting;
}
unset($tmp);

return $settings;
