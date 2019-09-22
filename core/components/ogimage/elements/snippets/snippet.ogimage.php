<?php
/** @var modX $modx */
/** @var array $scriptProperties */
/** @var ogImage $ogImage */

if (!$modx->loadClass('ogImage',  $modx->getOption('ogimage_core_path', null, $modx->getOption('core_path') . 'components/ogimage/') . 'model/ogimage/', false, true)) {
    return false;
}
$ogImage = new ogImage($modx, $scriptProperties);

$result = '';
$values = $ogImage->run();

if ($values) {
    if (!empty($values['image']))
        $result = $values['image'];
}

return $result;