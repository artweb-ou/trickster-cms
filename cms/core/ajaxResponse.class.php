<?php

class ajaxResponse
{
    public $status = 'invalid';
    public $preset;
    public $responseData = [];

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function setResponseData($type, $data)
    {
        if ($converter = $this->getConverter($type)) {
            if ($converter instanceof PresetDataResponseConverterInterface) {
                $converter->setPreset($this->preset);
            }
            $responseData = $converter->convert($data);
            $this->responseData[$type] = $responseData;
        } else {
            $this->responseData[$type] = $data;
        }
    }

    public function setLiteralResponseData($type, $data)
    {
        $this->responseData[$type] = $data;
    }

    protected function getConverter($type)
    {
        $converter = false;

        $className = $type . 'DataResponseConverter';
        $pathsManager = controller::getInstance()->getPathsManager();
        $fileDirectory = $pathsManager->getRelativePath('dataResponseConverters');
        if ($filePath = $pathsManager->getIncludeFilePath($fileDirectory . $type . '.class.php')) {
            include_once($filePath);
            $converter = new $className();
        }
        return $converter;
    }

    /**
     * @param mixed $preset
     */
    public function setPreset($preset)
    {
        $this->preset = $preset;
    }
}

abstract class dataResponseConverter
{
    /**
     * @param structureElement[] $data
     * @return array
     */
    abstract function convert($data);

    protected function htmlToPlainText($src)
    {
        $result = $src;
        $result = html_entity_decode($result, ENT_QUOTES);
        $result = preg_replace('/[\x0A]*/', '', $result);
        $result = preg_replace('#[\n\r\t]#', "", $result);
        $result = preg_replace('#[\s]+#', " ", $result);
        $result = preg_replace('#(</li>|</div>|</td>|</tr>|<br />|<br/>|<br>)#ui', "$1\n", $result);
        $result = preg_replace('#(</h1>|</h2>|</h3>|</h4>|</h5>|</p>)#ui', "$1\n\n", $result);
        $result = strip_tags($result);
        $result = preg_replace('#^ +#m', "", $result); //left trim whitespaces on each line
        $result = preg_replace('#([\n]){2,}#', "\n\n", $result); //limit newlines to 2 max
        $result = trim($result);
        return $result;
    }
}