<?php

class newsMailEmailDispatchmentType extends designThemeEmailDispatchmentType
{
    protected $displayUnsubscribeLink = true;
    protected $displayWebLink = true;
    protected $linksTrackingEnabled = true;

    public function initialize()
    {
        $this->cssThemeFilesStructure = [
            'default' => ['all_variables.less'],
            'public' => ['reset.less', 'component.forms.less'],
            'mallPublic' => ['shared.less', 'newsmails.less'],
            'project' => ['all_variables.less', 'variables.less', 'colors_variables.less', 'shared.less'],
            'email' => ['main.less', 'newsmails.less'],
            'projectEmail' => ['main.less', 'newsmails.less'],
        ];
        $this->imagesThemeName = 'project';
        $this->emailTemplateThemeName = 'projectEmail';
        $this->emailTemplateName = 'standardLayout.tpl';
        $this->contentTemplateThemeName = 'projectDocument';
        $this->contentTemplateName = 'content.newsMail.tpl';
    }
}