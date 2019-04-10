<?php

$pathsManager = controller::getInstance()->getPathsManager();
$parentClassPath = $pathsManager->getIncludeFilePath($pathsManager->getRelativePath('structureElements') . 'formSelect/structure.class.php');
include_once($parentClassPath);
// TODO remove this workaround, make a new base class under core
// and make formSelect and basketDropdownElement extend that?

class basketDropdownElement extends formSelectElement
{
    use AutocompleteOptionsTrait;
    protected $allowedTypes = ['basketDropdownOption'];

    protected function setMultiLanguageFields(&$multiLanguageFields)
    {
        $multiLanguageFields[] = 'title';
    }
}
