<?php

$properties = array();

$tmp = array(
    /*'tpl' => array(
        'type' => 'textfield',
        'value' => 'tpl.ograph',
    ),*/
    'caption' => array(
        'type' => 'textfield',
    ),
    /*'imageSrc' =>  array(
        'type' => 'textfield',
        'value' => '[[+assetsUrl]]ogimage_background.jpg'
    ),
    'previewsUrl' => array(
        'type' => 'textfield',
        'value' => '[[+assetsUrl]]previews/'
    ),
    'font' => array(
        'type' => 'textfield',
        'value' => '[[+assetsUrl]]fonts/OpenSans-Regular.ttf'
    ),*/
    'textPosition' => array(
        'type' => 'list',
        'options' => array(
            array('text' => 'top', 'value' => 'top'),
            array('text' => 'middle', 'value' => 'middle'),
            array('text' => 'bottom', 'value' => 'bottom'),
        ),
        'value' => 'top'
    ),
    'textAlign' => array(
        'type' => 'list',
        'options' => array(
            array('text' => 'left', 'value' => 'left'),
            array('text' => 'center', 'value' => 'center'),
            array('text' => 'right', 'value' => 'right'),
        ),
        'value' =>'left'
    ),
    'fontSize' => array(
        'type' => 'numberfield',
        'value' => 30,
    ),
    'lineHeight' => array(
        'type' => 'numberfield',
        'value' => 1.45,
    ),
    'fontColor' => array(
        'type' => 'textfield',
        'value' => '#FFFFFF'
    ),
    'padding' => array(
        'type' => 'numberfield',
        'value' => 0
    ),
    'vPadding' => array(
        'type' => 'numberfield',
        'value' => 20
    ),
    'hPadding' => array(
        'type' => 'numberfield',
        'value' => 20
    ),
    'width' => array(
        'type' => 'numberfield',
        'value' => ''
    ),
    'height' => array(
        'type' => 'numberfield',
        'value' => ''
    ),
    'brightness' => array(
        'type' => 'textfield',
        'value' => 0
    ),
    'quality' => array(
        'type' => 'numberfield',
        'value' => 90
    ),
    'override' => array(
        'type' => 'combo-boolean',
        'value' => 0,
    ),
    'resId' => array(
        'type' => 'textfield',
        'value' => ''
    )
);

foreach ($tmp as $k => $v) {
    $properties[] = array_merge(
        array(
            'name' => $k,
            'desc' => PKG_NAME_LOWER . '_prop_' . $k,
            'lexicon' => PKG_NAME_LOWER . ':properties',
        ), $v
    );
}

return $properties;