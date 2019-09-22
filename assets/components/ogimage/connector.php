<?php
if (file_exists(dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php')) {
    /** @noinspection PhpIncludeInspection */
    require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php';
}
else {
    require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/config.core.php';
}
/** @noinspection PhpIncludeInspection */
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
/** @noinspection PhpIncludeInspection */
require_once MODX_CONNECTORS_PATH . 'index.php';
/** @var ogImage $ogImage */
$ogImage = $modx->getService('ogimage', 'ogImage', $modx->getOption('ogimage_core_path', null,
        $modx->getOption('core_path') . 'components/ogimage/') . 'model/ogimage/'
);
$modx->lexicon->load('ogimage:default');

// handle request
$corePath = $modx->getOption('ogimage_core_path', null, $modx->getOption('core_path') . 'components/ogimage/');
$path = $modx->getOption('processorsPath', $ogImage->config, $corePath . 'processors/');
$modx->getRequest();

/** @var modConnectorRequest $request */
$request = $modx->request;
$request->handleRequest(array(
    'processors_path' => $path,
    'location' => '',
));