<?php

class widgetElement extends menuDependantStructureElement
{
    use ConfigurableLayoutsProviderTrait;
    public $dataResourceName = 'module_widget';
    protected $allowedTypes = [];
    public $defaultActionName = 'show';
    public $role = 'content';

    protected function setModuleStructure(&$moduleStructure)
    {
        $moduleStructure['title'] = 'text';
        $moduleStructure['hideTitle'] = 'checkbox';
        $moduleStructure['content'] = 'html';
        $moduleStructure['code'] = 'code';
        $moduleStructure['image'] = 'image';
        $moduleStructure['originalName'] = 'fileName';
        $moduleStructure['image2'] = 'image';
        $moduleStructure['image2OriginalName'] = 'fileName';
        $moduleStructure['layout'] = 'text';
        $moduleStructure['colorLayout'] = 'text';
    }

    public function getSVGcontent($userFile)
    {
        /**
         * @var PathsManager $pathsManager
         */
        $fileUploadPath = $this->getService('PathsManager')->getPath('uploads');

        if (is_file( $fileUploadPath . $userFile)) {
            $userSVGcontent = file_get_contents($fileUploadPath  . $userFile);
            $userSVGcontent = preg_replace('/<!--(.|\s)*?-->/', '', $userSVGcontent); // HTML comments remove
            $userSVGcontent = trim( $userSVGcontent );
            return self::encodeSvg($userSVGcontent);
        }
        else {
            $this->logError("Image file is missing: " . $userFile);
            return '';
        }
    }
    protected static function encodeSvg($input) // bg or dataset for, use: self::encodeSvg($userSVGcontent)
    {
        // https://codepen.io/tigt/post/optimizing-svgs-in-data-uris
        return str_replace([
            '%20',
            '%2F',
            '%3D',
            '%3A',
        ], [
            ' ',
            '/',
            '=',
            ':',
        ], rawurlencode($input));
    }

}


