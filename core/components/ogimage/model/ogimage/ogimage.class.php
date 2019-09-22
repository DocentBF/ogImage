<?php

class ogImage
{
    /**
     * @var modX $modx
     * @var array $params
     * @var array $config
     * @var string $error
     */
    public $modx, $params;
    private $config, $error = '';


    /**
     * @param modX $modx
     * @param array $params
     */
    function __construct(modX &$modx, array $params = array())
    {
        $this->modx =& $modx;

        $corePath = $this->modx->getOption('ogimage_core_path', null,
            $this->modx->getOption('core_path') . 'components/ogimage/'
        );
        $assetsUrl = $this->modx->getOption('ogimage_assets_url', null,
            $this->modx->getOption('assets_url') . 'components/ogimage/'
        );

        $this->config = array(
            'assetsUrl' => $assetsUrl,
            'cssUrl' => $assetsUrl . 'css/',
            'jsUrl' => $assetsUrl . 'js/',
            'imagesUrl' => $assetsUrl . 'images/',
            'connectorUrl' => $assetsUrl . 'connector.php',

            'corePath' => $corePath,
            'modelPath' => $corePath . 'model/',
            'chunksPath' => $corePath . 'elements/chunks/',
            'templatesPath' => $corePath . 'elements/templates/',
            'chunkSuffix' => '.chunk.tpl',
            'snippetsPath' => $corePath . 'elements/snippets/',
            'processorsPath' => $corePath . 'processors/',
        );

        if (!$this->modx->resource) {
            $this->error('ogimage_resource_nf', true);
            return false;
        }

        $this->setParams($params);

        $this->modx->addPackage('ogimage', $this->config['modelPath']);
        $this->modx->lexicon->load('ogimage:default');

        return;
    }

    /**
     * @param string $message
     * @param bool $fromLexicon
     */
    public function error($message, $fromLexicon = false)
    {
        if ($fromLexicon)
            $message = $this->modx->lexicon($message);
        $this->error = $message;
    }

    /**
     * @param array $params
     */
    public function setParams(array $params = array())
    {
        // valid params
        $valid = array(
            "textPosition" => array("top", "middle", "bottom"),
            "textAlign" => array("left", "right", "center")
        );

        if (isset($params["fontSize"]) && !is_numeric($params["fontSize"]))
            unset($params["fontSize"]);

        if (isset($params["lineHeight"]) && !is_numeric($params["lineHeight"]))
            unset($params["lineHeight"]);

        if (isset($params["brightness"]) && !is_numeric($params["brightness"]))
            unset($params["brightness"]);

        if (isset($params["padding"]) && !is_numeric($params["padding"]))
            unset($params["padding"]);

        if (isset($params["vPadding"]) && !is_numeric($params["vPadding"]))
            unset($params["vPadding"]);

        if (isset($params["hPadding"]) && !is_numeric($params["hPadding"]))
            unset($params["hPadding"]);

        if (isset($params["quality"]) && !is_numeric($params["quality"]))
            unset($params["quality"]);

        foreach ($valid as $key => $values) {
            if (isset($params[$key])) {
                if (!in_array($params[$key], $values)) {
                    unset($params[$key]);
                }
            }
        }

        if (isset($params["fontColor"])) {
            if (!preg_match("/#([a-f0-9]{3}){1,2}\b/i", $params["fontColor"]))
                unset($params["fontColor"]);
        }

        if(empty($params["resId"])) {
            $params["resId"] = $this->modx->resource->get('id');
        }


        // default params
        $this->params = array_merge(array(
            'caption' => $this->modx->getOption('site_name'),
            //'descriptionSrc' => 'description',
            'imageSrc' => $this->config['assetsUrl'] . "ogimage_background.jpg",
            'previewsUrl' => trim($this->modx->getOption('ogimage_previews_url', null)),

            'font' => trim($this->modx->getOption('ogimage_image_font_file', null)),
            'textPosition' => 'top',
            'textAlign' => 'left',
            'fontSize' => 30,
            'lineHeight' => 1.45,
            'fontColor' => '#FFFFFF',
            'padding' => 0,
            'vPadding' => 20,
            'hPadding' => 20,
            'width' => '',
            'height' => '',
            'brightness' => 0,
            'quality' => 90,

            'override' => false,
        ), $params);

        $previewsPath = MODX_BASE_PATH . trim($this->params["previewsUrl"], '/\\') . "/";
        if (!is_dir($previewsPath)) {
            $this->params['previewsUrl'] = $this->config['assetsUrl'] . "previews/";
        }
        $this->params['previewsPath'] = MODX_BASE_PATH . ltrim($this->params['previewsUrl'], '/\\');

        $this->params['font'] = MODX_BASE_PATH . ltrim($this->params['font'], '/\\');
        if (!file_exists($this->params['font']))
            $this->params["font"] = MODX_ASSETS_PATH . "components/ogimage/fonts/OpenSans-Regular.ttf";

        // color to array
        $this->params['fontColor'] = sscanf($this->params['fontColor'], '#%02x%02x%02x');
        // convert font size
        $this->params['fontSize'] = $this->convertFontSize($this->params['fontSize']);
        $this->params['lineHeight'] = $this->params['lineHeight'] * $this->params['fontSize'];

        if($this->params['padding']) {
            $this->params['vPadding'] = $this->params['hPadding'] = $this->params['padding'];
        }
    }

    /**
     * @param $size
     * @return float
     */
    protected function convertFontSize($size)
    {
        return 0.75 * $size;
    }

    /**
     * @return mixed
     */
    public function run()
    {
        $image = MODX_BASE_PATH . ltrim($this->params['imageSrc'], '/\\');
        if(!is_file($image))
            $this->error('ogimage_err_ne_image', true);

        $title = $this->params['caption'];

        $preview = $this->generatePreview($image, $title);

        if (!empty($this->error)) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, $this->error);
        }

        $results = array(
            'image' => $preview,
            'title' => $title,
            'error' => !empty($this->error)
        );

        return $results;
    }

    /**
     * @param string $image
     * @param string $text
     * @return mixed
     */
    public function generatePreview($image, $text)
    {
        if (!function_exists('gd_info')) {
            $this->error('ogimage_err_gd', true);
            return false;
        }
        if (!file_exists($image) || !is_file($image)) {
            $this->error('ogimage_err_ne_image', true);
            $this->error($image);
            return false;
        }

        $resID = $this->params["resId"];
        $resultImage = $this->params['previewsPath'] . "{$resID}.jpg";

        if (!$this->params['override']) {
            if (file_exists($resultImage)) {
                return $this->params['previewsUrl'] . "{$resID}.jpg";
            }
        }

        $imgDimensions = getimagesize($image);
        $customDimensions = false;
        if (empty($this->params['width']))
            $width = $imgDimensions[0];
        else {
            $width = $this->params['width'];
            $customDimensions = true;
        }

        if (empty($this->params['height']))
            $height = $imgDimensions[1];
        else {
            $height = $this->params['height'];
            $customDimensions = true;
        }

        if($customDimensions) {
            $aspectRatio = $imgDimensions[0] / $imgDimensions[1];
            if ($width / $height > $aspectRatio) {
                $width = $height * $aspectRatio;
            } else {
                $height = $width / $aspectRatio;
            }
        }


        $tmpImg = imagecreatetruecolor($width, $height);
        imagecopyresampled($tmpImg,
            $this->imageCreateFromAny($image),
            0, 0,
            0, 0,
            $width, $height,
            $imgDimensions[0], $imgDimensions[1]
        );

        imagefilter($tmpImg, IMG_FILTER_BRIGHTNESS, $this->params['brightness']);

        $textColor = imagecolorallocate($tmpImg, $this->params["fontColor"][0], $this->params["fontColor"][1], $this->params["fontColor"][2]);

        $lines = $this->prepareLines($text, $width);
        $lineHeight = $this->params['lineHeight'];
        $fontSize = $this->params['fontSize'];
        $textHeight = count($lines) * $lineHeight;
        $vPadding = $this->params['vPadding'];
        $hPadding = $this->params['hPadding'];
        $i = 0;
        foreach ($lines as $line) {
            $yShift = round($i * $lineHeight);
            $textBox = $this->calculateTextBox($fontSize, $this->params['font'], $line);
            switch ($this->params['textAlign']) {
                case 'center':
                    $x = ($width - $textBox['width']) / 2;
                    break;
                case 'right':
                    $x = ($width - $textBox['width']) - $hPadding;
                    break;
                case 'left':
                default:
                    $x = $hPadding;
            }

            switch ($this->params['textPosition']) {
                case 'middle':
                    $y = round(($height / 2) - ($textHeight / 2)) + $vPadding;
                    break;
                case 'bottom':
                    $y = $height - $vPadding - $textHeight + $fontSize;
                    break;
                case 'top':
                default:
                    $y = $fontSize + $vPadding;
            }
            $y += $yShift;

            imagettftext($tmpImg, $fontSize, 0, $x, $y, $textColor, $this->params["font"], $line);
            $i++;
        }

        $result = imagejpeg($tmpImg, $resultImage, $this->params['quality']);
        imagedestroy($tmpImg);

        if ($result)
            return $this->params['previewsUrl'] . "{$resID}.jpg";
        else
            return false;
    }

    /**
     * @param $filepath
     * @return bool|resource
     */
    protected function imageCreateFromAny($filepath)
    {
        $type = exif_imagetype($filepath);
        $allowedTypes = array(
            1,  // [] gif
            2,  // [] jpg
            3,  // [] png
        );
        if (!in_array($type, $allowedTypes)) {
            return false;
        }
        switch ($type) {
            case 1 :
                $im = imageCreateFromGif($filepath);
                break;
            case 2 :
                $im = imageCreateFromJpeg($filepath);
                break;
            case 3 :
                $im = imageCreateFromPng($filepath);
                break;
            default:
                $im = false;
                break;
        }
        return $im;
    }

    /**
     * @param $text
     * @param $imageWidth
     * @return array
     */
    protected function prepareLines($text, $imageWidth)
    {
        $imageWidth = $imageWidth - $this->params['hPadding'] * 2;
        $lines = array();
        $explicitLines = preg_split('/\n|\r\n?/', $text);
        foreach ($explicitLines as $line) {
            $words = explode(" ", $line);
            $line = $words[0];
            for ($i = 1; $i < count($words); $i++) {
                $textBox = $this->calculateTextBox($this->params['fontSize'], $this->params['font'], $line . " " . $words[$i]);
                if ($textBox['width'] >= $imageWidth) {
                    $lines[] = $line;
                    $line = $words[$i];
                } else {
                    $line .= " " . $words[$i];
                }
            }
            $lines[] = $line;
        }

        return $lines;
    }

    /**
     * @param $fontSize
     * @param $font
     * @param $text
     * @return array
     */
    protected function calculateTextBox($fontSize, $font, $text)
    {
        $rect = imagettfbbox($fontSize, 0, $font, $text);
        $minX = min(array($rect[0], $rect[2], $rect[4], $rect[6]));
        $maxX = max(array($rect[0], $rect[2], $rect[4], $rect[6]));
        $minY = min(array($rect[1], $rect[3], $rect[5], $rect[7]));
        $maxY = max(array($rect[1], $rect[3], $rect[5], $rect[7]));

        return array(
            "left" => abs($minX) - 1,
            "top" => abs($minY) - 1,
            "width" => $maxX - $minX,
            "height" => $maxY - $minY,
            "box" => $rect
        );
    }

}